import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

import '../../utils/app_color.dart' show AppColors;

class DeleteAccountDialog extends StatelessWidget {
  final VoidCallback? onTap;
  const DeleteAccountDialog({super.key, this.onTap});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 351,
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(
            Radius.circular(90),
          ),
        ),
        color: AppColors.white,
        child: Column(
          children: [
            Image.asset(
              AppAsset.delete,
              height: 90,
              color: AppColors.redColor,
            ),
            Text(
              EnumLocale.txtDeleteAccount.name.tr,
              style: AppFontStyle.fontStyleW700(
                fontSize: 22,
                fontColor: AppColors.redColor,
              ),
            ).paddingOnly(top: 8),
            Text(
              EnumLocale.desWantDeleteAccount.name.tr,
              textAlign: TextAlign.center,
              style: AppFontStyle.fontStyleW500(
                fontSize: 14,
                fontColor: AppColors.grey,
              ),
            ).paddingOnly(top: 8, bottom: 13),
            const Spacer(),
            PrimaryAppButton(
              onTap: onTap,
              height: 47,
              borderRadius: 8,
              color: AppColors.redColor,
              text: EnumLocale.txtDeleteAccount.name.tr,
              textStyle: AppFontStyle.fontStyleW700(
                fontSize: 17,
                fontColor: AppColors.white,
              ),
            ).paddingOnly(top: 20, bottom: 10),
            PrimaryAppButton(
              onTap: () {
                Get.back();
              },
              height: 47,
              borderRadius: 8,
              color: AppColors.lightGrey.withValues(alpha: 0.20),
              text: EnumLocale.txtCancel.name.tr,
              textStyle: AppFontStyle.fontStyleW700(
                fontSize: 17,
                fontColor: AppColors.appRedColor,
              ),
            ).paddingOnly(bottom: 5)
          ],
        ).paddingAll(15),
      ),
    );
  }
}
