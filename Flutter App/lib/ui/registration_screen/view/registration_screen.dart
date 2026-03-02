import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_background/app_background.dart';
import 'package:listify/ui/registration_screen/widget/registration_screen_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class RegistrationScreen extends StatelessWidget {
  const RegistrationScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // backgroundColor: AppColors.lightPurple,
      resizeToAvoidBottomInset: true,
      bottomNavigationBar: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          SubMitBottomButton().paddingOnly(bottom: 20),
        ],
      ),
      body: LoginBg(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              height: 42,
              width: 42,
              decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.categoriesBgColor),
              child: Center(
                  child: Image.asset(
                AppAsset.backArrowIcon,
                height: 26,
              )),
            ).paddingOnly(top: 25, left: 18),
            Expanded(
              child: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      EnumLocale.txtSelfRegistration.name.tr,
                      // textAlign: TextAlign.center,
                      style: AppFontStyle.fontStyleW900(
                        fontSize: 42,
                        fontColor: AppColors.appRedColor,
                      ),
                    ).paddingOnly(top: 48, left: 18),
                    Text(
                      EnumLocale.txtSelfRegistrationTxt.name.tr,
                      // textAlign: TextAlign.center,
                      style: AppFontStyle.fontStyleW500(
                        fontSize: 15,
                        fontColor: AppColors.black,
                      ),
                    ).paddingOnly(bottom: 24, left: 18, right: 18),
                    RegistrationAddInfoView().paddingOnly(bottom: 20),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
