import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_background/app_background.dart';
import 'package:listify/ui/forgot_password_screen/widget/forgot_password_widget.dart';

class ForgotPasswordScreen extends StatelessWidget {
  const ForgotPasswordScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBody: true,
      bottomNavigationBar: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          ForgotPasswordButtonView(),
        ],
      ).paddingOnly(right: 18, left: 18, bottom: 20),
      body: LoginBg(
        child: const Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ForgotPasswordDescriptionView(),
            ForgotPasswordView(),
          ],
        ).paddingOnly(left: 18, right: 18, top: 18),
      ),
    );
  }
}
