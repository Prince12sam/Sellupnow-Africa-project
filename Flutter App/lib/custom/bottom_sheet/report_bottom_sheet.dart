import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ReportBottomSheet extends StatelessWidget {
  final Function()? submitOnTap;
  const ReportBottomSheet({super.key, this.submitOnTap});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(
      // id: Constant.idReportReason,
      init: ProductDetailScreenController(),
      builder: (controller) {
        return Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(28),
            color: AppColors.white,
          ),
          // height: Get.height * 0.8,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              // Header
              Row(
                children: [
                  GestureDetector(
                    onTap: () {
                      Get.back();
                    },
                    child: Container(
                      height: 44,
                      width: 44,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: AppColors.categoriesBgColor,
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(7),
                        child: Image.asset(
                          AppAsset.backArrowIcon,
                          color: AppColors.black,
                        ),
                      ),
                    ),
                  ).paddingOnly(right: 18),
                  Text(
                    EnumLocale.txtReportThisAds.name.tr,
                    style: AppFontStyle.fontStyleW500(fontSize: 22, fontColor: AppColors.black),
                  ).paddingOnly(right: 44),
                  Spacer(),
                ],
              ).paddingOnly(bottom: 24, left: 16, right: 16, top: 18),

              Flexible(
                child: ListView.builder(
                  shrinkWrap: true,
                  itemCount: controller.reportReasonList.length + 1, // +1 for Other Reason
                  itemBuilder: (_, index) {
                    bool isOther = index == controller.reportReasonList.length;

                    return Padding(
                      padding: const EdgeInsets.symmetric(vertical: 6),
                      child: InkWell(
                        onTap: () {
                          if (isOther) {
                            controller.toggleOtherSelection();
                          } else {
                            controller.toggleSelection(index);
                          }
                        },
                        borderRadius: BorderRadius.circular(12),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
                          decoration: BoxDecoration(
                            border: Border.all(color: AppColors.borderColor.withValues(alpha: 0.4)),
                            color: isOther
                                ? (controller.isOtherSelected ? AppColors.lightRed100 : AppColors.reportAdContainer)
                                : (controller.selectedReasons.contains(index) ? AppColors.lightRed100 : AppColors.reportAdContainer),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  isOther ? "Other Reason" : (controller.reportReasonList[index].title ?? ''),
                                  style: AppFontStyle.fontStyleW400(
                                    fontSize: 16,
                                    fontColor: isOther
                                        ? (controller.isOtherSelected ? AppColors.appRedColor : AppColors.searchText)
                                        : (controller.selectedReasons.contains(index) ? AppColors.appRedColor : AppColors.searchText),
                                  ),
                                ),
                              ),
                              Container(
                                height: 21,
                                width: 21,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: isOther
                                        ? (controller.isOtherSelected ? AppColors.appRedColor : AppColors.grey300.withValues(alpha: 0.5))
                                        : (controller.selectedReasons.contains(index)
                                            ? AppColors.appRedColor
                                            : AppColors.grey300.withValues(alpha: 0.5)),
                                  ),
                                ),
                                child: (isOther ? controller.isOtherSelected : controller.selectedReasons.contains(index))
                                    ? Center(
                                        child: Container(
                                          decoration: const BoxDecoration(
                                            color: Colors.red,
                                            shape: BoxShape.circle,
                                          ),
                                        ).paddingAll(0.6),
                                      )
                                    : null,
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ).paddingOnly(left: 16, right: 16),
              ),

              8.height,
              // TextField and Buttons
              Container(
                decoration: BoxDecoration(
                  color: AppColors.white,
                  boxShadow: [
                    BoxShadow(
                      offset: Offset(0, -2),
                      color: AppColors.black.withValues(alpha: 0.1),
                      spreadRadius: 0,
                      blurRadius: 14,
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (controller.isOtherSelected) ...[
                      8.height,
                      Text(
                        EnumLocale.txtWriteHere.name.tr,
                        style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.popularProductText),
                      ).paddingOnly(top: 13, bottom: 8),
                      TextField(
                        controller: controller.reasonController,
                        maxLines: 3,
                        decoration: InputDecoration(
                          filled: true,
                          fillColor: AppColors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: AppColors.borderColor),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderSide: BorderSide(color: AppColors.borderColor),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: AppColors.borderColor),
                          ),
                        ),
                      ).paddingOnly(bottom: 17),
                    ],

                    // Buttons
                    Row(
                      children: [
                        Expanded(
                          child: PrimaryAppButton(
                            height: 52,
                            fontColor: AppColors.appRedColor,
                            color: AppColors.lightRed100,
                            text: EnumLocale.txtCancel.name.tr,
                          ),
                        ),
                        14.width,
                        Expanded(
                          child: PrimaryAppButton(
                            // onTap: () {
                            //   controller.adReportUserApi();
                            // },

                            onTap: submitOnTap,
                            height: 52,
                            text: EnumLocale.txtSubmit.name.tr,
                          ),
                        ),
                      ],
                    ).paddingOnly(bottom: 10, top: 17)
                  ],
                ).paddingOnly(left: 16, right: 16),
              ),
            ],
          ),
        );
      },
    );
  }
}
