import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/ui/subscription%20_plan_screen/controller/subscription_plan_screen_controller.dart';
import 'package:listify/ui/subscription%20_plan_screen/model/subscription_plan_response_model.dart';
import 'package:listify/ui/subscription%20_plan_screen/shimmer/subscription_plan_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SubscriptionPlanScreenAppBar extends StatelessWidget {
  final String? title;
  const SubscriptionPlanScreenAppBar({super.key, this.title});

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

class SubscriptionPlanScreenWidget extends StatelessWidget {
  const SubscriptionPlanScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SubscriptionPlanScreenController>(
        id: Constant.idSubscription,
        builder: (controller) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                EnumLocale.txtSubscriptionPlan.name.tr,
                style: AppFontStyle.fontStyleW800(fontSize: 24, fontColor: AppColors.appRedColor),
              ).paddingOnly(top: 18, left: 16),
              Text(
                EnumLocale.txtSubscriptionPlanDescription.name.tr,
                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.popularProductText, height: 1.8),
              ).paddingOnly(top: 2, left: 16, right: 20),
              SizedBox(
                // color: AppColors.green,
                child: GetBuilder<SubscriptionPlanScreenController>(
                  builder: (controller) {
                    final List<SubscriptionPlan> list = controller.subscriptionPlan;
                    final bool isSingle = list.length == 1;

                    return (list.isEmpty) || controller.isLoading
                        ? SubscriptionPlanShimmer()
                        : Container(
                      // color: Colors.pink,
                          child: CarouselSlider(
                              options: CarouselOptions(
                                height: 480,
                                autoPlay: !isSingle, // disable autoPlay if only one
                                enlargeCenterPage: !isSingle,
                                viewportFraction: 0.80,

                                enableInfiniteScroll: !isSingle,
                                onPageChanged: controller.onPageChanged,
                              ),
                              items: list.asMap().entries.map((entry) {
                                final index = entry.key;
                                final data = entry.value;
                                final imageAsset = controller.imageList[index % controller.imageList.length]['image'];
                                final planColor = controller.imageList[index % controller.imageList.length]['color'];

                                return Builder(
                                  builder: (BuildContext context) {
                                    return Stack(
                                      clipBehavior: Clip.antiAlias,
                                      children: [
                                        Container(

                                          // color: AppColors.yellowStarColor,
                                          child: Image.asset(
                                            imageAsset,
                                            height: 480,
                                            width: Get.width,
                                            fit: BoxFit.fill,
                                          ),
                                        ),

                                        Positioned(
                                          left: 120,
                                          top: 5,
                                          right: 120,
                                          child: SizedBox(
                                            width: 100,
                                            child: Text(
                                              data.name?.toUpperCase() ?? '',
                                              textAlign: TextAlign.center,
                                              style: AppFontStyle.fontStyleW700(
                                                fontSize: 15,
                                                fontColor: AppColors.white,
                                              ),
                                            ),
                                          ),
                                        ),

                                        // Price
                                        Positioned(
                                          left: 0,
                                          top: 77,
                                          right: 0,
                                          child: Column(
                                            children: [
                                              Text(
                                                '${Database.settingApiResponseModel?.data?.currency?.symbol}',
                                                style: AppFontStyle.fontStyleW800(
                                                  fontSize: 19,
                                                  fontColor: planColor,
                                                  height: 0,
                                                ),
                                              ),
                                              Text(
                                                data.price.toString(),
                                                textAlign: TextAlign.center,
                                                style: AppFontStyle.fontStyleW800(
                                                  fontSize: 23,
                                                  fontColor: planColor,
                                                  height: 0,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        // Info Section
                                        Positioned(
                                          bottom: 30,
                                          left: 30,
                                          right: 30,
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                data.name ?? '',
                                                style: AppFontStyle.fontStyleW700(
                                                  fontSize: 19,
                                                  fontColor: AppColors.appRedColor,
                                                ),
                                              ),
                                              SizedBox(
                                                width: Get.width * 0.7,
                                                child: Text(
                                                  data.description ?? '',
                                                  softWrap: true,
                                                  style: AppFontStyle.fontStyleW500(
                                                    fontSize: 12,
                                                    fontColor: AppColors.popularProductText,
                                                  ),
                                                ),
                                              ).paddingOnly(bottom: 19),

                                              // Feature list
                                              Row(
                                                children: [
                                                  Image.asset(
                                                    AppAsset.verificationRightIcon,
                                                    color: AppColors.green,
                                                    height: 21,
                                                    width: 21,
                                                  ).paddingOnly(right: 8),
                                                  Text(
                                                    '${data.days?.value} ${EnumLocale.txtAdsListingFree.name.tr}',
                                                    style: AppFontStyle.fontStyleW500(
                                                      fontSize: 14,
                                                      fontColor: AppColors.popularProductText,
                                                    ),
                                                  ),
                                                ],
                                              ).paddingOnly(bottom: 14),
                                              Row(
                                                children: [
                                                  Image.asset(
                                                    AppAsset.verificationRightIcon,
                                                    color: AppColors.green,
                                                    height: 21,
                                                    width: 21,
                                                  ).paddingOnly(right: 8),
                                                  Text(
                                                    '${data.advertisements?.value} ${EnumLocale.txtDaysFreeOffCostService.name.tr}',
                                                    style: AppFontStyle.fontStyleW500(
                                                      fontSize: 14,
                                                      fontColor: AppColors.popularProductText,
                                                    ),
                                                  ),
                                                ],
                                              ).paddingOnly(bottom: 14),
                                              // Row(
                                              //   children: [
                                              //     Image.asset(
                                              //       AppAsset.verificationRightIcon,
                                              //       color: AppColors.green,
                                              //       height: 21,
                                              //       width: 21,
                                              //     ).paddingOnly(right: 8),
                                              //     Text(
                                              //       EnumLocale.txtStandardCustomerSupport.name.tr,
                                              //       style: AppFontStyle.fontStyleW500(
                                              //         fontSize: 14,
                                              //         fontColor: AppColors.popularProductText,
                                              //       ),
                                              //     ),
                                              //   ],
                                              // ).paddingOnly(bottom: 14),
                                              // Column(
                                              //   children: List.generate(3, (index) {
                                              //     return Row(
                                              //       children: [
                                              //         Image.asset(
                                              //           AppAsset.verificationRightIcon,
                                              //           color: AppColors.green,
                                              //           height: 21,
                                              //           width: 21,
                                              //         ).paddingOnly(right: 8),
                                              //         Text(
                                              //           '3 Ads Listing Free',
                                              //           style: AppFontStyle.fontStyleW500(
                                              //             fontSize: 14,
                                              //             fontColor: AppColors.popularProductText,
                                              //           ),
                                              //         ),
                                              //       ],
                                              //     ).paddingOnly(bottom: 14);
                                              //   }),
                                              // ),

                                              // Button
                                              GetBuilder<SubscriptionPlanScreenController>(builder: (controller) {
                                                return PrimaryAppButton(
                                                  onTap: () {
                                                    controller.selectPlan(index);
                                                    Get.bottomSheet(
                                                      PaymentOptionBottomSheet(),
                                                      isScrollControlled: true,
                                                      backgroundColor: Colors.transparent,
                                                    );
                                                  },
                                                  height: 50,
                                                  text: EnumLocale.txtSubscribeNow.name.tr,
                                                  width: Get.width * 0.68,
                                                ).paddingSymmetric(horizontal: 6);
                                              }),
                                            ],
                                          ),
                                        ),
                                      ],
                                    );
                                  },
                                );
                              }).toList(),
                            ).paddingOnly(top: 30),
                        );
                  },
                ),
              ),
              GetBuilder<SubscriptionPlanScreenController>(
                  id: Constant.idPlanChange,
                  builder: (context) {
                    return Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(controller.subscriptionPlan.length, (index) {
                        bool isSelected = controller.currentIndex == index;
                        return AnimatedContainer(
                          duration: const Duration(milliseconds: 300),
                          margin: const EdgeInsets.symmetric(horizontal: 2),
                          width: isSelected ? 22 : 6,
                          height: 6,
                          decoration: BoxDecoration(
                            color: isSelected ? AppColors.appRedColor : AppColors.popularProductText.withValues(alpha: 0.3),
                            borderRadius: BorderRadius.circular(4),
                          ),
                        );
                      }),
                    ).paddingOnly(bottom: 10, top: 10);
                  }),
            ],
          );
        });
  }
}

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
      child: GetBuilder<SubscriptionPlanScreenController>(
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
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
                      color: AppColors.paymentTxtBgColor,
                    ),
                    child: Center(
                      child: Text(
                        EnumLocale.txtSelectPaymentMethod.name.tr,
                        style: AppFontStyle.fontStyleW600(fontSize: 17, fontColor: AppColors.black),
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
              // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true)
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

              // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true)
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

              // if (Database.settingApiResponseModel?.data?.enableGooglePlay == true)
              // PaymentOptionTile(
              //   index: 3,
              //   title: "In App Purchase",
              //   controller: controller,
              //   image: AppAsset.deleteVideoImage,
              //   width: 50,
              //   height: 26,
              //   // width: Platform.isIOS == false ? 60 : 50,
              // ),

              // PrimaryAppButton(
              //   onTap: () {
              //     if (controller.selectedIndex == -1) {
              //       Utils.showToast(context, 'Please select a plan first');
              //       return;
              //     }
              //
              //     if (controller.selectedPaymentMethod == -1) {
              //       Utils.showToast(context, 'Please select a payment method');
              //       return;
              //     }
              //
              //     final selectedPlan = controller.subscriptionPlan[controller.selectedIndex];
              //     Utils.showLog(controller.selectedIndex.toString());
              //     Utils.showLog('selectedPlan.id  ${selectedPlan.id}');
              //     Utils.showLog('selectedPlan.price  ${selectedPlan.price}');
              //
              //     controller.onClickPayNow(
              //       packageType: 'SubscriptionPlan',
              //       id: selectedPlan.id ?? '',
              //       amount: selectedPlan.finalPrice ?? 0,
              //     );
              //   },
              //   height: 50,
              //   borderRadius: 30,
              //   text: 'Pay',
              //   textStyle: AppFontStyle.fontStyleW600(fontSize: 16, fontColor: AppColors.white),
              // ).paddingOnly(bottom: 10, top: 18),
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
  final SubscriptionPlanScreenController controller;

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
            style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
          ).paddingOnly(left: 16),
          const Spacer(),
          // Container(
          //   height: 22,
          //   width: 22,
          //   decoration: BoxDecoration(
          //     shape: BoxShape.circle,
          //     border: Border.all(
          //       color: isSelected ? Colors.transparent : AppColors.appRedColor,
          //     ),
          //     color: isSelected ? AppColors.appRedColor : AppColors.white,
          //   ),
          //   child: isSelected
          //       ? Padding(
          //           padding: const EdgeInsets.all(0.5),
          //           child: Container(
          //             decoration: BoxDecoration(
          //               shape: BoxShape.circle,
          //               color: AppColors.appRedColor,
          //               border: Border.all(color: AppColors.white),
          //             ),
          //           ),
          //         )
          //       : null,
          // ),

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
