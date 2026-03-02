import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/step_indicator/step_indicator.dart';
import 'package:listify/ui/user_verification_screen/controller/user_verification_screen_controller.dart';
import 'package:listify/ui/user_verification_screen/widget/user_verification_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class UserVerificationScreenView extends StatelessWidget {
  const UserVerificationScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UserVerificationScreenController>(builder: (controller) {
      return WillPopScope(
        onWillPop: () async {
          final controller = Get.find<UserVerificationScreenController>();
          if (controller.currentStep > 1) {
            controller.previousStep();
            return false; // Don't exit screen
          } else {
            Utils.showLog("back not work:::::::::::");

            Get.back(); // Close screen

            return false;
          }
        },
        child: Scaffold(
          bottomNavigationBar: UserVerificationBottomBar(),
          backgroundColor: AppColors.white,
          appBar: AppBar(
            automaticallyImplyLeading: false,
            flexibleSpace: UserVerificationAppBar(
              title: EnumLocale.txtUserVerification.name.tr,
            ),
          ),
          body: GestureDetector(
            onTap: () {
              FocusScopeNode currentFocus = FocusScope.of(context);
              if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
                currentFocus.focusedChild?.unfocus();
              }
            },
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  EnumLocale.txtUserVerification.name.tr,
                  style: AppFontStyle.fontStyleW800(fontSize: 24, fontColor: AppColors.appRedColor),
                ).paddingOnly(top: 18, bottom: 2),
                Text(
                  EnumLocale.txtVerificationTxt.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText),
                ).paddingOnly(bottom: 18),
                Text(
                  EnumLocale.txtVerificationStep.name.tr,
                  style: AppFontStyle.fontStyleW700(fontSize: 14, fontColor: AppColors.faqTxt),
                ).paddingOnly(bottom: 22),
                StepProgressIndicator(
                  currentStep: controller.currentStep,
                  onStepTap: (index) {
                    controller.setStep(index);
                  },
                ).paddingOnly(bottom: 20),
                controller.currentStep == 1 ? Step1View() : Step2View(),
              ],
            ).paddingSymmetric(horizontal: 16),
          ),
        ),
      );
    });
  }
}
