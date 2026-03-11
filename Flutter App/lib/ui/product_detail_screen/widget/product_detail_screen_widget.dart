import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/bottom_sheet/escrow_bottom_sheet.dart';
import 'package:listify/custom/bottom_sheet/report_bottom_sheet.dart';
import 'package:listify/custom/bottom_sheet/safety_tips_bottom_sheet.dart';
import 'package:listify/custom/bottom_sheet/specific_ad_like_bottom_sheet.dart';
import 'package:listify/custom/bottom_sheet/specific_ad_view_bottom_sheet.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/dialog/bid_dialog.dart';
import 'package:listify/custom/map_fallback/map_unavailable_fallback.dart';
import 'package:listify/custom/dialog/remove_product_dialog.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/custom/timer_widget/timer_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/ui/product_detail_screen/controller/specific_product_like_show_controller.dart';
import 'package:listify/ui/product_detail_screen/shimmer/views_shimmer.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/google_maps_runtime.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';
import 'package:map_location_picker/map_location_picker.dart';

class DetailTopView extends StatefulWidget {
  final bool? iconShow;
  const DetailTopView({super.key, this.iconShow});

  @override
  State<DetailTopView> createState() => _DetailTopViewState();
}

class _DetailTopViewState extends State<DetailTopView> {
  final PageController _pageController = PageController();
  int _currentPage = 0;
  bool _galleryPreloaded = false;

  final List<String> imageList = [
    AppAsset.acImage,
    AppAsset.iphoneImage,
    AppAsset.acImage,
  ];

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(builder: (controller) {
      // Preload all gallery images once available so swipe is instant
      final gallery = controller.productDetail?.data?.galleryImages;
      if (gallery != null && gallery.length > 1 && !_galleryPreloaded) {
        _galleryPreloaded = true;
        WidgetsBinding.instance.addPostFrameCallback((_) {
          for (final url in gallery) {
            if (url.startsWith('http') && mounted) {
              precacheImage(CachedNetworkImageProvider(url), context);
            }
          }
        });
      }
      return Stack(
        children: [
          SizedBox(
            height: Get.height * 0.42,
            width: Get.width,
            child: PageView.builder(
              controller: _pageController,
              itemCount: controller.productDetail?.data?.galleryImages?.length,
              onPageChanged: (index) {
                setState(() {
                  _currentPage = index;
                });
              },
              itemBuilder: (context, index) {
                return CustomImageView(
                  image:
                      controller.productDetail?.data?.galleryImages?[index] ??
                          '',
                  fit: BoxFit.cover,
                );
              },
            ),
          ),
          Positioned(
            bottom: 10,
            left: 0,
            right: 0,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                  controller.productDetail?.data?.galleryImages?.length ?? 0,
                  (index) {
                return AnimatedContainer(
                  duration: Duration(milliseconds: 300),
                  margin: const EdgeInsets.symmetric(horizontal: 2),
                  height: 6,
                  width: _currentPage == index ? 20 : 6,
                  decoration: BoxDecoration(
                    color: _currentPage == index
                        ? AppColors.white
                        : AppColors.black.withValues(alpha: 0.54),
                    borderRadius: BorderRadius.circular(10),
                  ),
                );
              }),
            ),
          ),
        ],
      );
    });
  }
}

class ProductDetailView extends StatelessWidget {
  const ProductDetailView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(builder: (controller) {
      return Column(
        children: [
          Container(
            width: Get.width,
            decoration: BoxDecoration(color: AppColors.white, boxShadow: [
              BoxShadow(
                color: AppColors.black.withValues(alpha: 0.12),
                blurRadius: 8,
                spreadRadius: 0,
                offset: Offset(0, 0),
              ),
            ]),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Flexible(
                      child: Text(
                        controller.productDetail?.data?.title ?? '',
                        style: AppFontStyle.fontStyleW700(
                            fontSize: 18, fontColor: AppColors.black),
                        overflow: TextOverflow.visible,
                      ),
                    ),
                    controller.productDetail?.data?.isAuctionEnabled == true
                        ? SizedBox(
                            // width: 132,
                            child: TimerWidget(
                              endDate:
                                  "${controller.productDetail?.data?.auctionEndDate}",
                              borderRadius: BorderRadius.circular(7),
                              padding: EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 5),
                            ),
                          )
                        : Offstage()
                  ],
                ).paddingOnly(bottom: 6),
                Row(
                  children: [
                    Text(
                      '${Database.settingApiResponseModel?.data?.currency?.symbol} ${controller.productDetail?.data?.isAuctionEnabled == true ? controller.productDetail?.data?.auctionStartingPrice?.toString() ?? '' : controller.productDetail?.data?.price ?? "0"}',
                      style: AppFontStyle.fontStyleW800(
                          fontSize: 18, fontColor: AppColors.appRedColor),
                    ).paddingOnly(right: 8),
                  ],
                ).paddingOnly(bottom: 8),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Image.asset(
                      AppAsset.locationIcon,
                      height: 20,
                      width: 20,
                    ).paddingOnly(right: 6),
                    Expanded(
                      child: Text(
                        controller.formatAddress(
                            controller.productDetail?.data?.location),
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 14, fontColor: AppColors.black),
                      ),
                    ),
                    // Spacer(),
                    Text(
                      controller.formatCreated(
                          controller.productDetail?.data?.createdAt,
                          longMonth: true),
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 12, fontColor: AppColors.purpleBorder),
                    )
                  ],
                )
              ],
            ).paddingOnly(top: 9, left: 14, bottom: 11, right: 14),
          ).paddingOnly(bottom: 18),
          controller.viewLikeCount == true
              ? Row(
                  children: [
                    Expanded(
                      child: GestureDetector(
                        onTap: () {
                          // Utils.showLog("controller.adsData?.id${controller.productDetail?.data?.id}");
                          // Get.toNamed(AppRoutes.specifAdViewShowScreen, arguments: {
                          //   'adId': controller.productDetail?.data?.id ?? "",
                          // });
                          Get.bottomSheet(
                            SpecificAdViewBottomSheet(
                              controller: controller,
                            ),
                            isScrollControlled: true,
                            backgroundColor: Colors.transparent,
                            barrierColor:
                                AppColors.black.withValues(alpha: 0.8),
                          );
                        },
                        child: Container(
                          decoration: BoxDecoration(
                            border: Border.all(color: AppColors.adBorderColor),
                            borderRadius: BorderRadius.circular(10),
                            color: AppColors.white,
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Image.asset(
                                AppAsset.eyeIcon,
                                height: 28,
                                width: 28,
                              ).paddingOnly(right: 16),
                              Text(
                                '${controller.productDetail?.data?.viewsCount ?? 0}',
                                style: AppFontStyle.fontStyleW500(
                                    fontSize: 16, fontColor: AppColors.black),
                              ),
                            ],
                          ).paddingOnly(bottom: 10, top: 10),
                        ),
                      ),
                    ),
                    16.width,
                    Expanded(
                      child: GestureDetector(
                        onTap: () {
                          // Utils.showLog(
                          //     "controller.adsData?.id${controller.productDetail?.data?.id}");
                          // Get.toNamed(AppRoutes.specifAdLikeShowScreen,
                          //     arguments: {
                          //       'adId':
                          //           controller.productDetail?.data?.id ?? "",
                          //     });

                          Get.bottomSheet(
                            SpecificAdLikeBottomSheet(
                              controller: controller,
                            ),
                            isScrollControlled: true,
                            backgroundColor: Colors.transparent,
                            barrierColor:
                                AppColors.black.withValues(alpha: 0.8),
                          );
                        },
                        child: Container(
                          decoration: BoxDecoration(
                            border: Border.all(color: AppColors.adBorderColor),
                            borderRadius: BorderRadius.circular(10),
                            color: AppColors.white,
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Image.asset(
                                AppAsset.favouriteIcon,
                                height: 28,
                                width: 28,
                              ).paddingOnly(right: 16),
                              Text(
                                '${controller.productDetail?.data?.likesCount ?? 0}',
                                style: AppFontStyle.fontStyleW500(
                                    fontSize: 16, fontColor: AppColors.black),
                              ),
                            ],
                          ).paddingOnly(bottom: 10, top: 10),
                        ),
                      ),
                    ),
                  ],
                ).paddingOnly(right: 14, left: 14, bottom: 20)
              : SizedBox(),
        ],
      );
    });
  }
}

class ProductDescriptionDetailView extends StatelessWidget {
  const ProductDescriptionDetailView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(builder: (controller) {
      final hierarchy = controller.productDetail?.data?.categoryHierarchy ?? [];
      final fallbackCategory = controller.productDetail?.data?.category;
      final categoryNodes = hierarchy.isNotEmpty
          ? hierarchy
          : (fallbackCategory != null ? [fallbackCategory] : []);

      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            EnumLocale.txtProductDescription.name.tr,
            style: AppFontStyle.fontStyleW700(
                fontSize: 16, fontColor: AppColors.black),
          ).paddingOnly(left: 14, bottom: 4),
          Text(
            Utils.stripHtml(controller.productDetail?.data?.description),
            style: AppFontStyle.fontStyleW5003(
                fontSize: 12, fontColor: AppColors.productDesColor),
          ).paddingOnly(right: 14, left: 14, bottom: 20),

          ///
          Text(
            EnumLocale.txtProductCategories.name.tr,
            style: AppFontStyle.fontStyleW700(
                fontSize: 16, fontColor: AppColors.black),
          ).paddingOnly(left: 14, bottom: 10),

          ///
          Row(
            children: [
              Container(
                height: 7,
                width: 7,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.productDesColor,
                ),
              ).paddingOnly(bottom: 10, left: 14),

              // Category hierarchy
              Expanded(
                child: Wrap(
                  crossAxisAlignment: WrapCrossAlignment.center,
                  children: List.generate(
                    categoryNodes.length,
                    (index) {
                      final category = categoryNodes[index];
                      return Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            category.name ?? "",
                            style: AppFontStyle.fontStyleW500(
                              fontSize: 14,
                              fontColor: AppColors.productDesColor,
                            ),
                          ),
                          if (index != (categoryNodes.length - 1))
                            Text(
                              " > ",
                              style: AppFontStyle.fontStyleW500(
                                fontSize: 14,
                                fontColor: AppColors.productDesColor,
                              ),
                            ),
                        ],
                      );
                    },
                  ),
                ).paddingOnly(left: 6, bottom: 10),
              ),
            ],
          ),
          Container(
            width: Get.width,
            color: AppColors.productDetailBgColor,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  EnumLocale.txtProductDetail.name.tr,
                  style: AppFontStyle.fontStyleW700(
                      fontSize: 16, fontColor: AppColors.black),
                ).paddingOnly(left: 14, bottom: 20, top: 14),
                GetBuilder<ProductDetailScreenController>(
                  builder: (controller) {
                    // 1) Filter attributes first (so grid ma khali cell na bane)
                    final ignoredNames = {
                      'invoice'
                    }; // lowercased names to hide
                    final allAttrs =
                        controller.productDetail?.data?.attributes ?? [];

                    final attrs = allAttrs.where((attr) {
                      final name = (attr.name ?? '').trim().toLowerCase();
                      if (ignoredNames.contains(name)) return false;

                      // (optional) hide attributes whose value is a Map (like your Invoice object)
                      final v = attr.value;
                      if (v is Map) return false;

                      // (optional) hide empty values
                      if (v == null) return false;
                      if (v is String && v.trim().isEmpty) return false;
                      if (v is List &&
                          v
                              .where(
                                  (e) => (e ?? '').toString().trim().isNotEmpty)
                              .isEmpty) return false;

                      return true;
                    }).toList();

                    if (attrs.isEmpty) return const SizedBox.shrink();

                    return GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      padding: const EdgeInsets.only(
                          right: 16, top: 0, left: 16, bottom: 0),
                      gridDelegate:
                          const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        childAspectRatio: 2.3,
                        crossAxisSpacing: 30,
                      ),
                      // 2) Use filtered list length
                      itemCount: attrs.length,
                      itemBuilder: (context, index) {
                        final attr = attrs[index];

                        // Build value text
                        String valueText = '';
                        final v = attr.value;

                        if (v is List) {
                          valueText = v
                              .where((e) => e != null)
                              .map((e) => e is Map
                                  ? (e['name'] ?? e.toString())
                                  : e.toString())
                              .where((s) => s.toString().trim().isNotEmpty)
                              .join(', ');
                        } else if (v != null) {
                          valueText = v.toString();
                        } else if (attr.value.isNotEmpty == true) {
                          valueText = attr.value.join(', ');
                        }

                        final imgSource = (attr.image ?? '').trim();
                        final isSvg = imgSource.toLowerCase().contains('.svg');
                        final isNetwork = imgSource.startsWith('http://') || imgSource.startsWith('https://');
                        final imgUrl = isNetwork ? imgSource : (imgSource.isNotEmpty ? '${Api.baseUrl}$imgSource' : '');

                        Widget iconWidget;
                        if (imgSource.isEmpty) {
                          iconWidget = Icon(Icons.category_outlined, color: AppColors.popularProductText, size: 22);
                        } else if (isSvg) {
                          iconWidget = SvgPicture.network(
                            imgUrl,
                            colorFilter: ColorFilter.mode(AppColors.popularProductText, BlendMode.srcIn),
                            placeholderBuilder: (_) => Icon(Icons.category_outlined, color: AppColors.popularProductText, size: 22),
                          );
                        } else {
                          iconWidget = CustomImageView(
                            image: imgSource,
                            fit: BoxFit.cover,
                          );
                        }

                        return Container(
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              SizedBox(
                                width: 40,
                                height: 36,
                                child: iconWidget,
                              ).paddingOnly(top: 3),
                              const SizedBox(width: 14),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Text(
                                      attr.name ?? '',
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                      style: AppFontStyle.fontStyleW700(
                                        fontSize: 11,
                                        fontColor: AppColors.popularProductText,
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      valueText,
                                      softWrap: true,
                                      maxLines: 3,
                                      overflow: TextOverflow.ellipsis,
                                      style: AppFontStyle.fontStyleW700(
                                        fontSize: 12,
                                        fontColor: AppColors.black,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        );
                      },
                    );
                  },
                )
              ],
            ),
          ),
        ],
      );
    });
  }
}

class SellerDetailView extends StatelessWidget {
  const SellerDetailView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(builder: (controller) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            EnumLocale.txtSellerDetails.name.tr,
            style: AppFontStyle.fontStyleW700(
                fontSize: 16, fontColor: AppColors.black),
          ).paddingOnly(left: 14, top: 15, bottom: 13),
          Row(
            children: [
              GestureDetector(
                onTap: () {
                  Get.toNamed(
                    AppRoutes.sellerDetailScreenView,
                    arguments: {
                      'name': controller.productDetail?.data?.seller?.name,
                      'image':
                          controller.productDetail?.data?.seller?.profileImage,
                      'register':
                          controller.productDetail?.data?.seller?.registeredAt,
                      'userId': controller.productDetail?.data?.seller?.id,
                      'user': controller.productDetail?.data?.seller,
                    },
                  );
                },
                child: Container(
                  decoration: BoxDecoration(
                      color: AppColors.popularProductText,
                      shape: BoxShape.circle),
                  child: Container(
                    height: 52,
                    width: 52,
                    decoration: BoxDecoration(
                        color: AppColors.white, shape: BoxShape.circle),
                    child: ClipOval(
                      child: CustomImageView(
                          image: controller
                                  .productDetail?.data?.seller?.profileImage ??
                              ''),
                      // child: CustomImageView(image: ''),
                    ).paddingAll(1),
                  ).paddingAll(1),
                ).paddingOnly(right: 10),
              ),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      controller.productDetail?.data?.seller?.name ?? '',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: AppFontStyle.fontStyleW700(
                          fontSize: 15, fontColor: AppColors.black),
                      ).paddingOnly(bottom: 6),
                    controller.isFeaturedSeller == true
                        ? Container(
                            padding: EdgeInsets.symmetric(
                                vertical: 3, horizontal: 7),
                            decoration: BoxDecoration(
                              color: AppColors.blue,
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Text(
                              EnumLocale.txtTopSeller.name.tr,
                              style: AppFontStyle.fontStyleW500(
                                  fontSize: 9, fontColor: AppColors.white),
                            ),
                          )
                        : SizedBox(),
                  ],
                ),
              ),
              GestureDetector(
                onTap: () async {
                  final phone =
                      controller.productDetail?.data?.seller?.phoneNumber ?? "";

                  Utils.showLog("phone:::::::::::::::::::::$phone");
                  await controller.openDialer(phone);
                },
                child: Container(
                  padding: EdgeInsets.all(10),
                  decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      color: AppColors.redColor1),
                  child: Image.asset(
                    AppAsset.callIcon,
                    height: 28,
                    width: 28,
                  ),
                ),
              ),
              16.width,
              Container(
                padding: EdgeInsets.all(10),
                decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    color: AppColors.blue),
                child: Image.asset(
                  AppAsset.chatIcon,
                  height: 28,
                  width: 28,
                ),
              ),
            ],
          ).paddingOnly(left: 14, right: 14, bottom: 6),
        ],
      );
    });
  }
}

class ProductLocationView extends StatelessWidget {
  final ProductDetailScreenController productController;
  const ProductLocationView({super.key, required this.productController});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          EnumLocale.txtProductLocation.name.tr,
          style: AppFontStyle.fontStyleW700(
              fontSize: 16, fontColor: AppColors.black),
        ).paddingOnly(bottom: 5, top: 16),
        Stack(
          clipBehavior: Clip.none,
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(20),
              child: Container(
                height: Get.height * 0.25,
                width: Get.width,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(20),
                ),
                clipBehavior: Clip.hardEdge,
                child: GetBuilder<MapController>(
                  id: Constant.location,
                  builder: (mapController) {
                    if (!GoogleMapsRuntime.nativeMapsEnabled) {
                      return const MapUnavailableFallback(
                        borderRadius: BorderRadius.all(Radius.circular(20)),
                      );
                    }

                    return mapController.latitude != null &&
                            mapController.longitude != null
                        ? GoogleMap(
                            markers: Set<Marker>.from(mapController.markers),
                            initialCameraPosition: CameraPosition(
                              target: LatLng(mapController.latitude!,
                                  mapController.longitude!),
                              zoom: 18.0,
                            ),
                            myLocationEnabled: true,
                            myLocationButtonEnabled: false,
                            mapType: MapType.normal,
                            zoomGesturesEnabled: !mapController.isLoading,
                            zoomControlsEnabled: false,
                            onMapCreated:
                                (GoogleMapController controller) async {
                              mapController.mapController = controller;

                              final apiLat = productController
                                  .productDetail?.data?.location?.latitude
                                  ?.toDouble();
                              final apiLng = productController
                                  .productDetail?.data?.location?.longitude
                                  ?.toDouble();

                              if (apiLat != null && apiLng != null) {
                                final LatLng apiLatLng = LatLng(apiLat, apiLng);

                                await controller.animateCamera(
                                  CameraUpdate.newCameraPosition(
                                    CameraPosition(
                                      target: apiLatLng,
                                      zoom: 16,
                                    ),
                                  ),
                                );

                                // Marker add karo
                                await mapController.onHandleTapPoint(apiLatLng);
                              }
                            },
                            onTap: mapController.isLoading
                                ? null
                                : mapController.onHandleTapPoint,
                          )
                        : Center(
                            child:
                                CupertinoActivityIndicator()); // 👈 Show loader until coordinates are ready
                  },
                ),
              ),
            ),
            // BlurryContainer(
            //   blur: 8,
            //   color: AppColors.white.withValues(alpha: 0.70),
            //   height: Get.height * 0.25,
            //   width: Get.width,
            //   // elevation: 7,
            //   child: SizedBox(),
            // ),

            Positioned.fill(
              child: ClipRRect(
                borderRadius: BorderRadius.circular(20),
                child: Container(
                  height: Get.height * 0.25,
                  width: Get.width,
                  decoration: BoxDecoration(
                    color: AppColors.white.withValues(alpha: 0.50),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  clipBehavior: Clip.hardEdge,
                ),
              ),
            ),
            Positioned.fill(
              child: Align(
                alignment: Alignment.center,
                child: GestureDetector(
                  onTap: () {
                    Utils.showLog(
                        "location>>>>>>>>>>>>>>>>>${productController.productDetail?.data?.location?.country}");
                    Utils.showLog(
                        "location>>>>>>>>>>>>>>>>>${productController.productDetail?.data?.location?.state}");
                    Utils.showLog(
                        "location>>>>>>>>>>>>>>>>>${productController.productDetail?.data?.location?.city}");
                    Utils.showLog(
                        "location>>>>>>>>>>>>>>>>>${productController.productDetail?.data?.location?.fullAddress}");
                    Utils.showLog(
                        "location>>>>>>>>>>>>>>>>>${productController.productDetail?.data?.location?.longitude}");
                    Utils.showLog(
                        "location>>>>>>>>>>>>>>>>>${productController.productDetail?.data?.location?.latitude}");

                    final apiLat = productController
                        .productDetail?.data?.location?.latitude
                        ?.toDouble();
                    final apiLng = productController
                        .productDetail?.data?.location?.longitude
                        ?.toDouble();

                    if (apiLat != null && apiLng != null) {
                      final locationData = LocationDataModel(
                        latitude: apiLat,
                        longitude: apiLng,
                        country: productController
                            .productDetail?.data?.location?.country,
                        state: productController
                            .productDetail?.data?.location?.state,
                        city: productController
                            .productDetail?.data?.location?.city,
                        fullAddress: productController
                            .productDetail?.data?.location?.fullAddress,
                      );

                      Get.to(() => FullMapScreen(), arguments: locationData);
                    } else {
                      Utils.showToast(context, "Location not available");
                    }
                  },
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(8),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.black.withValues(alpha: 0.10),
                          blurRadius: 5,
                          offset: Offset(0, 0),
                        ),
                      ],
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Image.asset(
                          AppAsset.mapViewIcon,
                          height: 14,
                          width: 14,
                        ),
                        const SizedBox(width: 8),
                        Text(
                          EnumLocale.txtOpenMapView.name.tr,
                          style: AppFontStyle.fontStyleW500(
                            fontSize: 12,
                            fontColor: AppColors.black,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ).paddingOnly(left: 4, right: 4),
      ],
    ).paddingOnly(left: 14, right: 14, bottom: 20);
  }
}

class ReportAdsRelatedProductView extends StatelessWidget {
  const ReportAdsRelatedProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          const cross = 2;
          const tileHeight = 265.0;
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                EnumLocale.txtReportSpamAds.name.tr,
                style: AppFontStyle.fontStyleW500(
                    fontSize: 14, fontColor: AppColors.appRedColor),
              ).paddingOnly(bottom: 12, left: 14),
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Image.asset(
                    AppAsset.warningIcon,
                    height: 22,
                    width: 22,
                  ).paddingOnly(right: 6),
                  Flexible(
                    child: Text(
                      EnumLocale
                          .txtDidYouFindAnyProblemWithThisAdsProduct.name.tr,
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 13, fontColor: AppColors.black),
                    ),
                  )
                ],
              ).paddingOnly(bottom: 16, left: 14),
              GestureDetector(
                onTap: () {
                  showModalBottomSheet(
                    context: context,
                    isScrollControlled: true,
                    backgroundColor: Colors.white,
                    barrierColor: AppColors.black.withValues(alpha: 0.8),
                    builder: (context) => ReportBottomSheet(
                      submitOnTap: () {

                        if(Database.demoUser==true){

                          Utils.showLog("This demo app");
                        }else{
                         controller.adReportUserApi();}
                      },
                    ),
                  );
                },
                child: Container(
                  decoration: BoxDecoration(
                      color: AppColors.lightRed100,
                      borderRadius: BorderRadius.circular(6)),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Image.asset(
                        AppAsset.flagBorderIcon,
                        height: 14,
                        width: 14,
                      ).paddingOnly(right: 4),
                      Text(
                        EnumLocale.txtReportThisAds.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 12, fontColor: AppColors.appRedColor),
                      ),
                    ],
                  ).paddingSymmetric(horizontal: 7, vertical: 3),
                ).paddingOnly(bottom: 28),
              ).paddingOnly(
                left: 14,
              ),
              controller.relatedProductList.isEmpty
                  ? SizedBox()
                  : Text(EnumLocale.txtRelatedProduct.name.tr,
                          style: AppFontStyle.fontStyleW700(
                              fontSize: 16, fontColor: AppColors.black))
                      .paddingOnly(bottom: 16, left: 14),
              controller.isLoading
                  ? RelatedProductGridViewShimmer()
                  : controller.relatedProductList.isEmpty
                      ? SizedBox()
                      : GridView.builder(
                          padding: EdgeInsets.zero,
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          gridDelegate:
                              SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: cross,
                            mainAxisExtent:
                                tileHeight, // <- key change (no childAspectRatio)
                            crossAxisSpacing: 6,
                            mainAxisSpacing: 6,
                          ),
                          itemCount: controller.relatedProductList.length,
                          itemBuilder: (context, index) {
                            final relatedProduct =
                                controller.relatedProductList[index];

                            final isLiked = LikeManager.to.getLikeState(
                                relatedProduct.id ?? "",
                                fallback: relatedProduct.isLike);
                            return GestureDetector(
                              // onTap: () async {
                              //   Utils.showLog("Before direct Get.to");
                              //   Utils.showLog(
                              //       ">>>>>>>>>>>>>>>>>>${relatedProduct.title}");
                              //   Utils.showLog("relatedProduct.id>>>>>>>>>>>>>>>>>>${relatedProduct.id}");
                              //   Utils.showLog("isLiked>>>>>>>>>>>>>>>>>>${isLiked}");
                              //
                              //   try {
                              //
                              //     Get.delete<ProductDetailScreenController>();
                              //    await  Future.delayed(Duration(seconds: 2));
                              //
                              //     // Get.to(ProductDetailScreenView());
                              //     Get.toNamed(AppRoutes.productDetailScreen,
                              //         arguments: {
                              //           'sellerDetail': true,
                              //           'relatedProduct': true,
                              //           'viewLikeCount': true,
                              //           // 'ad': relatedProduct,
                              //           'adId': relatedProduct.id,
                              //         });
                              //
                              //     // Get.toNamed(AppRoutes.notificationScreenView);
                              //
                              //     Utils.showLog(
                              //         "After direct Get.to (returned)");
                              //   } catch (e, st) {
                              //     Utils.showLog(
                              //         "Direct navigation error: $e\n$st");
                              //   }
                              // },

                              onTap: () {
                                final targetId = (relatedProduct.id ?? '').trim();
                                if (targetId.isEmpty) {
                                  Utils.showToast(Get.context!, 'Unable to open this listing.');
                                  return;
                                }

                                Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                                  'sellerDetail': true,
                                  'relatedProduct': true,
                                  'viewLikeCount': true,
                                  'adId': targetId,
                                  'listing_id': targetId,
                                  'id': targetId,
                                }, preventDuplicates: false);
                              },
                              child: ProductGridView(
                                isVerify:
                                    relatedProduct.seller?.isVerified ?? false,
                                topSeller:
                                    relatedProduct.seller?.isFeaturedSeller ??
                                        false,
                                productImage: relatedProduct.primaryImage ?? '',
                                // isLiked: relatedProduct.isLike ?? false,
                                // isLiked: controller.isAdLiked(relatedProduct),
                                // onLikeTap: () {
                                //   controller.toggleLike(
                                //       index,
                                //       controller.relatedProductList[index].id ??
                                //           '');
                                // },

                                isLiked: isLiked,
                                onLikeTap: () {
                                  controller.toggleMostLike(
                                      index, relatedProduct.id ?? "");
                                },
                                newPrice:
                                    "${Database.settingApiResponseModel?.data?.currency?.symbol} ${relatedProduct.isAuctionEnabled == true ? relatedProduct.auctionStartingPrice?.toString() ?? '' : relatedProduct.price ?? "0"}",
                                // oldPrice: '${relatedProduct.price ?? ''}',
                                productName: relatedProduct.title ?? '',
                                sellerImage:
                                    relatedProduct.seller?.profileImage ?? '',
                                sellerLocation:
                                    relatedProduct.location?.country ?? '',
                                sellerName: relatedProduct.seller?.name ?? "",
                              ),
                            );
                          },
                        ).paddingOnly(bottom: 16, right: 14, left: 14),
              10.height,
            ],
          );
        });
  }
}

class DetailBottomView extends StatelessWidget {
  const DetailBottomView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(
        id: Constant.idProductDetail,
        builder: (controller) {
          return controller.isDetailLoading
              ? SizedBox()
              : SafeArea(
                  top: false,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          color: AppColors.white,
                          boxShadow: [
                            BoxShadow(
                              color: AppColors.black.withValues(alpha: 0.1),
                              blurRadius: 12,
                              offset: Offset(0, -2),
                              spreadRadius: 0,
                            ),
                          ],
                        ),
                        child: controller.productDetail?.data?.seller?.id ==
                              Database.getUserProfileResponseModel?.user?.id
                          ? Row(
                              children: [
                                Expanded(
                                  child: PrimaryAppButton(
                                    onTap: () {
                                      Get.toNamed(AppRoutes.editProductView,
                                          arguments: {
                                            'ad':
                                                controller.productDetail?.data,
                                            'editApi': true,
                                            'adId': controller
                                                .productDetail?.data?.id,
                                          });
                                    },
                                    height: 54,
                                    fontColor: AppColors.appRedColor,
                                    color: AppColors.lightRed100,
                                    text: EnumLocale.txtEdit.name.tr,
                                  ),
                                ),
                                14.width,
                                Expanded(
                                  child: PrimaryAppButton(
                                    onTap: () {
                                      // controller.removeAdListing();

                                      Get.dialog(
                                        barrierColor: AppColors.black.withValues(alpha: 0.8),
                                        Dialog(
                                          insetPadding: EdgeInsets.symmetric(horizontal: 32),
                                          backgroundColor: AppColors.transparent,
                                          shadowColor: Colors.transparent,
                                          surfaceTintColor: Colors.transparent,
                                          elevation: 0,
                                          child: RemoveProductDialog(
                                            onTap: () {
                                              controller.removeAdListing();
                                            },
                                          ),
                                        ),
                                      );
                                    },
                                    height: 54,
                                    text: EnumLocale.txtRemove.name.tr,
                                  ),
                                ),
                              ],
                            ).paddingOnly(
                              right: 16, left: 16, bottom: 12, top: 12)
                          : Column(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                if (controller.productDetail?.data?.escrowEnabled == true)
                                  PrimaryAppButton(
                                    onTap: () {
                                      Get.bottomSheet(
                                        EscrowBottomSheet(
                                          listingId: controller.productDetail?.data?.id ?? '',
                                          listingTitle: controller.productDetail?.data?.title ?? '',
                                          listingImage: controller.productDetail?.data?.primaryImage ?? '',
                                        ),
                                        isScrollControlled: true,
                                        backgroundColor: Colors.transparent,
                                        barrierColor: AppColors.black.withValues(alpha: 0.8),
                                      );
                                    },
                                    height: 48,
                                    color: AppColors.green,
                                    widget: Icon(Icons.shield_outlined, color: AppColors.white, size: 22),
                                    text: 'Buy with Escrow',
                                  ).paddingOnly(right: 16, left: 16, top: 12),
                                Row(
                              children: [
                                Expanded(
                                  child: PrimaryAppButton(
                                    onTap: () {
                                      Get.toNamed(
                                          AppRoutes.chatDetailScreenView,
                                          arguments: {
                                            'name': controller.productDetail
                                                ?.data?.seller?.name,
                                            'image': controller.productDetail
                                                ?.data?.seller?.profileImage,
                                            'profileImage': controller
                                                .productDetail
                                                ?.data
                                                ?.seller
                                                ?.profileImage,
                                            'adId': controller
                                                .productDetail?.data?.id,
                                            'receiverId': controller
                                                .productDetail
                                                ?.data
                                                ?.seller
                                                ?.id,
                                            'productPrice': controller
                                                .productDetail?.data?.price,
                                            'productName': controller
                                                .productDetail?.data?.title,
                                            'primaryImage': controller
                                                .productDetail
                                                ?.data
                                                ?.primaryImage,
                                            'isViewed': controller
                                                .productDetail?.data?.isViewed,
                                          })?.then(
                                        (value) {
                                          controller.productDetail?.data
                                              ?.isViewed = true;
                                          controller.update();
                                        },
                                      );
                                      Utils.showLog(
                                          'product name>>>>> >>>>> ${controller.productDetail?.data?.title}');
                                      Utils.showLog(
                                          'product price>>>>> >>>>> ${controller.productDetail?.data?.price}');
                                      Utils.showLog(
                                          'product primaryImage>>>>> >>>>> ${controller.productDetail?.data?.primaryImage}');
                                    },
                                    widget: Image.asset(
                                      AppAsset.chatIcon,
                                      height: 26,
                                      width: 26,
                                    ),
                                    height: 54,
                                    color: AppColors.green,
                                    text: EnumLocale.txtProductChat.name.tr,
                                  ),
                                ),
                                // 14.width,
                                controller.productDetail?.data?.saleType == 2
                                    ? controller.hasAuctionEnded
                                        ? Offstage()
                                        : Expanded(
                                            child: PrimaryAppButton(
                                              height: 54,
                                              widget: Image.asset(
                                                AppAsset.bidIcon,
                                                height: 26,
                                                width: 26,
                                              ),
                                              onTap: () {
                                                // DetailBottomView → Place Bid બટન
                                                Get.dialog(
                                                  barrierColor: AppColors.black
                                                      .withValues(alpha: 0.8),
                                                  Dialog(
                                                    insetPadding:
                                                        const EdgeInsets
                                                            .symmetric(
                                                            horizontal: 32),
                                                    backgroundColor:
                                                        AppColors.transparent,
                                                    shadowColor:
                                                        Colors.transparent,
                                                    surfaceTintColor:
                                                        Colors.transparent,
                                                    elevation: 0,
                                                    child: CustomBidDialog(
                                                      controller: controller,
                                                      title: controller
                                                              .productDetail
                                                              ?.data
                                                              ?.title ??
                                                          '',
                                                      price: controller
                                                                  .productDetail
                                                                  ?.data
                                                                  ?.isAuctionEnabled ==
                                                              true
                                                          ? controller
                                                                  .productDetail
                                                                  ?.data
                                                                  ?.auctionStartingPrice
                                                                  ?.toString() ??
                                                              ''
                                                          : controller
                                                                  .productDetail
                                                                  ?.data
                                                                  ?.price
                                                                  ?.toString() ??
                                                              '',
                                                      // oldPrice: controller.productDetail?.data?.mrp?.toString(),
                                                      primaryImage: controller
                                                              .productDetail
                                                              ?.data
                                                              ?.primaryImage ??
                                                          '',
                                                      locationText: controller
                                                          .formatAddress(
                                                              controller
                                                                  .productDetail
                                                                  ?.data
                                                                  ?.location),
                                                      auctionEnd: controller
                                                          .productDetail
                                                          ?.data
                                                          ?.auctionEndDate,
                                                      lastBidAmount: controller
                                                          .productDetail
                                                          ?.data
                                                          ?.lastBidAmount
                                                          ?.toString(),
                                                      description: controller
                                                          .productDetail
                                                          ?.data
                                                          ?.description,
                                                    ),
                                                  ),
                                                );
                                              },
                                              color: AppColors.redColor,
                                              text: EnumLocale
                                                  .txtPlaceBid.name.tr,
                                            ).paddingOnly(left: 14),
                                          )
                                    : controller.productDetail?.data
                                                    ?.isOfferPlaced ==
                                                true ||
                                            controller.productDetail?.data
                                                    ?.isOfferPlaced ==
                                                null
                                        ? Offstage()
                                        : Expanded(
                                            child: PrimaryAppButton(
                                              height: 54,
                                              widget: Image.asset(
                                                AppAsset.offerIcon,
                                                height: 26,
                                                width: 26,
                                              ),
                                              onTap: () {
                                                Get.bottomSheet(
                                                  SafetyTipsBottomSheet(
                                                    controller: controller,
                                                    productPrice: controller
                                                                .productDetail
                                                                ?.data
                                                                ?.isAuctionEnabled ==
                                                            true
                                                        ? controller
                                                                .productDetail
                                                                ?.data
                                                                ?.auctionStartingPrice
                                                                ?.toString() ??
                                                            ''
                                                        : controller
                                                                .productDetail
                                                                ?.data
                                                                ?.price
                                                                ?.toString() ??
                                                            '',
                                                    name: controller
                                                            .productDetail
                                                            ?.data
                                                            ?.title ??
                                                        '',
                                                    image: controller
                                                            .productDetail
                                                            ?.data
                                                            ?.primaryImage ??
                                                        '',
                                                    profileImage: controller
                                                            .productDetail
                                                            ?.data
                                                            ?.seller
                                                            ?.profileImage ??
                                                        '',
                                                    adId: controller
                                                            .productDetail
                                                            ?.data
                                                            ?.id ??
                                                        '',
                                                    receiverId: controller
                                                            .productDetail
                                                            ?.data
                                                            ?.seller
                                                            ?.id ??
                                                        '',
                                                    isOnline: false,
                                                  ),
                                                  isScrollControlled: true,
                                                  backgroundColor:
                                                      Colors.transparent,
                                                  barrierColor: AppColors.black
                                                      .withValues(alpha: 0.8),
                                                );
                                              },
                                              color: AppColors.blue,
                                              text: EnumLocale
                                                  .txtMakeAnOffer.name.tr,
                                            ).paddingOnly(left: 14),
                                          ),
                              ],
                            ).paddingOnly(
                              right: 16, left: 16, bottom: 12, top: 12),
                              ],
                            ),
                      ),
                    ],
                  ),
                );
        });
  }
}

class SpecificLikeShow extends StatelessWidget {
  const SpecificLikeShow({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SpecificProductLikeShowController>(
      id: Constant.productLike,
      builder: (controller) {
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: () async {
            await controller.init();
          },
          child: controller.isLoading
              ? ViewsShimmer()
              : controller.likeList.isEmpty
                  ? SizedBox(
                      height: Get.height * 0.76,
                      child: Center(
                        child: NoDataFound(
                            image: AppAsset.noProductFound,
                            imageHeight: 160,
                            text: EnumLocale.txtNoDataFound.name.tr),
                      ),
                    )
                  : ListView.builder(
                      physics:
                          const AlwaysScrollableScrollPhysics(), // scroll + refresh enable
                      shrinkWrap: true,
                      itemCount: controller.likeList.length,
                      itemBuilder: (context, index) {
                        return SpecificAdLikeItemView(
                          name: controller.likeList[index].user?.name,
                          profileImage:
                              controller.likeList[index].user?.profileImage,
                          id: controller.likeList[index].ad,
                        );
                      },
                    ),
        );
      },
    );
  }
}

class SpecificAdLikeItemView extends StatelessWidget {
  final String? name;
  final String? profileImage;
  final String? id;
  void Function()? onTap;
  SpecificAdLikeItemView({super.key, this.name, this.profileImage, this.id});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: AppColors.black.withValues(alpha: 0.05),
            spreadRadius: 2,
            blurRadius: 7,
            offset: Offset(0, 0),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            height: 45,
            width: 45,
            clipBehavior: Clip.hardEdge,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
            child: CustomProfileImage(image: profileImage ?? ""),
          ).paddingOnly(left: 6, top: 8, bottom: 8, right: 6),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                name ?? "",
                style: AppFontStyle.fontStyleW700(
                    fontSize: 14, fontColor: AppColors.black),
              ),
              // Text(
              //   id ?? "",
              //   style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.black),
              // ),
            ],
          ),
        ],
      ),
    ).paddingOnly(left: 16, right: 16, top: 16);
  }
}

class SpecificAdLikeShowAppBar extends StatelessWidget {
  final String? title;
  const SpecificAdLikeShowAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class FullMapScreen extends StatelessWidget {
  const FullMapScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final LocationDataModel location = Get.arguments;

    final LatLng apiLatLng = LatLng(location.latitude, location.longitude);

    return Scaffold(
      appBar: AppBar(
        title: Text(EnumLocale.txtProductLocation.name.tr),
      ),
      body: GoogleMapsRuntime.nativeMapsEnabled
          ? GoogleMap(
              initialCameraPosition: CameraPosition(
                target: apiLatLng,
                zoom: 16,
              ),
              markers: {
                Marker(
                  markerId: MarkerId("product_location"),
                  position: apiLatLng,
                  infoWindow: InfoWindow(
                    title: location.city ?? "Location",
                    snippet: location.fullAddress ?? "",
                  ),
                ),
              },
              myLocationEnabled: false,
              zoomControlsEnabled: true,
              mapType: MapType.normal,
            )
          : const MapUnavailableFallback(expand: true),
    );
  }
}

class LocationDataModel {
  final double latitude;
  final double longitude;
  final String? country;
  final String? state;
  final String? city;
  final String? fullAddress;

  LocationDataModel({
    required this.latitude,
    required this.longitude,
    this.country,
    this.state,
    this.city,
    this.fullAddress,
  });
}
