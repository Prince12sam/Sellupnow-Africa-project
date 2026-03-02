import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/custom/product_view/product_list_view_container.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/popular_poduct_screen/controller/popular_product_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_list_view_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

class PopularProductScreenAppBar extends StatelessWidget {
  final String? title;
  const PopularProductScreenAppBar({super.key, this.title});

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

class SearchView extends StatelessWidget {
  const SearchView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<PopularProductScreenController>(
      id: Constant.idViewType,
      builder: (controller) {
        return Row(
          children: [
            Expanded(
              child: Container(
                height: 48,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  color: AppColors.lightPurple.withValues(alpha: 0.2),
                  border: Border.all(color: AppColors.borderColor),
                ),
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 10),
                  child: Row(
                    children: [
                      Image.asset(AppAsset.searchIcon, height: 22, width: 22),
                      const SizedBox(width: 8),
                      Expanded(
                        child: TextField(
                          onChanged: (value) {
                            if (controller.debounce?.isActive ?? false)
                              controller.debounce!.cancel();
                            controller.debounce =
                                Timer(const Duration(milliseconds: 500), () {
                              final query = value.trim();
                              controller.getProduct(
                                  search: query.isNotEmpty ? query : null);
                            });
                          },
                          controller: controller.searchController,
                          decoration: InputDecoration(
                            hintText: EnumLocale.txtSearchHere.name.tr,
                            hintStyle: AppFontStyle.fontStyleW400(
                              fontSize: 16,
                              fontColor: AppColors.searchText,
                            ),
                            border: InputBorder.none,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            10.width,
            GestureDetector(
              onTap: () => controller.toggleView(ViewType.grid),
              child: Container(
                height: 48,
                padding:
                    const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  color: controller.selectedView == ViewType.grid
                      ? AppColors.appRedColor.withValues(alpha: 0.05)
                      : AppColors.lightPurple.withValues(alpha: 0.2),
                  border: Border.all(
                      color: controller.selectedView == ViewType.grid
                          ? AppColors.appRedColor
                          : AppColors.borderColor),
                ),
                child: Image.asset(
                  AppAsset.gridViewIcon,
                  height: 24,
                  width: 24,
                  color: controller.selectedView == ViewType.grid
                      ? AppColors.appRedColor
                      : AppColors.grey.withValues(alpha: 0.6),
                ),
              ),
            ),
            10.width,
            GestureDetector(
              onTap: () => controller.toggleView(ViewType.list),
              child: Container(
                height: 48,
                padding:
                    const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  color: controller.selectedView == ViewType.list
                      ? AppColors.appRedColor.withValues(alpha: 0.05)
                      : AppColors.lightPurple.withValues(alpha: 0.2),
                  border: Border.all(
                      color: controller.selectedView == ViewType.list
                          ? AppColors.appRedColor
                          : AppColors.borderColor),
                ),
                child: Image.asset(
                  AppAsset.listViewIcon,
                  height: 24,
                  width: 24,
                  color: controller.selectedView == ViewType.list
                      ? AppColors.appRedColor
                      : AppColors.grey.withValues(alpha: 0.6),
                ),
              ),
            ),
          ],
        ).paddingOnly(top: 18, left: 14, right: 14, bottom: 18);
      },
    );
  }
}

class GridProductView extends StatelessWidget {
  const GridProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: GetBuilder<PopularProductScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          if (controller.isLoading) {
            return const ProductGridViewShimmer();
          }

          if (controller.popularProductList.isEmpty) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.init(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: MediaQuery.of(context).size.height * 0.7,
                  child: NoDataFound(
                      image: AppAsset.noProductFound,
                      imageHeight: 180,
                      text: EnumLocale.txtNoDataFound.name.tr),
                ),
              ),
            );
          }

          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () => controller.init(),
            child: GetBuilder<PopularProductScreenController>(
                id: Constant.idPagination,
              builder: (context) {
                const cross = 2;
                const tileHeight = 255.0;
                return GridView.builder(
                  controller: controller.scrollController,
                  padding: EdgeInsets.only(bottom: 80),
                  physics: const AlwaysScrollableScrollPhysics(),
                  itemCount: controller.popularProductList.length +
                      (controller.isPaginationLoading || controller.hasMore
                          ? 1
                          : 0),
                  gridDelegate:  SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: cross,
                    mainAxisExtent: tileHeight,        // <- key change (no childAspectRatio)
                    crossAxisSpacing: 6,
                    mainAxisSpacing: 6,
                  ),
                  itemBuilder: (context, index) {
                    // ✅ show loader at the bottom
                    if (index >= controller.popularProductList.length) {
                      return GetBuilder<PopularProductScreenController>(
                        id: Constant.idPagination,
                        builder: (_) {
                          if (!controller.isPaginationLoading ||
                              !controller.hasMore) {
                            return const SizedBox.shrink();
                          }
                          return Center(
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: CircularProgressIndicator(
                                  color: AppColors.appRedColor),
                            ),
                          );
                        },
                      );
                    }

                    final popularItem = controller.popularProductList[index];
                    final isLiked = LikeManager.to.getLikeState(
                        popularItem.id ?? "",
                        fallback: popularItem.isLike);
                    return GestureDetector(
                      onTap: () {
                        Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                          'sellerDetail': true,
                          'relatedProduct': true,
                          'viewLikeCount': true,
                          'adId': popularItem.id,
                        })?.then((value) {
                          controller.update([Constant.idAllAds]);
                        },);
                      },
                      child: ProductGridView(
                        isVerify: popularItem.seller?.isVerified??false,
                        topSeller: popularItem.seller?.isFeaturedSeller??false,
                        // description: favouriteItem.description ?? "",
                        productImage: popularItem.primaryImage ?? "",
                        isLiked:isLiked,
                        onLikeTap: () =>
                            controller.toggleLike(index, popularItem.id ?? ""),
                        newPrice:
                            "${Database.settingApiResponseModel?.data?.currency?.symbol} ${popularItem.isAuctionEnabled == true ? popularItem.auctionStartingPrice?.toString() ?? '' : popularItem.price ?? "0"}",
                        productName: popularItem.title ?? "",
                        sellerImage: popularItem.seller?.profileImage ?? "",
                        sellerLocation: popularItem.location?.country ?? "",
                        sellerName: popularItem.seller?.name ?? "",
                      ),
                    );
                  },
                ).paddingOnly(left: 14, right: 14);
              }
            ),
          );
        },
      ),
    );
  }
}

class ListProductView extends StatelessWidget {
  const ListProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: GetBuilder<PopularProductScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          if (controller.isLoading) {
            return ProductListViewShimmer();
          }

          if (controller.popularProductList.isEmpty) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.init(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: MediaQuery.of(context).size.height * 0.7,
                  child: NoDataFound(
                      image: AppAsset.noProductFound,
                      imageHeight: 180,
                      text: EnumLocale.txtNoDataFound.name.tr),
                ),
              ),
            );
          }

          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () => controller.init(),
            child: SingleChildScrollView(
              controller: controller
                  .scrollController, // ✅ scrollController attach કર્યું
              child: Column(
                children: [
                  ListView.builder(
                    physics: const NeverScrollableScrollPhysics(),
                    shrinkWrap: true,
                    padding: EdgeInsets.only(bottom: 80),
                    itemCount: controller.popularProductList.length,
                    itemBuilder: (context, index) {
                      // ✅ show loader at the bottom
                      if (index >= controller.popularProductList.length) {
                        return GetBuilder<PopularProductScreenController>(
                          id: Constant.idPagination,
                          builder: (_) {
                            if (!controller.isPaginationLoading &&
                                !controller.hasMore) {
                              return const SizedBox.shrink();
                            }
                            return Center(
                              child: Padding(
                                padding: const EdgeInsets.all(16.0),
                                child: CircularProgressIndicator(
                                    color: AppColors.appRedColor),
                              ),
                            );
                          },
                        );
                      }
                      final popularItem = controller.popularProductList[index];
                      final isLiked = LikeManager.to.getLikeState(
                          popularItem.id ?? "",
                          fallback: popularItem.isLike);
                      return GestureDetector(
                        onTap: () {
                          Get.toNamed(AppRoutes.productDetailScreen,
                              arguments: {
                                'sellerDetail': true,
                                'relatedProduct': true,
                                'adId': popularItem.id,
                                // 'ad': popularItem,
                              });
                        },
                        child: ProductListViewContainer(
                          isVerify: popularItem.seller?.isVerified??false,
                          isOffer: popularItem.seller?.isFeaturedSeller ??false,
                          isLiked: isLiked, // 👈 direct GetBuilder thi pass
                          onLikeTap: () => controller.toggleLike(
                              index, popularItem.id ?? ""),
                          description: '${popularItem.description}',
                          productImage: '${popularItem.primaryImage}',
                          newPrice:
                              "${Database.settingApiResponseModel?.data?.currency?.symbol} ${popularItem.isAuctionEnabled == true ? popularItem.auctionStartingPrice?.toString() ?? '' : popularItem.price ?? "0"}",
                          productName: "${popularItem.title}",
                          sellerImage: "${popularItem.seller?.profileImage}",
                          sellerLocation: "${popularItem.location?.country}",
                          sellerName: "${popularItem.seller?.name}",
                        )

                            .paddingSymmetric(horizontal: 14),
                      );
                    },
                  ),
                  // ✅ Pagination loader
                  // GetBuilder<PopularProductScreenController>(
                  //   id: Constant.idPagination,
                  //   builder: (controller) => Visibility(
                  //     visible: controller.isPaginationLoading,
                  //     child: CircularProgressIndicator(color: AppColors.appRedColor),
                  //   ).paddingAll(8),
                  // ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
