import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class CustomAppBar extends StatelessWidget {
  String? title;
  List<Widget>? action;
  List<Color>? gradientColor;
  Color? textColor;
  Color? appBarColor;
  Color? iconColor;
  final bool showLeadingIcon;
  final bool showBoxShadow;
  Function()? onTap;
  Widget? child;

  CustomAppBar({
    super.key,
    this.title,
    this.action,
    this.appBarColor,
    required this.showLeadingIcon,
    this.gradientColor,
    this.textColor,
    this.iconColor,
    this.onTap,
    this.child,
    this.showBoxShadow = true,
  });

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: AppColors.white,
      elevation: 0,
      toolbarHeight: 300,
      centerTitle: true,
      flexibleSpace: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.vertical(bottom: Radius.circular(18)),
          boxShadow: showBoxShadow
              ? [
                  BoxShadow(
                    color: AppColors.lightGrey.withValues(alpha: 0.36),
                    spreadRadius: 0,
                    offset: const Offset(0.0, 0.0),
                    blurRadius: 12,
                  ),
                ]
              : null,
          color: appBarColor ?? AppColors.white,
        ),
      ),
      leading: showLeadingIcon == true
          ? GestureDetector(
              onTap: onTap ??
                  () {
                    Utils.showLog("fkhjuhjugfhuighuig");
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
                    // width: 26,
                    // height: 26,
                    color: iconColor ?? AppColors.black,
                  ),
                ),
              ),
            ).paddingOnly(left: 17)
          : const SizedBox.shrink(),
      actions: action,
      title: child ??
          Text(
            title ?? '',
            style: AppFontStyle.fontStyleW700(
              fontSize: 20,
              fontColor: textColor ?? AppColors.black,
            ),
          ),
    );
  }
}
