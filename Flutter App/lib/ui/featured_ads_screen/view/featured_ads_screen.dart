import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/featured_ads_screen/controller/featured_ads_screen_controller.dart';
import 'package:listify/ui/featured_ads_screen/widget/featured_ads_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';

class FeaturedAdsScreen extends StatelessWidget {
  const FeaturedAdsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      bottomNavigationBar: FeaturedAdsBottomButton(),
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: FeaturedAdsScreenAppBar(
          title: EnumLocale.txtMyFeaturedAds.name.tr,
        ),
      ),
      body: GetBuilder<FeaturedAdsScreenController>(
          id: Constant.idFeatureAdsPlan,
          builder: (controller) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.onRefresh(),
              child: SingleChildScrollView(
                physics: AlwaysScrollableScrollPhysics(),
                child: Column(
                  children: [
                    FeaturedAdsScreenWidget(),
                  ],
                ),
              ),
            );
          }),
    );
  }
}
