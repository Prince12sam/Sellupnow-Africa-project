import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/ui/login_screen/api/check_user_exist_api.dart';
import 'package:listify/ui/login_screen/model/user_exist_response_model.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';

class ForgotPasswordController extends GetxController {
  final formKey = GlobalKey<FormState>();
  final emailController = TextEditingController();
  UserExistResponseModel? checkUserExistModel;

  @override
  void onInit() {
    super.onInit();
    final args = Get.arguments as Map<String, dynamic>?;
    final passedEmail = (args?['email'] as String?)?.trim();
    if (passedEmail != null && passedEmail.isNotEmpty) {
      emailController.text = passedEmail;
    }
  }

  bool isValidEmail(String email) {
    final emailRegex = RegExp(
        r"^[a-zA-Z0-9.a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9]+\.[a-zA-Z]+");
    return emailRegex.hasMatch(email);
  }

  Future<void> onForgotPassword() async {
    if (emailController.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtEnterYourMail.name.tr);
    } else if (isValidEmail(emailController.text) == false) {
      Utils.showToast(
          Get.context!, EnumLocale.desEnterValidEmailAddress.name.tr);
    } else {
      Get.dialog(
          PopScope(
            canPop: false, // Block system back button
            child: const LoadingWidget(),
          ),
          barrierDismissible: false); // Start Loading...

      // ValidateUserModel? validateUserModel =
      //     await UserExistsForResetApi.callApi(loginType: 4, email: forgotPasswordEmailController.text);

      checkUserExistModel = await CheckUserExistApi.callApi(
        email: emailController.text.trim(),
        identity: Database.identity,
        loginType: 4.toString(),
      ); // Check Email Is Exist...

      if (checkUserExistModel?.isLogin != true) {
        Utils.showToast(
            Get.context!, EnumLocale.txtNoAccountFoundForThisEmail.name.tr);
        Utils.showLog("Validate User Email => ${checkUserExistModel?.status}");
        Get.back(); // Stop Loading...
        return;
      } else {
        Utils.showLog("Validate User Email => ${checkUserExistModel?.status}");

        var data = await resetPassword(emailController.text.trim());
        Utils.showLog("Validate User Email 124=>  $data");
        Get.back(); // Stop Loading...
        Get.back(); // Back To Login Page..
        emailController.text = emailController.text.trim();
        emailController.clear();
      }

      //   var data = await resetPassword(emailController.text.trim());
      //   Utils.showLog("Validate User Email 124=>  $data");
      //   Get.back(); // Stop Loading...
      //   Get.back(); // Back To Login Page..
      //   emailController.text = emailController.text.trim();
      //   emailController.clear();
      // }
      // ForgotPasswordAuthentication.resetPassword(emailController.text.trim());
    }
  }

  Future<UserCredential?> resetPassword(email) async {
    try {
      await FirebaseAuth.instance
          .sendPasswordResetEmail(email: email)
          .catchError(
        (e) {
          Utils.showLog("Validate User Email => Error: ${e.toString()}");
          Utils.showToast(Get.context!, "Error: ${e.toString()}");
        },
      );

      // Utils.showLog("Email Authentication Response resetPassword user => $emailsend");
      Utils.showToast(
          Get.context!, EnumLocale.txtResetLinkSentToYourEmail.name.tr);
    } catch (e) {
      Utils.showToast(Get.context!, "Error: ${e.toString()}");
      Utils.showLog("Validate User Email => Error: ${e.toString()}");
    }
    return null;
  }

  @override
  void onClose() {
    emailController.dispose();
    super.onClose();
  }
}
