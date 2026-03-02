import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class StartUserVerificationAppBar extends StatelessWidget {
  final String? title;
  const StartUserVerificationAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(300),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
        action: [
          GestureDetector(
            onTap: () async {
              await Future.delayed(Duration(milliseconds: 100)); // Small delay

              Get.back();
            },
            child: Container(
              color: Colors.transparent,
              child: Text(
                EnumLocale.txtSkip.name.tr,
                style: AppFontStyle.fontStyleW500(
                  fontSize: 15,
                  fontColor: AppColors.popularProductText,
                ),
              ).paddingOnly(right: 18),
            ),
          ),
        ],
      ),
    );
  }
}

class StartUserVerificationCenterView extends StatelessWidget {
  const StartUserVerificationCenterView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Center(
          child: Image.asset(
            AppAsset.userVerification,
            height: 300,
            width: 379,
          ),
        ).paddingOnly(top: 60, bottom: 38),
        Text(
          EnumLocale.txtUserVerification.name.tr,
          style: AppFontStyle.fontStyleW800(fontSize: 28, fontColor: AppColors.black),
        ).paddingOnly(bottom: 10),
        Text(
          EnumLocale.txtUserVerificationTxt.name.tr,
          style: AppFontStyle.fontStyleW400(
            fontSize: 16,
            fontColor: AppColors.userVerifyTxtColor,
          ),
          textAlign: TextAlign.center,
        ).paddingOnly(bottom: 16),
        Text(
          EnumLocale.txtItWillOnlyTake2minutes.name.tr,
          style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.appRedColor),
        )
      ],
    );
  }
}

class StartUserVerificationBottomBar extends StatelessWidget {
  const StartUserVerificationBottomBar({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          decoration: BoxDecoration(
            color: AppColors.white,
            boxShadow: [
              BoxShadow(
                color: AppColors.black.withValues(alpha: 0.1),
                blurRadius: 12,
                offset: Offset(0, -2),
                spreadRadius: 0,
              ),
            ],
          ),
          child: PrimaryAppButton(
            onTap: () {
              Get.toNamed(AppRoutes.userVerificationScreenView);
            },
            text: EnumLocale.txtStartVerification.name.tr,
            height: 54,
          ).paddingSymmetric(vertical: 12, horizontal: 16),
        ),
      ],
    );
  }
}
