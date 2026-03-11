import 'dart:developer';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:intl_phone_field/countries.dart' as intl_countries;
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/mobile_number_screen/api/fetch_phone_countries_api.dart';
import 'package:listify/utils/utils.dart';

class MobileNumberController extends GetxController {
  final FirebaseAuth auth = FirebaseAuth.instance;

  final formKey = GlobalKey<FormState>();
  final numberController = TextEditingController();

  String verificationId = '';
  String? dialCode;

  /// Countries fetched from backend — only admin-created countries are shown.
  List<intl_countries.Country> phoneCountries = [];
  String initialCountryCode = 'GH';

  @override
  void onInit() {
    numberController.clear();
    super.onInit();
    _fetchPhoneCountries(); // async — calls update() when done
  }

  /// Converts a 2-letter ISO country code to its emoji flag.
  String _flagEmoji(String code) {
    return code.toUpperCase().codeUnits
        .map((c) => String.fromCharCode(0x1F1E6 + c - 65))
        .join();
  }

  Future<void> _fetchPhoneCountries() async {
    final result = await FetchPhoneCountriesApi.call();
    if (result != null && result.isNotEmpty) {
      phoneCountries = result.map((c) {
        // Use the package's own entry to get correct validation lengths.
        try {
          return intl_countries.countries
              .firstWhere((p) => p.code == c.countryCode);
        } catch (_) {
          return intl_countries.Country(
            name: c.name,
            flag: _flagEmoji(c.countryCode),
            code: c.countryCode,
            dialCode: c.dialCode,
            nameTranslations: const {},
            minLength: 5,
            maxLength: 15,
          );
        }
      }).toList();
      initialCountryCode = result.first.countryCode;
      update();
    }
  }

  void sendOtp(BuildContext context) async {
    if (!formKey.currentState!.validate()) {
      Utils.showToast(context, "Enter a valid mobile number");
      return;
    }

    final number = numberController.text.trim();
    final code = dialCode ?? '+91';
    final phoneNumber = '$code$number';

    if (number.isEmpty) {
      Utils.showToast(context, "Please enter mobile number");
      return;
    }

    if (number.length < 7 || number.length > 15) {
      Utils.showToast(context, "Enter a valid mobile number");
      return;
    }

    try {
      Get.dialog(LoadingWidget(), barrierDismissible: false);

      await auth.verifyPhoneNumber(
        phoneNumber: phoneNumber,
        timeout: const Duration(seconds: 60),
        verificationCompleted: (PhoneAuthCredential credential) async {
          await auth.signInWithCredential(credential);
          // Utils.showToast(Get.context!, "Auto login successful");
        },
        verificationFailed: (FirebaseAuthException e) {
          if (Get.isDialogOpen == true) {
            Get.back();
          }
          Utils.showToast(Get.context!, e.message ?? "OTP sending failed");
        },
        codeSent: (String id, int? resendToken) {
          if (Get.isDialogOpen == true) {
            Get.back();
          }
          verificationId = id;
          Get.toNamed(
            AppRoutes.verifyOtp,
            arguments: [number, code, verificationId],
          );
        },
        codeAutoRetrievalTimeout: (String id) {
          log("Auto-retrieval timeout reached");
          verificationId = id;
          update();
        },
      );
    } catch (e) {
      if (Get.isDialogOpen == true) {
        Get.back();
      }
      Utils.showToast(Get.context!, "OTP process failed: $e");
    }
  }
}
