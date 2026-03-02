import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/socket/socket_emit.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/socket_params.dart';
import 'package:listify/utils/utils.dart';

class MakeAnOfferBottomSheet extends StatelessWidget {
  final String name;
  final String receiverId;
  final String adId;
  final String profileImage;
  final String image;
  final String productPrice;
  final bool isOnline;
  final ProductDetailScreenController controller;
  const MakeAnOfferBottomSheet(
      {super.key,
      required this.name,
      required this.receiverId,
      required this.adId,
      required this.profileImage,
      required this.isOnline,
      required this.image,
      required this.productPrice,
      required this.controller});

  @override
  Widget build(BuildContext context) {
    TextEditingController offerPriceController = TextEditingController();

    return Container(
      // padding: EdgeInsets.symmetric(vertical: 17, horizontal: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: GestureDetector(
        onTap: () {
          FocusScopeNode currentFocus = FocusScope.of(context);
          if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
            currentFocus.focusedChild?.unfocus();
          }
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: Get.width,
              decoration: BoxDecoration(
                color: AppColors.lightGrey100,
                borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Spacer(),
                  Text(
                    EnumLocale.txtMakeAnOffer.name.tr,
                    style: AppFontStyle.fontStyleW700(
                      fontSize: 18,
                      fontColor: AppColors.black,
                    ),
                  ).paddingOnly(left: 30, bottom: 19, top: 19),
                  Spacer(),
                  InkWell(
                    onTap: () {
                      Get.back();
                    },
                    child: Image.asset(
                      AppAsset.closeFillIcon,
                      width: 30,
                    ).paddingOnly(top: 14),
                  )
                ],
              ).paddingSymmetric(horizontal: 16),
            ),
            16.height,
            Text(
              EnumLocale.txtOfferInstructions.name.tr,
              style: AppFontStyle.fontStyleW700(fontSize: 19, fontColor: AppColors.black),
            ).paddingSymmetric(horizontal: 16),
            5.height,
            Text(
              EnumLocale.txtOfferInstructionsTxt.name.tr,
              style: AppFontStyle.fontStyleW400(fontSize: 13, fontColor: AppColors.grey300),
            ).paddingSymmetric(horizontal: 16),
            22.height,
            CustomTitle(
              title: EnumLocale.txtEnterOfferPrice.name.tr,
              method: CustomTextField(
                filled: true,
                textInputType: TextInputType.number,
                borderColor: AppColors.txtFieldBorder,
                controller: offerPriceController,
                fillColor: AppColors.white,
                cursorColor: AppColors.black,
                fontColor: AppColors.black,
                fontSize: 15,
                textInputAction: TextInputAction.next,
                maxLines: 1,
              ),
            ).paddingOnly(left: 16, right: 16, bottom: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Text(
                  EnumLocale.txtSellerPrice.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
                ),
                Text(
                  ' ${Database.settingApiResponseModel?.data?.currency?.symbol}$productPrice',
                  style: AppFontStyle.fontStyleW900(fontSize: 14, fontColor: AppColors.appRedColor),
                ),
              ],
            ).paddingOnly(right: 16, bottom: 28),
            Row(
              children: [
                Expanded(
                  child: PrimaryAppButton(
                    height: 54,
                    fontColor: AppColors.appRedColor,
                    color: AppColors.lightRed100,
                    text: EnumLocale.txtCancel.name.tr,
                  ),
                ),
                20.width,
                Expanded(
                  child: PrimaryAppButton(
                    onTap: () {
                      if (offerPriceController.text.trim().isEmpty) {
                        Utils.showToast(context, "Please Enter Offer Price");

                        return;
                      }
                      Utils.showLog('make offer product name ::::: $name');
                      Utils.showLog('make offer product image ::::: $image');
                      Utils.showLog('make offer product profileImage ::::: $profileImage');
                      Utils.showLog('make offer product adId ::::: $adId');
                      Utils.showLog('make offer product receiverId ::::: $receiverId');
                      Utils.showLog('make offer product isOnline ::::: $isOnline');
                      // Utils.showLog('make offer product offerPrice ::::: ${controller.offerPriceController}');

                      final messageValue = {
                        "productName": name,
                        "productPrice": productPrice,
                        "productImage": image,
                        "offerAmount": offerPriceController.text,
                        // "ProductMessageType": 2,
                        "message": 2,
                        "productId": adId,
                        // "view": true,
                        // "adId": adId,
                      };

                      final String finalPayload = messageValue.entries.map((e) => "${e.key}: ${e.value}").join(", ");

                      Utils.showLog("finalPayload>>>>>>>>>>>>>>>>>>>>>>>>>$finalPayload");

                      final messageData = {
                        SocketParams.userId: Database.getUserProfileResponseModel?.user?.id,
                        SocketParams.adId: adId,
                        SocketParams.offerAmount: offerPriceController.text,
                        SocketParams.message: finalPayload,
                      };

                      Utils.showLog('Offer placed emit event data ::::  $messageData');

                      SocketEmit.offerPlacedMessage(messageData);

                      Get.toNamed(AppRoutes.chatDetailScreenView, arguments: {
                        'name': name,
                        'image': image,
                        'profileImage': profileImage,
                        'adId': adId,
                        'receiverId': receiverId,
                        'isOnline': isOnline,
                        'isViewed': controller.productDetail?.data?.isViewed,
                        'productPrice': controller.productDetail?.data?.price,
                        'productName': controller.productDetail?.data?.title,
                        'primaryImage': controller.productDetail?.data?.primaryImage,
                      })?.then((value) {
                        Get.back();
                        Get.back();
                      },);
                    },
                    height: 54,
                    text: EnumLocale.txtSend.name.tr,
                  ),
                ),
              ],
            ).paddingOnly(left: 16, right: 16, bottom: 10),
            2.height
          ],
        ),
      ),
    );
  }
}
