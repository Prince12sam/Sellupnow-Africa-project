import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/wallet_screen/model/wallet_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class WalletApi {
  static int startPagination = 0;
  static const int limitPagination = 20;

  static Future<Map<String, String>> _headers() async {
    final token = await FirebaseAccessToken.onGet();
    return {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid:
          Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      ApiParams.contentType: "application/json",
    };
  }

  static Future<WalletResponseModel?> callBalanceApi() async {
    Utils.showLog("Wallet Balance Api Calling...");
    try {
      final response =
          await http.get(Uri.parse(Api.walletBalance), headers: await _headers());
      Utils.showLog("Wallet Balance Api Response Code => ${response.statusCode}");
      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        Utils.showLog("Wallet Balance Api Response => $jsonResponse");
        return WalletResponseModel.fromJson(jsonResponse);
      }
    } catch (error) {
      Utils.showLog("Wallet Balance Api Error => $error");
    }
    return null;
  }

  static Future<WalletResponseModel?> callTransactionsApi() async {
    Utils.showLog("Wallet Transactions Api Calling...");
    startPagination += 1;
    final query = Uri(queryParameters: {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    }).query;
    final uri = Uri.parse(Api.walletTransactions + (query.isNotEmpty ? query : ''));

    try {
      final response = await http.get(uri, headers: await _headers());
      Utils.showLog("Wallet Transactions Api => ${response.statusCode}");
      if (response.statusCode == 200) {
        return WalletResponseModel.fromJson(json.decode(response.body));
      }
    } catch (error) {
      Utils.showLog("Wallet Transactions Api Error => $error");
    }
    return null;
  }

  /// Initialize a Paystack wallet top-up. Returns {authorization_url, reference}.
  static Future<Map<String, dynamic>?> topupInit(double amount) async {
    Utils.showLog("Wallet Topup Init Calling... amount=$amount");
    try {
      final response = await http.post(
        Uri.parse(Api.walletTopupInit),
        headers: await _headers(),
        body: json.encode({'amount': amount}),
      );
      Utils.showLog("Wallet Topup Init => ${response.statusCode} ${response.body}");
      if (response.statusCode == 200) {
        final decoded = json.decode(response.body);
        if (decoded['status'] == true) return decoded;
      }
    } catch (e) {
      Utils.showLog("Wallet Topup Init Error => $e");
    }
    return null;
  }

  /// Verify a completed Paystack top-up. Returns {status, balance}.
  static Future<Map<String, dynamic>?> topupVerify(String reference) async {
    Utils.showLog("Wallet Topup Verify Calling... ref=$reference");
    try {
      final response = await http.post(
        Uri.parse(Api.walletTopupVerify),
        headers: await _headers(),
        body: json.encode({'reference': reference}),
      );
      Utils.showLog("Wallet Topup Verify => ${response.statusCode} ${response.body}");
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (e) {
      Utils.showLog("Wallet Topup Verify Error => $e");
    }
    return null;
  }
}
