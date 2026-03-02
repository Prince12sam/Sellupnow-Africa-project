// ignore_for_file: must_be_immutable

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class PrimaryAppButton extends StatelessWidget {
  double? height;
  double? width;
  double? borderRadius;
  TextStyle? textStyle;
  List<Color>? gradientColor;
  double? iconPadding;
  Color? color;
  Color? borderColor;
  Color? fontColor;
  String? text;
  Widget? widget;
  Widget? child;
  TextOverflow? overflow;
  double? fontSize;
  Function()? onTap;

  PrimaryAppButton({
    super.key,
    this.height,
    this.width,
    this.borderRadius,
    this.textStyle,
    this.gradientColor,
    this.iconPadding,
    this.color,
    this.borderColor,
    this.fontColor,
    this.text,
    this.widget,
    this.child,
    this.onTap,
    this.overflow,
    this.fontSize,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        height: height,
        width: width ?? Get.width,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(borderRadius ?? 12),
          gradient: LinearGradient(
            colors: gradientColor ?? [color ?? AppColors.appRedColor, color ?? AppColors.appRedColor],
          ),
          border: Border.all(
            color: borderColor ?? AppColors.transparent,
            width: 0.8,
          ),
        ),
        child: child ??
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                if (widget != null) ...[
                  widget ?? const SizedBox.shrink(),
                  10.width,
                ],
                Center(
                  child: Text(
                    text ?? "",
                    overflow: overflow,
                    style: textStyle ?? AppFontStyle.fontStyleW700(fontSize: fontSize ?? 16, fontColor: fontColor ?? AppColors.white),
                  ),
                ),
              ],
            ),
      ),
    );
  }
}
