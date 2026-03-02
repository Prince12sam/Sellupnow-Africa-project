import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/ui/contact_us_screen/controller/contact_us_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SendMailDialog extends StatelessWidget {
  final ContactUsScreenController controller;
  const SendMailDialog({super.key, required this.controller});

  @override
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
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              children: [
                GestureDetector(
                  onTap: () {
                    Get.back();
                  },
                  child: Container(
                    height: 44,
                    width: 44,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppColors.categoriesBgColor,
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(7),
                      child: Image.asset(
                        AppAsset.backArrowIcon,
                        color: AppColors.black,
                      ),
                    ),
                  ),
                ).paddingOnly(right: 18),
                Text(
                  EnumLocale.txtSendEmail.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 22, fontColor: AppColors.black),
                ).paddingOnly(right: 44),
              ],
            ).paddingOnly(bottom: 18),
            CustomTitle(

              title: EnumLocale.txtSubject.name.tr,
              method: CustomTextField(
                controller: controller.subject,

                filled: true,
                borderColor: AppColors.textFieldBorderColor,
                fillColor: AppColors.editTextFieldColor,
              ),
            ).paddingOnly(bottom: 20),
            CustomTitle(
              title: EnumLocale.txtEmailAddress.name.tr,
              method: CustomTextField(
                readOnly: true,
                controller: controller.email,
                filled: true,
                borderColor: AppColors.textFieldBorderColor,
                fillColor: AppColors.editTextFieldColor,
              ),
            ).paddingOnly(bottom: 20),
            CustomTitle(
              title: EnumLocale.txtDescription.name.tr,
              method: CustomTextField(
                filled: true,
                controller: controller.description,

                fontColor: AppColors.popularProductText,
                maxLines: 4,
                borderColor: AppColors.textFieldBorderColor,
                fillColor: AppColors.editTextFieldColor,
              ),
            ).paddingOnly(bottom: 20),
            PrimaryAppButton(
              height: 54,
              text: EnumLocale.txtSendEmail.name.tr,
              onTap: () async {
                final to = controller.email.text.trim();
                final subject = controller.subject.text.trim();
                final body = controller.description.text.trim();

                // Optional: basic validation / feedback
                if (to.isEmpty) {
                  Get.snackbar('Error', 'Email address is empty');
                  return;
                }
                if (subject.isEmpty) {
                  Get.snackbar('Error', 'Please enter subject');
                  return;
                }
                if (body.isEmpty) {
                  Get.snackbar('Error', 'Please enter description');
                  return;
                }

                // Close dialog first (optional)
                Get.back();

                // Open email compose
                await controller.openEmail(
                  to: to,
                  subject: subject,
                  body: body,
                  // cc: ['support2@example.com'],  // if needed
                  // bcc: ['audit@example.com'],    // if needed
                );
              },
            ),
          ],
        ).paddingAll(17),
      ),
    );
  }
}
