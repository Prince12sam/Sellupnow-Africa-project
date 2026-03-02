import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:listify/custom/custom_web_view/web_view_screen.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/login_screen/api/check_user_exist_api.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/api/login_api.dart';
import 'package:listify/ui/login_screen/model/login_response_model.dart';
import 'package:listify/ui/login_screen/model/user_exist_response_model.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/ui/profile_screen_view/api/setting_api.dart';
import 'package:listify/ui/profile_screen_view/model/setting_api_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';
import 'package:mobile_device_identifier/mobile_device_identifier.dart';

class LoginScreenController extends GetxController {
  String loginType = '4';
  bool isLoading = false;
  LoginApiResponseModel? loginModel;
  bool isObscure = true;
  TextEditingController emailTxtController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  TextEditingController passwordTxtController = TextEditingController();
  int? selectedValue;
  GetUserProfileResponseModel? getUserProfileResponseModel;

  SettingApiResponseModel? settingApiResponseModel;
  UserExistResponseModel? checkUserExistModel;
  // String emailController = 'testUser@example.com';
  // String passwordController = 'TestUser@123456';

  Map<String, dynamic> registerArguments = Get.arguments ?? {};

  @override
  onInit() {
    Utils.showLog("kjhgkjhgjbh");
    settingApi();
    passwordTxtController.clear();
    emailTxtController.clear();
    super.onInit();
  }

  bool isEmailValid(String email) {
    final emailRegex = RegExp(r'^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$');
    return emailRegex.hasMatch(email);
  }

  onClickObscure() {
    Utils.showLog("isObscure :: $isObscure");
    isObscure = !isObscure;
    update();
  }

  /// setting Api
  settingApi() async {
    settingApiResponseModel = await SettingApi.callApi();
    Database.settingApiResponseModel = settingApiResponseModel;
    Database.onSetCurrencySymbol(
        Database.settingApiResponseModel?.data?.currency?.symbol ?? "");
    Utils.showLog(
        "Database.currencySymbol${Database.onSetCurrencySymbol(Database.settingApiResponseModel?.data?.currency?.symbol ?? "")}");
    Utils.showLog("Database.currencySymbol${Database.currencySymbol}");
    update();
    Utils.showLog(
        "Setting Api Response Model :: ${settingApiResponseModel?.toJson()}");
  }

  /// email password login
  emailPasswordLogin() async {
    FocusManager.instance.primaryFocus?.unfocus();
    // Utils.showLog("Email => ${emailController.text}");
    // Utils.showLog("Password => ${passwordController.text}");

    if (emailTxtController.text.isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.desEnterEmail.name.tr);
      return;
    } else if (passwordTxtController.text.isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.desEnterPassword.name.tr);
      return;
    }

    // Step 1: Check if user exists using your custom API
    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      final checkUserExistModel = await CheckUserExistApi.callApi(
        loginType: loginType,
        email: emailTxtController.text,
        password: passwordTxtController.text,
      );
      Database.onSetUserExist(true);
      Utils.showLog("Database.userExist :: ${Database.userExist}");

      if (checkUserExistModel?.status == true &&
          checkUserExistModel?.isLogin == true) {
        // Step 2: User exists – Sign in with Firebase
        try {
          UserCredential userCredential =
              await FirebaseAuth.instance.signInWithEmailAndPassword(
            email: emailTxtController.text.trim(),
            password: passwordTxtController.text.trim(),
          );

          final token = await FirebaseAccessToken.onGet();
          String? firebaseUID = userCredential.user?.uid;

          Utils.showLog("Signed In Firebase UID => $firebaseUID");
          Utils.showLog("Firebase Token => $token");

          // Optional: Call Login API if needed after sign-in
          loginModel = await LoginApi.callApi(
            loginType: int.parse(loginType),
            fcmToken: Database.fcmToken,
            identity: Database.identity,
            profilePic: '',
            email: emailTxtController.text.trim(),
            password: passwordTxtController.text.trim(),
            // authToken: "Bearer $token",
            authUid: firebaseUID,
          );

          if (loginModel?.status == true) {
            GetUserProfileResponseModel? getUserProfileResponseModel;

            getUserProfileResponseModel = await GetUserProfileApi.callApi(
                loginUserId: userCredential.user?.uid ?? '');

            Database.getUserProfileResponseModel = getUserProfileResponseModel;

            Utils.showLog("Login Response => ${loginModel?.toJson()}");
          } else {
            Get.back(); // Stop loading
            Utils.showToast(
                Get.context!,
                loginModel?.message ??
                    EnumLocale.txtSomeThingWentWrong.name.tr);
            Utils.showLog("Login API call failed: ${loginModel?.message}");
          }

          Utils.showToast(Get.context!,
              "Signed in existing user: ${userCredential.user?.uid}");
        } catch (firebaseError) {
          // Firebase sign-in error handling
          Get.back();
          isLoading = false;
          Utils.showToast(Get.context!, "Error: ${firebaseError.toString()}");
          Utils.showLog("Firebase Sign-In Failed => $firebaseError");
        }
      } else {
        // Step 3: User does not exist – Create new Firebase user navigate signup screen
        if (checkUserExistModel?.status == false &&
            checkUserExistModel?.isLogin == false) {
          Get.back();
          Utils.showToast(
              Get.context!,
              checkUserExistModel?.message ??
                  "Password doesn't match for this user.");
        } else {
          Get.back();
          Utils.showToast(
              Get.context!, EnumLocale.txtYoumusthavesignup.name.tr);
          // Get.toNamed(AppRoutes.register);
        }
      }
    } catch (e) {
      Get.back();
      isLoading = false;
      Utils.showToast(Get.context!, "Error: ${e.toString()}");
      Utils.showLog("Sign In Failed => $e");
    }
  }

  Future<void> onClickPrivacyPolicy() async {
    final String privacyPolicyUrl =
        Database.settingApiResponseModel?.data?.privacyPolicyUrl ?? '';
    Utils.showLog('url:::::::::::::$privacyPolicyUrl');

    if (privacyPolicyUrl.isNotEmpty) {
      Get.to(
          () => WebViewScreen(url: privacyPolicyUrl, screen: "Privacy Policy"));
    } else {
      Utils.showLog('Invalid privacy policy URL');
    }
  }

  ///radio toggle

  void toggleValue(int value) {
    if (selectedValue == value) {
      selectedValue = null;
    } else {
      selectedValue = value;
    }
    update([Constant.radioButton]);
  }

  /// user get profile

  Future<void> onGetProfile(
      {required String loginUserId, required int loginType}) async {
    getUserProfileResponseModel =
        await GetUserProfileApi.callApi(loginUserId: loginUserId);
    Database.getUserProfileResponseModel = getUserProfileResponseModel;

    Utils.showLog(
        "fetchLoginUserProfileModel?.user?.id${getUserProfileResponseModel?.user?.id}");

    if (getUserProfileResponseModel?.user?.loginType != null) {
      Database.onSetIsNewUser(false);
      Utils.showLog(
          "fetchLoginUserProfileModel?.user?.Email${getUserProfileResponseModel?.user?.email}");

      Database.onSetLoginUserId(getUserProfileResponseModel!.user!.id!);
      Database.onSetLoginUserFirebaseId(
          getUserProfileResponseModel!.user!.firebaseUid!);
      Database.onSetLoginUserProfilePic(
          getUserProfileResponseModel?.user?.profileImage ?? "");
      Database.onSetLoginUserName(
          getUserProfileResponseModel!.user!.name ?? "");
      Database.onSetLoginUserNickName(
          getUserProfileResponseModel?.user?.name ?? "");
      Database.onSetLoginUserEmail(getUserProfileResponseModel!.user!.email!);
      // Database.onSetLoginUserCountry(getUserProfileResponseModel!.user!.country!);
      // Database.onSetLoginUserCountryFlag(getUserProfileResponseModel!.user!.countryFlag!);
      // Database.onSetLoginUserBirthDate(getUserProfileResponseModel?.user?.birthDate ?? "");
      // Database.onSetLoginUserGender(getUserProfileResponseModel?.user?.gender ?? "Male");
      Database.onSetLoginUserPhoneNumber(
          getUserProfileResponseModel?.user?.phoneNumber ?? "");
      Database.getUserProfileResponseModel = getUserProfileResponseModel;
      Utils.showLog("Database.loginUserId  ${Database.loginUserId}");
      Utils.showLog("Database.loginUserEmail  ${Database.loginUserEmail}");
      Utils.showLog(
          "Database.loginUserFirebaseId  ${Database.loginUserFirebaseId}");
      Utils.showLog("Database.image  ${Database.loginUserProfilePic}");
      Utils.showLog("Database.name  ${Database.loginUserName}");
      Utils.showLog(
          "Database.name  ${Database.getUserProfileResponseModel?.user?.name}");
    } else {
      Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
      Utils.showLog("Get Profile Api Calling Failed !!");
    }
  }

  /// email pass login

  Future<void> onClickSignIn() async {
    Database.onSetDemoUser(false);
    Utils.showLog("demo login>>>>>>>>>>>>>>>>${Database.demoUser}");
    if (selectedValue != 1) {
      Utils.showToast(
          Get.context!, "Please agree to the Privacy Policy to proceed.");
      return;
    }
    FocusManager.instance.primaryFocus?.unfocus();
    Utils.showLog("Email => ${emailTxtController.text}");
    Utils.showLog("Password => ${passwordTxtController.text}");
    final fcmToken = await FirebaseMessaging.instance.getToken();
    Database.onSetFcmToken(fcmToken ?? "");
    Utils.showLog("fcmToken => ${Database.fcmToken}");

    final identity = (await MobileDeviceIdentifier().getDeviceId())!;
    Database.onSetIdentity(identity);
    Utils.showLog("identity => ${Database.identity}");

    if (emailTxtController.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.desEnterEmail.name.tr);
      return;
    } else if (passwordTxtController.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.desEnterPassword.name.tr);
      return;
    }

    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      // Check if user exists first before trying Firebase login
      checkUserExistModel = await CheckUserExistApi.callApi(
        email: emailTxtController.text.trim(),
        password: passwordTxtController.text.trim(),
        identity: Database.identity,
        loginType: 4.toString(),
      );

      Database.onSetUserExist(true);
      Utils.showLog("Database.userExist :: ${Database.userExist}");

      if (checkUserExistModel?.status == true &&
          checkUserExistModel?.isLogin == true) {
        // User exists, proceed to Firebase login
        try {
          UserCredential userCredential =
              await FirebaseAuth.instance.signInWithEmailAndPassword(
            email: emailTxtController.text.trim(),
            password: passwordTxtController.text.trim(),
          );

          final token = await FirebaseAccessToken.onGet();
          String? firebaseUID = userCredential.user?.uid;

          Utils.showLog("Firebase UID => $firebaseUID");
          Utils.showLog("Firebase Token => $token");

          // Now call the login API with the Firebase credentials
          loginModel = await LoginApi.callApi(
            countryCode: Database.selectedCountryCode,
            loginType: 4,
            email: emailTxtController.text.trim(),
            password: passwordTxtController.text.trim(),
            identity: Database.identity,
            fcmToken: Database.fcmToken,
            authToken: token,
            authUid: firebaseUID,
          );

          if (loginModel?.status == true) {
            Database.onSetIsLogin(true);
            Database.onSetLoginType(loginModel?.user?.loginType ?? 0);
            Database.onSetSeenOnboarding(true);
            Database.onSetFillProfile(true);

            Get.back(); // Stop loading

            if (loginModel?.signUp == true) {
              Database.onSetFillProfile(false);
              Get.offAllNamed(AppRoutes.fillProfileScreen, arguments: [
                Database.loginUserName,
                Database.loginUserProfilePic,
                Database.loginUserEmail,
              ]);
            } else {
              Database.onSetFillProfile(true);
              await onGetProfile(
                loginUserId: userCredential.user!.uid,
                loginType: 4,
              );
              Database.getUserProfileResponseModel =
                  getUserProfileResponseModel;
              Get.toNamed(AppRoutes.bottomBar);
            }
          } else {
            Get.back(); // Stop loading
            Utils.showToast(
                Get.context!,
                loginModel?.message ??
                    EnumLocale.txtSomeThingWentWrong.name.tr);
            Utils.showLog("Login API call failed: ${loginModel?.message}");
          }
        } catch (firebaseError) {
          // Firebase sign-in error handling
          Get.back();
          isLoading = false;
          Utils.showToast(Get.context!, "Error: ${firebaseError.toString()}");
          Utils.showLog("Firebase Sign-In Failed => $firebaseError");
        }
      } else {
        if (checkUserExistModel?.status == false &&
            checkUserExistModel?.isLogin == false) {
          Get.back();
          Utils.showToast(
              Get.context!,
              checkUserExistModel?.message ??
                  "Password doesn't match for this user.");
        } else {
          Get.back();
          Utils.showToast(
              Get.context!, EnumLocale.txtYoumusthavesignup.name.tr);
          registerArguments.addAll({
            'email': emailTxtController.text.trim(),
          });

          Utils.showLog("registerArguments:::::::::::$registerArguments");
          Get.toNamed(AppRoutes.register, arguments: registerArguments);
        }
      }
    } catch (e) {
      Get.back();
      isLoading = false;
      Utils.showToast(Get.context!, "Error: ${e.toString()}");
      Utils.showLog("Sign In Failed => $e");
    }
  }

  Future<void> onDemoClickSignIn() async {
    String demoEmail = 'thomas@gmail.com';
    String demoPass = 'Thomas@123';
    Database.onSetDemoUser(true);

    FocusManager.instance.primaryFocus?.unfocus();
    Utils.showLog("Demo Email => $demoEmail");
    Utils.showLog("DEmo Password => $demoPass");
    final fcmToken = await FirebaseMessaging.instance.getToken();
    Database.onSetFcmToken(fcmToken ?? "");
    Utils.showLog("fcmToken => ${Database.fcmToken}");

    final identity = (await MobileDeviceIdentifier().getDeviceId())!;
    Database.onSetIdentity(identity);
    Utils.showLog("identity => ${Database.identity}");

    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      // Check if user exists first before trying Firebase login
      checkUserExistModel = await CheckUserExistApi.callApi(
        email: demoEmail,
        password: demoPass,
        identity: Database.identity,
        loginType: 4.toString(),
      );

      Database.onSetUserExist(true);
      Utils.showLog("Database.userExist :: ${Database.userExist}");

      if (checkUserExistModel?.status == true &&
          checkUserExistModel?.isLogin == true) {
        // User exists, proceed to Firebase login
        try {
          UserCredential userCredential =
              await FirebaseAuth.instance.signInWithEmailAndPassword(
            email: demoEmail,
            password: demoPass,
          );

          final token = await FirebaseAccessToken.onGet();
          String? firebaseUID = userCredential.user?.uid;

          Utils.showLog("Firebase UID => $firebaseUID");
          Utils.showLog("Firebase Token => $token");

          // Now call the login API with the Firebase credentials
          loginModel = await LoginApi.callApi(
            countryCode: Database.selectedCountryCode,
            loginType: 4,
            email: demoEmail,
            password: demoPass,
            identity: Database.identity,
            fcmToken: Database.fcmToken,
            authToken: token,
            authUid: firebaseUID,
          );

          if (loginModel?.status == true) {
            Database.onSetIsLogin(true);
            Database.onSetLoginType(loginModel?.user?.loginType ?? 0);
            Database.onSetSeenOnboarding(true);
            Database.onSetFillProfile(true);

            Get.back(); // Stop loading

            if (loginModel?.signUp == true) {
              Database.onSetFillProfile(false);
              Get.offAllNamed(AppRoutes.fillProfileScreen, arguments: [
                Database.loginUserName,
                Database.loginUserProfilePic,
                Database.loginUserEmail,
              ]);
            } else {
              Database.onSetFillProfile(true);
              await onGetProfile(
                loginUserId: userCredential.user!.uid,
                loginType: 4,
              );
              Database.getUserProfileResponseModel =
                  getUserProfileResponseModel;
              Get.toNamed(AppRoutes.bottomBar);
            }
          } else {
            Get.back(); // Stop loading
            Utils.showToast(
                Get.context!,
                loginModel?.message ??
                    EnumLocale.txtSomeThingWentWrong.name.tr);
            Utils.showLog("Login API call failed: ${loginModel?.message}");
          }
        } catch (firebaseError) {
          // Firebase sign-in error handling
          Get.back();
          isLoading = false;
          Utils.showToast(Get.context!, "Error: ${firebaseError.toString()}");
          Utils.showLog("Firebase Sign-In Failed => $firebaseError");
        }
      } else {
        if (checkUserExistModel?.status == false &&
            checkUserExistModel?.isLogin == false) {
          Get.back();
          Utils.showToast(
              Get.context!,
              checkUserExistModel?.message ??
                  "Password doesn't match for this user.");
        } else {
          Get.back();
          Utils.showToast(
              Get.context!, EnumLocale.txtYoumusthavesignup.name.tr);
          registerArguments.addAll({
            'email': emailTxtController.text.trim(),
          });

          Utils.showLog("registerArguments:::::::::::$registerArguments");
          Get.toNamed(AppRoutes.register, arguments: registerArguments);
        }
      }
    } catch (e) {
      Get.back();
      isLoading = false;
      Utils.showToast(Get.context!, "Error: ${e.toString()}");
      Utils.showLog("Sign In Failed => $e");
    }
  }

  ///===============================demo login start===================///
  Future<void> onClickDemoLogin() async {
    // Disable demo listener status before setting up new login
    Database.onSetDemoUser(true); // true to indicate it's a demo login

    Utils.showLog("demo login>>>>>>>>>>>>>>>>${Database.demoUser}");

    // Set static credentials
    const String demoEmail = "liamsteller@gmail.com";
    const String demoPassword = "Liam@123";

    // Unfocus keyboard
    FocusManager.instance.primaryFocus?.unfocus();

    Utils.showLog("Email => $demoEmail");
    Utils.showLog("Password => $demoPassword");

    // Get FCM token
    final fcmToken = await FirebaseMessaging.instance.getToken();
    Database.onSetFcmToken(fcmToken ?? "");
    Utils.showLog("fcmToken => ${Database.fcmToken}");

    // Get Device Identity
    final identity = (await MobileDeviceIdentifier().getDeviceId())!;
    Database.onSetIdentity(identity);
    Utils.showLog("identity => ${Database.identity}");

    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      // Firebase Login Directly
      try {
        UserCredential userCredential = await FirebaseAuth.instance
            .signInWithEmailAndPassword(
                email: demoEmail, password: demoPassword);

        final token = await FirebaseAccessToken.onGet();
        String? firebaseUID = userCredential.user?.uid;

        Utils.showLog("Firebase UID => $firebaseUID");
        Utils.showLog("Firebase Token => $token");

        // Login API Call
        loginModel = await LoginApi.callApi(
          countryCode: Database.selectedCountryCode,
          loginType: 4,
          email: demoEmail,
          password: demoPassword,
          identity: Database.identity,
          fcmToken: Database.fcmToken,
          authToken: token,
          authUid: firebaseUID,
        );

        if (loginModel?.status == true) {
          Database.onSetIsLogin(true);
          Database.onSetLoginType(loginModel?.user?.loginType ?? 0);
          Database.onSetSeenOnboarding(true);
          Database.onSetFillProfile(true);

          Get.back(); // Stop loading

          if (loginModel?.signUp == true) {
            Database.onSetFillProfile(false);
            Get.offAllNamed(AppRoutes.fillProfileScreen, arguments: [
              Database.loginUserName,
              Database.loginUserProfilePic,
              Database.loginUserEmail
            ]);
          } else {
            Database.onSetFillProfile(true);
            await onGetProfile(
              loginUserId: userCredential.user!.uid,
              loginType: 2,
            );
            Get.toNamed(AppRoutes.bottomBar);
          }
        } else {
          Utils.showToast(Get.context!,
              loginModel?.message ?? EnumLocale.txtSomeThingWentWrong.name.tr);
          Utils.showLog("Login API call failed");
        }
      } catch (firebaseError) {
        Get.back();
        Utils.showToast(Get.context!, "Error: ${firebaseError.toString()}");
        Utils.showLog("Firebase Sign-In Failed => $firebaseError");
      }
    } catch (e) {
      Get.back();
      Utils.showToast(Get.context!, "Error: ${e.toString()}");
      Utils.showLog("Sign In Failed => $e");
    }
  }

  ///===============================demo login end===================///

  ///email pass validation

  bool validateLogin() {
    final email = emailTxtController.text.trim();
    final password = passwordTxtController.text;

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
    //
    // if (password.length < 6) {
    //   Utils.showToast(Get.context!, "Password must be at least 6 characters");
    //   return false;
    // }

    return true;
  }

  /// google log in api
  Future<void> onGoogleLogin() async {
    if (selectedValue != 1) {
      Utils.showToast(
          Get.context!, "Please agree to the Privacy Policy to proceed.");
      return;
    }
    final identity = (await MobileDeviceIdentifier().getDeviceId())!;
    final fcmToken = await FirebaseMessaging.instance.getToken();
    Database.onSetFcmToken(fcmToken ?? "");
    Database.onSetIdentity(identity);

    Utils.showLog("Database.identity :: ${Database.identity}");
    Utils.showLog("Database.fcmToken :: ${Database.fcmToken}");

    UserCredential? userCredential = await signInWithGoogle();

    if (userCredential?.user?.email != null) {
      Get.dialog(LoadingWidget(), barrierDismissible: false);

      Utils.showLog("userCredential.name${userCredential?.user?.displayName}");
      Utils.showLog("userCredential.email${userCredential?.user?.email}");
      Utils.showLog("userCredential.photo${userCredential?.user?.photoURL}");

      loginModel = await LoginApi.callApi(
        authUid: userCredential?.user?.uid,
        countryCode: Database.selectedCountryCode,
        loginType: 2,
        email: userCredential?.user?.email ?? "",
        identity: Database.identity,
        fcmToken: Database.fcmToken,
        userName: userCredential?.user?.displayName ?? "",
        profilePic: Database.loginUserProfilePic == ''
            ? userCredential?.user?.photoURL
            : Database.loginUserProfilePic,
      );

      if (loginModel?.status == true) {
        Database.onSetIsLogin(true);
        Database.onSetLoginType(loginModel?.user?.loginType ?? 0);
        Database.onSetSeenOnboarding(true);
        Database.onSetFillProfile(true);
        Utils.showLog("userCredential.user!.uid${userCredential?.user?.uid}");
        await onGetProfile(
          loginUserId: userCredential!.user!.uid,
          loginType: 2,
        );

        if (loginModel?.signUp == true) {
          Database.onSetFillProfile(false);

          Database.onSetLoginUserName(userCredential.user?.displayName ?? "");
          Database.onSetLoginUserEmail(userCredential.user?.email ?? "");
          Database.onSetLoginUserProfilePic(
              userCredential.user?.photoURL ?? "");

          Utils.showLog("Database.loginUserName:::${Database.loginUserName}");
          Utils.showLog(
              "Database.loginUserProfilePic:::${Database.loginUserProfilePic}");
          Utils.showLog("Database.loginUserEmail:::${Database.loginUserEmail}");

          Get.offAllNamed(AppRoutes.fillProfileScreen, arguments: [
            Database.loginUserName,
            Database.loginUserProfilePic,
            Database.loginUserEmail,
          ]);
        } else {
          // get profile api

          Database.onSetFillProfile(true);

          Utils.showLog("userCredential.user!.uid${userCredential.user!.uid}");
          await onGetProfile(
            loginUserId: userCredential.user!.uid,
            loginType: 2,
          );

          Get.toNamed(AppRoutes.bottomBar);
        }
      } else {
        if (Get.isDialogOpen == true) {
          Get.back();
        }
        Utils.showToast(Get.context!, loginModel?.message ?? "");
        Utils.showLog("Login Api Calling Failed !!");
      }

      // Get.back();
    } else {
      if (Get.isDialogOpen == true) {
        Get.back();
      }
      Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
      Utils.showLog("Google Login Failed !!");
    }
  }

  /// google sign in firebase

  Future<UserCredential?> signInWithGoogle() async {
    try {
      await GoogleSignIn().signOut();

      Get.dialog(LoadingWidget(), barrierDismissible: false);
      final GoogleSignInAccount? googleUser = await GoogleSignIn().signIn();
      final GoogleSignInAuthentication? googleAuth =
          await googleUser?.authentication;
      final credential = GoogleAuthProvider.credential(
          accessToken: googleAuth?.accessToken, idToken: googleAuth?.idToken);
      final result =
          await FirebaseAuth.instance.signInWithCredential(credential);

      Utils.showLog("Google Login Email => ${result.user?.email}");
      Utils.showLog("Google Login uid => ${result.user?.uid}");

      Utils.showLog(
          "Google Login isNewUser => ${result.additionalUserInfo?.isNewUser}");
      Get.back();

      return result;
    } catch (error) {
      Get.back();
      Utils.showLog("Google Login Error => $error");
    }
    return null;
  }
}
