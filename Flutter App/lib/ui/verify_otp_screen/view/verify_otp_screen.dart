import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_background/app_background.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/ui/verify_otp_screen/controller/verify_otp_controller.dart';
import 'package:listify/ui/verify_otp_screen/widget/verify_otp_widget.dart';
import 'package:listify/utils/constant.dart';

class VerifyOtpScreen extends StatelessWidget {
  const VerifyOtpScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VerifyOtpController>(
      id: Constant.idLoginOrSignUp,
      builder: (logic) {
        return ProgressDialog(
          inAsyncCall: logic.isLoading,
          child: Scaffold(
            bottomNavigationBar: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                VerifyOtpButtonView(),
              ],
            ).paddingOnly(right: 18, left: 18, bottom: 20),
            resizeToAvoidBottomInset: false,
            body: LoginBg(
              child: const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  VerifyOtpDescriptionView(),
                  VerifyOtpView(),
                  VerifyOtpResendOTPView(),
                ],
              ).paddingOnly(left: 16, right: 16, top: 18),
            ),
          ),
        );
      },
    );
  }
}
