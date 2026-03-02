import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/custom/product_view/product_list_view_container.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/most_liked_view_all/controller/most_liked_view_all_controller.dart';
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

class MostLikedViewAllAppBar extends StatelessWidget {
  final String? title;
  const MostLikedViewAllAppBar({super.key, this.title});

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

class MostLikedViewAllSearchView extends StatelessWidget {
  const MostLikedViewAllSearchView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MostLikedViewAllController>(
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
                          controller: controller.searchController,
                          onChanged: (value) {
                            if (controller.debounce?.isActive ?? false) controller.debounce!.cancel();
                            controller.debounce = Timer(const Duration(milliseconds: 500), () {
                              final query = value.trim();
                              controller.getMostLikeProduct(search: query.isNotEmpty ? query : null,isRefresh: true);
                            });
                          },
                          decoration: InputDecoration(
                            hintText: EnumLocale.txtSearchHere.name.tr,
                            hintStyle: AppFontStyle.fontStyleW400(
                              fontSize: 16,
                              fontColor: AppColors.searchText,
                            ),
                            border: InputBorder.none,
                          ),
                          // onSubmitted: (_) {
                          //   // Optional: apply immediately (already debounced via listener)
                          //   controller
                          //       .applyClientFilter(); // if _applyClientFilter is private, you can call fetchFavouriteAds(isRefresh:false) to trigger update(), or expose a public method.
                          // },
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

// class GridProductView extends StatelessWidget {
//   const GridProductView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return Expanded(
//       child: GetBuilder<MostLikedViewAllController>(
//         id: Constant.idAllAds,
//         builder: (controller) {
//           if (controller.isLoading && controller.favouriteAds.isEmpty) {
//             return const ProductGridViewShimmer();
//           }
//
//           if (!controller.isLoading && controller.favouriteAds.isEmpty) {
//             return RefreshIndicator(
//               color: AppColors.appRedColor,
//               onRefresh: () => controller.init(),
//               child: SingleChildScrollView(
//                 physics: const AlwaysScrollableScrollPhysics(),
//                 child: SizedBox(
//                   height: MediaQuery.of(context).size.height * 0.7,
//                   child: NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr),
//                 ),
//               ),
//             );
//           }
//
//
//
//           return RefreshIndicator(
//             color: AppColors.appRedColor,
//             onRefresh: () => controller.init(),
//             child: GetBuilder<MostLikedViewAllController>(
//                 id: Constant.idAllAds,
//               builder: (context) {
//                 const cross = 2;
//                 const tileHeight = 255.0;
//                 return SingleChildScrollView(
//                         controller: controller.scrollController,
//                         physics: const AlwaysScrollableScrollPhysics(),
//                   child: Column(
//                     children: [
//                       GridView.builder(
//                         shrinkWrap: true,
//                         // controller: controller.scrollController,
//                         padding: EdgeInsets.zero,
//                         physics: const NeverScrollableScrollPhysics(),
//                         itemCount: controller.favouriteAds.length /*+ (controller.isPaginationLoading || controller.hasMoreData ? 1 : 0)*/, // ✅ add loader item
//                         gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
//                           crossAxisCount: cross,
//                           mainAxisExtent: tileHeight,        // <- key change (no childAspectRatio)
//                           crossAxisSpacing: 6,
//                           mainAxisSpacing: 6,
//                         ),
//                         itemBuilder: (context, index) {
//                           // // ✅ show loader at the bottom
//                           // if (index >= controller.favouriteAds.length) {
//                           //   return GetBuilder<MostLikedViewAllController>(
//                           //     id: Constant.idPagination,
//                           //     builder: (_) {
//                           //       if (!controller.isPaginationLoading || !controller.hasMoreData) {
//                           //         return const SizedBox.shrink();
//                           //       }
//                           //       return Center(
//                           //         child: Container(
//                           //           child: Padding(
//                           //             padding: const EdgeInsets.all(16.0),
//                           //             // padding: const EdgeInsets.all(16.0),
//                           //             child: CircularProgressIndicator(color: AppColors.appRedColor),
//                           //           ),
//                           //         ),
//                           //       );
//                           //     },
//                           //   );
//                           // }
//
//                           final favouriteItem = controller.favouriteAds[index];
//                           final isLiked = LikeManager.to.getLikeState(favouriteItem.id ?? "", fallback: favouriteItem.isLike);
//                           return GestureDetector(
//                             onTap: () {
//                               Get.toNamed(AppRoutes.productDetailScreen, arguments: {
//                                 'sellerDetail': true,
//                                 'relatedProduct': true,
//                                 'viewLikeCount': true,
//                                 'adId': favouriteItem.id,
//                               })?.then((value) {
//                                 controller.update([Constant.idAllAds]);
//                               },);
//                             },
//                             child: ProductGridView(
//                               isVerify: favouriteItem.seller?.isVerified??false,
//                               topSeller: favouriteItem.seller?.isFeaturedSeller??false,
//                               // description: favouriteItem.description ?? "",
//                               productImage: favouriteItem.primaryImage ?? "",
//                               isLiked: isLiked, // 👈 direct GetBuilder thi pass
//                               onLikeTap: () => controller.toggleLike(index, favouriteItem.id ?? ""),
//                               newPrice:
//                                   "${Database.settingApiResponseModel?.data?.currency?.symbol} ${favouriteItem.isAuctionEnabled == true ? favouriteItem.auctionStartingPrice?.toString() ?? '' : favouriteItem.price ?? "0"}",
//                               productName: favouriteItem.title ?? "",
//                               sellerImage: favouriteItem.seller?.profileImage ?? "",
//                               sellerLocation: favouriteItem.location?.country ?? "",
//                               sellerName: favouriteItem.seller?.name ?? "",
//                             ),
//                           );
//                         },
//                       ).paddingOnly(left: 14, right: 14),
//                       GetBuilder<MostLikedViewAllController>(
//                         id: Constant.favPagination,
//                         builder: (controller) => Visibility(
//                           visible: controller.isPaginationLoading,
//                           child: CircularProgressIndicator(color: AppColors.appRedColor),
//                         ),
//                       ),
//
//                     ],
//                   ),
//                 );
//               }
//             ),
//           );
//         },
//       ),
//     );
//   }
// }



class GridProductView extends StatelessWidget {
  const GridProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: GetBuilder<MostLikedViewAllController>(
        id: Constant.idAllAds,
        builder: (controller) {
          if (controller.isLoading && controller.favouriteAds.isEmpty) {
            return const ProductGridViewShimmer();
          }

          if (!controller.isLoading && controller.favouriteAds.isEmpty) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () async => controller.init(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: MediaQuery.of(context).size.height * 0.7,
                  child: NoDataFound(
                    image: AppAsset.noProductFound,
                    imageHeight: 180,
                    text: EnumLocale.txtNoDataFound.name.tr,
                  ),
                ),
              ),
            );
          }

          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () async => controller.init(),
            child: SingleChildScrollView(
              controller: controller.scrollController,
              physics: const AlwaysScrollableScrollPhysics(),
              child: GetBuilder<MostLikedViewAllController>(
                id: Constant.idAllAds, // same id ok
                builder: (_) {
                  const cross = 2;
                  const tileHeight = 255.0;

                  // 👇 Show loader as last grid item
                  final showLoaderItem = controller.isPaginationLoading;
                  final itemCount = controller.favouriteAds.length + (showLoaderItem ? 1 : 0);

                  return GridView.builder(
                    shrinkWrap: true,
                    padding: EdgeInsets.only(bottom: 70),
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: itemCount,
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: cross,
                      mainAxisExtent: tileHeight,
                      crossAxisSpacing: 6,
                      mainAxisSpacing: 6,
                    ),
                    itemBuilder: (context, index) {
                      // 🔄 loader cell
                      if (index >= controller.favouriteAds.length) {
                        return Center(
                          child: Padding(
                            padding: const EdgeInsets.all(12),
                            child: CircularProgressIndicator(color: AppColors.appRedColor),
                          ),
                        );
                      }

                      final favouriteItem = controller.favouriteAds[index];
                      final isLiked = LikeManager.to.getLikeState(
                        favouriteItem.id ?? "",
                        fallback: favouriteItem.isLike,
                      );

                      return GestureDetector(
                        onTap: () {
                          Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                            'sellerDetail': true,
                            'relatedProduct': true,
                            'viewLikeCount': true,
                            'adId': favouriteItem.id,
                          })?.then((_) => controller.update([Constant.idAllAds]));
                        },
                        child: ProductGridView(
                          isVerify: favouriteItem.seller?.isVerified ?? false,
                          topSeller: favouriteItem.seller?.isFeaturedSeller ?? false,
                          productImage: favouriteItem.primaryImage ?? "",
                          isLiked: isLiked,
                          onLikeTap: () => controller.toggleLike(index, favouriteItem.id ?? ""),
                          newPrice:
                          "${Database.settingApiResponseModel?.data?.currency?.symbol} ${favouriteItem.isAuctionEnabled == true ? favouriteItem.auctionStartingPrice?.toString() ?? '' : favouriteItem.price ?? "0"}",
                          productName: favouriteItem.title ?? "",
                          sellerImage: favouriteItem.seller?.profileImage ?? "",
                          sellerLocation: favouriteItem.location?.country ?? "",
                          sellerName: favouriteItem.seller?.name ?? "",
                        ),
                      );
                    },
                  ).paddingOnly(left: 14, right: 14);
                },
              ),
            ),
          );
        },
      ),
    );
  }
}

// class ListProductView extends StatelessWidget {
//   const ListProductView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return Expanded(
//       child: GetBuilder<MostLikedViewAllController>(
//         id: Constant.idAllAds,
//         builder: (controller) {
//           if (controller.isLoading && controller.favouriteAds.isEmpty) {
//             return const ProductListViewShimmer();
//           }
//
//           if (!controller.isLoading && controller.favouriteAds.isEmpty) {
//             return RefreshIndicator(
//               color: AppColors.appRedColor,
//               onRefresh: () => controller.init(),
//               child: SingleChildScrollView(
//                 physics: const AlwaysScrollableScrollPhysics(),
//                 child: SizedBox(
//                   height: MediaQuery.of(context).size.height * 0.7,
//                   child: NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr),
//                 ),
//               ),
//             );
//           }
//
//           return RefreshIndicator(
//             color: AppColors.appRedColor,
//             onRefresh: () => controller.init(),
//             child: SingleChildScrollView(
//
//                     physics: const AlwaysScrollableScrollPhysics(),
//                     controller: controller.scrollController,
//               child: Column(
//                 children: [
//                   ListView.builder(
//                     shrinkWrap: true,
//                     padding: const EdgeInsets.only(bottom: 30),
//                     itemCount: controller.favouriteAds.length /*+ (controller.isPaginationLoading || controller.hasMoreData ? 1 : 0)*/,
//                   physics: NeverScrollableScrollPhysics(),
//                     itemBuilder: (context, index) {
//                       // ✅ show loader at the bottom
//                       // if (index >= controller.favouriteAds.length) {
//                       //   return GetBuilder<MostLikedViewAllController>(
//                       //     id: Constant.idPagination,
//                       //     builder: (_) {
//                       //       if (!controller.isPaginationLoading && !controller.hasMoreData) {
//                       //         return const SizedBox.shrink();
//                       //       }
//                       //       return Center(
//                       //         child: Padding(
//                       //           padding: const EdgeInsets.all(16.0),
//                       //           child: CircularProgressIndicator(color: AppColors.appRedColor),
//                       //         ),
//                       //       );
//                       //     },
//                       //   );
//                       // }
//
//                       final favouriteItem = controller.favouriteAds[index];
//
//                       return GestureDetector(
//                         onTap: () {
//                           Get.toNamed(AppRoutes.productDetailScreen, arguments: {
//                             'sellerDetail': true,
//                             'relatedProduct': true,
//                             'viewLikeCount': true,
//                             'adId': favouriteItem.id,
//                           });
//                         },
//                         child: ProductListViewContainer(
//                           isVerify: favouriteItem.seller?.isVerified??false,
//                           isOffer: favouriteItem.seller?.isFeaturedSeller ??false,
//                           productImage: favouriteItem.primaryImage ?? "",
//                           isLiked: controller.isAdLiked(favouriteItem),
//                           onLikeTap: () {
//                             controller.toggleLike(index, favouriteItem.id ?? '');
//                           },
//                           newPrice:
//                               "${Database.settingApiResponseModel?.data?.currency?.symbol} ${favouriteItem.isAuctionEnabled == true ? favouriteItem.auctionStartingPrice?.toString() ?? '' : favouriteItem.price ?? "0"}",
//                           productName: favouriteItem.title ?? "",
//                           sellerImage: favouriteItem.seller?.profileImage ?? "",
//                           sellerLocation: favouriteItem.location?.country ?? "",
//                           sellerName: favouriteItem.seller?.name ?? "",
//                           description: favouriteItem.description ?? "",
//                         ),
//                       );
//                     },
//                   ).paddingOnly(left: 14, right: 14),
//
//
//                   GetBuilder<MostLikedViewAllController>(
//                     id: Constant.favPagination,
//                     builder: (controller) => Visibility(
//                       visible: controller.isPaginationLoading,
//                       child: CircularProgressIndicator(color: AppColors.appRedColor),
//                     ),
//                   ),
//                 ],
//               ),
//             ),
//           );
//         },
//       ),
//     );
//   }
// }
class ListProductView extends StatelessWidget {
  const ListProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: GetBuilder<MostLikedViewAllController>(
        id: Constant.idAllAds,
        builder: (controller) {
          if (controller.isLoading && controller.favouriteAds.isEmpty) {
            return const ProductListViewShimmer();
          }

          if (!controller.isLoading && controller.favouriteAds.isEmpty) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () async => controller.init(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: SizedBox(
                  height: MediaQuery.of(context).size.height * 0.7,
                  child: NoDataFound(
                    image: AppAsset.noProductFound,
                    imageHeight: 180,
                    text: EnumLocale.txtNoDataFound.name.tr,
                  ),
                ),
              ),
            );
          }

          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () async => controller.init(),
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              controller: controller.scrollController,
              child: Column(
                children: [
                  GetBuilder<MostLikedViewAllController>(
                    id: Constant.idAllAds,
                    builder: (_) {
                      final showLoaderItem = controller.isPaginationLoading || controller.hasMoreData;
                      final itemCount = controller.favouriteAds.length + (showLoaderItem ? 1 : 0);

                      return ListView.builder(
                        shrinkWrap: true,
                        padding: const EdgeInsets.only(left: 14, right: 14, bottom: 70),
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: itemCount,
                        itemBuilder: (context, index) {
                          // 🔄 loader row
                          if (index >= controller.favouriteAds.length) {
                            return Center(
                              child: Padding(
                                padding: const EdgeInsets.all(16.0),
                                child: CircularProgressIndicator(color: AppColors.appRedColor),
                              ),
                            );
                          }

                          final favouriteItem = controller.favouriteAds[index];

                          return GestureDetector(
                            onTap: () {
                              Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                                'sellerDetail': true,
                                'relatedProduct': true,
                                'viewLikeCount': true,
                                'adId': favouriteItem.id,
                              });
                            },
                            child: ProductListViewContainer(
                              isVerify: favouriteItem.seller?.isVerified ?? false,
                              isOffer: favouriteItem.seller?.isFeaturedSeller ?? false,
                              productImage: favouriteItem.primaryImage ?? "",
                              isLiked: controller.isAdLiked(favouriteItem),
                              onLikeTap: () => controller.toggleLike(index, favouriteItem.id ?? ''),
                              newPrice:
                              "${Database.settingApiResponseModel?.data?.currency?.symbol} ${favouriteItem.isAuctionEnabled == true ? favouriteItem.auctionStartingPrice?.toString() ?? '' : favouriteItem.price ?? "0"}",
                              productName: favouriteItem.title ?? "",
                              sellerImage: favouriteItem.seller?.profileImage ?? "",
                              sellerLocation: favouriteItem.location?.country ?? "",
                              sellerName: favouriteItem.seller?.name ?? "",
                              description: favouriteItem.description ?? "",
                            ),
                          );
                        },
                      );
                    },
                  ),

                  const SizedBox(height: 12),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
