import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class CustomTitle extends StatelessWidget {
  final String title;
  final TextStyle? textStyle;
  final Widget method;
  final Color? txtColor;

  const CustomTitle({
    super.key,
    required this.title,
    required this.method,
    this.textStyle,
    this.txtColor,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: textStyle ??
              AppFontStyle.fontStyleW500(
                fontSize: 15,
                fontColor: txtColor ?? AppColors.popularProductText,
              ),
        ).paddingOnly(bottom: 12, left: 5),
        method,
      ],
    );
  }
}
