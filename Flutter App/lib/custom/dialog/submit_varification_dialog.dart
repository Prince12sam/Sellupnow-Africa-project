import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SubmitVarificationDialog extends StatelessWidget {
  final TextEditingController bidController = TextEditingController();

  SubmitVarificationDialog({super.key});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(
            Radius.circular(58),
          ),
        ),
        color: AppColors.white,
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Image.asset(
                AppAsset.submitImage,
                height: 167,
                width: 172,
              ).paddingOnly(top: 15, bottom: 20.55),
              Text(
                EnumLocale.txtDetailsSubmitted.name.tr,
                style: AppFontStyle.fontStyleW800(fontSize: 26, fontColor: AppColors.appRedColor),
              ).paddingOnly(bottom: 6),
              Text(
                EnumLocale.txtVerificationSubmitTxt.name.tr,
                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.popularProductText),
                textAlign: TextAlign.center,
              ).paddingOnly(bottom: 17),
              PrimaryAppButton(
                height: 54,
                text: EnumLocale.txtBackToHome.name.tr,
                onTap: () {
                  Get.toNamed(AppRoutes.bottomBar);
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}
