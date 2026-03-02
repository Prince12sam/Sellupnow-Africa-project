import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/dialog/choose_number_dialog.dart';
import 'package:listify/custom/dialog/send_mail_dialog.dart';
import 'package:listify/ui/contact_us_screen/controller/contact_us_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ContactUsScreenAppBar extends StatelessWidget {
  final String? title;
  const ContactUsScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class ContactUsScreenWidget extends StatelessWidget {
  const ContactUsScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          EnumLocale.txtWhatCanWeHelpYou.name.tr,
          style: AppFontStyle.fontStyleW700(fontSize: 20, fontColor: AppColors.appRedColor),
        ).paddingOnly(bottom: 2),
        Text(
          "Lorem Ipsum is simply dummy text printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 15s, when an unknown printer took a galley.",
          style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText),
        ).paddingOnly(bottom: 22),
        Text(
          EnumLocale.txtSelectAnyone.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.searchText),
        ).paddingOnly(bottom: 18),
        GetBuilder<ContactUsScreenController>(
          builder: (controller) {
            return GestureDetector(
              onTap: () {
                Get.dialog(
                  barrierColor: AppColors.black.withValues(alpha: 0.8),
                  Dialog(
                    insetPadding: EdgeInsets.symmetric(horizontal: 32),
                    backgroundColor: AppColors.transparent,
                    shadowColor: Colors.transparent,
                    surfaceTintColor: Colors.transparent,
                    elevation: 0,
                    child: ChooseNumberDialog(controller :controller),
                  ),
                );
              },
              child: Container(
                decoration: BoxDecoration(
                  border: Border.all(color: AppColors.borderColor, width: 0.8),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: EdgeInsets.all(12),
                      decoration: BoxDecoration(color: AppColors.appRedColor, borderRadius: BorderRadius.circular(14)),
                      child: Image.asset(
                        AppAsset.callIcon,
                        height: 30,
                        width: 30,
                      ),
                    ).paddingAll(4),
                    14.width,
                    Text(EnumLocale.txtAskCall.name.tr, style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.faqTxt)),
                    Spacer(),
                    RotatedBox(
                      quarterTurns: 2,
                      child: Image.asset(
                        AppAsset.backArrowIcon,
                        height: 26,
                        width: 26,
                        color: AppColors.popularProductText,
                      ),
                    ).paddingOnly(right: 10),
                  ],
                ),
              ),
            );
          }
        ),
        18.height,
        GetBuilder<ContactUsScreenController>(
          builder: (controller) {
            return GestureDetector(
              onTap: () {
                Get.dialog(
                  barrierColor: AppColors.black.withValues(alpha: 0.8),
                  Dialog(
                    insetPadding: EdgeInsets.symmetric(horizontal: 32),
                    backgroundColor: AppColors.transparent,
                    shadowColor: Colors.transparent,
                    surfaceTintColor: Colors.transparent,
                    elevation: 0,
                    child: SendMailDialog(controller: controller,),
                  ),
                );
              },
              child: Container(
                decoration: BoxDecoration(
                  border: Border.all(color: AppColors.borderColor, width: 0.8),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: EdgeInsets.all(12),
                      decoration: BoxDecoration(color: AppColors.appRedColor, borderRadius: BorderRadius.circular(14)),
                      child: Image.asset(
                        AppAsset.mailFillIcon,
                        height: 30,
                        width: 30,
                      ),
                    ).paddingAll(4),
                    14.width,
                    Text(EnumLocale.txtWriteEmail.name.tr, style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.faqTxt)),
                    Spacer(),
                    RotatedBox(
                      quarterTurns: 2,
                      child: Image.asset(
                        AppAsset.backArrowIcon,
                        height: 26,
                        width: 26,
                        color: AppColors.popularProductText,
                      ),
                    ).paddingOnly(right: 10),
                  ],
                ),
              ),
            );
          }
        ),
      ],
    ).paddingOnly(left: 16, right: 16, top: 18);
  }
}
