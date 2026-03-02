import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/product_list_view_container.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/home_search_product_screen/controller/home_search_product_controller.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_list_view_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/like_manager.dart';

class HomeSearchAppBar extends StatelessWidget {
  final String? title;
  const HomeSearchAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(300),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
        showBoxShadow: false,
      ),
    );
  }
}

class HomeSearchTopView extends StatelessWidget {
  const HomeSearchTopView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeSearchProductController>(builder: (controller) {
      return Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: AppColors.lightGrey.withValues(alpha: 0.36),
              spreadRadius: 0,
              offset: const Offset(0.0, 0.0),
              blurRadius: 12,
            ),
          ],
          borderRadius: BorderRadius.vertical(
            bottom: Radius.circular(18),
          ),
        ),
        child: Row(
          children: [
            Expanded(
              child: Container(
                height: 48,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  color: AppColors.lightPurple,
                ),
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 10), // <-- ADD THIS
                  child: Row(
                    children: [
                      Image.asset(
                        AppAsset.searchIcon,
                        height: 22,
                        width: 22,
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: TextField(
                          controller: controller.searchController,
                          onChanged: (value) {
                            if (controller.debounce?.isActive ?? false) controller.debounce!.cancel();
                            controller.debounce = Timer(const Duration(milliseconds: 500), () {
                              final query = value.trim();
                              controller.getProduct(search: query.isNotEmpty ? query : null);
                            });
                          },
                          decoration: InputDecoration(
                            fillColor: AppColors.lightPurple,
                            filled: true,
                            border: InputBorder.none,
                            focusedBorder: InputBorder.none,
                            enabledBorder: InputBorder.none,
                            errorBorder: InputBorder.none,
                            disabledBorder: InputBorder.none,
                            contentPadding: const EdgeInsets.only(left: 8),
                            hintText: EnumLocale.txtSearchHere.name.tr,
                            hintStyle: AppFontStyle.fontStyleW400(
                              fontSize: 16,
                              fontColor: AppColors.searchText,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ).paddingOnly(bottom: 15, right: 14, left: 14),
      );
    });
  }
}

class ProductSHowView extends StatelessWidget {
  const ProductSHowView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeSearchProductController>(
        id: Constant.idAllAds,
        builder: (controller) {
          if (controller.isLoading && controller.popularProductList.isEmpty) {
            return const ProductListViewShimmer().paddingOnly(top: 15);
          }

          if (!controller.isLoading && controller.popularProductList.isEmpty) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.onRefresh(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: Get.height * 0.7,
                  child: Center(
                    child: NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr),
                  ),
                ),
              ),
            );
          }

          return Expanded(
            child: RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.onRefresh(),
              child: SingleChildScrollView(
                controller: controller.scrollController,
                physics: BouncingScrollPhysics(),
                child: ListView.builder(
                  shrinkWrap: true,
                  // controller: controller.scrollController,
                  physics: const NeverScrollableScrollPhysics(),
                  padding: const EdgeInsets.only(bottom: 30, top: 15),
                  itemCount: controller.popularProductList.length + (controller.isPaginationLoading ? 1 : 0),
                  itemBuilder: (context, index) {
                    // bottom loader cell
                    if (index >= controller.popularProductList.length) {
                      return  Padding(
                        padding: EdgeInsets.symmetric(vertical: 16),
                        child: Center(child: CircularProgressIndicator(color: AppColors.appRedColor,)),
                      );
                    }

                    final product = controller.popularProductList[index];
                    final isLiked = LikeManager.to.getLikeState(product.id ?? "", fallback: product.isLike);

                    return GestureDetector(
                      onTap: () {
                        Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                          'sellerDetail': true,
                          'relatedProduct': true,
                          'viewLikeCount': true,
                          'adId': product.id,
                        })?.then((value) {
                          controller.update([Constant.idAllAds]);
                        });
                      },
                      child: ProductListViewContainer(
                        isVerify: product.seller?.isVerified ?? false,
                        isOffer: product.seller?.isFeaturedSeller ?? false,
                        description: "${product.description}",
                        productImage: "${product.primaryImage}",
                        isLiked: isLiked,
                        onLikeTap: () => controller.toggleLike(index, product.id ?? ""),
                        newPrice: "${product.price}",
                        productName: "${product.title}",
                        sellerImage: "${product.seller?.profileImage}",
                        sellerLocation: "${product.location?.country}",
                        sellerName: "${product.seller?.name}",
                      ).paddingSymmetric(horizontal: 14),
                    );
                  },
                ),
              ),

            ),
          );
        });
  }
}
