import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class PurchaseProductDialog extends StatelessWidget {
  final VoidCallback? subscribeOnTap;
  final VoidCallback? cancelOnTap;
  const PurchaseProductDialog({super.key, this.subscribeOnTap, this.cancelOnTap});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      // height: 351,
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(
            Radius.circular(40),
          ),
        ),
        color: AppColors.white,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              "No Package Available",
              style: AppFontStyle.fontStyleW700(fontSize: 20, fontColor: AppColors.appRedColor),
            ).paddingOnly(bottom: 20, top: 10),
            Text(
              "Please Subscribe to any package to use this functionality",
              style: AppFontStyle.fontStyleW500(fontSize: 15, fontColor: AppColors.black),
            ).paddingOnly(bottom: 20, left: 8),
            Row(
              children: [
                Expanded(
                  child: PrimaryAppButton(
                    onTap: cancelOnTap,
                    height: 47,
                    borderRadius: 8,
                    color: AppColors.white,
                    borderColor: AppColors.borderColor,
                    text: "cancel",
                    textStyle: AppFontStyle.fontStyleW700(
                      fontSize: 17,
                      fontColor: AppColors.appRedColor,
                    ),
                  ).paddingOnly(top: 20, bottom: 10, right: 5),
                ),
                Expanded(
                  child: PrimaryAppButton(
                    onTap: subscribeOnTap,
                    height: 47,
                    borderRadius: 8,
                    color: AppColors.appRedColor,
                    text: "Subscribe",
                    textStyle: AppFontStyle.fontStyleW700(
                      fontSize: 17,
                      fontColor: AppColors.white,
                    ),
                  ).paddingOnly(top: 20, bottom: 10, left: 5),
                ),
              ],
            ).paddingSymmetric(horizontal: 10)
          ],
        ).paddingAll(15),
      ),
    );
  }
}
