import 'dart:convert';
import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:get/get.dart';
import 'app_color.dart';

class Utils {
  static RxBool isAppOpen = false.obs;
  static String? playStoreId = "com.company.sellupnow";
  static String? appStoreId = "6747668315";

  /// =================== Toast =================== ///
  static showToast(BuildContext context, String msg,
      {ToastGravity gravity = ToastGravity.BOTTOM,
      Color? color,
      Color? txtColor}) {
    return Fluttertoast.showToast(
      msg: msg,
      toastLength: Toast.LENGTH_LONG,
      gravity: gravity,
      backgroundColor: color ?? AppColors.appRedColor,
      textColor: txtColor ?? AppColors.white,
      fontSize: 15,
    );
  }

  /// =================== Current Focus Node =================== ///
  static currentFocus(BuildContext context) {
    FocusScopeNode currentFocus = FocusScope.of(context);
    if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
      currentFocus.focusedChild?.unfocus();
    }
  }

  /// =================== Console Log =================== ///
  static showLog(String text) {
    log(text);
  }

  static void onChangeStatusBar({
    required Brightness brightness,
    int? delay,
  }) {
    showLog("Change Status Bar => Brightness => $brightness => $delay");
    Future.delayed(
      Duration(milliseconds: delay ?? 0),
      () => SystemChrome.setSystemUIOverlayStyle(
        SystemUiOverlayStyle(
          statusBarColor: AppColors.transparent,
          statusBarIconBrightness: brightness,
        ),
      ),
    );
  }

  static String toJson(Map<String, dynamic> map) {
    return jsonEncode(map);
  }
}

extension HeightExtension on num {
  SizedBox get height => SizedBox(height: toDouble());
}

extension WidthExtension on num {
  SizedBox get width => SizedBox(width: toDouble());
}
