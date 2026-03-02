import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/on_boarding_screen/controller/on_boarding_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class OnBoardingScreen1 extends StatelessWidget {
  const OnBoardingScreen1({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<OnBoardingScreenController>(builder: (controller) {
      return Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              GestureDetector(
                onTap: () {
                  Database.onSetSeenOnboarding(true);
                  Get.toNamed(AppRoutes.loginScreen);
                },
                child: Container(
                  color: AppColors.transparent,
                  child: Text(
                    EnumLocale.txtSkipArrow.name.tr,
                    style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.popularProductText),
                  ),
                ),
              ),
            ],
          ).paddingOnly(top: 10, right: 23),
          Center(
              child: Image.asset(
            AppAsset.onBoarding1,
            height: Get.height * 0.36,
            width: Get.height * 0.39,
          )).paddingOnly(top: 50, bottom: 30),
          Text(
            EnumLocale.txtMakeADeal.name.tr,
            style: AppFontStyle.fontStyleW800(fontSize: 34, fontColor: AppColors.appRedColor),
          ).paddingOnly(bottom: 10),
          Text(
            EnumLocale.txtOnBoardingTxt1.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
            textAlign: TextAlign.center,
          ).paddingOnly(right: 30, left: 30),
        ],
      );
    });
  }
}

class OnBoardingScreen2 extends StatelessWidget {
  const OnBoardingScreen2({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.end,
          children: [
            GestureDetector(
              onTap: () {
                Database.onSetSeenOnboarding(true);
                Get.toNamed(AppRoutes.loginScreen);
              },
              child: Container(
                color: AppColors.transparent,
                child: Text(
                  EnumLocale.txtSkipArrow.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.popularProductText),
                ),
              ),
            ),
          ],
        ).paddingOnly(top: 10, right: 23),
        Center(
            child: Image.asset(
          AppAsset.onBoarding2,
          height: Get.height * 0.36,
          width: Get.height * 0.39,
        )).paddingOnly(top: 50, bottom: 30),
        Text(
          EnumLocale.txtSecurePayment.name.tr,
          style: AppFontStyle.fontStyleW800(fontSize: 34, fontColor: AppColors.appRedColor),
        ).paddingOnly(bottom: 10),
        Text(
          EnumLocale.txtOnBoardingTxt2.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
          textAlign: TextAlign.center,
        ).paddingOnly(right: 30, left: 30),
      ],
    );
  }
}

class OnBoardingScreen3 extends StatelessWidget {
  const OnBoardingScreen3({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.end,
          children: [
            GestureDetector(
              onTap: () {
                Database.onSetSeenOnboarding(true);
                Get.toNamed(AppRoutes.loginScreen);
              },
              child: Container(
                color: AppColors.transparent,
                child: Text(
                  EnumLocale.txtSkipArrow.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.popularProductText),
                ),
              ),
            ),
          ],
        ).paddingOnly(top: 10, right: 23),
        Center(
            child: Image.asset(
          AppAsset.onBoarding3,
          height: Get.height * 0.36,
          width: Get.height * 0.39,
        )).paddingOnly(top: 50, bottom: 30),
        Text(
          EnumLocale.txtFastDelivery.name.tr,
          style: AppFontStyle.fontStyleW800(fontSize: 34, fontColor: AppColors.appRedColor),
        ).paddingOnly(bottom: 10),
        Text(
          EnumLocale.txtOnBoardingTxt3.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
          textAlign: TextAlign.center,
        ).paddingOnly(right: 30, left: 30),
      ],
    );
  }
}

class OnBoardingScreen4 extends StatelessWidget {
  const OnBoardingScreen4({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.end,
          children: [
            Text(
              EnumLocale.txtSkipArrow.name.tr,
              style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.popularProductText),
            ),
          ],
        ).paddingOnly(top: 10, right: 23),
        Center(
            child: Image.asset(
          AppAsset.onBoarding4,
          height: Get.height * 0.36,
          width: Get.height * 0.39,
        )).paddingOnly(top: 50, bottom: 30),
        Text(
          EnumLocale.txtReceiveYourOrder.name.tr,
          style: AppFontStyle.fontStyleW800(fontSize: 34, fontColor: AppColors.appRedColor),
        ).paddingOnly(bottom: 10),
        Text(
          EnumLocale.txtOnBoardingTxt4.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
          textAlign: TextAlign.center,
        ).paddingOnly(right: 30, left: 30),
      ],
    );
  }
}

class OnBoardingScreenBottomView extends StatelessWidget {
  final int currentIndex;
  final int totalScreens;

  const OnBoardingScreenBottomView({
    super.key,
    required this.currentIndex,
    required this.totalScreens,
  });

  @override
  Widget build(BuildContext context) {
    return GetBuilder<OnBoardingScreenController>(builder: (controller) {
      return Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Stack(
            clipBehavior: Clip.none,
            children: [
              Container(
                width: Get.width,
                height: Get.height * 0.25,
                decoration: BoxDecoration(
                  color: AppColors.white,
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.black.withValues(alpha: 0.10),
                      blurRadius: 6,
                      offset: const Offset(0, -2),
                    ),
                  ],
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(
                        totalScreens,
                        (index) {
                          return Container(
                            height: 6,
                            width: 24,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(10),
                              color: currentIndex == index ? AppColors.appRedColor : AppColors.onBoardingColor,
                            ),
                          ).paddingOnly(right: 3, left: 3);
                        },
                      ),
                    ),
                    PrimaryAppButton(
                      onTap: () {
                        Database.onSetSeenOnboarding(true);
                        Get.toNamed(AppRoutes.loginScreen);
                      },
                      height: 58,
                      width: Get.width,
                      child: Center(
                        child: Text(
                          EnumLocale.txtSignIn.name.tr,
                          style: AppFontStyle.fontStyleW700(
                            fontSize: 18,
                            fontColor: AppColors.white,
                          ),
                        ),
                      ),
                    ).paddingOnly(
                      left: 16,
                      right: 16,
                      bottom: 24,
                      top: 26,
                    )
                  ],
                ),
              ),
            ],
          )
        ],
      );
    });
  }
}

class CenterButton extends StatelessWidget {
  final VoidCallback onTap;
  const CenterButton({super.key, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap, // ✅ dynamic callback
      child: Container(
        height: 80,
        width: 80,
        decoration: BoxDecoration(
          color: AppColors.appRedColor,
          shape: BoxShape.circle,
          border: Border.all(
            color: AppColors.adScreenBgColor,
            width: 3,
          ),
        ),
        child: Center(
          child: Image.asset(
            AppAsset.whiteOnBoardingRight,
            height: 32,
          ),
        ),
      ),
    );
  }
}
