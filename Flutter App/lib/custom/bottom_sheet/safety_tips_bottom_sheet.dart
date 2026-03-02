import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/bottom_sheet/make_an_offer_bottom_sheet.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class SafetyTipsBottomSheet extends StatelessWidget {
  final String name;
  final String receiverId;
  final String adId;
  final String profileImage;
  final String image;
  final bool isOnline;
  final String productPrice;
  final ProductDetailScreenController controller;

  const SafetyTipsBottomSheet(
      {super.key,
      required this.name,
      required this.receiverId,
      required this.adId,
      required this.profileImage,
      required this.image,
      required this.isOnline,
      required this.productPrice,
      required this.controller});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(
        id: Constant.idSafetyTips,
        builder: (controller) {
          return Container(
            // height: Get.height * 0.8,
            // padding: EdgeInsets.symmetric(vertical: 17, horizontal: 16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
            ),
            child: SingleChildScrollView(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.end,
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Row(
                    children: [
                      Spacer(),
                      InkWell(
                        onTap: () {
                          Get.back();
                        },
                        child: Image.asset(
                          AppAsset.closeIcon,
                          height: 30,
                          width: 30,
                        ).paddingOnly(right: 18, top: 18),
                      ),
                    ],
                  ),
                  Image.asset(
                    AppAsset.safetyTipsImage,
                    height: 160,
                    width: 160,
                  ).paddingOnly(bottom: 21),
                  Text(EnumLocale.txtOfferSafetyTips.name.tr, style: AppFontStyle.fontStyleW700(fontSize: 23, fontColor: AppColors.appRedColor))
                      .paddingOnly(bottom: 22),
                  ListView.builder(
                    shrinkWrap: true,
                    itemCount: controller.safetyTipsList.length,
                    physics: NeverScrollableScrollPhysics(),
                    itemBuilder: (context, index) {
                      return TipsTile(tips: controller.safetyTipsList[index].description ?? '');
                    },
                  ),
                  6.height,
                  // Spacer(),
                  PrimaryAppButton(
                    height: 54,
                    onTap: () {
                      Get.back();
                      Utils.showLog("isOnline>>>>>>>>>>>>>>>$isOnline");
                      Get.bottomSheet(
                        MakeAnOfferBottomSheet(
                          controller: controller,
                          productPrice: productPrice,
                          name: name,
                          image: image,
                          profileImage: profileImage,
                          adId: adId,
                          receiverId: receiverId,
                          isOnline: isOnline,
                        ),
                        isScrollControlled: true,
                        backgroundColor: Colors.transparent,
                        barrierColor: AppColors.black.withValues(alpha: 0.8),
                      );
                    },
                    text: EnumLocale.txtContinueToOffer.name.tr,
                  ).paddingSymmetric(horizontal: 16),
                  12.height,
                ],
              ),
            ),
          );
        });
  }
}

class TipsTile extends StatelessWidget {
  final String tips;
  const TipsTile({super.key, required this.tips});

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Image.asset(
          AppAsset.checkGreenIcon,
          height: 30,
          width: 30,
        ).paddingOnly(right: 21),
        Flexible(
          child: Text(
            tips,
            style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
          ),
        ),
      ],
    ).paddingSymmetric(horizontal: 20, vertical: 12);
  }
}
