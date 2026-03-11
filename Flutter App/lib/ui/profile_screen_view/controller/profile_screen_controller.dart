import 'package:get/get.dart';
import 'package:listify/custom/custom_web_view/web_view_screen.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/ui/profile_screen_view/api/delete_user_api.dart';
import 'package:listify/ui/profile_screen_view/api/setting_api.dart';
import 'package:listify/ui/profile_screen_view/model/delete_user_account_model.dart';
import 'package:listify/ui/profile_screen_view/model/setting_api_response_model.dart';
import 'package:listify/ui/user_verification_screen/model/user_verification_response_model.dart';
import 'package:intl/intl.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class ProfileScreenController extends GetxController {
  SettingApiResponseModel? settingApiResponseModel;
  GetUserProfileResponseModel? getUserProfileResponseModel;
  DeleteUserResponseModel? deleteUserModel;
  UserVerificationResponseModel? userVerificationResponseModel;

  @override
  onInit() {
    settingApi();
    profileApi();

    Utils.showLog("profile name:::::::${Database.getUserProfileResponseModel?.user?.name}");
    Utils.showLog("profile email:::::::${Database.getUserProfileResponseModel?.user?.email}");
    Utils.showLog("profile image:::::::${Database.getUserProfileResponseModel?.user?.profileImage}");
    Utils.showLog("profile image:::::::${Database.loginUserProfilePic}");

    Utils.showLog("data>>>>>>>>>>>${Database.getUserProfileResponseModel?.user?.name}");

    super.onInit();
  }

  profileApi() async {
    if (Database.authUid.isEmpty) {
      Utils.showLog("profileApi: authUid is empty, skipping profile fetch");
      return;
    }
    try {
      getUserProfileResponseModel = await GetUserProfileApi.callApi(loginUserId: Database.authUid);
      if (getUserProfileResponseModel?.user != null) {
        Database.getUserProfileResponseModel = getUserProfileResponseModel;
        Database.syncIdentityVerificationState(
          isVerified: getUserProfileResponseModel?.user?.isVerified == true,
          verificationStatus: getUserProfileResponseModel?.user?.verificationStatus,
          verificationId: getUserProfileResponseModel?.user?.verificationId,
          verificationSubmittedAt: _formatVerifyTime(getUserProfileResponseModel?.user?.verificationSubmittedAt),
        );
      }
    } catch (e) {
      Utils.showLog("profileApi fetch failed: $e");
    }
    update([Constant.idProfile]);
  }

  String _formatVerifyTime(String? dateString) {
    try {
      if (dateString == null || dateString.isEmpty) return "";
      final parsedDate = DateTime.parse(dateString).toLocal();
      return DateFormat("dd MMM yyyy, hh:mm a").format(parsedDate);
    } catch (e) {
      return dateString ?? "";
    }
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

  ///  on click privacy policy
  Future<void> onClickPrivacyPolicy() async {
    final String privacyPolicyUrl = Database.settingApiResponseModel?.data?.privacyPolicyUrl ?? '';

    if (privacyPolicyUrl.isNotEmpty) {
      Get.to(() => WebViewScreen(url: privacyPolicyUrl, screen: "Privacy Policy"));
    } else {
      Utils.showLog('Invalid privacy policy URL');
    }
  }

  ///  on click About us
  Future<void> onClickAboutUs() async {
    final String privacyPolicyUrl = Database.settingApiResponseModel?.data?.aboutPageUrl ?? '';

    if (privacyPolicyUrl.isNotEmpty) {
      Get.to(() => WebViewScreen(url: privacyPolicyUrl, screen: "About Us"));
    } else {
      Utils.showLog('Invalid About Us URL');
    }
  }

  ///  on click Terms & conditions
  Future<void> onClickTermsConditions() async {
    final String privacyPolicyUrl = Database.settingApiResponseModel?.data?.termsAndConditionsUrl ?? '';

    if (privacyPolicyUrl.isNotEmpty) {
      Get.to(() => WebViewScreen(url: privacyPolicyUrl, screen: "Terms & Conditions"));
    } else {
      Utils.showLog('Invalid Terms & Conditions URL');
    }
  }

  /// user account delete

  Future<void> onDeleteAccount() async {
    Get.back(); // Close confirmation dialog

    Get.dialog(const LoadingWidget(), barrierDismissible: false); // Show loading spinner

    deleteUserModel = await DeleteUserApi.callApi(); // Call the API

    Get.back(); // Close loading spinner

    // ✅ If delete successful
    if (deleteUserModel?.status ?? false) {
      // Clear local user session
      Database.onLogOut();

      // Show log + toast message
      final message = deleteUserModel?.message ?? "User account deleted successfully.";

      Utils.showLog(message);
      Utils.showToast(Get.context!, message);
    } else {
      // ❌ Show error toast if failed
      final errorMsg = deleteUserModel?.message ?? "Failed to delete account. Please try again.";
      Utils.showLog(errorMsg);
      Utils.showToast(Get.context!, errorMsg);
    }
  }
}
