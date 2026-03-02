import 'package:flutter/material.dart';
import 'package:listify/utils/app_color.dart';

class AppFontStyle {
  static fontStyleW400({required double fontSize, required Color fontColor, TextDecoration? textDecoration, double? height, Color? decorationColor}) {
    return TextStyle(
        color: fontColor,
        fontSize: fontSize,
        fontWeight: FontWeight.w400,
        fontFamily: "AirbnbCereal_W_Bk",
        decoration: textDecoration,
        decorationColor: decorationColor);
  }

  static fontStyleW500({required double fontSize, required Color fontColor, TextDecoration? textDecoration, double? height, Color? decorationColor}) {
    return TextStyle(
        color: fontColor,
        fontSize: fontSize,
        height: height,
        fontWeight: FontWeight.w500,
        fontFamily: "AirbnbCereal_W_Md",
        decoration: textDecoration,
        decorationColor: decorationColor);
  }

  static fontStyleW600({
    required double fontSize,
    required Color fontColor,
    TextDecoration? textDecoration,
    Color? decorationColor,
  }) {
    return TextStyle(
        color: fontColor,
        fontSize: fontSize,
        fontWeight: FontWeight.w500,
        fontFamily: "AirbnbCereal_W_Md",
        decoration: textDecoration,
        decorationColor: decorationColor);
  }

  static fontStyleW700(
      {required double fontSize, required Color fontColor, TextDecoration? textDecoration, Color? decorationColor, double? letterSpace}) {
    return TextStyle(
        color: fontColor,
        fontSize: fontSize,
        fontWeight: FontWeight.w700,
        fontFamily: "AirbnbCereal_W_Bd",
        decoration: textDecoration,
        decorationColor: decorationColor);
  }

  static fontStyleW800(
      {required double fontSize,
      required Color fontColor,
      TextDecoration? textDecoration,
      Color? decorationColor,
      double? letterSpace,
      double? height}) {
    return TextStyle(
        color: fontColor,
        fontSize: fontSize,
        fontWeight: FontWeight.w800,
        fontFamily: "AirbnbCereal_W_Bd",
        decoration: textDecoration,
        height: height,
        decorationColor: decorationColor);
  }

  static fontStyleW900(
      {required double fontSize, required Color fontColor, TextDecoration? textDecoration, Color? decorationColor, double? letterSpace}) {
    return TextStyle(
        color: fontColor,
        fontSize: fontSize,
        fontWeight: FontWeight.w900,
        fontFamily: "AirbnbCereal_W_XBd",
        decoration: textDecoration,
        decorationColor: decorationColor);
  }

  static fontStyleW5002(
      {required double fontSize, required Color fontColor, TextDecoration? textDecoration, Color? decorationColor, double? letterSpace}) {
    return TextStyle(
        color: fontColor,
        fontSize: fontSize,
        fontWeight: FontWeight.w800,
        fontFamily: "AirbnbCereal_W_Md",
        decoration: textDecoration,
        decorationColor: decorationColor);
  }

  static fontStyleW5003(
      {required double fontSize, required Color fontColor, TextDecoration? textDecoration, double? height, Color? decorationColor}) {
    return TextStyle(
      color: fontColor,
      fontSize: fontSize,
      fontWeight: FontWeight.w500,
      fontFamily: "AirbnbCereal_W_Md",
      height: 1.6,
    );
  }

  static fontStyleW7002(
      {required double fontSize, required Color fontColor, TextDecoration? textDecoration, Color? decorationColor, double? letterSpace}) {
    return TextStyle(
        shadows: [
          Shadow(
            color: AppColors.black.withValues(alpha: 0.15),
            offset: Offset(0, 1),
          )
        ],
        color: fontColor,
        fontSize: fontSize,
        fontWeight: FontWeight.w700,
        fontFamily: "AirbnbCereal_W_Bd",
        decoration: textDecoration,
        decorationColor: decorationColor);
  }
}
