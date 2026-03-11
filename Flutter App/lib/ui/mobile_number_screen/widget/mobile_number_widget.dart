import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:intl_phone_field/country_picker_dialog.dart';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/ui/mobile_number_screen/controller/mobile_number_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

/// =================== Description =================== ///
class MobileNumberDescriptionView extends StatelessWidget {
  const MobileNumberDescriptionView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        GestureDetector(
          onTap: () {
            if (Get.key.currentState?.canPop() ?? false) {
              Get.back();
            }
          },
          child: Container(
            height: 42,
            width: 42,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.categoriesBgColor,
            ),
            child: Center(
              child: Image.asset(
                AppAsset.backArrowIcon,
                height: 26,
              ),
            ),
          ),
        ).paddingOnly(top: 25),
        Text(
          EnumLocale.txtLogInWithMobile.name.tr,
          // textAlign: TextAlign.center,
          style: AppFontStyle.fontStyleW900(
            fontSize: 42,
            fontColor: AppColors.appRedColor,
          ),
        ).paddingOnly(top: 50),
        Text(
          EnumLocale.txtMobileLoginDescription.name.tr, // textAlign: TextAlign.center,
          style: AppFontStyle.fontStyleW500(
            height: 1.6,
            fontSize: 15,
            fontColor: AppColors.black,
          ),
        ).paddingOnly(right: 30, top: 3),
      ],
    );
  }
}

/// =================== Complete Registration OTP =================== ///
class MobileNumberOTPView extends StatelessWidget {
  const MobileNumberOTPView({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomTitle(
      title: EnumLocale.txtEnterMobileNumber.name.tr,
      method: GetBuilder<MobileNumberController>(
        builder: (logic) {
          return Form(
            key: logic.formKey,
            child: Container(
              decoration: BoxDecoration(boxShadow: [
                BoxShadow(
                  color: AppColors.black.withValues(alpha: 0.08),
                  offset: Offset(0, 0),
                  blurRadius: 16,
                  spreadRadius: 0,
                ),
              ]),
              child: IntlPhoneField(
                  key: ValueKey(logic.phoneCountries.length),
                  flagsButtonPadding: const EdgeInsets.all(8),
                  flagsButtonMargin: const EdgeInsets.only(right: 13),
                  dropdownIconPosition: IconPosition.trailing,
                  controller: logic.numberController,
                  obscureText: false,
                  validator: (value) {
                    if (value == null || (value.number).trim().isEmpty) {
                      return EnumLocale.desEnterMobile.name.tr;
                    }
                    return null;
                  },
                  style: AppFontStyle.fontStyleW600(
                    fontSize: 14,
                    fontColor: AppColors.black,
                  ),
                  cursorColor: AppColors.appRedColor,
                  dropdownTextStyle: AppFontStyle.fontStyleW700(
                    fontSize: 16,
                    fontColor: AppColors.black,
                  ),
                  pickerDialogStyle: PickerDialogStyle(
                    countryCodeStyle: AppFontStyle.fontStyleW700(
                      fontSize: 13,
                      fontColor: AppColors.black,
                    ),
                    countryNameStyle: AppFontStyle.fontStyleW700(
                      fontSize: 13,
                      fontColor: AppColors.black,
                    ),
                    searchFieldCursorColor: AppColors.appRedColor,
                    searchFieldInputDecoration: InputDecoration(
                      hintStyle: AppFontStyle.fontStyleW400(
                        fontSize: 14,
                        fontColor: AppColors.grey,
                      ),
                      hintText: EnumLocale.txtSearchCountryCode.name.tr,
                    ),
                  ),
                  dropdownIcon: Icon(
                    Icons.keyboard_arrow_down_rounded,
                    color: AppColors.downArrowColor,
                  ),
                  keyboardType: TextInputType.number,
                  inputFormatters: [
                    LengthLimitingTextInputFormatter(15), // Limit phone number input to 15 digits
                  ],
                  showCountryFlag: true,
                  decoration: InputDecoration(
                    counterText: '',
                    hintStyle: AppFontStyle.fontStyleW600(
                      fontSize: 12,
                      fontColor: AppColors.white,
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide(color: AppColors.txtFieldBorder),
                    ),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide(color: AppColors.txtFieldBorder),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide(color: AppColors.txtFieldBorder),
                    ),
                    filled: true,
                    fillColor: AppColors.white,
                    errorStyle: AppFontStyle.fontStyleW500(
                      fontSize: 8,
                      fontColor: AppColors.redColor,
                    ),
                    errorBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide(color: AppColors.redColor),
                    ),
                    focusedErrorBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide(color: AppColors.redColor),
                    ),
                    counterStyle: AppFontStyle.fontStyleW500(
                      fontSize: 9,
                      fontColor: AppColors.grey,
                    ),
                  ),
                  countries: logic.phoneCountries.isEmpty
                      ? null
                      : logic.phoneCountries,
                  initialCountryCode: logic.initialCountryCode,
                  onCountryChanged: (value) {
                    log("message================= ${value.code}");
                    // Database.onSetSelectedCountryCode(value.code);
                    // Database.getDialCode();

                    // log("Database.selectedCountryCode message================= ${Database.selectedCountryCode}");
                  },
                  onChanged: (phone) {
                    logic.dialCode = phone.countryCode; // example: +91
                    logic.numberController.text = phone.number; // only number part
                  }),
            ),
          );
        },
      ),
    ).paddingOnly(top: 42);
  }
}

/// =================== Complete Registration Button =================== ///
class MobileNumberButtonView extends StatelessWidget {
  const MobileNumberButtonView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MobileNumberController>(
      builder: (logic) {
        return Container(
          decoration: BoxDecoration(
            color: AppColors.white,
            image: DecorationImage(
              image: AssetImage(AppAsset.loginBgImage),
              fit: BoxFit.cover,
            ),
          ),
          child: PrimaryAppButton(
            height: 50,
            width: Get.width,
            onTap: () {
              logic.sendOtp(context);
              // Get.toNamed(
              //   AppRoutes.verifyOtp,
              // );
            },
            text: EnumLocale.txtContinue.name.tr,
            textStyle: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.white),
          ),
        );
      },
    );
  }
}
