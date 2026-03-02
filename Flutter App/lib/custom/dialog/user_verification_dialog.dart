import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class UserVerificationDialog extends StatelessWidget {
  final String id;
  const UserVerificationDialog({super.key, required this.id});

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
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            Image.asset(AppAsset.verificationPendingImage, height: 162).paddingOnly(top: 20, bottom: 25),
            Text(EnumLocale.txtVerificationPending.name.tr, style: AppFontStyle.fontStyleW800(fontSize: 26, fontColor: AppColors.appRedColor))
                .paddingOnly(bottom: 6),
            Text(EnumLocale.txtUserVerificationTxtDialog.name.tr,
                    textAlign: TextAlign.center, style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText))
                .paddingOnly(bottom: 25, left: 10, right: 10),
            Container(
              // height: 50,
              width: Get.width,
              color: AppColors.pendingTxtBgColor,
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        EnumLocale.txtUserName.name.tr,
                        style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.greyTxt),
                      ),
                      Text(
                        Database.getUserProfileResponseModel?.user?.name ?? "",
                        style: AppFontStyle.fontStyleW700(fontSize: 12, fontColor: AppColors.txtPending),
                      ),
                    ],
                  ).paddingOnly(right: 14, left: 14, top: 14),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        EnumLocale.txtVerificationID.name.tr,
                        style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.greyTxt),
                      ),
                      Text(
                        Database.uniqueId,
                        style: AppFontStyle.fontStyleW700(fontSize: 12, fontColor: AppColors.txtPending),
                      ),
                    ],
                  ).paddingOnly(right: 14, left: 14, top: 14),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        EnumLocale.txtDateTime2.name.tr,
                        style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.greyTxt),
                      ),
                      Text(
                        Database.verifyTime,
                        style: AppFontStyle.fontStyleW700(fontSize: 12, fontColor: AppColors.txtPending),
                      ),
                    ],
                  ).paddingOnly(right: 14, left: 14, top: 14),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        EnumLocale.txtStatus.name.tr,
                        style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.greyTxt),
                      ),
                      Container(
                        decoration: BoxDecoration(
                          color: AppColors.redLight,
                          borderRadius: BorderRadius.circular(5),
                        ),
                        child: Text(
                          EnumLocale.txtPending.name.tr,
                          style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.redColor),
                        ).paddingSymmetric(horizontal: 8, vertical: 4),
                      ),
                    ],
                  ).paddingOnly(right: 14, left: 14, top: 14, bottom: 10)
                ],
              ),
            ),
            PrimaryAppButton(
              height: 52,
              onTap: () {
                // Get.toNamed(AppRoutes.bottomBar);
                Get.close(2);
              },
              text: EnumLocale.txtBackToHome.name.tr,
              textStyle: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.white),
            ).paddingOnly(left: 18, bottom: 18, right: 18, top: 15)
          ],
        ).paddingAll(0),
      ),
    );
  }
}
