import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class ReportUserDialog extends StatelessWidget {
  final VoidCallback? onTap;
  final TextEditingController? controller;
  const ReportUserDialog({super.key, this.onTap, this.controller});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      // height: 351,
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(
            Radius.circular(90),
          ),
        ),
        color: AppColors.white,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              "Add Report",
              style: AppFontStyle.fontStyleW700(fontSize: 20, fontColor: AppColors.appRedColor),
            ).paddingOnly(bottom: 20, top: 10),
            Text(
              "Please provide additional details about why you're reporting this user...",
              style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.black),
              textAlign: TextAlign.center,
            ).paddingOnly(bottom: 20),
            CustomTextField(
              filled: true,
              controller: controller,
              fillColor: AppColors.white,
              cursorColor: AppColors.black,
            ),
            // Spacer(),
            PrimaryAppButton(
              onTap: onTap,
              height: 47,
              borderRadius: 8,
              color: AppColors.appRedColor,
              text: "Add Report",
              textStyle: AppFontStyle.fontStyleW700(
                fontSize: 17,
                fontColor: AppColors.white,
              ),
            ).paddingOnly(top: 20, bottom: 10),
          ],
        ).paddingAll(15),
      ),
    );
  }
}
