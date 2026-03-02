import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ExitAppDialog extends StatelessWidget {
  const ExitAppDialog({super.key});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      // height: 365,
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(
            Radius.circular(80),
          ),
        ),
        color: AppColors.white,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Image.asset(
              AppAsset.exitApp,
              height: 166,
            ).paddingOnly(top: 16),
            Text(
              EnumLocale.txtExitAppText.name.tr,
              textAlign: TextAlign.center,
              style: AppFontStyle.fontStyleW800(
                fontSize: 25,
                fontColor: AppColors.appRedColor,
              ),
            ).paddingOnly(top: 8, left: 30, right: 30),
            Text(
              EnumLocale.txtExitAppDescription.name.tr,
              textAlign: TextAlign.center,
              style: AppFontStyle.fontStyleW500(
                fontSize: 12,
                fontColor: AppColors.popularProductText,
              ),
            ).paddingOnly(top: 8, bottom: 13, left: 10, right: 10),
            // const Spacer(),
            Row(
              children: [
                Expanded(
                    child: PrimaryAppButton(
                  onTap: () {
                    Get.back();
                  },
                  height: 47,
                  borderRadius: 8,
                  color: AppColors.lightRed100.withValues(alpha: 0.6),
                  text: EnumLocale.txtCancel.name.tr,
                  textStyle: AppFontStyle.fontStyleW500(
                    fontSize: 17,
                    fontColor: AppColors.appRedColor,
                  ),
                )),
                10.width,
                Expanded(
                    child: PrimaryAppButton(
                  onTap: () {
                    exit(0);
                  },
                  height: 47,
                  borderRadius: 8,
                  text: EnumLocale.txtExit.name.tr,
                  textStyle: AppFontStyle.fontStyleW500(
                    fontSize: 17,
                    fontColor: AppColors.white,
                  ),
                )),
              ],
            )
          ],
        ).paddingAll(15),
      ),
    );
  }
}
