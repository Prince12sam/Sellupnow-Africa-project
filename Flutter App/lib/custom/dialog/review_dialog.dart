import 'dart:developer';

import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/ui/chat_detail_screen/controller/chat_detail_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

import '../../utils/app_color.dart' show AppColors;

class ReviewDialog extends StatelessWidget {
  final TextEditingController? controller;
  final ChatDetailController? controllerType;
  const ReviewDialog({super.key, this.controller, this.controllerType});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      // height: 420,
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(
            Radius.circular(60),
          ),
        ),
        color: AppColors.white,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                GestureDetector(
                  onTap: () {
                    FocusScopeNode currentFocus = FocusScope.of(context);
                    if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
                      currentFocus.focusedChild?.unfocus();
                    }
                    Get.back();
                    controllerType?.reviewController.clear();
                  },
                  child: Container(
                    height: 42,
                    width: 42,
                    decoration: BoxDecoration(color: AppColors.categoriesBgColor, shape: BoxShape.circle),
                    child: Center(
                        child: Image.asset(
                      AppAsset.backArrowIcon,
                      height: 26,
                      width: 26,
                    )),
                  ).paddingOnly(right: 18),
                ),
                Text(
                  EnumLocale.txtRateSeller.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 22, fontColor: AppColors.black),
                )
              ],
            ).paddingOnly(bottom: 32),
            Text(
              EnumLocale.txtRateYourExperience.name.tr,
              style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
            ).paddingOnly(bottom: 20),
            RatingBar.builder(
              itemPadding: const EdgeInsets.only(),
              ignoreGestures: false,
              glow: false,
              unratedColor: AppColors.ratingContainerColor,
              itemSize: 40,
              initialRating: 0.0,
              minRating: 0,
              maxRating: 5,
              glowRadius: 10,
              direction: Axis.horizontal,
              allowHalfRating: false,
              itemCount: 5,
              itemBuilder: (context, _) => const Icon(
                Icons.star_rounded,
                size: 50,
                color: Color(0xffF0BB52),
              ),
              onRatingUpdate: (rating) {
                controllerType?.rating.value = rating.toDouble();
                log("Selected Rating :: ${controllerType?.rating.value}");
              },
            ).paddingOnly(bottom: 23),
            CustomTextField(
              filled: true,
              controller: controllerType?.reviewController,
              cursorColor: AppColors.black,
              fillColor: AppColors.editTextFieldColor,
              maxLines: 5,
              hintText: "Write a review...",
              hintTextColor: AppColors.popularProductText,
              hintTextSize: 14,
            ).paddingOnly(bottom: 22),
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Expanded(
                    child: PrimaryAppButton(
                  height: 54,
                  color: AppColors.lightRed100,
                  onTap: () {
                    FocusScopeNode currentFocus = FocusScope.of(context);
                    if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
                      currentFocus.focusedChild?.unfocus();
                    }
                    Get.back();
                    controllerType?.reviewController.clear();
                  },
                  child: Center(
                    child: Text(
                      EnumLocale.txtCancel.name.tr,
                      style: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.appRedColor),
                    ),
                  ),
                ).paddingOnly(right: 14)),
                Expanded(
                    child: PrimaryAppButton(
                  height: 54,
                  onTap: () {
                    FocusScopeNode currentFocus = FocusScope.of(context);
                    if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
                      currentFocus.focusedChild?.unfocus();
                    }

                    if(Database.demoUser==true){
                      Utils.showLog("This is demo app");
                    }else{
                    if (controllerType!.reviewController.text.trim().isEmpty) {
                      Utils.showToast(context, "Please enter a review");
                      return;
                    } else {
                      controllerType?.giveReview();
                    }}
                  },
                  color: AppColors.appRedColor,
                  child: Center(
                    child: Text(
                      EnumLocale.txtSubmit.name.tr,
                      style: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.white),
                    ),
                  ),
                )),
              ],
            )
          ],
        ).paddingAll(15),
      ),
    );
  }
}
