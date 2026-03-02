import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/ui/edit_product_screen/controller/edit_product_detail_controller.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class EditProductAppBar extends StatelessWidget {
  final String? title;
  const EditProductAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
        onTap: () {
          Get.back(result: true); // Return to previous screen and trigger refresh
        },
      ),
    );
  }
}

class EditProductTopView extends StatelessWidget {
  const EditProductTopView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          EnumLocale.txtEnterProductDetail.name.tr,
          style: AppFontStyle.fontStyleW700(fontSize: 18, fontColor: AppColors.appRedColor),
        ).paddingOnly(top: 18, left: 12),
        Text(
          EnumLocale.txtEnterProductDetailTxt.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText),
        ).paddingOnly(top: 6, left: 12, right: 12, bottom: 20),
      ],
    );
  }
}

class EditProductDetailView extends StatelessWidget {
  const EditProductDetailView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<EditProductDetailController>(builder: (controller) {
      return Column(
        children: [
          CustomTitle(
            txtColor: AppColors.searchText,
            title: EnumLocale.txtProductTitle.name.tr,
            method: CustomTextField(
              filled: true,
              borderColor: AppColors.txtFieldBorder,
              controller: controller.productTitle,
              fillColor: AppColors.white,
              cursorColor: AppColors.black,
              fontColor: AppColors.black,
              fontSize: 15,
              textInputAction: TextInputAction.next,
              maxLines: 1,
            ),
          ).paddingOnly(bottom: 24),
          CustomTitle(
            txtColor: AppColors.searchText,
            title: EnumLocale.txtProductSubTitle.name.tr,
            method: CustomTextField(
              filled: true,
              borderColor: AppColors.txtFieldBorder,
              controller: controller.productSubTitle,
              fillColor: AppColors.white,
              cursorColor: AppColors.black,
              fontColor: AppColors.black,
              fontSize: 15,
              textInputAction: TextInputAction.next,
              maxLines: 1,
            ),
          ).paddingOnly(bottom: 24),
          CustomTitle(
            txtColor: AppColors.searchText,
            title: EnumLocale.txtProductPrice.name.tr,
            method: CustomTextField(
              textInputType: TextInputType.phone,
              filled: true,
              borderColor: AppColors.txtFieldBorder,
              controller: controller.productPrice,
              fillColor: AppColors.white,
              cursorColor: AppColors.black,
              fontColor: AppColors.black,
              fontSize: 15,
              textInputAction: TextInputAction.next,
              maxLines: 1,
              prefixIcon: Container(
                padding: EdgeInsets.only(left: 9),
                width: 0,
                // color: Colors.red,
                child: Center(
                  child: GetBuilder<EditProductDetailController>(builder: (controller) {
                    return Text(
                      Database.currencySymbol,
                      style: AppFontStyle.fontStyleW600(fontSize: 16, fontColor: AppColors.black),
                    );
                  }),
                ),
              ),
            ),
          ).paddingOnly(bottom: 24),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    EnumLocale.txtProductDescription.name.tr,
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 15,
                      fontColor: AppColors.searchText,
                    ),
                  ),
                  TextButton(
                    onPressed: controller.isAiLoading ? null : controller.aiAssist,
                    child: controller.isAiLoading
                        ? SizedBox(
                            width: 16,
                            height: 16,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          )
                        : Text(
                            "AI Assist",
                            style: AppFontStyle.fontStyleW600(
                              fontSize: 13,
                              fontColor: AppColors.appRedColor,
                            ),
                          ),
                  ),
                ],
              ).paddingOnly(bottom: 12, left: 5),
              CustomTextField(
                filled: true,
                borderColor: AppColors.txtFieldBorder,
                controller: controller.productDescription,
                fillColor: AppColors.white,
                cursorColor: AppColors.black,
                fontColor: AppColors.black,
                fontSize: 15,
                textInputAction: TextInputAction.done,
                maxLines: 7,
              ),
            ],
          ).paddingOnly(bottom: 24),
        ],
      ).paddingOnly(right: 16, left: 16);
    });
  }
}

class EditProductBottomBar extends StatelessWidget {
  const EditProductBottomBar({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        GetBuilder<EditProductDetailController>(builder: (controller) {
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
            child:
            GetBuilder<EditProductDetailController>(
              builder: (controller) {
                final allFilled =
                    controller.productTitle.text.trim().isNotEmpty &&
                        controller.productSubTitle.text.trim().isNotEmpty &&
                        controller.productPrice.text.trim().isNotEmpty &&
                        controller.productDescription.text.trim().isNotEmpty;

                return PrimaryAppButton(
                  color: allFilled
                      ? AppColors.appRedColor
                      : AppColors.grey.withValues(alpha: 0.4),
                  onTap: controller.editProductDetailValidation,
                  text: EnumLocale.txtNext.name.tr,
                  height: 54,
                );
              },
            ).paddingSymmetric(vertical: 12, horizontal: 16),

            //   PrimaryAppButton(
            //   color: (controller.productTitle.text.trim().isEmpty||controller.productSubTitle.text.trim().isEmpty ||controller.productPrice.text.trim().isEmpty ||controller.productDescription.text.trim().isEmpty)?AppColors.grey.withValues(alpha: 0.40):AppColors.appRedColor,
            //   onTap: () {
            //     controller.editProductDetailValidation();
            //   },
            //   text: EnumLocale.txtNext.name.tr,
            //   height: 54,
            // ).paddingSymmetric(vertical: 12, horizontal: 16),
          );
        })
      ],
    );
  }
}
