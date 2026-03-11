import 'dart:developer';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/edit_profile_screen/api/contact_permission_api.dart';
import 'package:listify/ui/edit_profile_screen/api/edit_profile_api.dart';
import 'package:listify/ui/edit_profile_screen/api/notification_switch_api.dart';
import 'package:listify/ui/edit_profile_screen/model/edit_profile_model.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';
import 'package:permission_handler/permission_handler.dart';

class FillProfileScreenController extends GetxController {
  final formKey = GlobalKey<FormState>();

  var mobileNumberValidate = false.obs;
  var countryCode = "91";
  XFile? xFiles;
  String? name;
  String? email;
  String? photo;
  String? pickImage;
  TextEditingController nameController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController numberController = TextEditingController();
  TextEditingController addressController = TextEditingController();

  bool isNotificationSwitch = false;
  bool isContactInfoSwitch = false;

  notificationChange(bool value) {
    isNotificationSwitch = value;
    update([Constant.idNotification]);
  }

  contactInfoChange(bool value) {
    isContactInfoSwitch = value;
    update([Constant.idContact]);
  }

  GetUserProfileResponseModel? getUserProfileResponseModel;
  EditProfileModel? editProfileModel;

  int selectedIndex = 0;
  String? dialCode;

  final ImagePicker imagePicker = ImagePicker();
  dynamic args = Get.arguments;

  @override
  void onInit() async {
    nameController.text = Database.getUserProfileResponseModel?.user?.name ?? '';
    emailController.text = Database.getUserProfileResponseModel?.user?.email ?? '';
    numberController.text = Database.getUserProfileResponseModel?.user?.phoneNumber ?? '';
    photo = Database.getUserProfileResponseModel?.user?.profileImage ?? '';
    dialCode = Database.dialCode;

    Utils.showLog("emailController:::::::${emailController.text}");
    Utils.showLog("nameController:::::::${nameController.text}");
    Utils.showLog("nameController:::::::${numberController.text}");
    Utils.showLog("Database.getUserProfileResponseModel?.user?.name:::::::${Database.getUserProfileResponseModel?.user?.name}");
    Utils.showLog("Database.getUserProfileResponseModel?.user?.email:::::::${Database.getUserProfileResponseModel?.user?.email}");
    Utils.showLog("PHOTO:::::$photo");
    Utils.showLog("PHOTO:::::${Database.getUserProfileResponseModel?.user?.profileImage}");

    super.onInit();
  }

  File? pickedImage;

  Future<void> imageShow() async {
    try {
      final XFile? image = await imagePicker.pickImage(source: ImageSource.gallery);

      if (image != null) {
        pickedImage = File(image.path);
        pickImage = image.path;
        Utils.showLog("🖼️ Image picked: $pickImage");
        update(); // refresh UI
      } else {
        Utils.showLog("🚫 No image selected");
      }
    } catch (e) {
      Utils.showLog("❌ Error picking image: $e");
    }
  }

  /// save profile button on tap
  Future<void> onSaveProfile() async {
    Utils.showLog("Click On Save Profile => ${Database.loginUserId}");

    if (photo == "" && pickImage == null) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectProfileImage.name.tr);
    } else if (numberController.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseEnterMobileNumber.name.tr);
    } else {
      Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...

      await callEditApi();
      Database.onSetFillProfile(true);
    }
  }

  /// fill profile api
  Future<void> callEditApi({String? image}) async {
    log('Database.countryCode  ::::  ${Database.selectedCountryCode}');
    editProfileModel = await EditProfileApi.callApi(
      address: addressController.text,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      image: pickImage == "" ? photo : pickImage,
      phoneNumber: numberController.text,
      name: nameController.text,
      email: emailController.text,
      isNotificationsAllowed:isNotificationSwitch,
      isContactInfoVisible:isContactInfoSwitch,
    );

    if (editProfileModel?.status == true) {
      // Update local Database values immediately
      Database.onSetLoginUserName(nameController.text);
      Database.onSetLoginUserEmail(emailController.text);
      Database.onSetLoginUserPhoneNumber(numberController.text);
      if (pickImage != null && pickImage!.isNotEmpty) {
        Database.onSetLoginUserProfilePic(pickImage!);
      }

      // Try to refresh full profile from API (non-critical)
      try {
        final profileResult = await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
        if (profileResult != null) {
          getUserProfileResponseModel = profileResult;
          Database.getUserProfileResponseModel = profileResult;
          Database.onSetLoginUserProfilePic(profileResult.user?.profileImage ?? "");
          Database.onSetLoginUserName(profileResult.user?.name ?? nameController.text);
          Database.onSetLoginUserEmail(profileResult.user?.email ?? emailController.text);
          Database.onSetLoginUserPhoneNumber(profileResult.user?.phoneNumber ?? numberController.text);
        }
      } catch (e) {
        Utils.showLog("Fill profile refresh failed (non-critical): $e");
      }

      log(" loginUserProfilePic ::: ${Database.loginUserProfilePic}");
      log(" loginUserName ::: ${Database.loginUserName}");
      log(" profile name ::: ${Database.getUserProfileResponseModel?.user?.name}");

      update([Constant.idProfile]);

      log("${Database.getUserProfileResponseModel?.user}");

      update();
      Get.toNamed(AppRoutes.bottomBar);
    } else {
      Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
    }
  }

  /// take photo

  String verificationId = '';

  Future<void> takePhoto() async {
    final cameraStatus = await Permission.camera.request();
    PermissionStatus storageStatus;

    // Handle platform-specific permission
    if (Platform.isAndroid) {
      storageStatus = await Permission.storage.request();
    } else {
      storageStatus = await Permission.photos.request();
    }

    if (!cameraStatus.isGranted || !storageStatus.isGranted) {
      Utils.showLog("Permission denied.");
      return;
    }

    try {
      final XFile? image = await ImagePicker().pickImage(
        source: ImageSource.camera,
        imageQuality: 80,
      );
      if (image != null) {
        pickImage = image.path;
        log("Camera Image Path ::: $pickImage");
        update();
      } else {
        log("Image capture cancelled.");
      }
    } catch (e) {
      log("Error during image capture: $e");
      Utils.showLog("Camera failed to open.");
    }
  }

  ///notification api

  Future<void> notificationPermission(bool value) async {
    isNotificationSwitch = value;
    update([Constant.idNotification]);

    Utils.showLog("Notification Switch Changed => $value");

    // Call API
    final response = await NotificationPermissionApi.updateUserPermission(
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      type: "isNotificationsAllowed",
    );

    if (response?.status == true) {
      // Utils.showToast(Get.context!, response?.message ?? "Updated ✅");
    } else {
      // Utils.showToast(Get.context!, response?.message ?? "Something went wrong");
      // API fail → rollback switch
      isNotificationSwitch = !value;
      update([Constant.idNotification]);
    }
  }
  Future<void> contactPermission(bool value) async {
    isContactInfoSwitch = value;
    update([Constant.idContact]);

    Utils.showLog("isContactInfoSwitch Switch Changed => $value");

    // Call API
    final response = await ContactPermissionApi.updateUserPermission(
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      type: "isContactInfoVisible",
    );

    if (response?.status == true) {
      // Utils.showToast(Get.context!, response?.message ?? "Updated ✅");
    } else {
      // Utils.showToast(Get.context!, response?.message ?? "Something went wrong");
      // API fail → rollback switch
      isContactInfoSwitch = !value;
      update([Constant.idContact]);
    }
  }
}
