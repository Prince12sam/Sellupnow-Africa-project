import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/ui/review_screen/shimmer/get_review_shimmer.dart';
import 'package:listify/ui/seller_detail_screen/controller/seller_detail_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';

class SellerDetailScreenAppBar extends StatelessWidget {
  final String? title;
  const SellerDetailScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: const Size.fromHeight(120),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class SellerDetailScreenTopView extends StatelessWidget {
  const SellerDetailScreenTopView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SellerDetailScreenController>(builder: (controller) {
      return Column(
        children: [
          Center(
            child: DottedBorder(
              borderType: BorderType.Circle,
              color: AppColors.black,
              dashPattern: const [3, 2],
              strokeWidth: 1,
              child: Container(
                clipBehavior: Clip.hardEdge,
                height: 116,
                width: 116,
                decoration: const BoxDecoration(shape: BoxShape.circle),
                child: CustomProfileImage(
                  image: controller.image ?? '',
                ),
              ),
            ),
          ).paddingOnly(top: 24, bottom: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                controller.name ?? "",
                style: AppFontStyle.fontStyleW700(fontSize: 18, fontColor: AppColors.black),
              ).paddingOnly(right: 6),
              Image.asset(
                AppAsset.verificationRightIcon,
                height: 20,
                width: 20,
              ),
            ],
          ).paddingOnly(bottom: 9),
          Text(
            "Member Since ${controller.register}",
            style: AppFontStyle.fontStyleW500(fontSize: 15, fontColor: AppColors.searchText),
          ).paddingOnly(bottom: 11),
          GetBuilder<SellerDetailScreenController>(
              id: Constant.review,
            builder: (context) {
              return Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Image.asset(
                    AppAsset.starIcon,
                    height: 20,
                    width: 20,
                  ).paddingOnly(right: 6),
                  Text(
                    "${controller.avgForUI.toStringAsFixed(2) ?? "NA"}",
                    style: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.yellowStarColor),
                  ).paddingOnly(right: 11),
                  Container(
                    height: 20,
                    width: 1.5,
                    color: AppColors.ratingDivider,
                  ),
                  Text(
                    "${controller.reviews.length} Ratings",
                    style: AppFontStyle.fontStyleW500(fontSize: 15, fontColor: AppColors.darkGreyColor),
                  ).paddingOnly(left: 11),
                ],
              ).paddingOnly(bottom: 20);
            }
          ),
          Divider(color: AppColors.lightDividerColor),
        ],
      );
    });
  }
}

/// ---------- TAB 1 : Seller products (sliver layout) ----------
class SellerProductTab extends StatelessWidget {
  const SellerProductTab({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SellerDetailScreenController>(
        id: Constant.idUserAds,
        builder: (controller) {
          return CustomScrollView(
            slivers: [
              SliverToBoxAdapter(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      EnumLocale.txtSellerProduct.name.tr,
                      style: AppFontStyle.fontStyleW700(fontSize: 17, fontColor: AppColors.black),
                    ),
                    GetBuilder<SellerDetailScreenController>(
                        id: Constant.idUserAds,
                        builder: (controller) {
                          return GestureDetector(
                            onTap: () {
                              Utils.showLog("View all tapped");
                              Get.toNamed(AppRoutes.sellerDetailProductAllView, arguments: {
                                'adProduct': controller.userAllAds,
                              });
                            },
                            child: Container(
                              padding: const EdgeInsets.symmetric(vertical: 7, horizontal: 12),
                              decoration: BoxDecoration(
                                color: AppColors.appRedColor,
                                borderRadius: BorderRadius.circular(7),
                              ),
                              child: Text(
                                EnumLocale.txtViewAll.name.tr,
                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.white),
                              ),
                            ),
                          );
                        }),
                  ],
                ).paddingOnly(bottom: 24, top: 20, left: 14, right: 14),
              ),
              GetBuilder<SellerDetailScreenController>(
                id: Constant.idAllAds,
                builder: (controller) {
                  if (controller.isLoading) {
                    return SliverFillRemaining(
                      child: UserProductGridViewShimmer(),
                    );
                  }

                  if (controller.userAllAds.isEmpty) {
                    return SliverFillRemaining(
                        child: Center(
                      child: NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr),
                    ));
                  }
                  const cross = 2;
                  const tileHeight = 245.0;

                  return SliverPadding(
                    padding: const EdgeInsets.symmetric(horizontal: 17),
                    sliver: SliverGrid(
                      gridDelegate:  SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: cross,
                        mainAxisExtent: tileHeight,        // <- key change (no childAspectRatio)
                        crossAxisSpacing: 6,
                        mainAxisSpacing: 6,
                      ),
                      delegate: SliverChildBuilderDelegate(
                        (context, index) {
                          final ad = controller.userAllAds[index];
                          final isLiked = LikeManager.to.getLikeState(
                              ad.id ?? "",
                              fallback: ad.isLike);
                          return GestureDetector(
                            onTap: () {

                              Utils.showLog("ad.id...................${ad.id}");
                             Get.delete<ProductDetailScreenController>();
                              Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                                'sellerDetail': true,
                                'relatedProduct': true,
                                'viewLikeCount': true,
                                // 'ad': ad,
                                'adId': ad.id,
                              })?.then((value) {
                                controller.update([Constant.idAllAds]);
                                controller.fetchUserAds();
                              },);
                            },
                            child: SellerProductGridView(
                              productImage: "${ad.primaryImage}",
                              isLiked:isLiked,
                              onLikeTap: () =>
                                  controller.toggleLike(index, ad.id ?? ""),
                              newPrice:
                                  "${ad.isAuctionEnabled == true ? ad.auctionStartingPrice?.toString() ?? '' : ad.price ?? "0"}",
                              productName: "${ad.title}",
                              sellerImage: "${ad.primaryImage}",
                              sellerLocation: ad.location?.country ?? "",
                              sellerName: ad.seller?.name ?? "",
                            ),
                          );
                        },
                        childCount: controller.userAllAds.length > 4 ? 4 : controller.userAllAds.length,
                      ),
                    ),
                  );
                },
              ),
            ],
          );
        });
  }
}

/// ---------- TAB 2 : Notes / Reviews (sliver layout) ----------
class NotesTab extends StatelessWidget {
  const NotesTab({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SellerDetailScreenController>(builder: (controller) {
      return CustomScrollView(
        slivers: [
          SliverToBoxAdapter(
            child: Container(
              width: Get.width,
              decoration: BoxDecoration(
                color: AppColors.profileItemBgColor,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Column(
                    children: [
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(
                            "${controller.avgForUI.toStringAsFixed(1) ?? "0"}",
                            style: AppFontStyle.fontStyleW700(fontSize: 38, fontColor: AppColors.yellowStarColor),
                          ),
                          Text(" /", style: AppFontStyle.fontStyleW500(fontSize: 18, fontColor: AppColors.searchText)).paddingOnly(bottom: 7),
                          Text("5", style: AppFontStyle.fontStyleW500(fontSize: 18, fontColor: AppColors.searchText)).paddingOnly(bottom: 7),
                        ],
                      ).paddingOnly(bottom: 5),
                      RatingBarIndicator(
                        rating: controller.avgForUI.toDouble() ?? 0,
                        itemBuilder: (context, index) => Icon(
                          Icons.star_rounded,
                          color: AppColors.yellowStarColor,
                        ),
                        itemCount: 5,
                        itemSize: 23,
                        unratedColor: AppColors.ratingContainerColor,
                        direction: Axis.horizontal,
                      ).paddingOnly(bottom: 12),
                      Text(
                        "(${controller.reviews.length} Users)",
                        style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.searchText),
                      ),
                    ],
                  ).paddingOnly(left: 18, bottom: 20, top: 14, right: 18),
                  Container(
                    height: 144,
                    width: 2,
                    color: AppColors.ratingContainerColor,
                  ).paddingOnly(top: 10, bottom: 10, right: 16),
                  Column(
                    children: List.generate(5, (i) {
                      final star = 5 - i; // 5★ → 1★
                      // smoothing ઉપયોગ કરો
                      final percent = controller.smoothedPercentForStar(star, alpha: 1.0).clamp(0.0, 1.0);
                      final count   = controller.countForStar(star);

                      return Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          SizedBox(
                            width: 16,
                            child: Text(
                              "$star",
                              textAlign: TextAlign.center,
                              style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText),
                            ),
                          ),
                          const SizedBox(width: 8),
                          SizedBox(
                            width: 140,
                            child: LinearPercentIndicator(
                              lineHeight: 4.0,
                              percent: percent,                 // ← હવે full fill નહિ થાય
                              backgroundColor: Colors.grey.shade200,
                              progressColor: Colors.orange,
                              barRadius: const Radius.circular(10),
                              padding: EdgeInsets.zero,
                              animation: true,
                              animateFromLastPercent: true,
                            ),
                          ),
                          const SizedBox(width: 8),
                          // optional: count બતાવો
                        ],
                      ).paddingOnly(bottom: 11, top: i == 0 ? 11 : 0);
                    }),
                  ),
                ],
              ),
            ).paddingOnly(top: 16, right: 14, left: 14, bottom: 12),
          ),
          controller.isReview
              ? GetReviewShimmer()
              : GetBuilder<SellerDetailScreenController>(
                  id: Constant.review,
                  builder: (context) {
                    if (controller.reviews.isEmpty) {
                      return SliverToBoxAdapter(
                        child: Center(
                          child: NoDataFound(
                            image: AppAsset.noProductFound,
                            imageHeight: 180,
                            text: EnumLocale.txtNoDataFound.name.tr,
                          ).paddingOnly(top: 60),
                        ),
                      );
                    }

                    return SliverList.separated(
                      itemCount: controller.reviews.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        return SellerRatingItemView(
                          name: controller.reviews[index].reviewer?.name,
                          image: controller.reviews[index].reviewer?.profileImage,
                          rating: controller.reviews[index].rating,
                          review: controller.reviews[index].reviewText,
                          controller: controller,
                          reviewTime: controller.formatReviewTime(
                            controller.reviews[index].reviewedAt.toString(),
                          ),
                        );
                      },
                    );
                  },
                ),
          const SliverToBoxAdapter(child: SizedBox(height: 12)),
        ],
      );
    });
  }
}

class SellerRatingItemView extends StatelessWidget {
  final SellerDetailScreenController controller;
  final String? name;
  final String? image;
  final String? review;
  final String? reviewTime;
  final double? rating;
  const SellerRatingItemView({
    super.key,
    required this.controller,
    this.name,
    this.image,
    this.review,
    this.rating,
    this.reviewTime,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: Get.width,
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: AppColors.black.withValues(alpha: 0.10),
            offset: const Offset(0, 0),
            blurRadius: 8,
            spreadRadius: 0,
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                height: 48,
                width: 48,
                clipBehavior: Clip.hardEdge,
                decoration: const BoxDecoration(
                  color: Colors.white,
                  shape: BoxShape.circle,
                ),
                child: CustomProfileImage(image: image ?? ""),
              ).paddingOnly(left: 16, top: 8, right: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    name ?? "",
                    style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
                  ),
                  Row(
                    children: [
                      RatingBarIndicator(
                        rating: rating!.toDouble(),
                        itemBuilder: (context, index) => Icon(
                          Icons.star_rounded,
                          color: AppColors.yellowStarColor,
                        ),
                        itemCount: 5,
                        itemSize: 23,
                        unratedColor: AppColors.ratingContainerColor,
                        direction: Axis.horizontal,
                      ),
                      Text(
                        (rating ?? 0).toStringAsFixed(1),
                        style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.searchText),
                      ).paddingOnly(left: 8),
                    ],
                  ),
                ],
              ),
              Spacer(),
              Text(
                reviewTime ?? "10:34 AM",
                style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.timeColor),
              ).paddingOnly(right: 13, bottom: 15)
            ],
          ),
          Text(
            review ?? "",
            style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText, height: 1.6),
          ).paddingOnly(right: 14, left: 14, top: 10, bottom: 8),
        ],
      ),
    ).paddingOnly(right: 14, left: 14);
  }
}
