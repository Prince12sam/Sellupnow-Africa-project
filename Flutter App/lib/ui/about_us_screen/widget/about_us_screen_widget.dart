import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class AboutUsScreenAppBar extends StatelessWidget {
  final String? title;
  const AboutUsScreenAppBar({super.key, this.title});

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

class AboutUsScreenWidget extends StatelessWidget {
  const AboutUsScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          '${EnumLocale.txtAboutUs.name.tr} :',
          style: AppFontStyle.fontStyleW800(fontSize: 24, fontColor: AppColors.black),
        ).paddingOnly(bottom: 4),
        Text(
          "Lorem Ipsum is simply dummy text the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 15s, when an unknown printer took a galley of type and scrambled.",
          style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.popularProductText, height: 2),
        ).paddingOnly(bottom: 18),
        Text(
          "Classified App Details :",
          style: AppFontStyle.fontStyleW700(fontSize: 18, fontColor: AppColors.appRedColor),
        ).paddingOnly(bottom: 8),
        Text(
          "Lorem Ipsum is simply dummy text the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 15s, when an unknown printer took a galley of type and scrambled.Lorem Ipsum is simply dummy text the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 15s, when an unknown printer took a galley of type and scrambled.Lorem Ipsum is simply dummy text the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 15s, when an unknown printer took a galley of type and scrambled.Lorem Ipsum is simply dummy text the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 15s, when an unknown printer took a galley of type and scrambled.",
          style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.popularProductText, height: 2),
        ).paddingOnly(bottom: 18),
      ],
    ).paddingSymmetric(horizontal: 14, vertical: 18);
  }
}
