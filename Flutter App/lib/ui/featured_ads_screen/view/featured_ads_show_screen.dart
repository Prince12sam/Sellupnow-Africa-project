import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/featured_ads_screen/controller/featured_ads_show_screen_controller.dart';
import 'package:listify/ui/featured_ads_screen/widget/featured_ads_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class FeaturedAdsShowScreen extends StatelessWidget {
  const FeaturedAdsShowScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        backgroundColor: AppColors.profileItemBgColor,
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: FeatureProductAppBar(
            title: EnumLocale.txtProducts.name.tr,
          ),
          actions: [
            
            GetBuilder<FeaturedAdsShowScreenController>(
              id: Constant.idAllAds,
              builder: (controller) {
                return Text("${controller.selectedIds.length}/${controller.plan}",style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.appRedColor),).paddingOnly(right: 10);
              }
            )
          ],
        ),
        body: ProductShowFeature(),
        bottomNavigationBar: BottomButton());
  }
}
