import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class LogOutDialog extends StatelessWidget {
  const LogOutDialog({super.key});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      // height: 365,
      // width: 60,
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(
            Radius.circular(58),
          ),
        ),
        color: AppColors.white,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            Image.asset(AppAsset.logOutImage, height: 162).paddingOnly(top: 20, bottom: 25),
            Text(EnumLocale.txtLogoutConfirmation.name.tr, style: AppFontStyle.fontStyleW800(fontSize: 25, fontColor: AppColors.appRedColor))
                .paddingOnly(bottom: 6),
            Text(EnumLocale.txtLogoutDescriptionText.name.tr,
                    textAlign: TextAlign.center, style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText))
                .paddingOnly(bottom: 25, left: 6, right: 6),
            Row(
              children: [
                Expanded(
                  child: PrimaryAppButton(
                    height: 52,
                    color: AppColors.lightRed100,
                    onTap: () {
                      Get.back();
                    },
                    text: EnumLocale.txtCancel.name.tr,
                    textStyle: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.appRedColor),
                  ),
                ),
                12.width,
                Expanded(
                  child: PrimaryAppButton(
                    height: 52,
                    onTap: () {
                      Database.onLogOut();
                      Get.toNamed(AppRoutes.loginScreen);
                    },
                    text: EnumLocale.txtLogOut.name.tr,
                    textStyle: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.white),
                  ),
                ),
              ],
            )
          ],
        ).paddingAll(17),
      ),
    );
  }
}
