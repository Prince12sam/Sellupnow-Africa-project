import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/splash_screen/controller/splash_screen_controller.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class SplashScreenView extends StatelessWidget {
  const SplashScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SplashScreenController>(builder: (controller) {
      return Scaffold(
        body: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Center(
              child: Text(
                "Sellupnow",
                style: AppFontStyle.fontStyleW900(
                    fontSize: 40, fontColor: AppColors.appRedColor),
              ),
            )
          ],
        ),
      );
    });
  }
}
