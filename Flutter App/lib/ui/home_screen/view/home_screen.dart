import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/dialog/exit_app_dialog.dart';
import 'package:listify/ui/home_screen/controller/home_screen_controller.dart';
import 'package:listify/ui/home_screen/shimmer/slider_image_shimmer.dart';
import 'package:listify/ui/home_screen/widget/home_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        Get.dialog(
          barrierColor: AppColors.black.withValues(alpha: 0.8),
          Dialog(
            backgroundColor: AppColors.transparent,
            shadowColor: Colors.transparent,
            surfaceTintColor: Colors.transparent,
            elevation: 0,
            child: const ExitAppDialog(),
          ),
        );
        if (didPop) return;
      },
      child: Scaffold(
        backgroundColor: AppColors.white,
        body: GestureDetector(
          onTap: () {
            FocusScopeNode currentFocus = FocusScope.of(context);
            if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
              currentFocus.focusedChild?.unfocus();
            }
          },
          child: GetBuilder<HomeScreenController>(builder: (controller) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.onRefresh(),
              child: Column(
                children: [
                  TopHomeView(),
                  Expanded(
                    child: SingleChildScrollView(
                      physics: AlwaysScrollableScrollPhysics(),
                      child: Column(
                        children: [
                          OurCategoriesView(),
                           LiveAuctionView(),
                          controller.isBanner ? SliderImageShimmer() : OfferImageView(),
                          PopularItemsView(),
                           MostLikedItemsView(),
                          100.height,
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            );
          }),
        ),
      ),
    );
  }
}
