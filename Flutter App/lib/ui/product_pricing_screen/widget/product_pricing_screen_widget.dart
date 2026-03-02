import 'package:cool_dropdown/cool_dropdown.dart';
import 'package:cool_dropdown/models/cool_dropdown_item.dart';
import 'package:flutter/cupertino.dart' hide Size;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart' hide Size;
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/switch/switch.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/ui/product_pricing_screen/controller/product_pricing_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ProductPricingScreenAppBar extends StatelessWidget {
  final String? title;
  const ProductPricingScreenAppBar({super.key, this.title});

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

class ProductPricingScreenWidget extends StatelessWidget {
  const ProductPricingScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductPricingScreenController>(builder: (controller) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            EnumLocale.txtEnterProductDetail.name.tr,
            style: AppFontStyle.fontStyleW700(
                fontSize: 18, fontColor: AppColors.appRedColor),
          ).paddingOnly(top: 18),
          Text(
            EnumLocale.txtEnterProductDetailTxt.name.tr,
            style: AppFontStyle.fontStyleW500(
                fontSize: 12, fontColor: AppColors.searchText),
          ).paddingOnly(top: 6, bottom: 18),
          // buy it now
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    EnumLocale.txtBuyItNow.name.tr,
                    style: AppFontStyle.fontStyleW800(
                        fontSize: 18, fontColor: AppColors.black),
                  ),
                  Text(
                    EnumLocale
                        .txtBuyerCanPurchaseImmediatelyAtThisPrice.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 13, fontColor: AppColors.searchText),
                  ),
                ],
              ),
              GetBuilder<ProductPricingScreenController>(
                id: Constant.switchUpdate,
                builder: (controller) {
                  return CustomSwitchView(
                    value: controller.buyNowSwitch,
                    onChanged: (val) => controller.toggleBuyNowSwitch(val),
                  );
                },
              ),
            ],
          ).paddingOnly(bottom: 21),
          GetBuilder<ProductPricingScreenController>(
              id: Constant.switchUpdate,
              builder: (controller) {
                return controller.buyNowSwitch == true
                    ? Column(
                        children: [
                          // item price
                          Container(
                            color: AppColors.adScreenBgColor,
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  EnumLocale.txtItemPrice.name.tr,
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 14,
                                    fontColor: AppColors.black,
                                  ),
                                ),
                                110.width,
                                Expanded(
                                  child: Container(
                                    height: 46,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(14),
                                      border: Border.all(
                                          color: AppColors.borderColor),
                                    ),
                                    child: Row(
                                      children: [
                                        Container(
                                          padding: EdgeInsets.symmetric(
                                              horizontal: 15),
                                          height: double.infinity,
                                          decoration: BoxDecoration(
                                            borderRadius:
                                                BorderRadius.horizontal(
                                                    left: Radius.circular(14)),
                                            color: AppColors.lightRed100
                                                .withValues(alpha: 0.5),
                                          ),
                                          alignment: Alignment.center,
                                          child: Text(
                                            Database.settingApiResponseModel
                                                    ?.data?.currency?.symbol ??
                                                "\$",
                                            style: AppFontStyle.fontStyleW700(
                                              fontSize: 18,
                                              fontColor: AppColors.appRedColor,
                                            ),
                                          ),
                                        ),

                                        // TextField with proper constraints
                                        Expanded(
                                          child: Container(
                                            clipBehavior: Clip.hardEdge,
                                            decoration: BoxDecoration(
                                                borderRadius: BorderRadius.only(
                                                    bottomRight:
                                                        Radius.circular(12),
                                                    topRight:
                                                        Radius.circular(12))),
                                            child: TextField(
                                              controller: controller
                                                  .itemPriceController,
                                              onChanged: (val) {
                                                controller.buyNowPrice = val;
                                                controller
                                                    .calculateFinalPrice();
                                              },
                                              style: AppFontStyle.fontStyleW700(
                                                fontSize: 18,
                                                fontColor:
                                                    AppColors.appRedColor,
                                              ),
                                              maxLines: 1,
                                              keyboardType:
                                                  TextInputType.number,
                                              inputFormatters: [
                                                FilteringTextInputFormatter
                                                    .digitsOnly
                                              ],
                                              cursorColor:
                                                  AppColors.appRedColor,
                                              decoration: InputDecoration(
                                                contentPadding:
                                                    EdgeInsets.symmetric(
                                                        horizontal: 12),
                                                border: InputBorder.none,
                                                filled: true,
                                                fillColor: AppColors.white,
                                              ),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                )
                              ],
                            ).paddingSymmetric(horizontal: 18, vertical: 6),
                          ).paddingOnly(bottom: 6),
                          // offer rate
                          Container(
                            color: AppColors.adScreenBgColor,
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  EnumLocale.txtOffersRate.name.tr,
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 14,
                                    fontColor: AppColors.black,
                                  ),
                                ),
                                _buildLabelWithDropdown(
                                  value: controller.selectedPercentage ?? "",
                                  items: controller.percentage,
                                  selectedValue: controller.selectedPercentage,
                                  onChanged: (value) {
                                    controller.selectedPercentage = value;
                                    controller.calculateFinalPrice();
                                  },
                                  dropDownController:
                                      controller.percentageDropDownController,
                                  dropdownItems:
                                      controller.percentageDropdownItems,
                                  arrowColor: AppColors.appRedColor,
                                ),
                              ],
                            ).paddingSymmetric(horizontal: 18, vertical: 6),
                          ).paddingOnly(bottom: 6),
                          // final price
                          Container(
                            color: AppColors.adScreenBgColor,
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  EnumLocale.txtFinalPrice.name.tr,
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 14,
                                    fontColor: AppColors.black,
                                  ),
                                ),
                                110.width,
                                Expanded(
                                  child: Container(
                                    height: 46,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(14),
                                      border: Border.all(
                                          color: AppColors.borderColor),
                                    ),
                                    child: Row(
                                      children: [
                                        Container(
                                          padding: EdgeInsets.symmetric(
                                              horizontal: 15),
                                          height: double.infinity,
                                          decoration: BoxDecoration(
                                            borderRadius:
                                                BorderRadius.horizontal(
                                                    left: Radius.circular(14)),
                                            color: AppColors.lightRed100
                                                .withValues(alpha: 0.5),
                                          ),
                                          alignment: Alignment.center,
                                          child: Text(
                                            Database.settingApiResponseModel
                                                    ?.data?.currency?.symbol ??
                                                "\$",
                                            style: AppFontStyle.fontStyleW700(
                                              fontSize: 18,
                                              fontColor: AppColors.appRedColor,
                                            ),
                                          ),
                                        ),

                                        // TextField with proper constraints
                                        Expanded(
                                          child: Container(
                                            decoration: BoxDecoration(
                                                color: AppColors.white,
                                                borderRadius: BorderRadius.only(
                                                    topRight:
                                                        Radius.circular(12),
                                                    bottomRight:
                                                        Radius.circular(12))),
                                            child: TextField(
                                              readOnly: true,
                                              controller: controller
                                                  .finalPriceController,
                                              style: AppFontStyle.fontStyleW700(
                                                fontSize: 18,
                                                fontColor:
                                                    AppColors.appRedColor,
                                              ),
                                              onChanged: (val) {
                                                controller.finalPrice = val;
                                              },
                                              maxLines: 1,
                                              keyboardType:
                                                  TextInputType.number,
                                              inputFormatters: [
                                                FilteringTextInputFormatter
                                                    .digitsOnly
                                              ],
                                              cursorColor:
                                                  AppColors.appRedColor,
                                              decoration: InputDecoration(
                                                contentPadding:
                                                    EdgeInsets.symmetric(
                                                        horizontal: 12),
                                                border: InputBorder.none,
                                                filled: true,
                                                fillColor: Colors.transparent,
                                              ),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                )
                              ],
                            ).paddingSymmetric(horizontal: 18, vertical: 6),
                          ).paddingOnly(bottom: 6),
                          // quantity
                          Container(
                            color: AppColors.adScreenBgColor,
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text(
                                  EnumLocale.txtQuantity.name.tr,
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 14,
                                    fontColor: AppColors.black,
                                  ),
                                ),
                                116.width,
                                Expanded(
                                  child: Container(
                                    height: 46,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(14),
                                      color: AppColors.white,
                                      border: Border.all(
                                          color: AppColors.borderColor),
                                    ),
                                    child: TextField(
                                      onChanged: (val) {
                                        controller.quantity.text = val;
                                      },
                                      style: AppFontStyle.fontStyleW700(
                                        fontSize: 18,
                                        fontColor: AppColors.appRedColor,
                                      ),
                                      controller: controller.quantity,
                                      maxLines: 1,
                                      keyboardType: TextInputType.number,
                                      inputFormatters: [
                                        FilteringTextInputFormatter.digitsOnly
                                      ],
                                      cursorColor: AppColors.appRedColor,
                                      decoration: InputDecoration(
                                        hintText: "0",
                                        hintStyle: AppFontStyle.fontStyleW400(
                                          fontSize: 18,
                                          fontColor: AppColors.lightGrey,
                                        ),
                                        contentPadding: EdgeInsets.symmetric(
                                            horizontal: 12),
                                        border: InputBorder.none,
                                        filled: true,
                                        fillColor: Colors.transparent,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ).paddingSymmetric(horizontal: 18, vertical: 6),
                          ),
                          // date picker
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      EnumLocale.txtScheduleYourListingStartTime
                                          .name.tr,
                                      style: AppFontStyle.fontStyleW700(
                                        fontSize: 15,
                                        fontColor: AppColors.black,
                                      ),
                                    ).paddingOnly(bottom: 4),
                                    Text(
                                      EnumLocale
                                          .txtChooseWhenYouWantYourListingToAppearOnEbay
                                          .name
                                          .tr,
                                      style: AppFontStyle.fontStyleW500(
                                        fontSize: 13,
                                        fontColor: AppColors.searchText,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              50.width,
                              GestureDetector(
                                onTap: () {
                                  controller.toggleSchedule(context);
                                },
                                child: Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 12, vertical: 7),
                                  decoration: BoxDecoration(
                                    color: controller.isScheduleEnabled==true?AppColors.green.withValues(alpha: 0.30):AppColors.lightRed100
                                        .withValues(alpha: 0.5),
                                    borderRadius: BorderRadius.circular(7),
                                  ),
                                  child: Text(
                                    controller.isScheduleEnabled ? "On" : "Off",
                                    style: AppFontStyle.fontStyleW500(
                                      fontSize: 15,
                                      fontColor: controller.isScheduleEnabled==true?AppColors.green:AppColors.appRedColor,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ).paddingOnly(top: 16, bottom: 10),
                          controller.isScheduleEnabled == true
                              ? GestureDetector(
                                  onTap: () async {
                                    await controller.pickDate(context);
                                  },
                                  child: Container(
                                    height: 52,
                                    width: Get.width,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(14),
                                      border: Border.all(
                                          color: AppColors.borderColor),
                                    ),
                                    child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(
                                          controller.scheduledDate == null
                                              ? 'Select date and time'
                                              : DateFormat('dd MMM yyyy')
                                                  .format(controller
                                                      .scheduledDate!),
                                          style: AppFontStyle.fontStyleW500(
                                            fontSize: 15,
                                            fontColor:
                                                controller.scheduledDate == null
                                                    ? AppColors.unSelected
                                                        .withValues(alpha: 0.4)
                                                    : AppColors.appRedColor,
                                          ),
                                        ),
                                        Image.asset(
                                          AppAsset.calenderFillIcon,
                                          color: AppColors.appRedColor,
                                          height: 20,
                                          width: 20,
                                        )
                                      ],
                                    ).paddingSymmetric(horizontal: 10),
                                  ).paddingOnly(bottom: 17),
                                )
                              : Offstage(),
                        ],
                      )
                    : SizedBox();
              }),
          // auction switch
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      EnumLocale.txtAuction.name.tr,
                      style: AppFontStyle.fontStyleW800(
                          fontSize: 18, fontColor: AppColors.black),
                    ),
                    Text(
                      EnumLocale
                          .txtSetAStartingAmountAndLetBuyersCompeteForYourItem
                          .name
                          .tr,
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 13, fontColor: AppColors.searchText),
                    ),
                  ],
                ),
              ),
              GetBuilder<ProductPricingScreenController>(
                id: Constant.switchUpdate,
                builder: (controller) {
                  return CustomSwitchView(
                    value: controller.auctionSwitch,
                    onChanged: (val) => controller.toggleAuctionSwitch(val),
                  );
                },
              ),
            ],
          ).paddingOnly(bottom: 21),
          // auction
          GetBuilder<ProductPricingScreenController>(
            id: Constant.switchUpdate,
            builder: (controller) {
              return controller.auctionSwitch == true
                  ? Column(
                      children: [
                        // start bid price
                        Container(
                          color: AppColors.adScreenBgColor,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                EnumLocale.txtStartingBid.name.tr,
                                style: AppFontStyle.fontStyleW500(
                                  fontSize: 14,
                                  fontColor: AppColors.black,
                                ),
                              ),
                              110.width,
                              Expanded(
                                child: Container(
                                  height: 46,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(14),
                                    border: Border.all(
                                        color: AppColors.borderColor),
                                  ),
                                  child: Row(
                                    children: [
                                      Container(
                                        padding: EdgeInsets.symmetric(
                                            horizontal: 15),
                                        height: double.infinity,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.horizontal(
                                              left: Radius.circular(14)),
                                          color: AppColors.lightRed100
                                              .withValues(alpha: 0.5),
                                        ),
                                        alignment: Alignment.center,
                                        child: Text(
                                          Database.settingApiResponseModel?.data
                                                  ?.currency?.symbol ??
                                              "\$",
                                          style: AppFontStyle.fontStyleW700(
                                            fontSize: 18,
                                            fontColor: AppColors.appRedColor,
                                          ),
                                        ),
                                      ),

                                      // TextField with proper constraints
                                      Expanded(
                                        child: Container(
                                          decoration: BoxDecoration(
                                              borderRadius: BorderRadius.only(
                                                  bottomRight:
                                                      Radius.circular(12),
                                                  topRight:
                                                      Radius.circular(12)),
                                              color: AppColors.white),
                                          child: TextField(
                                            controller: controller
                                                .auctionStartController,
                                            onChanged: (val) => controller
                                                .auctionStartingBid = val,
                                            style: AppFontStyle.fontStyleW700(
                                              fontSize: 18,
                                              fontColor: AppColors.appRedColor,
                                            ),
                                            maxLines: 1,
                                            keyboardType: TextInputType.number,
                                            inputFormatters: [
                                              FilteringTextInputFormatter
                                                  .digitsOnly
                                            ],
                                            cursorColor: AppColors.appRedColor,
                                            decoration: InputDecoration(
                                              contentPadding:
                                                  EdgeInsets.symmetric(
                                                      horizontal: 12),
                                              border: InputBorder.none,
                                              filled: true,
                                              fillColor: Colors.transparent,
                                            ),
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              )
                            ],
                          ).paddingSymmetric(horizontal: 18, vertical: 6),
                        ).paddingOnly(bottom: 6),

                        // days duration  🔁 changed to INT-based dropdown
                        Container(
                          color: AppColors.adScreenBgColor,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      EnumLocale.txtDuration.name.tr,
                                      style: AppFontStyle.fontStyleW700(
                                        fontSize: 15,
                                        fontColor: AppColors.black,
                                      ),
                                    ),
                                    Text(
                                      EnumLocale
                                          .txtIfYourItemDoesntSellWeLlRelistItUpTo8TimesForFree
                                          .name
                                          .tr,
                                      style: AppFontStyle.fontStyleW500(
                                        fontSize: 13,
                                        fontColor: AppColors.searchText,
                                      ),
                                    ),
                                  ],
                                ).paddingOnly(right: 4),
                              ),
                              20.width,
                              _buildDurationDropdownInt(
                                width: 80,
                                items: controller.durationOptions, // List<int>
                                selectedValue:
                                    controller.adsData?.auctionDurationDays ??
                                        controller.selectedDurationDays, // int?
                                dropDownController: controller
                                    .durationDropDownController, // DropdownController<int>
                                dropdownItems: controller
                                    .durationDropdownItems, // List<CoolDropdownItem<int>>
                                arrowColor: AppColors.appRedColor,
                                onChanged: (int? v) {
                                  controller.selectedDurationDays = v;
                                  controller.update([Constant.switchUpdate]);
                                },
                              ),
                            ],
                          ).paddingSymmetric(horizontal: 18, vertical: 12),
                        ).paddingOnly(bottom: 6),

                        // quantity
                        Container(
                          color: AppColors.adScreenBgColor,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                EnumLocale.txtQuantity.name.tr,
                                style: AppFontStyle.fontStyleW500(
                                  fontSize: 14,
                                  fontColor: AppColors.black,
                                ),
                              ),
                              116.width,
                              Expanded(
                                child: Container(
                                  height: 46,
                                  decoration: BoxDecoration(
                                    color: AppColors.white,
                                    borderRadius: BorderRadius.circular(14),
                                    border: Border.all(
                                        color: AppColors.borderColor),
                                  ),
                                  child: TextField(
                                    onChanged: (val) {
                                      controller.quantity.text = val;
                                    },
                                    style: AppFontStyle.fontStyleW700(
                                      fontSize: 18,
                                      fontColor: AppColors.appRedColor,
                                    ),
                                    controller: controller.quantity,
                                    maxLines: 1,
                                    keyboardType: TextInputType.number,
                                    inputFormatters: [
                                      FilteringTextInputFormatter.digitsOnly
                                    ],
                                    cursorColor: AppColors.appRedColor,
                                    decoration: InputDecoration(
                                      hintText: "0",
                                      hintStyle: AppFontStyle.fontStyleW400(
                                        fontSize: 18,
                                        fontColor: AppColors.lightGrey,
                                      ),
                                      contentPadding:
                                          EdgeInsets.symmetric(horizontal: 12),
                                      border: InputBorder.none,
                                      filled: true,
                                      fillColor: Colors.transparent,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ).paddingSymmetric(horizontal: 18, vertical: 6),
                        ).paddingOnly(bottom: 6),

                        // reserve price switch
                        Container(
                          color: AppColors.adScreenBgColor,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      EnumLocale.txtAddReservePrice.name.tr,
                                      style: AppFontStyle.fontStyleW700(
                                        fontSize: 15,
                                        fontColor: AppColors.black,
                                      ),
                                    ),
                                    Text(
                                      EnumLocale
                                          .txtThisIsTheLowestAmountYoullAccept
                                          .name
                                          .tr,
                                      style: AppFontStyle.fontStyleW500(
                                          fontSize: 13,
                                          fontColor: AppColors.searchText),
                                    ),
                                  ],
                                ),
                              ),
                              20.width,
                              GestureDetector(
                                onTap: () {
                                  controller.toggleReservePrice(context);
                                },
                                child: Container(
                                  width: 50,
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 7),
                                  decoration: BoxDecoration(
                                    color: controller.isReservePrice==true?AppColors.green.withValues(alpha: 0.30):AppColors.lightRed100
                                        .withValues(alpha: 0.5),
                                    borderRadius: BorderRadius.circular(7),
                                  ),
                                  child: Text(
                                    textAlign: TextAlign.center,
                                    controller.isReservePrice ? "On" : "Off",
                                    style: AppFontStyle.fontStyleW500(
                                      fontSize: 15,
                                      fontColor: controller.isReservePrice==true?AppColors.green:AppColors.appRedColor,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ).paddingSymmetric(horizontal: 18, vertical: 12),
                        ),

                        // reserve price text field
                        controller.isReservePrice == true
                            ? CustomTextField(
                                controller: controller.reservePriceController,
                                onChanged: (val) {
                                  controller.reservePrice = val;
                                  return controller.reservePrice;
                                },
                                textInputType: TextInputType.number,
                                filled: true,
                                hintText:
                                    EnumLocale.txtEnterReservePrice.name.tr,
                              ).paddingOnly(left: 12, right: 12)
                            : Offstage(),

                        // schedule date picker
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    EnumLocale.txtScheduleYourListingStartTime
                                        .name.tr,
                                    style: AppFontStyle.fontStyleW700(
                                      fontSize: 15,
                                      fontColor: AppColors.black,
                                    ),
                                  ).paddingOnly(bottom: 4),
                                  Text(
                                    EnumLocale
                                        .txtChooseWhenYouWantYourListingToAppearOnEbay
                                        .name
                                        .tr,
                                    style: AppFontStyle.fontStyleW500(
                                      fontSize: 13,
                                      fontColor: AppColors.searchText,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            50.width,
                            GestureDetector(
                              onTap: () {
                                controller.toggleSchedule(context);
                              },
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12, vertical: 7),
                                decoration: BoxDecoration(
                                  color: controller.isScheduleEnabled==true?AppColors.green.withValues(alpha: 0.30):AppColors.lightRed100
                                      .withValues(alpha: 0.5),
                                  borderRadius: BorderRadius.circular(7),
                                ),
                                child: Text(
                                  controller.isScheduleEnabled ? "On" : "Off",
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 15,
                                    fontColor: controller.isScheduleEnabled==true?AppColors.green:AppColors.appRedColor,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ).paddingOnly(top: 16, bottom: 10),
                        controller.isScheduleEnabled == true
                            ? GestureDetector(
                                onTap: () async {
                                  await controller.pickDate(context);
                                },
                                child: Container(
                                  height: 52,
                                  width: Get.width,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(14),
                                    border: Border.all(
                                        color: AppColors.borderColor),
                                  ),
                                  child: Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        controller.scheduledDate == null
                                            ? 'Select date and time'
                                            : DateFormat('dd MMM yyyy').format(
                                                controller.scheduledDate!),
                                        style: AppFontStyle.fontStyleW500(
                                          fontSize: 15,
                                          fontColor:
                                              controller.scheduledDate == null
                                                  ? AppColors.unSelected
                                                      .withValues(alpha: 0.4)
                                                  : AppColors.appRedColor,
                                        ),
                                      ),
                                      Image.asset(
                                        AppAsset.calenderFillIcon,
                                        color: AppColors.appRedColor,
                                        height: 20,
                                        width: 20,
                                      )
                                    ],
                                  ).paddingSymmetric(horizontal: 10),
                                ).paddingOnly(bottom: 17),
                              )
                            : Offstage(),
                      ],
                    )
                  : SizedBox();
            },
          )
        ],
      ).paddingOnly(left: 14, right: 14);
    });
  }
}

/// ---------- EXISTING helper (Strings) : percentage માટે યથાવત ----------
Widget _buildLabelWithDropdown({
  required String value,
  required List<String> items,
  required Function(String?) onChanged,
  required DropdownController<String> dropDownController,
  required List<CoolDropdownItem<String>> dropdownItems,
  required String? selectedValue,
  required Color arrowColor,
  double? width,
}) {
  // Build dropdown items only if empty
  if (dropdownItems.isEmpty) {
    dropdownItems.addAll(
      items.map(
        (e) => CoolDropdownItem<String>(
          label: e,
          value: e,
          icon: const SizedBox(height: 25, width: 25),
        ),
      ),
    );
  }

  // Set default selected item
  final defaultItem = (selectedValue != null && items.contains(selectedValue))
      ? dropdownItems[items.indexOf(selectedValue)]
      : dropdownItems.first;

  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      CoolDropdown<String>(
        controller: dropDownController,
        dropdownList: dropdownItems,
        defaultItem: defaultItem,
        onChange: (v) {
          onChanged(v);
          dropDownController.close(); // close dropdown after selection
        },
        resultOptions: ResultOptions(
          width: width ?? 75,
          height: 40,
          render: ResultRender.label,
          icon: SizedBox(
            width: 10,
            height: 10,
            child: CustomPaint(
              painter: DropdownArrowPainter(color: arrowColor),
            ),
          ),
          boxDecoration: BoxDecoration(
            color: AppColors.lightRed100.withValues(alpha: 0.5),
            borderRadius: BorderRadius.circular(10),
          ),
          openBoxDecoration: BoxDecoration(
            color: AppColors.lightRed100.withValues(alpha: 0.5),
            borderRadius: BorderRadius.circular(10),
          ),
          textStyle: AppFontStyle.fontStyleW600(
              fontColor: AppColors.appRedColor, fontSize: 15),
        ),
        dropdownOptions: DropdownOptions(
          width: 90,
          top: 10,
          color: AppColors.white,
          align: DropdownAlign.right,
          animationType: DropdownAnimationType.scale,
          curve: Curves.bounceInOut,
        ),
        dropdownItemOptions: DropdownItemOptions(
          textStyle: AppFontStyle.fontStyleW500(
              fontColor: AppColors.unSelected, fontSize: 14),
          selectedTextStyle: AppFontStyle.fontStyleW700(
              fontColor: AppColors.appRedColor, fontSize: 14),
          selectedBoxDecoration:
              BoxDecoration(color: AppColors.white.withValues(alpha: .08)),
          padding: const EdgeInsets.only(left: 20),
          selectedPadding: const EdgeInsets.only(left: 20),
        ),
      ),
    ],
  );
}

/// ---------- NEW helper (INT-based) : duration માટે ----------
Widget _buildDurationDropdownInt({
  required List<int> items,
  required int? selectedValue,
  required void Function(int?) onChanged,
  required DropdownController<int> dropDownController,
  required List<CoolDropdownItem<int>> dropdownItems,
  required Color arrowColor,
  double? width,
}) {
  // Build dropdown items only once
  if (dropdownItems.isEmpty) {
    dropdownItems.addAll(
      items.map(
        (d) => CoolDropdownItem<int>(
          label: d == 1 ? '1 day' : '$d days', // UI label
          value: d, // int value
          icon: const SizedBox(height: 25, width: 25),
        ),
      ),
    );
  }

  // Resolve default item
  final defaultItem = (selectedValue != null && items.contains(selectedValue))
      ? dropdownItems[items.indexOf(selectedValue)]
      : dropdownItems.first;

  return CoolDropdown<int>(
    controller: dropDownController,
    dropdownList: dropdownItems,
    defaultItem: defaultItem,
    onChange: (v) {
      onChanged(v);
      dropDownController.close(); // close after selection
    },
    resultOptions: ResultOptions(
      width: width ?? 80,
      height: 35,
      render: ResultRender.label,
      icon: SizedBox(
        width: 10,
        height: 10,
        child: CustomPaint(
          painter: DropdownArrowPainter(color: arrowColor),
        ),
      ),
      boxDecoration: BoxDecoration(
        color: AppColors.lightRed100.withValues(alpha: 0.5),
        borderRadius: BorderRadius.circular(10),
      ),
      openBoxDecoration: BoxDecoration(
        color: AppColors.lightRed100.withValues(alpha: 0.5),
        borderRadius: BorderRadius.circular(10),
      ),
      textStyle: AppFontStyle.fontStyleW600(
          fontColor: AppColors.appRedColor, fontSize: 15),
    ),
    dropdownOptions: DropdownOptions(
      width: 90,
      top: 10,
      color: AppColors.white,
      align: DropdownAlign.right,
      animationType: DropdownAnimationType.scale,
      curve: Curves.bounceInOut,
    ),
    dropdownItemOptions: DropdownItemOptions(
      textStyle: AppFontStyle.fontStyleW500(
          fontColor: AppColors.unSelected, fontSize: 14),
      selectedTextStyle: AppFontStyle.fontStyleW700(
          fontColor: AppColors.appRedColor, fontSize: 14),
      selectedBoxDecoration:
          BoxDecoration(color: AppColors.white.withValues(alpha: .08)),
      padding: const EdgeInsets.only(left: 20),
      selectedPadding: const EdgeInsets.only(left: 20),
    ),
  );
}

class CustomSwitchView extends StatelessWidget {
  final bool value;
  final ValueChanged<bool> onChanged;

  const CustomSwitchView({
    super.key,
    required this.value,
    required this.onChanged,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CommonCupertinoSwitch(
          value: value,
          onChanged: onChanged,
          activeColor: CupertinoColors.activeGreen,
          trackColor: AppColors.switchColor,
          scale: 0.9,
        ),
      ],
    );
  }
}

class SaveChangeButton extends StatelessWidget {
  const SaveChangeButton({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        GetBuilder<ProductPricingScreenController>(
            id: Constant.switchUpdate,
            builder: (controller) {
              // if (controller.adListing) {
              //   /// Loader show
              //   return Center(
              //     child: CircularProgressIndicator(
              //       color: AppColors.appRedColor,
              //     ),
              //   ).paddingOnly(bottom: 20);
              // }
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
                    // GetBuilder<ProductPricingScreenController>(
                    //     id: Constant.switchUpdate,
                    //     builder: (controller) {
                    //       return controller.editListing
                    //           ? Container(
                    //               color: AppColors.white,
                    //               height: 60,
                    //               width: Get.width,
                    //               child: Center(
                    //                   child: CircularProgressIndicator(
                    //                 color: AppColors.appRedColor,
                    //               )))
                    //           : PrimaryAppButton(
                    //         color: (controller.quantity.text.toString().isEmpty || controller.quantity.text.toString() == '0')?AppColors.grey.withValues(alpha: 0.40):AppColors.appRedColor,
                    //               text: EnumLocale.txtChangeSaved.name.tr,
                    //               height: 54,
                    //               onTap: () {
                    //
                    //                 if(Database.demoUser==true){
                    //                   Utils.showLog("This is demo app");
                    //                 }else{
                    //                 Utils.showLog('////////////////////////');
                    //
                    //                 if (controller.isEdit == true) {
                    //                   Utils.showLog('edit api calllllll');
                    //                   controller.editApiCall();
                    //                 } else {
                    //                   Utils.showLog('add api callllllll');
                    //                   controller.submitListing();
                    //                 }}
                    //               },
                    //             ).paddingSymmetric(
                    //               vertical: 12, horizontal: 16);
                    //     }),

                GetBuilder<ProductPricingScreenController>(
                id: Constant.switchUpdate,
                  builder: (controller) {
                    // 🔍 Button inactive logic
                    bool isInactive = (controller.isScheduleEnabled && controller.scheduledDate == null) ||
                        (controller.quantity.text.isEmpty || controller.quantity.text == '0');

                    return controller.editListing
                        ? Container(
                      color: AppColors.white,
                      height: 60,
                      width: Get.width,
                      child: Center(
                        child: CircularProgressIndicator(
                          color: AppColors.appRedColor,
                        ),
                      ),
                    )
                        : PrimaryAppButton(
                      color: isInactive
                          ? AppColors.grey.withValues(alpha: 0.4)
                          : AppColors.appRedColor,
                      text: EnumLocale.txtChangeSaved.name.tr,
                      height: 54,
                      onTap: () {
                        if (Database.demoUser == true) {
                          Utils.showLog("This is demo app");
                          return;
                        }

                        // 🔒 Validation check
                        if (controller.isScheduleEnabled && controller.scheduledDate == null) {
                          Utils.showToast(Get.context!, "Please enter Date");
                          return;
                        }

                        if (controller.quantity.text.isEmpty || controller.quantity.text == '0') {
                          Utils.showToast(Get.context!, "Please enter product quantity");
                          return;
                        }

                        // 🧩 Proceed with API
                        if (controller.isEdit) {
                          controller.editApiCall();
                        } else {
                          controller.submitListing();
                        }
                      },
                    ).paddingSymmetric(vertical: 12, horizontal: 16);
                  },
                ),

                ],
                ),
              );
            }),
      ],
    );
  }
}
