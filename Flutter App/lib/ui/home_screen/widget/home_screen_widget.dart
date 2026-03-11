import 'package:carousel_slider/carousel_slider.dart';
import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/custom/timer_widget/timer_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/home_screen/controller/home_screen_controller.dart';
import 'package:listify/ui/home_screen/shimmer/auction_shimmer.dart';
import 'package:listify/ui/home_screen/shimmer/our_category_shimmer.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/controller/sub_categories_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/controller/gloable_controller.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class TopHomeView extends StatelessWidget {
  const TopHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(MapController());
    Utils.showLog('${controller.latitude}');

    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(18)),
        color: AppColors.white,
        boxShadow: [
          BoxShadow(
            color: AppColors.lightPurple,
            blurRadius: 16,
            offset: Offset(0, 0),
            spreadRadius: 0,
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              DottedBorder(
                borderType: BorderType.Circle,
                color: Colors.black,
                dashPattern: [3, 2],
                strokeWidth: 1,
                child: GestureDetector(
                  onTap: () {
                    Get.toNamed(AppRoutes.profileScreenView);
                  },
                  child: Container(
                    clipBehavior: Clip.hardEdge,
                    height: 50,
                    width: 50,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      shape: BoxShape.circle,
                    ),
                    child: CustomProfileImage(
                      image: Database.getUserProfileResponseModel?.user
                              ?.profileImage ??
                          '',
                    ),
                  ),
                ),
              ).paddingOnly(right: 12),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    maxLines: 1,
                    "Hello, ${Database.getUserProfileResponseModel?.user?.name ?? (Database.loginUserName.isNotEmpty ? Database.loginUserName : 'User')}",
                    overflow: TextOverflow.ellipsis,
                    style: AppFontStyle.fontStyleW700(
                        fontSize: 16, fontColor: AppColors.black),
                  ).paddingOnly(right: 5, bottom: 1),
                  /* GetBuilder<HomeScreenController>(
                    builder: (cnt) {
                      return GestureDetector(
                        onTap: () {

                          Get.toNamed(
                            AppRoutes.locationScreen,
                            arguments: {
                              'homeLocation': cnt.homeLocation,
                            },
                          );
                        },
                        child: Container(
                          color: AppColors.transparent,
                          child: Row(
                            children: [
                              Image.asset(
                                AppAsset.locationIcon,
                                height: 16,
                                width: 16,
                              ).paddingOnly(right: 4),
                              GetBuilder<MapController>(
                                  id: Constant.location,
                                  builder: (controller) {
                                    return controller.isLoading
                                        ?  SizedBox(
                                      width: Get.width * 0.45,
                                      child: Text(
                                         'Getting location...',
                                        style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.black),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    )
                                        : GetBuilder<MapController>(
                                            id: Constant.location,
                                            builder: (controller) {
                                              return controller.isLoading
                                                  ? Shimmer.fromColors(
                                                      baseColor: Color(0xffEBEDF9),
                                                      highlightColor: Color(0xffF3F5FD),
                                                      child: Column(
                                                        crossAxisAlignment: CrossAxisAlignment.start,
                                                        children: [
                                                          Container(
                                                            height: 7,
                                                            width: Get.width * 0.45,
                                                            decoration: BoxDecoration(
                                                              borderRadius: BorderRadius.circular(2),
                                                              color: AppColors.white,
                                                            ),
                                                          ).paddingOnly(bottom: 2),
                                                          Container(
                                                            height: 7,
                                                            width: Get.width * 0.25,
                                                            decoration: BoxDecoration(
                                                              borderRadius: BorderRadius.circular(2),
                                                              color: AppColors.white,
                                                            ),
                                                          ),
                                                        ],
                                                      ),
                                                    )
                                                  : SizedBox(
                                                      width: Get.width * 0.45,
                                                      child: Text(
                                                         (controller.currentAddress.isNotEmpty)
                                                                ? controller.currentAddress
                                                                : 'Getting location...',
                                                        style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.black),
                                                        maxLines: 1,
                                                        overflow: TextOverflow.ellipsis,
                                                      ),
                                                    );
                                            });
                                  }),
                              Icon(
                                Icons.keyboard_arrow_down,
                                size: 20,
                              ),
                            ],
                          ),
                        ),
                      );
                    }
                  )*/

                  GetBuilder<HomeScreenController>(
                    builder: (cnt) {
                      return GestureDetector(
                        onTap: () {
                          Get.toNamed(
                            AppRoutes.locationScreen,
                            arguments: {'homeLocation': cnt.homeLocation},
                          );
                        },
                        child: Container(
                          color: AppColors.transparent,
                          child: Row(
                            children: [
                              Image.asset(
                                AppAsset.locationIcon,
                                height: 16,
                                width: 16,
                              ).paddingOnly(right: 4),
                              GetBuilder<MapController>(
                                id: Constant.location,
                                builder: (controller) {
                                  return Obx(() {
                                    String displayText = "";

                                    // 1) User selected → always show persisted selection
                                    if (Database.hasSelectedLocation.value) {
                                      displayText =
                                          Database.selectedLocationText();
                                    } else {
                                      // 2) Else show GPS address
                                      if (controller.isLoading &&
                                          controller.currentAddress.isEmpty) {
                                        displayText = "Getting location...";
                                      } else {
                                        // here use addressStreet + addressName
                                        if ((controller.addressStreet
                                                    ?.isNotEmpty ??
                                                true) ||
                                            (controller
                                                    .addressName?.isNotEmpty ??
                                                true)) {
                                          displayText =
                                              "${controller.addressStreet}, ${controller.addressName}";
                                        } else if (controller
                                            .currentAddress.isNotEmpty) {
                                          displayText =
                                              controller.currentAddress;
                                        } else {
                                          displayText = "Getting location...";
                                        }
                                      }
                                    }

                                    return SizedBox(
                                      width: Get.width * 0.45,
                                      child: Text(
                                        displayText.isNotEmpty
                                            ? displayText
                                            : "Location not found",
                                        style: AppFontStyle.fontStyleW500(
                                          fontSize: 13,
                                          fontColor: AppColors.black,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    );
                                  });
                                },
                              ),
                              const Icon(Icons.keyboard_arrow_down, size: 20),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ],
              ),
              Spacer(),
              GestureDetector(
                onTap: () {
                  Get.toNamed(AppRoutes.notificationScreenView);
                },
                child: Image.asset(
                  AppAsset.notificationIcon,
                  height: 32,
                  width: 32,
                ),
              ),
            ],
          ).paddingOnly(
              top: Get.height * 0.042, bottom: 20, left: 16, right: 16),
          Row(
            children: [
              Expanded(
                child: Container(
                  height: 48,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(14),
                    color: AppColors.lightPurple,
                  ),
                  child: Padding(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 10), // <-- ADD THIS
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
                            onTap: () {
                              Get.toNamed(AppRoutes.homeScreenProductScreenView,
                                  arguments: {
                                    "search": true,
                                  })?.then(
                                (value) {
                                  FocusScopeNode currentFocus =
                                      FocusScope.of(context);
                                  if (!currentFocus.hasPrimaryFocus &&
                                      currentFocus.focusedChild != null) {
                                    currentFocus.focusedChild?.unfocus();
                                  }
                                },
                              );
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
          ).paddingOnly(left: 16, right: 16, bottom: 16),
        ],
      ),
    );
  }
}

class OurCategoriesView extends StatelessWidget {
  const OurCategoriesView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeScreenController>(
        id: Constant.idAllCategory,
        builder: (controller) {
          return Column(
            children: [
              Row(
                children: [
                  Text(
                    EnumLocale.txtOurCategories.name.tr,
                    style: AppFontStyle.fontStyleW700(
                        fontSize: 17, fontColor: AppColors.black),
                  ),
                  Spacer(),
                  GestureDetector(
                    onTap: () async {
                      Get.toNamed(AppRoutes.categoriesScreen,
                          arguments: {"subcategory": controller.subcategory});
                    },
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(vertical: 7, horizontal: 12),
                      decoration: BoxDecoration(
                        color: AppColors.appRedColor,
                        borderRadius: BorderRadius.circular(7),
                      ),
                      child: Text(
                        EnumLocale.txtViewAll.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 12, fontColor: AppColors.white),
                      ),
                    ),
                  ),
                ],
              ).paddingOnly(left: 14, right: 14, top: 18, bottom: 20),
              SizedBox(
                height: Get.height * 0.30,
                width: Get.width,
                child: controller.isCategory
                    ? OurCategoryShimmer()
                    : GridView.builder(
                        padding: EdgeInsets.zero,
                        scrollDirection: Axis.horizontal,
                        shrinkWrap: true,
                        physics: const AlwaysScrollableScrollPhysics(),
                        itemCount: controller.allCategoryList.length,
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            mainAxisSpacing: 6,
                            childAspectRatio: 1.5),
                        itemBuilder: (context, index) {
                          return GestureDetector(
                            onTap: () async {
                              SubCategoryApi.startPagination = 0;
                              final isEmpty =
                                  await controller.getSubCategoryApi(
                                      controller.allCategoryList[index].id ??
                                          "");

                              if (isEmpty) {
                                Utils.showLog("empty enter.................");

                                Get.toNamed(AppRoutes.subCategoryProductScreen,
                                    arguments: {
                                      'categoryId': controller
                                          .allCategoryResponseModel
                                          ?.data?[index]
                                          .id,
                                      'categoryTitle': controller
                                          .allCategoryResponseModel
                                          ?.data?[index]
                                          .name,
                                      "subcategory": controller.subcategory,
                                    })?.then((value) {
                                  SubCategoryApi.startPagination = 0;
                                });
                              } else {
                                Utils.showLog(
                                    "categoryId enter.................${controller.allCategoryResponseModel?.data?[index].id}");
                                Utils.showLog(
                                    "categoryTitle enter.................${controller.allCategoryResponseModel?.data?[index].name}");

                                Get.toNamed(
                                  AppRoutes.subCategoriesScreen,
                                  arguments: {
                                    "categoryId": controller
                                        .allCategoryResponseModel
                                        ?.data?[index]
                                        .id,
                                    "categoryTitle": controller
                                        .allCategoryResponseModel
                                        ?.data?[index]
                                        .name,
                                    "subcategory": controller.subcategory,
                                  },
                                );
                              }
                            },
                            child: Container(
                              color: Colors.transparent,
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Container(
                                    height: 76,
                                    width: 76,
                                    decoration: BoxDecoration(
                                        color: AppColors.categoriesBgColor,
                                        borderRadius:
                                            BorderRadius.circular(12)),
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(12),
                                      child: CustomImageView(
                                        image:
                                            "${controller.allCategoryList[index].image}",
                                        fit: BoxFit.contain,
                                      ).paddingAll(6),
                                    ),
                                  ).paddingOnly(top: 4),
                                  Expanded(
                                    child: Text(
                                      capitalizeWords(controller
                                              .allCategoryList[index].name ??
                                          ''),
                                      textAlign: TextAlign.center,
                                      style: AppFontStyle.fontStyleW500(
                                          fontSize: 10,
                                          fontColor: AppColors.black,
                                          height: 0),
                                    ).paddingOnly(top: 8),
                                  )
                                ],
                              ),
                            ),
                          );
                        },
                      ),
              ).paddingOnly(left: 14),
            ],
          );
        });
  }
}

class OfferImageView extends StatelessWidget {
  const OfferImageView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeScreenController>(
      id: Constant.idBanner,
      init: HomeScreenController(),
      builder: (controller) {
        return Column(
          children: [
            ///uncomment code
            CarouselSlider(
              options: CarouselOptions(
                height: 160,
                autoPlay: controller.bannerList.length > 1,
                enableInfiniteScroll: controller.bannerList.length > 1,
                enlargeCenterPage: false,
                onPageChanged: controller.onPageChanged,
              ),
              items: controller.bannerList.map((item) {
                return Builder(
                  builder: (BuildContext context) {
                    return Container(
                      width: Get.width,
                      decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(20),
                          color: Color(0xffF3F5FD).withValues(alpha: 0.70)),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(25),
                        child: CustomImageView(
                          image: item.image ?? '',
                          // fit: BoxFit.contain,
                          // padding: EdgeInsets.all(50),
                        ),
                      ),
                    ).paddingOnly(left: 12);
                  },
                );
              }).toList(),
            ).paddingOnly(top: 30, bottom: 20),
          ],
        );
      },
    );
  }
}

class PopularItemsView extends StatelessWidget {
  const PopularItemsView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Row(
          children: [
            Text(
              EnumLocale.txtPopularItems.name.tr,
              style: AppFontStyle.fontStyleW700(
                  fontSize: 17, fontColor: AppColors.black),
            ),
            Spacer(),
            GestureDetector(
              onTap: () {
                Get.toNamed(
                  AppRoutes.popularProductScreen,
                  arguments: {"popular": true},
                );
              },
              child: Container(
                padding: EdgeInsets.symmetric(vertical: 7, horizontal: 12),
                decoration: BoxDecoration(
                  color: AppColors.appRedColor,
                  borderRadius: BorderRadius.circular(7),
                ),
                child: Text(
                  EnumLocale.txtViewAll.name.tr,
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 12, fontColor: AppColors.white),
                ),
              ),
            ),
          ],
        ).paddingOnly(left: 14, right: 14, top: 18, bottom: 20),
        GetBuilder<HomeScreenController>(
            id: Constant.idAllAds,
            builder: (controller) {
              const cross = 2;
              const tileHeight = 255.0;

              return controller.isPopularLoading ?

              ProductGridViewShimmer()
                  : controller.popularProductList.isEmpty
                  ? NoDataFound(
                      image: AppAsset.noProductFound,
                      imageHeight: 180,
                      text: EnumLocale.txtNoDataFound.name.tr)
                  :GridView.builder(
                      padding: EdgeInsets.zero,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: controller.popularProductList.length > 4
                          ? 4
                          : controller.popularProductList.length,
                      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: cross,
                        mainAxisExtent:
                            tileHeight, // <- key change (no childAspectRatio)
                        crossAxisSpacing: 6,
                        mainAxisSpacing: 6,
                      ),
                      itemBuilder: (context, index) {
                        final popularItem =
                            controller.popularProductList[index];
                        final isLiked = LikeManager.to.getLikeState(
                            popularItem.id ?? "",
                            fallback: popularItem.isLike);

                        return GestureDetector(
                          onTap: () {
                            Get.toNamed(AppRoutes.productDetailScreen,
                                arguments: {
                                  'sellerDetail': true,
                                  'relatedProduct': true,
                                  'viewLikeCount': true,
                                  'adId': popularItem.id,
                                });
                          },
                          child: ProductGridView(
                            isVerify: popularItem.seller?.isVerified ?? false,
                            topSeller:
                                popularItem.seller?.isFeaturedSeller ?? false,
                            productImage: '${popularItem.primaryImage}',
                            isLiked: isLiked,
                            onLikeTap: () => controller.toggleLike(
                                index, popularItem.id ?? ""),
                            newPrice:
                                "${Database.settingApiResponseModel?.data?.currency?.symbol} ${popularItem.isAuctionEnabled == true ? popularItem.auctionStartingPrice?.toString() ?? '' : popularItem.price ?? "0"}",
                            productName:
                                capitalizeWords(popularItem.title ?? ""),
                            sellerImage: "${popularItem.seller?.profileImage}",
                            sellerLocation: "${popularItem.location?.country}",
                            sellerName: "${popularItem.seller?.name}",
                          ),
                        );
                      },
                    ).paddingOnly(left: 14, right: 14);
            })
      ],
    );
  }
}

class MostLikedItemsView extends StatelessWidget {
  const MostLikedItemsView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Row(
          children: [
            Text(
              EnumLocale.txtMostLikedItems.name.tr,
              style: AppFontStyle.fontStyleW700(
                  fontSize: 17, fontColor: AppColors.black),
            ),
            Spacer(),
            GestureDetector(
              onTap: () {
                Get.toNamed(
                  AppRoutes.mostLikedViewAllScreen,
                  arguments: {"mostLike": true},
                );
              },
              child: Container(
                padding: EdgeInsets.symmetric(vertical: 7, horizontal: 12),
                decoration: BoxDecoration(
                  color: AppColors.appRedColor,
                  borderRadius: BorderRadius.circular(7),
                ),
                child: Text(
                  EnumLocale.txtViewAll.name.tr,
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 12, fontColor: AppColors.white),
                ),
              ),
            ),
          ],
        ).paddingOnly(left: 14, right: 14, top: 18, bottom: 20),
        GetBuilder<HomeScreenController>(
            id: Constant.idAllAds,
            builder: (controller) {
              const cross = 2;
              const tileHeight = 255.0;
              return controller.favouriteAds.isEmpty
                  ? NoDataFound(
                      image: AppAsset.noProductFound,
                      imageHeight: 180,
                      text: EnumLocale.txtNoDataFound.name.tr)
                  : GridView.builder(
                      padding: EdgeInsets.zero,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: controller.favouriteAds.length > 4
                          ? 4
                          : controller.favouriteAds.length,
                      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: cross,
                        mainAxisExtent:
                            tileHeight, // <- key change (no childAspectRatio)
                        crossAxisSpacing: 6,
                        mainAxisSpacing: 6,
                      ),
                      itemBuilder: (context, index) {
                        final popularItem = controller.favouriteAds[index];

                        final isLiked = LikeManager.to.getLikeState(
                            popularItem.id ?? "",
                            fallback: popularItem.isLike);

                        return GestureDetector(
                          onTap: () {
                            Get.toNamed(AppRoutes.productDetailScreen,
                                arguments: {
                                  'sellerDetail': true,
                                  'relatedProduct': true,
                                  'viewLikeCount': true,
                                  // 'ad': popularItem,
                                  'adId': popularItem.id, // ✅ only ID
                                });
                          },
                          child: ProductGridView(
                            isVerify: popularItem.seller?.isVerified ?? false,
                            topSeller:
                                popularItem.seller?.isFeaturedSeller ?? false,
                            productImage: '${popularItem.primaryImage}',
                            isLiked: isLiked,
                            onLikeTap: () {
                              controller.toggleMostLike(
                                  index, popularItem.id ?? "");
                            },
                            newPrice:
                                "${Database.settingApiResponseModel?.data?.currency?.symbol} ${popularItem.isAuctionEnabled == true ? popularItem.auctionStartingPrice?.toString() ?? '' : popularItem.price ?? "0"}",
                            productName:
                                capitalizeWords(popularItem.title ?? ""),
                            sellerImage: "${popularItem.seller?.profileImage}",
                            sellerLocation: "${popularItem.location?.country}",
                            sellerName: "${popularItem.seller?.name}",
                          ),
                        );
                      },
                    ).paddingOnly(left: 14, right: 14);
            })
      ],
    );
  }
}

class LiveAuctionView extends StatelessWidget {
  const LiveAuctionView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              EnumLocale.txtLiveAuction.name.tr,
              style: AppFontStyle.fontStyleW700(
                  fontSize: 17, fontColor: AppColors.black),
            ),
            GestureDetector(
              onTap: () {
                Get.toNamed(AppRoutes.liveAuctionScreen);
              },
              child: Container(
                padding: EdgeInsets.symmetric(vertical: 7, horizontal: 12),
                decoration: BoxDecoration(
                  color: AppColors.appRedColor,
                  borderRadius: BorderRadius.circular(7),
                ),
                child: Text(
                  EnumLocale.txtViewAll.name.tr,
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 12, fontColor: AppColors.white),
                ),
              ),
            ),
          ],
        ).paddingOnly(left: 14, right: 14, top: 18, bottom: 20),
        GetBuilder<HomeScreenController>(
            id: Constant.idAuction,
            builder: (controller) {
          return SizedBox(
            height: 220,
            child: controller.isAuction
                ? AuctionShimmer()
                :ListView.builder(
                        shrinkWrap: true,
                        scrollDirection: Axis.horizontal,
                        physics: const AlwaysScrollableScrollPhysics(),
                        itemCount:
                            controller.liveAuctionProductList.take(5).length,
                        itemBuilder: (context, index) {
                          final liveAuctionProduct =
                              controller.liveAuctionProductList[index];

                          return GestureDetector(
                            onTap: () {
                              Get.toNamed(AppRoutes.productDetailScreen,
                                  arguments: {
                                    'liveAuctionTime': true,
                                    'sellerDetail': true,
                                    'relatedProduct': true,
                                    'ad': liveAuctionProduct,
                                    'adId': liveAuctionProduct.id,
                                  });
                            },
                            child: Container(
                              decoration: BoxDecoration(
                                color: AppColors.white,
                                border:
                                    Border.all(color: AppColors.borderColor),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SizedBox(
                                    height: 110,
                                    width: 135,
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.vertical(
                                          top: Radius.circular(10)),
                                      child: CustomImageView(
                                        image: controller
                                                .liveAuctionProductList[index]
                                                .primaryImage ??
                                            '',
                                        // fit: BoxFit.cover,
                                      ),
                                    ).paddingAll(1),
                                  ),
                                  SizedBox(
                                    width: 135,
                                    child: Text(
                                      controller.liveAuctionProductList[index]
                                              .title ??
                                          '',
                                      style: AppFontStyle.fontStyleW700(
                                          fontSize: 10,
                                          fontColor: AppColors.black),
                                      overflow: TextOverflow.ellipsis,
                                    ).paddingOnly(
                                        left: 4, right: 4, bottom: 4, top: 4),
                                  ),
                                  SizedBox(
                                    width: 135,
                                    child: Text(
                                      controller.liveAuctionProductList[index]
                                              .subTitle ??
                                          '',
                                      style: AppFontStyle.fontStyleW500(
                                          fontSize: 9,
                                          fontColor: AppColors.grey),
                                      overflow: TextOverflow.ellipsis,
                                    ).paddingOnly(
                                        left: 4, right: 4, bottom: 4.9),
                                  ),Spacer(),
                                  TimerWidget(
                                    padding: EdgeInsets.symmetric(vertical: 6),
                                    width: 135,
                                    endDate:
                                        "${controller.liveAuctionProductList[index].auctionEndDate}",
                                  ),
                                ],
                              ),
                            ).paddingOnly(left: 5, right: 5),
                          );
                        },
                      ),
          ).paddingOnly(left: 6);
        }),
      ],
    );
  }
}
