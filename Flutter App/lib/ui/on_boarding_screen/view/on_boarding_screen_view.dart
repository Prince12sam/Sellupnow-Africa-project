import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/on_boarding_screen/controller/on_boarding_screen_controller.dart';
import 'package:listify/ui/on_boarding_screen/widget/on_boarding_screen_widget.dart';
import 'package:listify/utils/app_color.dart';

class OnBoardingScreenView extends StatefulWidget {
  const OnBoardingScreenView({super.key});

  @override
  State<OnBoardingScreenView> createState() => _OnBoardingScreenViewState();
}

class _OnBoardingScreenViewState extends State<OnBoardingScreenView> {
  @override
  Widget build(BuildContext context) {
    return GetBuilder<OnBoardingScreenController>(builder: (controller) {
      return WillPopScope(
        onWillPop: controller.onWillPop,
        child: Scaffold(
          bottomNavigationBar: OnBoardingScreenBottomView(
            currentIndex: controller.currentIndex,
            totalScreens: controller.screens.length,
          ),
          floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
          floatingActionButton: CenterButton(
            onTap: controller.goNext,
          ),
          backgroundColor: AppColors.adScreenBgColor,
          body: SafeArea(
            child: PageView(
              controller: controller.pageController,
              onPageChanged: (index) {
                setState(() {
                  controller.currentIndex = index;
                });
              },
              children: controller.screens,
            ),
          ),
        ),
      );
    });
  }
}
