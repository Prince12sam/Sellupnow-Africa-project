import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/seller_detail_screen/controller/seller_detail_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';

class SellerDetailProductAllViewWidget extends StatelessWidget {
  const SellerDetailProductAllViewWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SellerDetailScreenController>(
      id: Constant.idUserAds,
      builder: (controller) {
        // Show shimmer while loading
        if (controller.isLoading) {
          return UserProductGridViewShimmer();
        }
        // Check if the list is empty
        if (controller.userAllAds.isEmpty) {
          return RefreshIndicator(
            color: AppColors.appRedColor, // Change to your app color
            onRefresh: () async {
              controller.init(); // Re-fetch data from API
            },
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              child: SizedBox(
                  height: MediaQuery.of(context).size.height * 0.7,
                  child: Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr),
                      ],
                    ),
                  )),
            ),
          );
        }
        const cross = 2;
        const tileHeight = 245.0;
        // GridView with products
        return RefreshIndicator(
          color: AppColors.appRedColor, // Change to your app color
          onRefresh: () async {
            controller.init(); // Re-fetch data
          },
          child: GridView.builder(
            padding: const EdgeInsets.only(top: 20, bottom: 20, left: 17, right: 17),
            shrinkWrap: true,
            physics: const AlwaysScrollableScrollPhysics(),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: cross,
              mainAxisExtent: tileHeight,        // <- key change (no childAspectRatio)
              crossAxisSpacing: 6,
              mainAxisSpacing: 6,
            ),
            itemCount: controller.userAllAds.length,
            itemBuilder: (context, index) {
              final adProduct = controller.userAllAds[index];
              return Column(
                children: [
                  GestureDetector(
                    onTap: () {
                      Utils.showLog("name:::::::::::::::::${adProduct.title}");
                      Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                        'sellerDetail': true,
                        'relatedProduct': true,
                        'adId': adProduct.id,
                        // 'ad': adProduct,
                      });
                    },
                    child: SellerProductGridView(
                      productImage: adProduct.primaryImage.toString(),
                      isLiked: controller.isAdLiked(adProduct),
                      onLikeTap: () {
                        controller.toggleLike(index, adProduct.id ?? "");
                      },
                      newPrice:
                          "${adProduct.isAuctionEnabled == true ? adProduct.auctionStartingPrice?.toString() ?? '' : adProduct.price ?? "0"}",
                      productName: adProduct.title.toString(),
                      sellerImage: adProduct.seller?.profileImage.toString() ?? "",
                      sellerLocation: adProduct.location?.country.toString() ?? "",
                      sellerName: adProduct.seller?.name.toString() ?? "",
                    ),
                  ),
                ],
              );
            },
          ),
        );
      },
    );
  }
}

class SellerDetailProductAllViewAppBar extends StatelessWidget {
  final String? title;
  const SellerDetailProductAllViewAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}
