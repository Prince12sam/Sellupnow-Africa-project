import 'dart:developer';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/dialog/exit_app_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/on_boarding_screen/widget/on_boarding_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';

class OnBoardingScreenController extends GetxController {
  @override
  void onInit() {
    Database.onSetSeenOnboarding(false);
    log("init isSeenOnBoarding ::  ${Database.isSeenOnBoarding}");

    super.onInit();
  }

  final PageController pageController = PageController();

  int currentIndex = 0;
  List<Widget> screens = [
    const OnBoardingScreen1(),
    const OnBoardingScreen2(),
    const OnBoardingScreen3(),
    const OnBoardingScreen4(),
  ];

  void goNext() {
    if (currentIndex < screens.length - 1) {
      pageController.animateToPage(
        currentIndex + 1,
        duration: const Duration(milliseconds: 400),
        curve: Curves.easeInOut,
      );
    } else {
      Database.onSetSeenOnboarding(true);
      Get.toNamed(AppRoutes.loginScreen);
      // Get.snackbar("Done", "Onboarding Completed!");
    }
  }

  Future<bool> onWillPop() async {
    if (currentIndex > 0) {
      pageController.animateToPage(
        currentIndex - 1,
        duration: const Duration(milliseconds: 400),
        curve: Curves.easeInOut,
      );
      return false;
    } else {
      // show exit dialog
      Get.dialog(
        barrierColor: AppColors.black.withValues(alpha: 0.8),
        Dialog(
          backgroundColor: AppColors.transparent,
          shadowColor: AppColors.transparent,
          surfaceTintColor: AppColors.transparent,
          elevation: 0,
          child: const ExitAppDialog(),
        ),
      );
      return false; // prevent immediate exit
    }
  }
}
