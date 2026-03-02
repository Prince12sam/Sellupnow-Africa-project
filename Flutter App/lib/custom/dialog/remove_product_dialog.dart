import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class RemoveProductDialog extends StatelessWidget {
  final void Function()? onTap;

  const RemoveProductDialog({super.key, this.onTap});

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
            Image.asset(AppAsset.deleteVideoImage, height: 162).paddingOnly(top: 6, bottom: 20),
            Text(EnumLocale.txtDoYouWantToSureRemoveThisProduct.name.tr,
                textAlign: TextAlign.center, style: AppFontStyle.fontStyleW800(fontSize: 25, fontColor: AppColors.appRedColor))
                .paddingOnly(bottom: 6, left: 15, right: 15),
            Text(EnumLocale.txtDeleteProductDescription.name.tr,
                textAlign: TextAlign.center,
                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.popularProductText, height: 1.8))
                .paddingOnly(bottom: 10),
            Row(
              children: [
                Expanded(
                  child: PrimaryAppButton(
                    height: 52,
                    color: AppColors.lightRed100,
                    onTap: () {
                      Get.back();
                    },
                    textStyle: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.appRedColor),
                    text: EnumLocale.txtCancel.name.tr,
                  ),
                ),
                12.width,
                Expanded(
                  child: PrimaryAppButton(
                    height: 52,
                    onTap: onTap,
                    text: EnumLocale.txtRemove.name.tr,
                    textStyle: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.white),
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
