import 'dart:async';
import 'dart:developer';

import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/login_screen/api/login_api.dart';
import 'package:listify/ui/login_screen/controller/login_screen_controller.dart';
import 'package:listify/ui/login_screen/model/login_response_model.dart';
import 'package:listify/ui/login_screen/model/user_exist_response_model.dart';
import 'package:listify/ui/mobile_number_screen/controller/mobile_number_controller.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';
import 'package:mobile_device_identifier/mobile_device_identifier.dart';

class VerifyOtpController extends GetxController {
  final formKey = GlobalKey<FormState>();
  dynamic args = Get.arguments;
  int countdown = 0;
  bool isOtpExpired = false;

  Timer? timer;
  String? mobileNumber;
  String? dialCode;
  bool isLoading = false;

  TextEditingController otpController = TextEditingController();
  MobileNumberController mobileNumberController = Get.find<MobileNumberController>();
  LoginScreenController mainScreenController = Get.find<LoginScreenController>();

  String? verificationId;
  LoginApiResponseModel? loginModel;
  UserExistResponseModel? checkUserExistModel;

  /// Store essential user data directly from the login API response.
  void _storeUserFromLoginResponse(LoginApiResponseModel loginModel, {String? firebaseUID}) {
    final user = loginModel.user;
    if (user == null) return;

    Database.onSetLoginUserId(user.id ?? '');
    Database.onSetLoginUserFirebaseId(firebaseUID ?? user.firebaseUid ?? '');
    Database.onSetLoginUserName(user.name ?? '');
    Database.onSetLoginUserNickName(user.name ?? '');
    Database.onSetLoginUserEmail(user.email ?? '');
    Database.onSetLoginUserProfilePic(user.profileImage ?? '');
    Database.onSetLoginUserPhoneNumber(user.phoneNumber ?? '');
    Database.syncIdentityVerificationState(
      isVerified: user.isVerified == true,
      verificationStatus: user.verificationStatus,
      verificationId: user.verificationId,
      verificationSubmittedAt: user.verificationSubmittedAt,
    );
    if (user.country != null && user.country!.isNotEmpty) {
      Database.onSetSelectedCountryCode(user.country!);
      Database.getDialCode();
    }
    Database.onSetIsNewUser(false);
  }

  @override
  void onInit() async {
    await getDataFromArgs();
    startCountdown();

    super.onInit();
  }

  @override
  void dispose() {
    otpController.clear();
    super.dispose();
  }

  getDataFromArgs() {
    if (args != null) {
      if (args[0] != null) mobileNumber = args[0];
      if (args[1] != null) dialCode = args[1];
      if (args[2] != null) verificationId = args[2];

      log("Mobile Number :: $mobileNumber");
      log("Selected Country Code :: $dialCode");
      log("Verification ID :: $verificationId");
    }
  }

  /// verify otp
  Future<void> verifyOtp() async {
    Database.onSetDemoUser(false);
    final identity = (await MobileDeviceIdentifier().getDeviceId())!;
    String? fcmToken;
    try { fcmToken = await FirebaseMessaging.instance.getToken().timeout(const Duration(seconds: 5)); } catch (_) {}
    Database.onSetFcmToken(fcmToken ?? "");
    Database.onSetIdentity(identity);

    log("Database.identity :: ${Database.identity}");
    log("Database.fcmToken :: ${Database.fcmToken}");

    final smsCode = otpController.text.trim();

    if (smsCode.isEmpty) {
      Utils.showToast(Get.context!, "Please enter OTP");
      return;
    }

    if (isOtpExpired) {
      Utils.showToast(Get.context!, "OTP expired. Please request a new one.");
      return;
    }

    if (verificationId == null) {
      Utils.showToast(Get.context!, "Verification ID missing");
      return;
    }

    isLoading = true;
    update([Constant.idVerifyOtp]);

    try {
      final credential = PhoneAuthProvider.credential(
        verificationId: verificationId!,
        smsCode: smsCode,
      );

      final userCredential = await FirebaseAuth.instance.signInWithCredential(credential);

      // Success
      log("User logged in: ${userCredential.user?.uid}");

      if (userCredential.user?.uid != null) {
        Get.dialog(LoadingWidget(), barrierDismissible: false);

        loginModel = await LoginApi.callApi(
          countryCode: Database.selectedCountryCode,
          loginType: 1,
          identity: Database.identity,
          fcmToken: Database.fcmToken,
          mobileNumber: "$mobileNumber",
        );

        log("userCredential?.user?.id :::  ${userCredential.user?.uid}");
        log("loginModel?.status ${loginModel?.status}");
        log("loginModel?.signUp ${loginModel?.signUp}");

        if (loginModel?.status == true) {
          Database.onSetIsLogin(true);
          Database.onSetSeenOnboarding(true);

          Database.onSetLoginType(loginModel?.user?.loginType ?? 0);

          // Store user data from login response immediately
          _storeUserFromLoginResponse(loginModel!, firebaseUID: userCredential.user?.uid);

          try {
            await mainScreenController.onGetProfile(
              loginUserId: userCredential.user!.uid,
              loginType: 1,
            );
          } catch (e) {
            Utils.showLog("Profile enrichment failed (non-critical): $e");
          }

          if (loginModel?.signUp == true) {
            Database.onSetFillProfile(false);

            log("Database.loginUserName  ${Database.loginUserName}");
            log("Database.loginUserProfilePic  ${Database.loginUserProfilePic}");
            log("Database.loginUserEmail  ${Database.loginUserEmail}");

            Get.offAllNamed(AppRoutes.fillProfileScreen, arguments: [
              Database.loginUserName,
              Database.loginUserProfilePic,
              Database.loginUserEmail,
            ]);
          } else {
            Database.onSetFillProfile(true);

            Get.toNamed(AppRoutes.bottomBar);
          }
        } else {
          Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          Utils.showLog("mobile number Login Api Calling Failed !!");
        }

        // Get.back();
      } else {
        Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
        Utils.showLog("mobile number Login Failed !!");
      }
      // Navigate to main screen
      // Get.offAllNamed(AppRoutes.bottomBar);
    } on FirebaseAuthException catch (e) {
      Utils.showToast(Get.context!, e.message ?? "Invalid OTP");
    } catch (e) {
      Utils.showToast(Get.context!, "Something went wrong");
    } finally {
      if (Get.isDialogOpen == true) {
        Get.back();
      }
      isLoading = false;
      update();
    }
  }

  /// resend otp click
  onResendOtpClick(BuildContext context) async {
    Utils.currentFocus(context);
    Utils.showToast(Get.context!, "Please check your SMS inbox");
    Constant.storage.write("isResendOtp", true);

    otpController.clear();
    startCountdown();

    final number = mobileNumberController.numberController.text.trim();
    final code = mobileNumberController.dialCode ?? '+91';
    final phoneNumber = '$code$number';

    try {
      await FirebaseAuth.instance.verifyPhoneNumber(
        phoneNumber: phoneNumber,
        timeout: const Duration(seconds: 60),
        verificationCompleted: (PhoneAuthCredential credential) async {
          await FirebaseAuth.instance.signInWithCredential(credential);
          if (Get.isDialogOpen == true) Get.back(); // close loader
          // Utils.showToast(Get.context!, "Auto login successful");
        },
        verificationFailed: (FirebaseAuthException e) {
          if (Get.isDialogOpen == true) Get.back(); // close loader
          Utils.showToast(Get.context!, e.message ?? "OTP sending failed");
        },
        codeSent: (String id, int? resendToken) {
          if (Get.isDialogOpen == true) Get.back(); // close loader
          verificationId = id;
          Utils.showToast(Get.context!, "OTP resent successfully");
        },
        codeAutoRetrievalTimeout: (String id) {
          verificationId = id;
        },
      );
    } catch (e) {
      Utils.showToast(Get.context!, "Resend OTP failed: $e");
    }
  }

  String get formattedCountdown {
    final minutes = (countdown ~/ 60).toString().padLeft(2, '0');
    final seconds = (countdown % 60).toString().padLeft(2, '0');
    return '$minutes:$seconds';
  }

  void startCountdown() {
    countdown = 30;
    update([Constant.idResendOtp]);

    timer?.cancel();
    timer = Timer.periodic(const Duration(seconds: 1), (t) {
      if (countdown > 0) {
        countdown--;
        update([Constant.idResendOtp]);
      } else {
        isOtpExpired = true;
        t.cancel();
        Constant.storage.write("isResendOtp", false);
        update([Constant.idResendOtp]);
      }
    });
  }

  @override
  void onClose() {
    timer?.cancel();
    super.onClose();
  }
}
