import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/timer_widget/timer_widget.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class CustomBidDialog extends StatelessWidget {
  final String title;
  final String price;
  final String? oldPrice;
  final String primaryImage;
  final String locationText;
  final String? auctionEnd;
  final String? lastBidAmount;
  final String? description;
  final ProductDetailScreenController controller;

  const CustomBidDialog({
    super.key,
    required this.title,
    required this.price,
    required this.primaryImage,
    required this.locationText,
    this.oldPrice,
    this.auctionEnd,
    this.lastBidAmount,
    this.description,
    required this.controller,
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      child: Material(
        shape: const SquircleBorder(radius: BorderRadius.all(Radius.circular(58))),
        color: AppColors.white,
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                children: [
                  GestureDetector(
                    onTap: Get.back,
                    child: Container(
                      height: 44,
                      width: 44,
                      decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.categoriesBgColor),
                      child: Padding(
                        padding: const EdgeInsets.all(7),
                        child: Image.asset(AppAsset.backArrowIcon, color: AppColors.black),
                      ),
                    ),
                  ).paddingOnly(right: 18),
                  Text(EnumLocale.txtAskBid.name.tr, style: AppFontStyle.fontStyleW500(fontSize: 22, fontColor: AppColors.black)),
                ],
              ).paddingOnly(bottom: 18),

              // Product row (dynamic data)
              Container(
                decoration: BoxDecoration(
                    color: Color(0xffF9F9FC), borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.redColorBorder)),
                child: Row(
                  children: [
                    Container(
                      height: 120,
                      width: 110,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(14)),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(14),
                        child: CustomImageView(image: primaryImage, fit: BoxFit.cover),
                      ),
                    ).paddingOnly(right: 12, left: 5, top: 5, bottom: 5),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          Text(title, overflow: TextOverflow.ellipsis, style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black))
                              .paddingOnly(bottom: 4),
                          Row(
                            children: [
                              Text("${Database.settingApiResponseModel?.data?.currency?.symbol}$price",
                                  style: AppFontStyle.fontStyleW900(fontSize: 16, fontColor: AppColors.redColor)),
                            ],
                          ).paddingOnly(bottom: 4),
                          if ((auctionEnd ?? '').isNotEmpty)
                            TimerWidget(
                              padding: EdgeInsets.symmetric(vertical: 4, horizontal: 6),
                              borderRadius: BorderRadius.circular(7),
                              endDate: "$auctionEnd",
                            ).paddingOnly(bottom: 6),
                          Row(children: [
                            Image.asset(AppAsset.locationIcon, height: 16, width: 16, color: AppColors.searchText).paddingOnly(right: 6),
                            Expanded(
                              child: Text(locationText,
                                  style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText), overflow: TextOverflow.ellipsis),
                            ),
                          ]).paddingOnly(bottom: 4),
                        ],
                      ),
                    ),
                  ],
                ),
              ).paddingOnly(bottom: 10),

              // Description (optional)
              Text(
                description ?? '',
                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.productDesColor),
              ).paddingOnly(bottom: 12),
              if ((lastBidAmount ?? '').isNotEmpty)
                Container(
                  decoration: BoxDecoration(
                    color: AppColors.lightRed100.withValues(alpha: 0.5),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    children: [
                      Image.asset(AppAsset.bidIcon, color: AppColors.appRedColor, height: 24, width: 24)
                          .paddingOnly(right: 12, left: 11, top: 11, bottom: 11),
                      Text(EnumLocale.txtLastBidAmount.name.tr, style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.appRedColor)),
                      const Spacer(),
                      Text("${Database.settingApiResponseModel?.data?.currency?.symbol} ${lastBidAmount == '0' || lastBidAmount == null ? 'No Bid' : lastBidAmount}",
                              style: AppFontStyle.fontStyleW800(fontSize: 15, fontColor: AppColors.appRedColor))
                          .paddingOnly(right: 13),
                    ],
                  ),
                ).paddingOnly(bottom: 16),

              Text(EnumLocale.txtAskBidPrice.name.tr, style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.searchText))
                  .paddingOnly(bottom: 13),

              TextField(

                style: AppFontStyle.fontStyleW600(
                  fontSize: 13,
                  fontColor: AppColors.black,
                ),
                controller: controller.bidController,
                keyboardType: const TextInputType.numberWithOptions(decimal: true, signed: false),
                inputFormatters: [
                  FilteringTextInputFormatter.allow(RegExp(r'^[0-9]*[.,]?[0-9]*$')),
                ],

                decoration: InputDecoration(
                  prefixIcon: Container(
                    padding: EdgeInsets.only(left: 9),
                    width: 0,
                    // color: Colors.red,
                    child: Center(
                      child: Text(
                        Database.settingApiResponseModel?.data?.currency?.symbol??"",
                        style: AppFontStyle.fontStyleW600(fontSize: 16, fontColor: AppColors.black),
                      ),
                    ),
                  ),
                  fillColor: AppColors.textFieldColor,
                  filled: true,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: AppColors.textFieldBorderColor)),
                  focusedBorder:
                      OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: AppColors.textFieldBorderColor)),
                  enabledBorder:
                      OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: AppColors.textFieldBorderColor)),
                  contentPadding: const EdgeInsets.only(left: 8),
                  hintStyle: AppFontStyle.fontStyleW400(fontSize: 16, fontColor: AppColors.searchText),
                ),
              ).paddingOnly(bottom: 22),

              PrimaryAppButton(
                height: 54,
                text: EnumLocale.txtSubmitBid.name.tr,
                onTap: () async {
                  final enteredText = controller.bidController.text.trim();
                  if (enteredText.isEmpty) {
                    Utils.showToast(context, "Please enter your bid amount");
                    return;
                  }

                  double parseAmount(String? s) {
                    if (s == null || s.trim().isEmpty) return 0.0;
                    final normalized = s.replaceAll(RegExp(r'[^0-9,.\-]'), '').replaceAll(',', '');
                    return double.tryParse(normalized) ?? 0.0;
                  }

                  final entered = parseAmount(enteredText);

                  final bool noLastBid = (lastBidAmount == null) || (lastBidAmount!.trim().isEmpty) || (lastBidAmount == '0');

                  final double baseToBeat = noLastBid ? parseAmount(price) : parseAmount(lastBidAmount);

                  final symbol = Database.settingApiResponseModel?.data?.currency?.symbol ?? '';
                  final String label = noLastBid ? "current price" : "last bid";
                  final String amountStr = baseToBeat.toStringAsFixed(2);

                  if (entered <= baseToBeat) {
                    Utils.showToast(
                      context,
                      "Your bid must be higher than the $label ($symbol$amountStr).",
                    );
                    return;
                  }

                  final ok = await controller.placeBidApiCall();
                  if (ok) Get.back(result: enteredText);
                },
              ),
            ],
          ).paddingAll(17),
        ),
      ),
    );
  }
}
