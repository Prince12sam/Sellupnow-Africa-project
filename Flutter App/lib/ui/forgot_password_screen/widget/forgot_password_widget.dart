import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl_phone_field/country_picker_dialog.dart';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/ui/forgot_password_screen/controller/forgot_password_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

/// =================== Description =================== ///
class ForgotPasswordDescriptionView extends StatelessWidget {
  const ForgotPasswordDescriptionView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          height: 42,
          width: 42,
          decoration: BoxDecoration(
              shape: BoxShape.circle, color: AppColors.categoriesBgColor),
          child: Center(
              child: Image.asset(
            AppAsset.backArrowIcon,
            height: 26,
          )),
        ).paddingOnly(top: 25),
        Text(
          EnumLocale.txtForgotYourPassword.name.tr,
          // textAlign: TextAlign.center,
          style: AppFontStyle.fontStyleW900(
            fontSize: 42,
            fontColor: AppColors.appRedColor,
          ),
        ).paddingOnly(top: 50),
        Text(
          EnumLocale
              .txtForgotPassDescription.name.tr, // textAlign: TextAlign.center,
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
class ForgotPasswordView extends StatelessWidget {
  const ForgotPasswordView({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomTitle(
      title: EnumLocale.txtEnterMobileNumber.name.tr,
      method: GetBuilder<ForgotPasswordController>(
        builder: (logic) {
          return Form(
            key: logic.formKey,

            // child: CustomTextField(
            //   filled: true,
            //   // hintText: EnumLocale.txtAddYourNickName.name.tr,
            //   controller: logic.emailController,
            //   fillColor: AppColors.white,
            //   cursorColor: AppColors.black,
            //   fontColor: AppColors.black,
            //   fontSize: 15,
            //   textInputAction: TextInputAction.next,
            //   // inputFormatters: [UpperCaseTextFormatter()],
            // ),
            child: Container(
              decoration: BoxDecoration(boxShadow: [
                BoxShadow(
                  color: AppColors.black.withValues(alpha: 0.08),
                  offset: Offset(0, 0),
                  blurRadius: 16,
                  spreadRadius: 0,
                ),
              ]),
              child: CustomTextField(
                filled: true,
                controller: logic.emailController,
                textInputAction: TextInputAction.next,
              ),

              // IntlPhoneField(
              //     showCountryFlag: true,
              //     flagsButtonPadding: const EdgeInsets.all(8),
              //     flagsButtonMargin: const EdgeInsets.only(right: 13),
              //     dropdownIconPosition: IconPosition.trailing,
              //     controller: logic.number,
              //     obscureText: false,
              //     validator: (value) {
              //       if (value == null) {
              //         return EnumLocale.desEnterMobile.name.tr;
              //       }
              //       return null;
              //     },
              //     style: AppFontStyle.fontStyleW600(
              //       fontSize: 14,
              //       fontColor: AppColors.black,
              //     ),
              //     cursorColor: AppColors.appRedColor,
              //     dropdownTextStyle: AppFontStyle.fontStyleW700(
              //       fontSize: 16,
              //       fontColor: AppColors.black,
              //     ),
              //     pickerDialogStyle: PickerDialogStyle(
              //       countryCodeStyle: AppFontStyle.fontStyleW700(
              //         fontSize: 13,
              //         fontColor: AppColors.black,
              //       ),
              //       countryNameStyle: AppFontStyle.fontStyleW700(
              //         fontSize: 13,
              //         fontColor: AppColors.black,
              //       ),
              //       searchFieldCursorColor: AppColors.appRedColor,
              //       searchFieldInputDecoration: InputDecoration(
              //         hintStyle: AppFontStyle.fontStyleW400(
              //           fontSize: 14,
              //           fontColor: AppColors.grey,
              //         ),
              //         hintText: EnumLocale.txtSearchCountryCode.name.tr,
              //       ),
              //     ),
              //     dropdownIcon: Icon(
              //       Icons.keyboard_arrow_down_rounded,
              //       color: AppColors.downArrowColor,
              //     ),
              //     keyboardType: TextInputType.number,
              //     // showCountryFlag: false,
              //     decoration: InputDecoration(
              //       counterText: '',
              //       hintStyle: AppFontStyle.fontStyleW600(
              //         fontSize: 12,
              //         fontColor: AppColors.white,
              //       ),
              //       enabledBorder: OutlineInputBorder(
              //         borderRadius: BorderRadius.circular(12),
              //         borderSide: BorderSide(color: AppColors.txtFieldBorder),
              //       ),
              //       border: OutlineInputBorder(
              //         borderRadius: BorderRadius.circular(12),
              //         borderSide: BorderSide(color: AppColors.txtFieldBorder),
              //       ),
              //       focusedBorder: OutlineInputBorder(
              //         borderRadius: BorderRadius.circular(12),
              //         borderSide: BorderSide(color: AppColors.txtFieldBorder),
              //       ),
              //       filled: true,
              //       fillColor: AppColors.white,
              //       errorStyle: AppFontStyle.fontStyleW500(
              //         fontSize: 8,
              //         fontColor: AppColors.redColor,
              //       ),
              //       errorBorder: OutlineInputBorder(
              //         borderRadius: BorderRadius.circular(12),
              //         borderSide: BorderSide(color: AppColors.redColor),
              //       ),
              //       focusedErrorBorder: OutlineInputBorder(
              //         borderRadius: BorderRadius.circular(12),
              //         borderSide: BorderSide(color: AppColors.redColor),
              //       ),
              //       counterStyle: AppFontStyle.fontStyleW500(
              //         fontSize: 9,
              //         fontColor: AppColors.grey,
              //       ),
              //     ),
              //     // initialCountryCode: Database.selectedCountryCode,
              //     onCountryChanged: (value) {
              //       log("message================= ${value.code}");
              //       // Database.onSetSelectedCountryCode(value.code);
              //       // Database.getDialCode();
              //
              //       // log("Database.selectedCountryCode message================= ${Database.selectedCountryCode}");
              //     },
              //     onChanged: (phone) {
              //       logic.dialCode = phone.countryCode; // example: +91
              //       logic.number.text = phone.number; // only number part
              //     }),
            ),
          );
        },
      ),
    ).paddingOnly(top: 42);
  }
}

/// =================== Complete Registration Button =================== ///
class ForgotPasswordButtonView extends StatelessWidget {
  const ForgotPasswordButtonView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ForgotPasswordController>(
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
              /* Get.toNamed(
                AppRoutes.createNewPassScreen,
                arguments: logic.emailController.text.trim(),
              );*/

              logic.onForgotPassword();
            },
            text: EnumLocale.txtContinue.name.tr,
            textStyle: AppFontStyle.fontStyleW500(
                fontSize: 16, fontColor: AppColors.white),
          ).paddingOnly(
            bottom: 0,
            top: 10
          ),
        );
      },
    );
  }
}
