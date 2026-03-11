import 'dart:async';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/services/notification_service.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/ui/profile_screen_view/api/setting_api.dart';
import 'package:listify/ui/profile_screen_view/model/setting_api_response_model.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class SplashScreenController extends GetxController {
  SettingApiResponseModel? settingApiResponseModel;
  GetUserProfileResponseModel? getUserProfileResponseModel;

  @override
  void onInit() {
    init();
    NotificationServices.firebaseInit();
    super.onInit();
  }

  /// setting Api
  settingApi() async {
    settingApiResponseModel = await SettingApi.callApi();
    Database.settingApiResponseModel = settingApiResponseModel;
    Database.onSetCurrencySymbol(Database.settingApiResponseModel?.data?.currency?.symbol ?? "");
    Utils.showLog("Database.currencySymbol${Database.onSetCurrencySymbol(Database.settingApiResponseModel?.data?.currency?.symbol ?? "")}");
    update();
    Utils.showLog("Setting Api Response Model :: ${settingApiResponseModel?.toJson()}");
  }

  Future<void> init() async {
    /// for privacy policy link and app live key

    if (Database.isLogin) {
      final currentUser = FirebaseAuth.instance.currentUser;
      final token = await FirebaseAccessToken.onGet();

      if (currentUser == null || token == null || token.isEmpty) {
        Utils.showLog(
            "Persisted app login had no valid Firebase session. Logging out stale local session.");
        await Database.onLogOut();
        return;
      }
    }

    // Only attempt profile fetch if user is logged in and has a Firebase UID
    if (Database.isLogin && Database.loginUserFirebaseId.isNotEmpty) {
      try {
        getUserProfileResponseModel = await GetUserProfileApi.callApi(
            loginUserId: Database.loginUserFirebaseId);
        Database.getUserProfileResponseModel = getUserProfileResponseModel;
      } catch (e) {
        Utils.showLog("Splash profile fetch failed (non-critical): $e");
      }

      if (getUserProfileResponseModel?.status == false ||
          getUserProfileResponseModel?.message == "User not found in the database.") {
        Utils.showLog("Login user not found, redirecting to login screen...");
        Get.offAllNamed(AppRoutes.loginScreen);
        return;
      }
    }

    Utils.showLog("Database.selectedCountryCode :: ${Database.selectedCountryCode}");
    Database.getDialCode();

    await splashScreen();
    settingApiResponseModel = await SettingApi.callApi();
    Database.settingApiResponseModel = settingApiResponseModel;
  }
}

Future<void> splashScreen() async {
  Timer(Duration(seconds: 2), () async {
    // Check User Is Login Or Not...

    Utils.showLog("isLogin :: ${Database.isLogin}");
    Utils.showLog("isFillProfile :: ${Database.isFillProfile}");
    Utils.showLog("isSeenOnBoarding :: ${Database.isSeenOnBoarding}");

    // if (Database.settingApiResponseModel?.data?.isApplicationLive == false) {
    //   Utils.showLog("Application is not live...");
    //   Get.dialog(
    //     barrierColor: AppColors.black.withValues(alpha: 0.8),
    //     Dialog(
    //       backgroundColor: AppColors.transparent,
    //       shadowColor: Colors.transparent,
    //       surfaceTintColor: Colors.transparent,
    //       elevation: 0,
    //       child: const AppNotLiveDialog(),
    //     ),
    //   );
    // } else {
    //
    // }

    ///on boarding change
    Utils.showLog('Splash onBoarding screen view ${Database.isSeenOnBoarding}');

    if (Database.isSeenOnBoarding == true) {
      if (Database.isLogin == true) {
        if (Database.isFillProfile == true) {
          Get.toNamed(AppRoutes.bottomBar);
        } else {
          Get.offAllNamed(AppRoutes.fillProfileScreen, arguments: [
            Database.loginUserName,
            Database.loginUserProfilePic,
            Database.loginUserEmail,
          ]);
        }
      } else {
        Get.offAllNamed(AppRoutes.loginScreen);
      }
    } else {
      Get.offAllNamed(AppRoutes.onBoardingScreenView);
    }

    // if (Database.isLogin == true) {
    //   if (Database.isFillProfile == true) {
    //     Get.toNamed(AppRoutes.bottomBar);
    //   } else {
    //     Get.offAllNamed(AppRoutes.fillProfileScreen, arguments: [
    //       Database.loginUserName,
    //       Database.loginUserProfilePic,
    //       Database.loginUserEmail,
    //     ]);
    //   }
    // } else {
    //   Get.offAllNamed(AppRoutes.loginScreen);
    // }
  });
}
