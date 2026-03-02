import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/product_view/grid_product_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/featured_ads_screen/controller/featured_ads_screen_controller.dart';
import 'package:listify/ui/featured_ads_screen/controller/featured_ads_show_screen_controller.dart';
import 'package:listify/ui/featured_ads_screen/shimmer/featured_ads_plan_shimmer.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_gridview_shimmer.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_list_view_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class FeaturedAdsScreenAppBar extends StatelessWidget {
  final String? title;
  const FeaturedAdsScreenAppBar({super.key, this.title});

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

class FeaturedAdsScreenWidget extends StatelessWidget {
  const FeaturedAdsScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<FeaturedAdsScreenController>(
        id: Constant.idFeatureAdsPlan,
        builder: (controller) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Image.asset(AppAsset.featuredAdsImage)
                  .paddingOnly(top: 28, left: 40, right: 40, bottom: 22),
              Text(
                EnumLocale.txtFeaturedAds.name.tr,
                style: AppFontStyle.fontStyleW800(
                    fontSize: 20, fontColor: AppColors.appRedColor),
              ).paddingOnly(left: 14, right: 30, bottom: 3),
              Text(
                "Lorem Ipsum is simply dummy text the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 15s, when an unknown printer took a galley of type and scrambled.",
                style: AppFontStyle.fontStyleW500(
                    fontSize: 11,
                    fontColor: AppColors.popularProductText,
                    height: 1.8),
              ).paddingOnly(left: 14, right: 30, bottom: 14),
              controller.isLoading
                  ? FeaturedAdsPlanShimmer()
                  : ListView.builder(
                      itemCount: controller.featuredAdsPlan.length,
                      shrinkWrap: true,
                      physics: NeverScrollableScrollPhysics(),
                      itemBuilder: (context, index) {
                        bool isSelected = controller.selectedIndex == index;
                        final plan = controller.featuredAdsPlan[index];
                        return GestureDetector(
                          onTap: () {
                            controller.selectPlan(index);
                          },
                          child: Container(
                            width: Get.width,
                            margin: EdgeInsets.only(
                                left: 14, right: 14, bottom: 14),
                            decoration: BoxDecoration(
                              color: AppColors.white,
                              border: Border.all(
                                  color: isSelected
                                      ? AppColors.appRedColor
                                      : AppColors.borderColor,
                                  width: 0.5),
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  // height: 74,
                                  // width: 74,
                                  padding: EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                      color: AppColors.categoriesBgColor,
                                      borderRadius: BorderRadius.circular(14)),
                                  // child: Image.asset(
                                  //   AppAsset.freeTrialImage,
                                  //   height: 50,
                                  //   width: 50,
                                  // ),
                                  child: SizedBox(
                                      height: 50,
                                      width: 50,
                                      child: CustomImageView(
                                          image: plan.image ?? '')),
                                ).paddingAll(6),
                                5.width,
                                Expanded(
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        plan.name ?? '',
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: AppFontStyle.fontStyleW500(
                                            fontSize: 15,
                                            fontColor: isSelected
                                                ? AppColors.appRedColor
                                                : AppColors.black),
                                      ).paddingOnly(bottom: 6),
                                      Text(
                                        plan.description ?? '',
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: AppFontStyle.fontStyleW500(
                                            fontSize: 11,
                                            fontColor: AppColors
                                                .popularProductText
                                                .withValues(alpha: 0.7)),
                                      ).paddingOnly(bottom: 8),
                                      Row(
                                        children: [
                                          Image.asset(
                                            AppAsset.adsFillIcon,
                                            height: 18,
                                            width: 18,
                                          ).paddingOnly(right: 5),
                                          Text(
                                            "${plan.advertisementLimit} Ads",
                                            style: AppFontStyle.fontStyleW500(
                                                fontSize: 11,
                                                fontColor: AppColors
                                                    .popularProductText),
                                          ).paddingOnly(right: 16),
                                          Image.asset(
                                            AppAsset.calenderFillIcon,
                                            height: 18,
                                            width: 18,
                                          ).paddingOnly(right: 5),
                                          Text(
                                            "${plan.days} Days",
                                            style: AppFontStyle.fontStyleW500(
                                                fontSize: 11,
                                                fontColor: AppColors
                                                    .popularProductText),
                                          ).paddingOnly(right: 16),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                                6.width,
                                Container(
                                  padding: EdgeInsets.symmetric(
                                      horizontal: 11, vertical: 4),
                                  decoration: BoxDecoration(
                                      color: AppColors.lightRed100
                                          .withValues(alpha: 0.5),
                                      borderRadius: BorderRadius.circular(8)),
                                  child: Text(
                                    '\$${plan.price}',
                                    style: AppFontStyle.fontStyleW700(
                                        fontSize: 18,
                                        fontColor: AppColors.appRedColor),
                                  ),
                                ).paddingOnly(right: 14)
                              ],
                            ),
                          ),
                        );
                      },
                    )
            ],
          );
        });
  }
}

class FeaturedAdsBottomButton extends StatelessWidget {
  const FeaturedAdsBottomButton({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
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
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          GetBuilder<FeaturedAdsScreenController>(builder: (controller) {
            return PrimaryAppButton(
              text: EnumLocale.txtSelectProduct.name.tr,
              height: 54,
              onTap: () {
                // controller.selectedIndex = -1;
                // controller.selectedPaymentMethod = -1;
                // controller.update();

                if (controller.selectedIndex == -1) {
                  Utils.showToast(Get.context!, 'Please select a plan first');
                  return;
                }
                //
                // Get.bottomSheet(
                //   PaymentOptionBottomSheet(),
                //   isScrollControlled: true,
                //   backgroundColor: Colors.transparent, // or keep AppColors.white if you're not wrapping in Container
                // );

                Get.toNamed(AppRoutes.featuredAdsShowScreen, arguments: {
                  "plan": controller.featuredAdsPlan[controller.selectedIndex]
                      .advertisementLimit
                });
              },
            ).paddingSymmetric(vertical: 12, horizontal: 16);
          }),
        ],
      ),
    );
  }
}

// class PaymentOptionBottomSheet extends StatelessWidget {
//   const PaymentOptionBottomSheet({
//     super.key,
//   });
//
//   @override
//   Widget build(BuildContext context) {
//     return Container(
//       decoration: BoxDecoration(
//         color: AppColors.white,
//         borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
//       ),
//       padding: const EdgeInsets.all(16.0),
//       child: GetBuilder<FeaturedAdsScreenController>(
//         // id: Constant.onChangePaymentMethod,
//         builder: (controller) {
//           return Column(
//             crossAxisAlignment: CrossAxisAlignment.start,
//             mainAxisSize: MainAxisSize.min,
//             children: [
//               Text(
//                 "Payment",
//                 style: AppFontStyle.fontStyleW600(fontSize: 17, fontColor: AppColors.black),
//               ).paddingOnly(bottom: 15, top: 5),
//
//               // if (Database.settingApiResponseModel?.data?.enableRazorpay == true)
//               PaymentOptionTile(
//                 index: 0,
//                 title: "Razorpay",
//                 controller: controller,
//                 image: AppAsset.razorPay,
//               ),
//               // if (Database.settingApiResponseModel?.data?.enableStripe == true)
//               PaymentOptionTile(
//                 index: 1,
//                 title: "Stripe",
//                 controller: controller,
//                 image: AppAsset.stripe,
//               ),
//               // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true)
//               PaymentOptionTile(
//                 index: 2,
//                 title: "Flutterwave",
//                 controller: controller,
//                 image: AppAsset.flutterWave,
//               ),
//               // if (Database.settingApiResponseModel?.data?.enableGooglePlay == true)
//               // PaymentOptionTile(
//               //   index: 3,
//               //   title: "In App Purchase",
//               //   controller: controller,
//               //   image: AppAsset.deleteVideoImage,
//               //   width: 50,
//               //   height: 26,
//               //   // width: Platform.isIOS == false ? 60 : 50,
//               // ),
//
//               PrimaryAppButton(
//                 // onTap: () {
//                 //   // Utils.showLog("message ${controller.featuredAdsPlan[index].id}");
//                 //   // log("message ${controller.selectedCoinPlan?.productId}");
//                 //
//                 //   //
//                 //
//                 //   // controller.onClickPayNow(
//                 //   //   packageType: 'FeatureAdPackage',
//                 //   //   id: controller.featuredAdsPlan[index].id ?? '',
//                 //   //   amount: controller.featuredAdsPlan[index].price ?? 0,
//                 //   // );
//                 // },
//
//                 onTap: () {
//                   if (controller.selectedIndex == -1) {
//                     Utils.showToast(context, 'Please select a plan first');
//                     return;
//                   }
//
//                   final selectedPlan = controller.featuredAdsPlan[controller.selectedIndex];
//                   Utils.showLog(controller.selectedIndex.toString());
//                   Utils.showLog('selectedPlan.id  ${selectedPlan.id}');
//                   Utils.showLog('selectedPlan.price  ${selectedPlan.price}');
//
//                   controller.onClickPayNow(
//                     packageType: 'FeatureAdPackage',
//                     id: selectedPlan.id ?? '',
//                     amount: selectedPlan.finalPrice ?? 0,
//                   );
//                 },
//                 height: 50,
//                 borderRadius: 30,
//                 text: 'Pay',
//                 textStyle: AppFontStyle.fontStyleW600(fontSize: 16, fontColor: AppColors.white),
//               ).paddingOnly(bottom: 10, top: 18),
//             ],
//           );
//         },
//       ),
//     );
//   }
// }

class PaymentOptionBottomSheet extends StatelessWidget {
  const PaymentOptionBottomSheet({
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
      ),
      // padding: const EdgeInsets.all(16.0),
      child: GetBuilder<FeaturedAdsScreenController>(
        // id: Constant.onChangePaymentMethod,
        builder: (controller) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Stack(
                clipBehavior: Clip.none,
                children: [
                  Container(
                    decoration: BoxDecoration(
                      borderRadius:
                          const BorderRadius.vertical(top: Radius.circular(30)),
                      color: AppColors.paymentTxtBgColor,
                    ),
                    child: Center(
                      child: Text(
                        EnumLocale.txtSelectPaymentMethod.name.tr,
                        style: AppFontStyle.fontStyleW600(
                            fontSize: 17, fontColor: AppColors.black),
                      ).paddingSymmetric(vertical: 18),
                    ),
                  ),
                  Positioned(
                    right: 0,
                    top: 0,
                    bottom: 0,
                    child: GestureDetector(
                      onTap: () {
                        Get.back();
                      },
                      child: Image.asset(
                        AppAsset.closePayment,
                        width: 34,
                        height: 34,
                      ).paddingOnly(right: 18),
                    ),
                  ),
                ],
              ),

              // if (Database.settingApiResponseModel?.data?.enableRazorpay == true)
              PaymentOptionTile(
                index: 0,
                title: EnumLocale.txtRazorpay.name.tr,
                controller: controller,
                image: AppAsset.razorPay,
                height: 26,
                width: 26,
              ),
              Divider(
                color: AppColors.paymentDividerColor,
                height: 0,
              ),
              // if (Database.settingApiResponseModel?.data?.enableStripe == true)
              PaymentOptionTile(
                index: 1,
                title: EnumLocale.txtStripe.name.tr,
                controller: controller,
                image: AppAsset.stripe,
                height: 30,
                width: 30,
              ),
              Divider(
                color: AppColors.paymentDividerColor,
                height: 0,
              ),
              // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true)
              PaymentOptionTile(
                index: 2,
                title: EnumLocale.txtFlutterWave.name.tr,
                controller: controller,
                image: AppAsset.flutterWave,
                height: 23,
                width: 28,
              ),
              Divider(
                color: AppColors.paymentDividerColor,
                height: 0,
              ),
              PaymentOptionTile(
                index: 3,
                title: EnumLocale.txtPayStack.name.tr,
                controller: controller,
                image: AppAsset.payStackImage,
                height: 22,
                width: 22,
              ),
              Divider(
                color: AppColors.paymentDividerColor,
                height: 0,
              ),

              PaymentOptionTile(
                index: 4,
                title: EnumLocale.txtPhonePe.name.tr,
                controller: controller,
                image: AppAsset.phonePayImage,
                height: 28,
                width: 28,
              ),

              Divider(
                color: AppColors.paymentDividerColor,
                height: 0,
              ),

              // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true)
              PaymentOptionTile(
                index: 5,
                title: EnumLocale.txtPayPal.name.tr,
                controller: controller,
                image: AppAsset.payPalIcon,
                height: 28,
                width: 28,
              ),




              Divider(
                color: AppColors.paymentDividerColor,
                height: 0,
              ),

              // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true)
              PaymentOptionTile(
                index: 6,
                title: EnumLocale.txtInAppPurchase.name.tr,
                controller: controller,
                image: AppAsset.inAppPurchaseIcon,
                height: 28,
                width: 28,
              ),

              Divider(
                color: AppColors.paymentDividerColor,
                height: 0,
              ),

              // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true)
              PaymentOptionTile(
                index: 7,
                title: EnumLocale.txtCashFree.name.tr,
                controller: controller,
                image: AppAsset.cashFreeIcon,
                height: 28,
                width: 28,
              ),

            ],
          );
        },
      ),
    );
  }
}



class PaymentOptionTile extends StatelessWidget {
  final int index;
  final double? width;
  final double? height;
  final String title;
  final String image;
  final FeaturedAdsScreenController controller;

  const PaymentOptionTile({
    super.key,
    required this.index,
    required this.title,
    required this.image,
    required this.controller,
    this.width,
    this.height,
  });

  @override
  Widget build(BuildContext context) {
    final isSelected = controller.selectedPaymentMethod == index;

    return InkWell(
      onTap: () => controller.onChangePaymentMethod(index),
      child: Row(
        children: [
          Container(
            height: 46,
            width: 46,
            decoration: BoxDecoration(
              color: AppColors.paymentImageBgColor,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Center(
              child: Image.asset(
                image,
                width: width ?? 50,
                height: height ?? 50,
                fit: BoxFit.contain,
              ),
            ),
          ),
          Text(
            title,
            style: AppFontStyle.fontStyleW400(
                fontSize: 17, fontColor: AppColors.black),
          ).paddingOnly(left: 16),
          const Spacer(),


          isSelected
              ? Container(
                  height: 24,
                  width: 24,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.redColor,
                  ),
                  child: Center(
                    child: Image.asset(
                      AppAsset.whiteRight,
                      height: 16,
                      width: 16,
                    ),
                  ),
                )
              : Container(
                  height: 24,
                  width: 24,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: AppColors.unSelectPaymentBorder,
                    ),
                  ),
                )
        ],
      ).paddingSymmetric(horizontal: 20, vertical: 12),
    );
  }
}

class ProductShowFeature extends StatelessWidget {
  const ProductShowFeature({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<FeaturedAdsShowScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        return controller.isLoading
            ? ProductGridViewShimmer().paddingOnly(top: 20)
            : controller.allAdsList.isEmpty
                ? NoDataFound(
                    image: AppAsset.noProductFound,
                    imageHeight: 180,
                    text: "No Data Found",
                  )
                : GridView.builder(
                    shrinkWrap: true,
                    padding: EdgeInsets.only(top: 20),
                    physics: const AlwaysScrollableScrollPhysics(),
                    itemCount: controller.allAdsList.length,
                    itemBuilder: (context, index) {
                      final product = controller.allAdsList[index];
                      final isSelected =
                          controller.isSelected(product.id.toString());

                      return GestureDetector(
                          onTap: () {
                            controller.toggleSelection(product.id.toString());
                          },
                          child: SelectProductGridView(
                            isSelect: isSelected,
                            productImage: product.primaryImage,
                            newPrice: product.price.toString(),
                            productName: product.title,
                          ));
                    },
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 0.86,
                      crossAxisSpacing: 6,
                      mainAxisSpacing: 6,
                    ),
                  ).paddingOnly(left: 15, right: 15);
      },
    );
  }
}

class FeatureProductAppBar extends StatelessWidget {
  final String? title;

  const FeatureProductAppBar({super.key, this.title});

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

class BottomButton extends StatelessWidget {
  const BottomButton({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<FeaturedAdsShowScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        final int requiredCount = (controller.plan ?? 0).toInt();
        final int selectedCount = controller.selectedIds.length;

        // enable ત્યારે જ: selected count == requiredCount અને total > 0
        final bool canPay = requiredCount > 0 &&
            selectedCount == requiredCount &&
            controller.selectedTotal > 0;

        // ₹ ⇄ $ બદલી લો જો dollar જોઈતી હોય
        final String amountText = "${Database.settingApiResponseModel?.data?.currency?.symbol}${controller.selectedTotalString}";

        return SafeArea(
          top: false,
          child: Container(
            color: Colors.white,
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 12), // side gap + home indicator safe
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: canPay
                    ? () {
                  Get.bottomSheet(
                    PaymentOptionBottomSheet(),
                    isScrollControlled: true,
                    backgroundColor: Colors.transparent,
                  );
                }
                    : null, // disabled state (grey) – image જેવી feel
                style: ElevatedButton.styleFrom(
                  elevation: 0, // flat look (image처럼)
                  backgroundColor: AppColors.appRedColor,
                  disabledBackgroundColor: AppColors.lightGrey,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14), // image-like radius
                  ),
                ),
                child: Text(
                  "Pay $amountText",
                  style: const TextStyle(fontSize: 16, color: Colors.white),
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}

