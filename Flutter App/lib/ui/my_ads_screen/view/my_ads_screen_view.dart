import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/bottom_bar/controller/bottom_bar_controller.dart';
import 'package:listify/ui/my_ads_screen/widget/my_ads_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class MyAdsScreenView extends StatelessWidget {
  const MyAdsScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        Get.find<BottomBarController>().onClick(0);
        if (didPop) return;
      },
      child: Scaffold(
        backgroundColor: AppColors.adScreenBgColor,
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: AdsScreenAppBar(
            title: EnumLocale.txtMyAds.name.tr,
          ),
        ),
        body: Column(
          children: [
            AdsTabBar(),
          ],
        ),
      ),
    );
  }
}
