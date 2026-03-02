import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_background/app_background.dart';
import 'package:listify/ui/mobile_number_screen/widget/mobile_number_widget.dart';

class MobileNumberScreen extends StatelessWidget {
  const MobileNumberScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      resizeToAvoidBottomInset: true,
      // backgroundColor: AppColors.lightPurple,
      // appBar: AppBar(
      //   automaticallyImplyLeading: false,
      //   flexibleSpace: const MobileNumberAppBarView(),
      // ),
      bottomNavigationBar: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          MobileNumberButtonView(),
        ],
      ).paddingOnly(right: 18, left: 18, bottom: 20),
      body: LoginBg(
        child: SingleChildScrollView(
          child: const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              MobileNumberDescriptionView(),
              MobileNumberOTPView(),
            ],
          ).paddingOnly(left: 18, right: 18, top: 18),
        ),
      ),
    );
  }
}
