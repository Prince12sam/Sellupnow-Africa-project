import 'dart:developer';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/api/login_api.dart';
import 'package:listify/ui/login_screen/model/login_response_model.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';
import 'package:mobile_device_identifier/mobile_device_identifier.dart';

class RegistrationController extends GetxController {
  final formKey = GlobalKey<FormState>();
  bool isObscure = true;
  bool isObscure1 = true;
  bool isCheck = false;

  String? email;
  Map<String, dynamic> fillArguments = Get.arguments ?? {};

  @override
  void onInit() {
    super.onInit();

    email = Get.arguments['email'] ?? "";
    // Null check before accessing
    fillArguments = Get.arguments ?? {};
    email = fillArguments['email'] ?? "";

    emailController.text = email ?? "";

    Utils.showLog("email:::::::::::$email");
    Utils.showLog("email:::::::::::${emailController.text}");

    super.onInit();
  }

  TextEditingController nameController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();
  TextEditingController confirmPassController = TextEditingController();
  LoginApiResponseModel? loginModel;
  GetUserProfileResponseModel? fetchLoginUserProfileModel;
  // MainScreenController mainScreenController = Get.put(MainScreenController());
  final FirebaseAuth _auth = FirebaseAuth.instance;

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

  void _applyProfileUser(GetUserProfileResponseModel? profileModel) {
    final user = profileModel?.user;
    if (user == null) {
      return;
    }

    final backendId = user.id?.trim() ?? '';
    final firebaseUid = user.firebaseUid?.trim() ?? '';
    final profileImage = user.profileImage?.trim() ?? '';
    final name = user.name?.trim() ?? '';
    final email = user.email?.trim() ?? '';
    final phoneNumber = user.phoneNumber?.trim() ?? '';
    final isVerified = user.isVerified == true;

    Database.onSetIsNewUser(false);
    if (backendId.isNotEmpty) Database.onSetLoginUserId(backendId);
    if (firebaseUid.isNotEmpty) Database.onSetLoginUserFirebaseId(firebaseUid);
    Database.syncIdentityVerificationState(
      isVerified: isVerified,
      verificationStatus: user.verificationStatus,
      verificationId: user.verificationId,
      verificationSubmittedAt: user.verificationSubmittedAt,
    );
    if (profileImage.isNotEmpty) Database.onSetLoginUserProfilePic(profileImage);
    if (name.isNotEmpty) {
      Database.onSetLoginUserName(name);
      Database.onSetLoginUserNickName(name);
    }
    if (email.isNotEmpty) Database.onSetLoginUserEmail(email);
    if (phoneNumber.isNotEmpty) Database.onSetLoginUserPhoneNumber(phoneNumber);

    Database.getUserProfileResponseModel = profileModel;
  }

  bool isEmailValid(String email) {
    final emailRegex = RegExp(r'^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$');
    return emailRegex.hasMatch(email);
  }

  void onClickObscure() {
    log("isObscure :: $isObscure");
    isObscure = !isObscure;
    update();
  }

  void onClickObscure1() {
    log("isObscure1 :: $isObscure1");
    isObscure1 = !isObscure1;
    update();
  }

  void onClickCheck() {
    log("isCheck:: $isCheck");
    isCheck = !isCheck;
    update([Constant.idAcceptTerms]);
  }

  bool validateRegistration() {
    final isValid = formKey.currentState?.validate() ?? false;

    if (!isValid) {
      return false;
    }

    Get.dialog(LoadingWidget(), barrierDismissible: false);
    final name = nameController.text.trim();
    final email = emailController.text.trim();
    final password = passwordController.text.trim();
    final rePassword = confirmPassController.text.trim();

    if (name.isEmpty) {
      Utils.showToast(Get.context!, "Please enter your full name");
      return false;
    }

    if (email.isEmpty) {
      Utils.showToast(Get.context!, "Please enter your email");
      return false;
    }

    if (!isEmailValid(email)) {
      Utils.showToast(Get.context!, "Please enter a valid email address");
      return false;
    }

    if (password.isEmpty) {
      Utils.showToast(Get.context!, "Please enter your password");
      return false;
    }

    if (password.length < 6) {
      Utils.showToast(Get.context!, "Password must be at least 6 characters");
      return false;
    }

    if (rePassword.isEmpty) {
      Utils.showToast(Get.context!, "Please re-enter your password");
      return false;
    }

    if (rePassword != password) {
      Utils.showToast(Get.context!, "Passwords do not match");
      return false;
    }
    Get.back();
    return true;
  }

  Future<void> signUpWithEmailPassword() async {
    try {
      // Get identity and FCM token first
      final identity = (await MobileDeviceIdentifier().getDeviceId())!;
      String? fcmToken;
      try { fcmToken = await FirebaseMessaging.instance.getToken().timeout(const Duration(seconds: 5)); } catch (_) {}
      Database.onSetIdentity(identity);
      Database.onSetFcmToken(fcmToken ?? "");

      log("Database.identity :: ${Database.identity}");
      log("Database.fcmToken :: ${Database.fcmToken}");

      UserCredential userCredential;

      // Firebase signup
      try {
        userCredential = await _auth.createUserWithEmailAndPassword(
          email: emailController.text.trim(),
          password: passwordController.text.trim(),
        );
        log("Signup successful! UID: ${userCredential.user?.uid}");
      } on FirebaseAuthException catch (e) {
        if (e.code == 'email-already-in-use') {
          // Firebase account exists but MongoDB user might not (desync).
          // Fall back to signing in to Firebase, then let backend create/find the user.
          log("Firebase account exists, falling back to signIn...");
          try {
            userCredential = await _auth.signInWithEmailAndPassword(
              email: emailController.text.trim(),
              password: passwordController.text.trim(),
            );
            log("SignIn fallback successful! UID: ${userCredential.user?.uid}");
          } catch (signInError) {
            // Password mismatch means the account truly belongs to someone else
            Utils.showToast(Get.context!,
                "This email is already registered. Please sign in instead.");
            log("SignIn fallback failed: $signInError");
            return;
          }
        } else if (e.code == 'weak-password') {
          Utils.showToast(
              Get.context!, "Password is too weak. Use at least 6 characters.");
          return;
        } else {
          Utils.showToast(Get.context!, "Registration failed: ${e.message}");
          return;
        }
      }

      // Show loading and call backend API
      Get.dialog(LoadingWidget(), barrierDismissible: false);
      Database.onSetUserExist(false);

      loginModel = await LoginApi.callApi(
        countryCode: Database.selectedCountryCode,
        loginType: 4,
        email: userCredential.user?.email ?? "",
        identity: Database.identity,
        fcmToken: Database.fcmToken,
        userName: nameController.text,
        password: passwordController.text,
        authUid: userCredential.user?.uid,
      );

      if (loginModel?.status == true) {
        Database.onSetIsLogin(true);
        Database.onSetSeenOnboarding(true);
        Database.onSetLoginType(loginModel?.user?.loginType ?? 0);

        // Store user data from login response immediately
        _storeUserFromLoginResponse(loginModel!, firebaseUID: userCredential.user?.uid);

        try {
          await onGetProfile(
            loginUserId: userCredential.user?.uid ?? loginModel?.user?.firebaseUid ?? '',
            loginType: 4,
          );
        } catch (e) {
          Utils.showLog("Profile enrichment failed (non-critical): $e");
        }

        Get.back(); // Dismiss loading

        if (loginModel?.signUp == true) {
          Database.onSetFillProfile(false);
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
        Get.back(); // Dismiss loading
        Utils.showToast(Get.context!,
            loginModel?.message ?? EnumLocale.txtSomeThingWentWrong.name.tr);
        Utils.showLog("Login Api Calling Failed: ${loginModel?.message}");
      }
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      log("Signup failed: $e");
      Utils.showToast(Get.context!, "Something went wrong. Please try again.");
    }
  }

  Future<void> onGetProfile(
      {required String loginUserId, required int loginType}) async {
    // Get.dialog(const LoadingUi(), barrierDismissible: false); // Start Loading...
    fetchLoginUserProfileModel =
        await GetUserProfileApi.callApi(loginUserId: loginUserId);

    log("fetchLoginUserProfileModel?.user?.id  :: ${fetchLoginUserProfileModel?.user?.id}");

    if (fetchLoginUserProfileModel?.user != null) {
      log("fetchLoginUserProfileModel?.user?.Email  :: ${fetchLoginUserProfileModel?.user?.email}");

      _applyProfileUser(fetchLoginUserProfileModel);
      log("Database.loginUserEmail  ${Database.loginUserEmail}");
      log("Database.loginUserName  ${Database.loginUserName}");
      log("Database.loginUserFirebaseId  ${Database.loginUserFirebaseId}");
      log("Database.image  ${Database.loginUserProfilePic}");
    } else {
      Utils.showLog("Get Profile Api Calling Failed !!");
    }
  }
}
