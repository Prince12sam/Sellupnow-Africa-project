import 'package:get/get.dart';
import 'package:flutter/material.dart';
import 'package:listify/ui/change_password_screen/api/change_password_api.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class ChangePasswordController extends GetxController {
  final formKey = GlobalKey<FormState>();

  final currentPasswordController = TextEditingController();
  final newPasswordController = TextEditingController();
  final confirmPasswordController = TextEditingController();

  var obscureCurrent = true.obs;
  var obscureNew = true.obs;
  var obscureConfirm = true.obs;
  var isLoading = false.obs;

  Future<void> changePassword() async {
    if (!formKey.currentState!.validate()) return;

    isLoading.value = true;

    final result = await ChangePasswordApi.callApi(
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      currentPassword: currentPasswordController.text,
      newPassword: newPasswordController.text,
      confirmPassword: confirmPasswordController.text,
    );

    isLoading.value = false;

    if (result == null) {
      Utils.showToast(Get.context!, 'Something went wrong. Please try again.');
      return;
    }

    if (result['status'] == true) {
      Utils.showToast(Get.context!, result['message'] ?? 'Password changed successfully');
      Get.back();
    } else {
      Utils.showToast(Get.context!, result['message'] ?? 'Failed to change password');
    }
  }

  @override
  void onClose() {
    currentPasswordController.dispose();
    newPasswordController.dispose();
    confirmPasswordController.dispose();
    super.onClose();
  }
}
