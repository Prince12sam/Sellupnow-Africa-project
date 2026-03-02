import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/custom/product_view/product_list_view_container.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/sub_category_product_screen/controller/sub_category_product_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_list_view_shimmer.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

class SubCategoriesProductScreenAppBar extends StatelessWidget {
  final String? title;
  const SubCategoriesProductScreenAppBar({super.key, this.title});

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
    return GetBuilder<SubCategoryProductScreenController>(
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
                            if (controller.debounce?.isActive ?? false) controller.debounce!.cancel();
                            controller.debounce = Timer(const Duration(milliseconds: 500), () {
                              final query = value.trim();
                              controller.getCategoryWiseProduct(search: query.isNotEmpty ? query : null);
                            });
                          },
                          controller: controller.searchController, // 🔑 Attach controller
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
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  color: controller.selectedView == ViewType.grid
                      ? AppColors.appRedColor.withValues(alpha: 0.05)
                      : AppColors.lightPurple.withValues(alpha: 0.2),
                  border: Border.all(color: controller.selectedView == ViewType.grid ? AppColors.appRedColor : AppColors.borderColor),
                ),
                child: Image.asset(
                  AppAsset.gridViewIcon,
                  height: 24,
                  width: 24,
                  color: controller.selectedView == ViewType.grid ? AppColors.appRedColor : AppColors.grey.withValues(alpha: 0.6),
                ),
              ),
            ),
            10.width,
            GestureDetector(
              onTap: () => controller.toggleView(ViewType.list),
              child: Container(
                height: 48,
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  color: controller.selectedView == ViewType.list
                      ? AppColors.appRedColor.withValues(alpha: 0.05)
                      : AppColors.lightPurple.withValues(alpha: 0.2),
                  border: Border.all(color: controller.selectedView == ViewType.list ? AppColors.appRedColor : AppColors.borderColor),
                ),
                child: Image.asset(
                  AppAsset.listViewIcon,
                  height: 24,
                  width: 24,
                  color: controller.selectedView == ViewType.list ? AppColors.appRedColor : AppColors.grey.withValues(alpha: 0.6),
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
    return GetBuilder<SubCategoryProductScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {

        const cross = 2;
        const tileHeight = 255.0;
        return Expanded(
          child: RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: controller.onRefresh,
            child: CustomScrollView(
              controller: controller.scrollController,
              physics: const BouncingScrollPhysics(), // 👈 No-data માં પણ pull-to-refresh work
              slivers: [
                // 1) Loading shimmer
                if (controller.isLoading)
                  const SliverToBoxAdapter(
                    child: ProductGridViewShimmer(),
                  )

                // 2) No data
                else if (controller.categoryWiseProductList.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: Center(
                      child: NoDataFound(
                        image: AppAsset.noProductFound,
                        imageHeight: 180,
                        text: EnumLocale.txtNoDataFound.name.tr,
                      ),
                    ),
                  )

                // 3) Grid data
                else
                  SliverPadding(
                    padding: const EdgeInsets.symmetric(horizontal: 14),
                    sliver: SliverGrid(
                      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: cross,
                        mainAxisExtent: tileHeight,        // <- key change (no childAspectRatio)
                        crossAxisSpacing: 6,
                        mainAxisSpacing: 6,
                      ),
                      delegate: SliverChildBuilderDelegate(
                        (context, index) {
                          final product = controller.categoryWiseProductList[index];

                          final isLiked = LikeManager.to.getLikeState(
                              product.id ?? "",
                              fallback: product.isLike);

                          return GestureDetector(
                            onTap: () {
                              Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                                'sellerDetail': true,
                                'relatedProduct': true,
                                'viewLikeCount': true,
                                'adId': product.id,
                              })?.then((value) {
                                controller.update([Constant.idAllAds]);
                              },);
                            },
                            child: ProductGridView(
                              isVerify: product.seller?.isVerified??false,
                              topSeller: product.seller?.isFeaturedSeller??false,
                              isLiked:isLiked,
                              onLikeTap: () =>
                                  controller.toggleLike(index, product.id ?? ""),
                              productImage: product.primaryImage ?? '',
                              newPrice:
                                  "${Database.settingApiResponseModel?.data?.currency?.symbol} ${product.isAuctionEnabled == true ? (product.auctionStartingPrice?.toString() ?? '0') : (product.price ?? '0')}",
                              productName: capitalizeWords(product.title.toString()),
                              sellerImage: product.seller?.profileImage ?? '',
                              sellerLocation: product.location?.country ?? '',
                              sellerName: product.seller?.name ?? '',
                            ),
                          );
                        },
                        childCount: controller.categoryWiseProductList.length,
                      ),
                    ),
                  ),

                // 4) Pagination loader
                GetBuilder<SubCategoryProductScreenController>(
                  id: Constant.idPagination,
                  builder: (c) => SliverToBoxAdapter(
                    child: Visibility(
                      visible: c.isPaginationLoading,
                      child:  Padding(
                        padding: EdgeInsets.all(8),
                        child: Center(child: CircularProgressIndicator(color: AppColors.appRedColor,)),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}

// class ListProductView extends StatelessWidget {
//   const ListProductView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<SubCategoryProductScreenController>(
//       id: Constant.idAllAds,
//       builder: (controller) {
//         if (controller.isLoading) {
//           return const Expanded(child: ProductListViewShimmer());
//         }
//
//         return Expanded(
//           child: RefreshIndicator(
//             color: AppColors.appRedColor,
//             onRefresh: controller.onRefresh,
//             child: SingleChildScrollView(
//               controller: controller.scrollController,
//               physics: const AlwaysScrollableScrollPhysics(), // 👈 ensures pull-to-refresh
//               child: Column(
//                 children: [
//                   ListView.builder(
//                     padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
//                     shrinkWrap: true,
//                     physics: const NeverScrollableScrollPhysics(),
//                     itemCount: controller.categoryWiseProductList.length,
//                     itemBuilder: (context, index) {
//                       final product = controller.categoryWiseProductList[index];
//                       return GestureDetector(
//                         onTap: () {
//                           Get.toNamed(AppRoutes.productDetailScreen, arguments: {
//                             'sellerDetail': true,
//                             'relatedProduct': true,
//                             'adId': product.id,
//                           });
//                         },
//                         child: ProductListViewContainer(
//                           isVerify: product.seller?.isVerified??false,
//                           isOffer: product.seller?.isFeaturedSeller ??false,
//                           isLiked: controller.isAdLiked(product),
//                           onLikeTap: () {
//                             final id = product.id ?? '';
//                             if (id.isNotEmpty) {
//                               controller.toggleLike(index, id);
//                             }
//                           },
//                           description: product.description ?? '',
//                           productImage: product.primaryImage ?? '',
//                           newPrice: "${Database.settingApiResponseModel?.data?.currency?.symbol} ${(product.price ?? '0')}",
//                           productName: capitalizeWords(product.title.toString()),
//                           sellerImage: product.seller?.profileImage ?? '',
//                           sellerLocation: product.location?.country ?? '',
//                           sellerName: product.seller?.name ?? '',
//                         ).paddingOnly(bottom: 10),
//                       );
//                     },
//                   ),
//                   GetBuilder<SubCategoryProductScreenController>(
//                     id: Constant.idPagination,
//                     builder: (controller) => Visibility(
//                       visible: controller.isPaginationLoading,
//                       child:  Padding(
//                         padding: EdgeInsets.all(8),
//                         child: CircularProgressIndicator(color: AppColors.appRedColor,),
//                       ),
//                     ),
//                   ),
//                 ],
//               ),
//             ),
//           ),
//         );
//       },
//     );
//   }
// }


class ListProductView extends StatelessWidget {
  const ListProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SubCategoryProductScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        if (controller.isLoading) {
          return const Expanded(child: ProductListViewShimmer());
        }

        // ✅ If no data, show image + message (with pull-to-refresh)
        if (controller.categoryWiseProductList.isEmpty) {
          return Expanded(
            child: RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: controller.onRefresh,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: MediaQuery.of(context).size.height * 0.78, // ensures pull works
                  child: Center(
                    child: NoDataFound(
                      image: AppAsset.noProductFound,
                      imageHeight: 180,
                      text: EnumLocale.txtNoDataFound.name.tr,
                    ),
                  ),
                ),
              ),
            ),
          );
        }

        // ✅ Main List
        return Expanded(
          child: RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: controller.onRefresh,
            child: SingleChildScrollView(
              controller: controller.scrollController,
              physics: const BouncingScrollPhysics(),
              child: Column(
                children: [
                  ListView.builder(

                    padding:
                    const EdgeInsets.symmetric(horizontal: 14,),
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: controller.categoryWiseProductList.length,
                    itemBuilder: (context, index) {
                      final product = controller.categoryWiseProductList[index];
                      return GestureDetector(
                        onTap: () {
                          Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                            'sellerDetail': true,
                            'relatedProduct': true,
                            'adId': product.id,
                          });
                        },
                        child: ProductListViewContainer(
                          isVerify: product.seller?.isVerified ?? false,
                          isOffer: product.seller?.isFeaturedSeller ?? false,
                          isLiked: controller.isAdLiked(product),
                          onLikeTap: () {
                            final id = product.id ?? '';
                            if (id.isNotEmpty) {
                              controller.toggleLike(index, id);
                            }
                          },
                          description: product.description ?? '',
                          productImage: product.primaryImage ?? '',
                          newPrice:
                          "${Database.settingApiResponseModel?.data?.currency?.symbol} ${(product.price ?? '0')}",
                          productName:
                          capitalizeWords(product.title.toString()),
                          sellerImage: product.seller?.profileImage ?? '',
                          sellerLocation: product.location?.country ?? '',
                          sellerName: product.seller?.name ?? '',
                        ),
                      );
                    },
                  ),
                  GetBuilder<SubCategoryProductScreenController>(
                    id: Constant.idPagination,
                    builder: (controller) => Visibility(
                      visible: controller.isPaginationLoading,
                      child: Padding(
                        padding: const EdgeInsets.all(8),
                        child: CircularProgressIndicator(
                          color: AppColors.appRedColor,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}

