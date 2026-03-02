import 'dart:convert';
import 'dart:developer';
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:get/get_rx/src/rx_typedefs/rx_typedefs.dart';
import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class StripeService {
  bool isTest = false;

  init({
    required bool isTest,
    String? publishableKey,
  }) async {
    final key =
        publishableKey ?? Database.settingApiResponseModel?.data?.stripePublicKey ?? '';
    if (key.isEmpty) {
      throw "Stripe publishable key is missing.";
    }

    Stripe.publishableKey = key;
    Stripe.merchantIdentifier = 'merchant.flutter.stripe.test';

    await Stripe.instance.applySettings().catchError((e) {
      log("Stripe Apply Settings => $e");
      throw e.toString();
    });

    this.isTest = isTest;
  }

  Future<dynamic> stripePay({
    required int amount,
    required Callback callback,
    String? currency,
    String? description,
  }) async {
    try {
      if (amount <= 0) {
        throw "Invalid amount.";
      }

      final token = await FirebaseAccessToken.onGet();
      final uid =
          Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId;

      if (token == null || token.isEmpty || uid.isEmpty) {
        throw "User authentication is required.";
      }

      final headers = {
        ApiParams.authToken: "${ApiParams.tokenStartPoint}$token",
        ApiParams.authUid: uid,
        ApiParams.contentType: "application/json",
      };

      if (Api.secretKey.isNotEmpty) {
        headers[ApiParams.key] = Api.secretKey;
      }

      final body = {
        "amount": amount,
        "currency":
            currency ?? Database.settingApiResponseModel?.data?.currency?.currencyCode ?? "USD",
        "description": description ??
            'Name: ${Database.getUserProfileResponseModel?.user?.name} - Email: "${Database.getUserProfileResponseModel?.user?.email}"',
      };

      log("Start Payment Intent Request (Backend)...");

      final response = await http.post(
        Uri.parse("${Api.baseUrl}api/client/stripe/create-payment-intent"),
        headers: headers,
        body: jsonEncode(body),
      );

      log("Payment Intent Response => ${response.body}");

      final decoded = jsonDecode(response.body);
      if (response.statusCode != 200 || decoded["status"] != true) {
        throw decoded["message"] ?? "Failed to create payment intent.";
      }

      final clientSecret = decoded["data"]?["clientSecret"];
      if (clientSecret == null || clientSecret.toString().isEmpty) {
        throw "Missing client secret.";
      }

      final setupPaymentSheetParameters = SetupPaymentSheetParameters(
        paymentIntentClientSecret: clientSecret,
        merchantDisplayName: Database.getUserProfileResponseModel?.user?.name ?? '',
        googlePay: PaymentSheetGooglePay(
          merchantCountryCode: Database.settingApiResponseModel?.data?.currency?.countryCode ?? '',
          testEnv: isTest,
        ),
      );

      await Stripe.instance
          .initPaymentSheet(paymentSheetParameters: setupPaymentSheetParameters)
          .then((value) async {
        await Stripe.instance.presentPaymentSheet().then((value) async {
          log("***** Payment Done *****");
          callback.call();
          Utils.showLog("Stripe Payment Success Method Called....");
          Utils.showLog("Stripe Payment Successfully");
        }).catchError((e) {
          log("Present Payment Sheet Error => $e");
          throw e;
        });
      }).catchError((e) {
        log("Init Payment Sheet Error => $e");
        throw e;
      });

      return decoded;
    } catch (e) {
      log('Error Charging User: ${e.toString()}');
    }
  }
}
