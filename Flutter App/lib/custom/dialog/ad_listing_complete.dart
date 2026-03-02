import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';

class AdListingCompleteDialog extends StatelessWidget {
  const AdListingCompleteDialog({super.key});

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
            Image.asset(AppAsset.completeDialogImage, height: 162).paddingOnly(top: 6, bottom: 20),
            Text(EnumLocale.txtAdListingComplete.name.tr, style: AppFontStyle.fontStyleW800(fontSize: 25, fontColor: AppColors.appRedColor))
                .paddingOnly(bottom: 6),
            Text(EnumLocale.txtAdListingCompleteDesTxt.name.tr,
                    textAlign: TextAlign.center, style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText))
                .paddingOnly(bottom: 10),
            PrimaryAppButton(
              height: 54,
              onTap: () {
                Get.offAllNamed(AppRoutes.bottomBar);
              },
              text: EnumLocale.txtViewMyFeaturedAds.name.tr,
            )
          ],
        ).paddingAll(17),
      ),
    );
  }
}
