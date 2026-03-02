import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/custom/product_view/product_list_view_container.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/favourrite_screen/controller/favourite_screen_controller.dart';
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

class FavoriteScreenAppBar extends StatelessWidget {
  final String? title;
  const FavoriteScreenAppBar({super.key, this.title});

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

  class FavScreenSearchView extends StatelessWidget {
    const FavScreenSearchView({super.key});

    @override
    Widget build(BuildContext context) {
      return GetBuilder<FavoriteScreenController>(
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
                            controller: controller.searchController, // NEW
                            textInputAction: TextInputAction.search,
                            onSubmitted: (_) => controller.fetchFavouriteAds(isRefresh: true),// optional
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
      child: GetBuilder<FavoriteScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          if (controller.isLoading && controller.favouriteAds.isEmpty) {
            return const ProductGridViewShimmer();
          }

          if (!controller.isLoading && controller.favouriteAds.isEmpty) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.onRefresh(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: Get.height * 0.7,
                  child: Center(
                    child: NoDataFound(
                      image: AppAsset.noProductFound,
                      imageHeight: 180,
                      text: EnumLocale.txtNoDataFound.name.tr,
                    ),
                  ),
                ),
              ),
            );
          }

          const cross = 2;
          const tileHeight = 255.0;

          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () => controller.onRefresh(),
            child: SingleChildScrollView(
              controller: controller.scrollController,
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                children: [
                  GridView.builder(
                    shrinkWrap: true,
                    // controller: controller.scrollController,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.symmetric(horizontal: 14),
                    itemCount: controller.favouriteAds.length ,
                        // +
                        // (controller.isPaginationLoading && controller.hasMoreData ? 1 : 0),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: cross,
                      mainAxisExtent: tileHeight,
                      crossAxisSpacing: 6,
                      mainAxisSpacing: 6,
                    ),
                    itemBuilder: (context, index) {
                      // // Loading indicator item
                      // if (index >= controller.favouriteAds.length) {
                      //   return Container(
                      //     height: tileHeight,
                      //     alignment: Alignment.center,
                      //     child: CircularProgressIndicator(
                      //       color: AppColors.appRedColor,
                      //     ),
                      //   );
                      // }

                      final item = controller.favouriteAds[index];
                      final isLiked = LikeManager.to.getLikeState(
                        item.id ?? "",
                        fallback: item.isLike,
                      );

                      return GestureDetector(
                        onTap: () {
                          Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                            'sellerDetail': true,
                            'relatedProduct': true,
                            'viewLikeCount': true,
                            'adId': item.id,
                          })?.then((value) {
                            controller.fetchFavouriteAds(isRefresh: true);
                          });
                        },
                        child: ProductGridView(
                          isVerify: item.seller?.isVerified ?? false,
                          topSeller: item.seller?.isFeaturedSeller ?? false,
                          productImage: item.primaryImage ?? "",
                          isLiked: isLiked,
                          onLikeTap: () =>
                              controller.toggleLike(index, item.id ?? ""),
                          newPrice:
                          "${Database.settingApiResponseModel?.data?.currency?.symbol} ${item.isAuctionEnabled == true ? item.auctionStartingPrice?.toString() ?? '' : item.price ?? "0"}",
                          productName: item.title ?? "",
                          sellerImage: item.seller?.profileImage ?? "",
                          sellerLocation: item.location?.country ?? "",
                          sellerName: item.seller?.name ?? "",
                        ),
                      );
                    },
                  ),

                  GetBuilder<FavoriteScreenController>(
                    id: Constant.favPagination,
                    builder: (controller) => Visibility(
                      visible: controller.isPaginationLoading,
                      child: CircularProgressIndicator(color: AppColors.appRedColor),
                    ),
                  ),
                ],
              ),
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
      child: GetBuilder<FavoriteScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          if (controller.isLoading && controller.favouriteAds.isEmpty) {
            return const ProductListViewShimmer();
          }

          if (!controller.isLoading && controller.favouriteAds.isEmpty) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.onRefresh(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: Get.height * 0.7,
                  child: Center(
                    child: NoDataFound(
                        image: AppAsset.noProductFound,
                        imageHeight: 180,
                        text: EnumLocale.txtNoDataFound.name.tr),
                  ),
                ),
              ),
            );
          }

          final itemCount = controller.favouriteAds.length +
              (controller.isPaginationLoading ? 1 : 0);

          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () => controller.onRefresh(),
            child: SingleChildScrollView(
              controller: controller.scrollController,
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                children: [
                  ListView.builder(
                    shrinkWrap: true,
                    // controller: controller.scrollController,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.only(bottom: 30),
                    itemCount: controller.favouriteAds.length,
                    itemBuilder: (context, index) {
                      // final isLoaderRow = index >= controller.favouriteAds.length;
                      // if (isLoaderRow) {
                      //   return GetBuilder<FavoriteScreenController>(
                      //     id: Constant.idPagination,
                      //     builder: (_) {
                      //       if (!controller.isPaginationLoading &&
                      //           !controller.hasMoreData) {
                      //         return const SizedBox.shrink();
                      //       }
                      //       return Center(
                      //         child: Column(
                      //           mainAxisSize: MainAxisSize.min,
                      //           crossAxisAlignment: CrossAxisAlignment.center,
                      //           mainAxisAlignment: MainAxisAlignment.center,
                      //           children: [
                      //             Center(
                      //                 child: CircularProgressIndicator(
                      //                     color: AppColors.appRedColor)),
                      //           ],
                      //         ),
                      //       );
                      //     },
                      //   );
                      // }

                      final favouriteItem = controller.favouriteAds[index];
                      return GestureDetector(
                        onTap: () {
                          Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                            'sellerDetail': true,
                            'relatedProduct': true,
                            'adId': favouriteItem.id,
                            // 'ad': favouriteItem,
                          });
                        },
                        child: ProductListViewContainer(
                          isVerify: favouriteItem.seller?.isVerified??false,
                          isOffer: favouriteItem.seller?.isFeaturedSeller ?? false,
                          description: favouriteItem.description ?? "",
                          productImage: favouriteItem.primaryImage ?? "",
                          isLiked: controller.isAdLiked(favouriteItem),
                          onLikeTap: () =>
                              controller.toggleLike(index, favouriteItem.id ?? ''),
                          newPrice: "${favouriteItem.price ?? ""}",
                          productName: favouriteItem.title ?? "",
                          sellerImage: favouriteItem.seller?.profileImage ?? "",
                          sellerLocation: favouriteItem.location?.country ?? "",
                          sellerName: favouriteItem.seller?.name ?? "",
                        ).paddingSymmetric(horizontal: 14),
                      );
                    },
                  ),
                  GetBuilder<FavoriteScreenController>(
                    id: Constant.favPagination,
                    builder: (controller) => Visibility(
                      visible: controller.isPaginationLoading,
                      child: CircularProgressIndicator(color: AppColors.appRedColor),
                    ),
                  ),

                ],
              ),
            ),
          );
        },
      ),
    );
  }
}


