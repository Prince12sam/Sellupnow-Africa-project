import 'dart:developer';
import 'dart:io';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:listify/ui/edit_profile_screen/api/contact_permission_api.dart';
import 'package:listify/ui/edit_profile_screen/api/edit_profile_api.dart';
import 'package:listify/ui/edit_profile_screen/api/notification_switch_api.dart';
import 'package:listify/ui/edit_profile_screen/model/edit_profile_model.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class EditProfileController extends GetxController {
  final formKey = GlobalKey<FormState>();
  bool isContactInfoSwitch = false;
  var mobileNumberValidate = false.obs;
  var countryCode = "91";
  String? photo;
  String? pickImage;
  GetUserProfileResponseModel? getUserProfileResponseModel;
  EditProfileModel? editProfileModel;
  TextEditingController name = TextEditingController();
  TextEditingController address = TextEditingController();
  File? pickedImage;
  String? selectedCountryCode;
  TextEditingController number = TextEditingController();
  String? dialCode;
  XFile? xFiles;
  int selectedIndex = 0;
  bool editData = false;
  final ImagePicker imagePicker = ImagePicker();
  TextEditingController email = TextEditingController();

  @override
  void onInit() {
    // TODO: implement onInit
    super.onInit();

    name.text = Database.getUserProfileResponseModel?.user?.name ?? Database.loginUserName;
    address.text = Database.getUserProfileResponseModel?.user?.address ?? "";
    number.text = Database.getUserProfileResponseModel?.user?.phoneNumber ?? Database.loginUserPhoneNumber;
    email.text = Database.getUserProfileResponseModel?.user?.email ?? Database.loginUserEmail;
    isNotificationSwitch = Database.getUserProfileResponseModel?.user?.isNotificationsAllowed ?? false;
    isContactInfoSwitch = Database.getUserProfileResponseModel?.user?.isContactInfoVisible ?? false;

    log("name.text::${name.text}");
    log("Database.getUserProfileResponseModel?.user?.profileImage${Database.getUserProfileResponseModel?.user?.profileImage}");

    // Use country code from backend if available, otherwise fall back to local storage
    final backendCountry = Database.getUserProfileResponseModel?.user?.country;
    if (backendCountry != null && backendCountry.isNotEmpty) {
      selectedCountryCode = backendCountry;
      Database.onSetSelectedCountryCode(backendCountry);
    }

    dialCode = Database.getUserProfileResponseModel?.user?.phoneCode ?? Database.dialCode;
  }

  ///SWITCH

  notificationChange(bool value) {
    isNotificationSwitch = value;
    update([Constant.idNotification]);
  }

  contactInfoChange(bool value) {
    isContactInfoSwitch = value;
    update([Constant.idContact]);
  }

  ///GALLERY IMAGE

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

  /// fill profile api
  Future<void> callEditApi({String? image}) async {
    log('Database.countryCode  ::::  ${Database.selectedCountryCode}');
    log('Database.loginUserFirebaseId  ::::  ${Database.loginUserFirebaseId}');
    log('pickImage  ::::  $pickImage');
    log('photo  ::::  $photo');

    final String countryCodeToSave = selectedCountryCode ?? Database.selectedCountryCode;

    editProfileModel = await EditProfileApi.callApi(
      address: address.text,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      image: pickImage == "" ? photo : pickImage,
      phoneNumber: number.text,
      name: name.text,
      email: email.text,
      phoneCode: dialCode,
      country: countryCodeToSave,
      isNotificationsAllowed:isNotificationSwitch,
      isContactInfoVisible:isContactInfoSwitch,
    );

    if (editProfileModel?.status == true) {
      // Update local Database values immediately from the form data
      Database.onSetLoginUserName(name.text);
      Database.onSetLoginUserNickName(name.text);
      Database.onSetLoginUserEmail(email.text);
      Database.onSetLoginUserPhoneNumber(number.text);
      Database.onSetSelectedCountryCode(countryCodeToSave);
      if (pickImage != null && pickImage!.isNotEmpty) {
        Database.onSetLoginUserProfilePic(pickImage!);
      }

      // Try to refresh full profile from API (non-critical)
      try {
        final profileResult = await GetUserProfileApi.callApi(
            loginUserId: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId);
        if (profileResult != null) {
          getUserProfileResponseModel = profileResult;
          Database.getUserProfileResponseModel = profileResult;
          Database.onSetLoginUserProfilePic(profileResult.user?.profileImage ?? "");
          Database.onSetLoginUserName(profileResult.user?.name ?? name.text);
          Database.onSetLoginUserNickName(profileResult.user?.name ?? name.text);
          Database.onSetLoginUserEmail(profileResult.user?.email ?? email.text);
          Database.onSetLoginUserPhoneNumber(profileResult.user?.phoneNumber ?? number.text);
        }
      } catch (e) {
        Utils.showLog("Edit profile refresh failed (non-critical): $e");
      }

      log(" loginUserProfilePic ::: ${Database.loginUserProfilePic}");

      update([Constant.idProfile]);

      Get.back();
      log("${Database.getUserProfileResponseModel?.user}");

      Utils.showLog("data>>>>>>>>>>>${Database.getUserProfileResponseModel?.user?.name}");
    } else {
      Utils.showToast(Get.context!, editProfileModel?.message ?? "");
    }
  }

  /// Take Photo

  takePhoto() async {
    xFiles = await imagePicker.pickImage(source: ImageSource.camera);
    if (xFiles != null) {
      pickImage = xFiles!.path;
    }
    update();
  }

  ///notification permission api

  bool isNotificationSwitch = false;

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

  @override
  void onClose() {
    pickImage = null;
    pickedImage = null;
    super.onClose();
  }
}
