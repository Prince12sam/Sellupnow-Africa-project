import 'dart:developer';
import 'package:dotted_border/dotted_border.dart';
import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/review_screen/controller/review_screen_controller.dart';
import 'package:listify/ui/review_screen/shimmer/get_review_shimmer.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';

class ReviewScreenAppbar extends StatelessWidget
    implements PreferredSizeWidget {
  final String? title;
  const ReviewScreenAppbar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return CustomAppBar(
      title: title,
      showLeadingIcon: true,
    );
  }

  @override
  Size get preferredSize => const Size.fromHeight(55);
}

class ReviewScreenTopView extends StatelessWidget {
  const ReviewScreenTopView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ReviewScreenController>(builder: (controller) {
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
                  image: Database
                          .getUserProfileResponseModel?.user?.profileImage ??
                      '',
                ),
              ),
            ),
          ).paddingOnly(top: 24, bottom: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                Database.getUserProfileResponseModel?.user?.name ?? Database.loginUserName,
                style: AppFontStyle.fontStyleW700(
                    fontSize: 18, fontColor: AppColors.black),
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
            style: AppFontStyle.fontStyleW500(
                fontSize: 15, fontColor: AppColors.searchText),
          ).paddingOnly(bottom: 11),
          GetBuilder<ReviewScreenController>(
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
                    "${controller.avgForUI.toStringAsFixed(2)}",
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 17, fontColor: AppColors.yellowStarColor),
                  ).paddingOnly(right: 11),
                  Container(
                    height: 20,
                    width: 1.5,
                    color: AppColors.ratingDivider,
                  ),
                  Text(
                    "${controller.reviews.length} Ratings",
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 15, fontColor: AppColors.darkGreyColor),
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

/// TabBarView My rating View

class MyRatingTabView extends StatelessWidget {
  const MyRatingTabView({super.key});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      primary: false,
      physics: const NeverScrollableScrollPhysics(),
      child: GetBuilder<ReviewScreenController>(
        id: Constant.review,
        builder: (controller) {
          return Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
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
                              "${controller.avgForUI.toStringAsFixed(2)}",
                              style: AppFontStyle.fontStyleW700(
                                  fontSize: 38,
                                  fontColor: AppColors.yellowStarColor),
                            ),
                            Text(
                              " /",
                              style: AppFontStyle.fontStyleW500(
                                  fontSize: 18,
                                  fontColor: AppColors.searchText),
                            ).paddingOnly(bottom: 7),
                            Text(
                              "5",
                              style: AppFontStyle.fontStyleW500(
                                  fontSize: 18,
                                  fontColor: AppColors.searchText),
                            ).paddingOnly(bottom: 7),
                          ],
                        ).paddingOnly(bottom: 5),
                        // RatingBar.builder(
                        //   itemPadding: const EdgeInsets.only(),
                        //   ignoreGestures: false,
                        //   glow: false,
                        //   unratedColor: AppColors.ratingContainerColor,
                        //   itemSize: 23,
                        //   initialRating: 0.0,
                        //   minRating: 0,
                        //   maxRating: 5,
                        //   glowRadius: 10,
                        //   direction: Axis.horizontal,
                        //   allowHalfRating: false,
                        //   itemCount: 5,
                        //   itemBuilder: (context, _) => Icon(
                        //     Icons.star_rounded,
                        //     size: 50,
                        //     color: AppColors.yellowStarColor,
                        //   ),
                        //   onRatingUpdate: (rating) {
                        //     controller.rating.value = rating.toDouble();
                        //     log("Selected Rating :: ${controller.rating.value}");
                        //   },
                        // ).paddingOnly(bottom: 12),
                        RatingBarIndicator(
                          rating: controller.avgForUI.toDouble()
                              .toDouble(),
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
                          style: AppFontStyle.fontStyleW500(
                              fontSize: 13, fontColor: AppColors.searchText),
                        ),
                      ],
                    ).paddingOnly(left: 18, bottom: 20, top: 14, right: 18),
                    Container(
                      height: 144,
                      width: 2,
                      color: AppColors.ratingContainerColor,
                    ).paddingOnly(top: 10, bottom: 10, right: 16),
                    // Column(
                    //   children: List.generate(
                    //     5,
                    //     (index) {
                    //       return Row(
                    //         mainAxisSize: MainAxisSize.min,
                    //         children: [
                    //           Text("${index.bitLength}"),
                    //           SizedBox(
                    //             width: 180,
                    //             child: LinearPercentIndicator(
                    //               lineHeight: 4.0,
                    //               percent: 0.9, // demo
                    //               backgroundColor: Colors.grey.shade200,
                    //               progressColor: Colors.orange,
                    //               barRadius: const Radius.circular(10),
                    //             ),
                    //           ),
                    //         ],
                    //       ).paddingOnly(bottom: 11, top: index == 0 ? 11 : 0);
                    //     },
                    //   ),
                    // )

                    // replace the Column(List.generate(...)) with this:
                    // Right side bars: 5★ → 1★
                    // Right side bars: 5★ → 1★ (use buckets)
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
              controller.isReview
                  ? GetReviewShimmer()
                  : controller.reviews.isEmpty
                      ? SizedBox(
                          height: Get.height * 0.5,
                          child: Center(
                            child: NoDataFound(
                                image: AppAsset.noProductFound,
                                imageHeight: 160,
                                text: EnumLocale.txtNoReviews.name.tr),
                          ),
                        )
                      : ListView.builder(
                          itemCount: controller.reviews.length,
                          shrinkWrap: true,
                          primary: false,
                          physics: const NeverScrollableScrollPhysics(),
                          itemBuilder: (context, index) {
                            // if (controller.isReview) {
                            //   return const Center(
                            //     child: Padding(
                            //       padding: EdgeInsets.all(20),
                            //       child: CircularProgressIndicator(),
                            //     ),
                            //   );
                            // }
                            return MyRatingItemView(
                              name: controller.reviews[index].reviewer?.name,
                              image: controller
                                  .reviews[index].reviewer?.profileImage,
                              rating:
                                  controller.reviews[index].rating?.toDouble(),
                              review: controller.reviews[index].reviewText,
                              controller: controller,
                              reviewTime: controller.formatReviewTime(controller
                                  .reviews[index].reviewedAt
                                  .toString()),
                            ).paddingOnly(bottom: 12);
                          },
                        )
            ],
          );
        },
      ),
    );
  }
}

/// TabBarView Live Ads

// class MyProductView extends StatelessWidget {
//   const MyProductView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<ReviewScreenController>(
//         id: Constant.idUserAds,
//         builder: (controller) {
//           if (controller.isLoading) {
//             return UserProductGridViewShimmer();
//           }
//
//           if (controller.userAllAds.isEmpty) {
//             return const Center(child: Text("No Ads Found"));
//           }
//           return SingleChildScrollView(
//             primary: false,
//             physics: const NeverScrollableScrollPhysics(),
//             child: GridView.builder(
//               shrinkWrap: true,
//               itemCount: controller.userAllAds.length,
//               gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
//                 crossAxisCount: 2,
//                 crossAxisSpacing: 10,
//                 mainAxisSpacing: 10,
//                 childAspectRatio: 0.73,
//               ),
//               itemBuilder: (context, index) {
//                 return GestureDetector(
//                   onTap: () {
//                     Get.toNamed(AppRoutes.productDetailScreen, arguments: {
//                       'sellerDetail': true,
//                       'relatedProduct': true,
//                       'viewLikeCount': true,
//                       'ad': controller.userAllAds[index],
//                       'adId': controller.userAllAds[index].id,
//                     });
//                   },
//                   child: SellerProductGridView(
//                     productImage: "${controller.userAllAds[index].primaryImage}",
//                     isLiked: controller.isAdLiked(controller.userAllAds[index]),
//                     onLikeTap: () {
//                       controller.toggleLike(index, controller.userAllAds[index].id ?? '');
//                     },
//                     newPrice: "${controller.userAllAds[index].price}",
//                     // oldPrice: "${controller.userAllAds[index].price}",
//                     productName: "${controller.userAllAds[index].title}",
//                     sellerImage: "${controller.userAllAds[index].primaryImage}",
//                     sellerLocation: "${controller.userAllAds[index].location?.country}",
//                     sellerName: "${controller.userAllAds[index].seller?.name}",
//                   ),
//                 );
//               },
//             ).paddingOnly(top: 16, right: 16, left: 16),
//           );
//         });
//   }
// }

class MyProductView extends StatelessWidget {
  const MyProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ReviewScreenController>(
      id: Constant.idUserAds,
      builder: (controller) {
        if (controller.isLoading) {
          return const UserProductGridViewShimmer();
        }

        if (controller.userAllAds.isEmpty) {
          return SizedBox(
            height: Get.height * 0.7,
            child: Center(
              child: NoDataFound(
                  image: AppAsset.noProductFound,
                  imageHeight: 160,
                  text: EnumLocale.txtNoDataFound.name.tr),
            ),
          );
        }
        const cross = 2;
        const tileHeight = 245.0;
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: () async {
            await controller.refreshUserAds();
          },
          child: GridView.builder(
            padding:
                const EdgeInsets.only(top: 16, right: 16, left: 16, bottom: 16),
            itemCount: controller.userAllAds.length,
            physics:
                const AlwaysScrollableScrollPhysics(), // pull-to-refresh works even with short lists
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: cross,
              mainAxisExtent: tileHeight,        // <- key change (no childAspectRatio)
              crossAxisSpacing: 6,
              mainAxisSpacing: 6,
            ),
            itemBuilder: (context, index) {
              final ad = controller.userAllAds[index];
              final isLiked =
                  LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
              return GestureDetector(
                onTap: () {
                  Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                    'sellerDetail': true,
                    'relatedProduct': true,
                    'viewLikeCount': true,
                    // 'ad': ad,
                    'adId': ad.id,
                  })?.then(
                    (value) {
                      controller.update([Constant.idAllAds]);
                      controller.fetchUserAds();
                    },
                  );
                },
                child: SellerProductGridView(
                  productImage: "${ad.primaryImage}",
                  isLiked: isLiked,
                  onLikeTap: () => controller.toggleMostLike(index, ad.id ?? ""),
                  newPrice:
                      "${ad.isAuctionEnabled == true ? ad.auctionStartingPrice?.toString() ?? '' : ad.price ?? "0"}",
                  productName: "${ad.title}",
                  sellerImage: "${ad.primaryImage}",
                  sellerLocation: "${ad.location?.country}",
                  sellerName: "${ad.seller?.name}",
                ),
              );
            },
          ),
        );
      },
    );
  }
}

class MyRatingItemView extends StatelessWidget {
  final ReviewScreenController controller;
  final String? name;
  final String? image;
  final String? review;
  final String? reviewTime;
  final double? rating;
  const MyRatingItemView({
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
                decoration: BoxDecoration(
                  color: AppColors.white,
                  shape: BoxShape.circle,
                ),
                child: CustomProfileImage(image: image ?? ""),
              ).paddingOnly(left: 16, top: 8, right: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    name ?? "",
                    style: AppFontStyle.fontStyleW700(
                        fontSize: 15, fontColor: AppColors.black),
                  ),
                  Row(
                    children: [
                      RatingBar.builder(
                        itemPadding: const EdgeInsets.only(),
                        ignoreGestures: false,
                        glow: false,
                        unratedColor: AppColors.ratingContainerColor,
                        itemSize: 16,
                        initialRating: (rating ?? 0).toDouble(),
                        minRating: 0,
                        maxRating: 5,
                        glowRadius: 10,
                        direction: Axis.horizontal,
                        allowHalfRating: false,
                        itemCount: 5,
                        itemBuilder: (context, _) => Icon(
                          Icons.star_rounded,
                          size: 50,
                          color: AppColors.yellowStarColor,
                        ),
                        onRatingUpdate: (r) {
                          controller.rating.value = r.toDouble();
                          log("Selected Rating :: ${controller.rating.value}");
                        },
                      ),
                      Text(
                        (rating ?? 0).toString(),
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 14, fontColor: AppColors.searchText),
                      ).paddingOnly(left: 8),
                    ],
                  ),
                ],
              ),
              Spacer(),
              Text(
                reviewTime ?? "10:34 AM",
                style: AppFontStyle.fontStyleW500(
                    fontSize: 11, fontColor: AppColors.timeColor),
              ).paddingOnly(right: 13, bottom: 15)
            ],
          ),
          Text(
            review ?? "",
            style: AppFontStyle.fontStyleW500(
              fontSize: 12,
              fontColor: AppColors.searchText,
              height: 1.6,
            ),
          ).paddingOnly(right: 14, left: 14, top: 10, bottom: 8),
        ],
      ),
    ).paddingOnly(right: 14, left: 14);
  }
}

class LiveAdsItemView extends StatelessWidget {
  const LiveAdsItemView({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.appRedColor),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Container(
                height: 76,
                width: 76,
                decoration: BoxDecoration(
                  color: AppColors.categoriesBgColor,
                  borderRadius: BorderRadius.circular(14),
                ),
              ).paddingOnly(top: 6, left: 6, right: 12),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        "Platinum Package",
                        style: AppFontStyle.fontStyleW700(
                            fontSize: 16, fontColor: AppColors.appRedColor),
                      ),
                      Image.asset(
                        AppAsset.verificationRightIcon,
                        height: 19,
                        width: 19,
                      ).paddingOnly(left: 8),
                    ],
                  ).paddingOnly(bottom: 8, top: 7),
                  Text(
                    "Lorem Ipsum is simply dummy.",
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 12,
                      fontColor: AppColors.greyTxtColor2,
                    ),
                  ).paddingOnly(bottom: 10),
                  Row(
                    children: [
                      Image.asset(
                        AppAsset.adsIcon,
                        height: 18,
                        width: 18,
                      ).paddingOnly(right: 8),
                      Text(
                        "10 Ads",
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 13,
                            fontColor: AppColors.popularProductText),
                      ).paddingOnly(right: 17),
                      Image.asset(
                        AppAsset.daysIcon,
                        height: 18,
                        width: 18,
                      ).paddingOnly(right: 8),
                      Text(
                        "15 Days",
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 13,
                            fontColor: AppColors.popularProductText),
                      ),
                    ],
                  ),
                ],
              )
            ],
          ).paddingOnly(bottom: 18),
          DottedLine(dashColor: AppColors.dottedLineColor)
              .paddingSymmetric(horizontal: 10),
          Row(
            children: [
              Image.asset(
                AppAsset.validIcon,
                height: 20,
                width: 20,
              ).paddingOnly(right: 8),
              Row(
                children: [
                  Text(
                    "Plan Valid Still : ",
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 12, fontColor: AppColors.greyTxtColor2),
                  ),
                  Text(
                    "30 Aug 2025",
                    style: AppFontStyle.fontStyleW700(
                        fontSize: 12, fontColor: AppColors.appRedColor),
                  ),
                ],
              ),
            ],
          ).paddingOnly(top: 12, bottom: 10, left: 12),
        ],
      ),
    ).paddingOnly(left: 14, right: 14);
  }
}
