import 'dart:io';

import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl_phone_field/country_picker_dialog.dart';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/ui/user_verification_screen/controller/user_verification_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class UserVerificationAppBar extends StatelessWidget {
  final String? title;
  const UserVerificationAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(300),
      child: GetBuilder<UserVerificationScreenController>(builder: (controller) {
        return CustomAppBar(
          title: title,
          showLeadingIcon: true,
          onTap: () {
            if (controller.currentStep > 1) {
              controller.previousStep();
            } else {
              Get.back(); // Only pop when on step 1
            }
          },
        );
      }),
    );
  }
}

class UserVerificationBottomBar extends StatelessWidget {
  const UserVerificationBottomBar({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UserVerificationScreenController>(builder: (controller) {
      return Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            decoration: BoxDecoration(
              color: AppColors.white,
              boxShadow: [
                BoxShadow(
                  color: AppColors.black.withValues(alpha: 0.1),
                  blurRadius: 12,
                  offset: const Offset(0, -2),
                  spreadRadius: 0,
                ),
              ],
            ),
            child: PrimaryAppButton(
              onTap: () {
                if (controller.currentStep == 1) {
                  controller.nextStep();
                } else {
                  if (controller.frontImage == null || controller.backImage == null || controller.selfieImage == null) {
                    Utils.showToast(context, "Please upload ID proof (front, back) and selfie picture", color: AppColors.white, txtColor: AppColors.appRedColor);
                    return;
                  }

                  // 🟢 Log all data here
                  final name = controller.name.text.trim();
                  final email = controller.email.text.trim();
                  final phone = controller.number.text.trim();
                  final countryCode = controller.countryCode;
                  final frontImagePath = controller.frontImage;
                  final backImagePath = controller.backImage;

                  Utils.showLog("🔍 User Verification Data:");
                  Utils.showLog("Full Name: $name");
                  Utils.showLog("Email: $email");
                  Utils.showLog("Phone: +$countryCode $phone");
                  Utils.showLog("Front ID Path: $frontImagePath");
                  Utils.showLog("Back ID Path: $backImagePath");

                  // Proceed with dialog or API call
                  controller.userVerificationApi();
                }
              },
              text: controller.currentStep == 1 ? EnumLocale.txtContinue.name.tr : EnumLocale.txtSubmit.name.tr,
              height: 54,
            ).paddingSymmetric(vertical: 12, horizontal: 16),
          ),
        ],
      );
    });
  }
}

class Step1View extends StatelessWidget {
  const Step1View({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UserVerificationScreenController>(builder: (controller) {
      return Expanded(
        child: SingleChildScrollView(
          physics: AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                EnumLocale.txtPersonalInformation.name.tr,
                style: AppFontStyle.fontStyleW800(fontSize: 18, fontColor: AppColors.black),
              ).paddingOnly(bottom: 4),
              Text(
                EnumLocale.txtPersonalInformationTxt.name.tr,
                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText, height: 1.7),
              ).paddingOnly(bottom: 22),
              CustomTitle(
                txtColor: AppColors.popularProductText,
                title: EnumLocale.txtFullName.name.tr,
                method: CustomTextField(
                  filled: true,
                  // borderColor: AppColors.txtFieldBorder,
                  controller: controller.name,
                  fillColor: AppColors.editTextFieldColor,
                  cursorColor: AppColors.black,
                  fontColor: AppColors.black,
                  fontSize: 15,
                  textInputAction: TextInputAction.next,
                  maxLines: 1,
                ),
              ).paddingOnly(bottom: 28),
              CustomTitle(
                  txtColor: AppColors.popularProductText,
                  title: EnumLocale.txtPhoneNumber.name.tr,
                  method: GetBuilder<UserVerificationScreenController>(
                    builder: (logic) {
                      return Form(
                        key: logic.formKey,
                        child: IntlPhoneField(
                          flagsButtonPadding: const EdgeInsets.all(8),
                          flagsButtonMargin: const EdgeInsets.only(right: 13),
                          dropdownIconPosition: IconPosition.trailing,
                          controller: logic.number,
                          obscureText: false,
                          validator: (value) {
                            if (value == null) {
                              return EnumLocale.desEnterMobile.name.tr;
                            }
                            return null;
                          },
                          style: AppFontStyle.fontStyleW600(
                            fontSize: 16,
                            fontColor: AppColors.black,
                          ),
                          // readOnly: Database.loginType == 1 ? true : false,
                          cursorColor: AppColors.black,
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
                            searchFieldCursorColor: AppColors.black,
                            searchFieldInputDecoration: InputDecoration(
                              hintStyle: AppFontStyle.fontStyleW400(
                                fontSize: 14,
                                fontColor: AppColors.grey,
                              ),
                              hintText: EnumLocale.txtSearchCountryCode.name.tr,
                            ),
                          ),
                          dropdownIcon: Icon(
                            Icons.arrow_drop_down_outlined,
                            color: AppColors.black,
                          ),
                          keyboardType: TextInputType.number,
                          showCountryFlag: true,
                          decoration: InputDecoration(
                            counterText: '',
                            hintStyle: AppFontStyle.fontStyleW500(
                              fontSize: 16,
                              fontColor: AppColors.black.withValues(alpha: 0.2),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide.none,
                            ),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide.none,
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: AppColors.editTextFieldColor,
                            errorStyle: AppFontStyle.fontStyleW500(
                              fontSize: 8,
                              fontColor: AppColors.appRedColor,
                            ),
                            errorBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide(color: AppColors.appRedColor),
                            ),
                            focusedErrorBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide(color: AppColors.appRedColor),
                            ),
                            counterStyle: AppFontStyle.fontStyleW500(
                              fontSize: 16,
                              fontColor: AppColors.black.withValues(alpha: 0.2),
                            ),
                          ),
                          onCountryChanged: (value) {
                            controller.countryCode = value.dialCode;
                            debugPrint("country code: ${controller.countryCode}");
                          },
                          initialCountryCode: 'IN',
                          onChanged: (phone) {
                            logic.dialCode = phone.countryCode; // example: +91
                            logic.number.text = phone.number; // only number part
                          },
                        ),
                      );
                    },
                  )).paddingOnly(bottom: 20),
              CustomTitle(
                txtColor: AppColors.popularProductText,
                title: EnumLocale.txtEmailAddress.name.tr,
                method: CustomTextField(
                  filled: true,
                  // borderColor: AppColors.txtFieldBorder,
                  controller: controller.email,
                  fillColor: AppColors.editTextFieldColor,
                  cursorColor: AppColors.black,
                  fontColor: AppColors.black,
                  fontSize: 15,
                  textInputAction: TextInputAction.next,
                  maxLines: 1,
                ),
              ).paddingOnly(bottom: 28)
            ],
          ),
        ),
      );
    });
  }
}

class Step2View extends StatelessWidget {
  const Step2View({super.key});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: SingleChildScrollView(
        physics: AlwaysScrollableScrollPhysics(),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              EnumLocale.txtIDVerification.name.tr,
              style: AppFontStyle.fontStyleW800(fontSize: 18, fontColor: AppColors.black),
            ),
            Text(
              EnumLocale.txtIDVerificationTxt.name.tr,
              style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText, height: 1.7),
            ).paddingOnly(bottom: 22),
            GetBuilder<UserVerificationScreenController>(
              id: Constant.idIdentityProof,
              builder: (controller) {
                return Container(
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.dottedBorderColor),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      ListTile(
                        onTap: controller.toggleIdentityExpansion,
                        title: Text(
                          controller.selectedIdentityProof?.title ?? EnumLocale.txtSelectIdentityProof.name.tr,
                          style: controller.selectedIdentityProof == null
                              ? AppFontStyle.fontStyleW500(
                                  fontSize: 14,
                                  fontColor: AppColors.dottedBorderColor,
                                )
                              : AppFontStyle.fontStyleW600(
                                  fontSize: 14,
                                  fontColor: AppColors.black,
                                ),
                        ),
                        trailing: Icon(
                          controller.isIdentityExpanded ? Icons.expand_less : Icons.expand_more,
                          color: AppColors.dottedBorderColor,
                        ),
                      ),
                      if (controller.isIdentityExpanded)
                        Column(
                          children: List.generate(controller.idProofList.length, (index) {
                            final item = controller.idProofList[index];
                            return ListTile(
                              title: Text(
                                item.title ?? '',
                                style: AppFontStyle.fontStyleW500(
                                  fontSize: 14,
                                  fontColor: AppColors.dottedBorderColor,
                                ),
                              ),
                              onTap: () => controller.selectIdentityProof(item),
                            );
                          }),
                        ),
                    ],
                  ),
                ).paddingOnly(bottom: 24);
              },
            ),
            AddImageView().paddingOnly(bottom: 18),
            Text(
              EnumLocale.txtImageTypeTxt.name.tr,
              style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.appRedColor),
            ).paddingOnly(bottom: 20)
          ],
        ),
      ),
    );
  }
}

class AddImageView extends StatelessWidget {
  const AddImageView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UserVerificationScreenController>(builder: (controller) {
      return Column(
        children: [
          Row(
            children: [
              // Front ID Proof
              Expanded(
                child: _buildImagePicker(
                  context: context,
                  imageFile: controller.frontImage,
                  onPick: () => controller.pickFrontImage(),
                  onRemove: () => controller.removeFrontImage(),
                  label: EnumLocale.txtIDProofFront.name.tr,
                ),
              ),
              SizedBox(width: 12),

              // Back ID Proof
              Expanded(
                child: _buildImagePicker(
                  context: context,
                  imageFile: controller.backImage,
                  onPick: () => controller.pickBackImage(),
                  onRemove: () => controller.removeBackImage(),
                  label: EnumLocale.txtIDProofBack.name.tr,
                ),
              ),
            ],
          ),
          SizedBox(height: 18),
          // Selfie
          _buildImagePicker(
            context: context,
            imageFile: controller.selfieImage,
            onPick: () => controller.pickSelfieImage(),
            onRemove: () => controller.removeSelfieImage(),
            label: "Selfie Picture 🤳",
            isFullWidth: true,
          ),
        ],
      );
    });
  }

  Widget _buildImagePicker({
    required BuildContext context,
    required String? imageFile,
    required VoidCallback onPick,
    required VoidCallback onRemove,
    required String label,
    bool isFullWidth = false,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        GestureDetector(
          onTap: imageFile == null ? onPick : null,
          child: imageFile == null
              ? DottedBorder(
                  strokeCap: StrokeCap.round,
                  radius: Radius.circular(18),
                  borderType: BorderType.RRect,
                  color: AppColors.dottedBorderColor,
                  dashPattern: [3, 3],
                  child: Container(
                    height: 150,
                    width: 134,
                    decoration: BoxDecoration(
                      color: AppColors.addImageBgColor,
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Image.asset(
                          AppAsset.purpleAddIcon,
                          height: 32,
                          width: 32,
                        ),
                        Text(
                          EnumLocale.txtAddFile.name.tr,
                          style: AppFontStyle.fontStyleW500(
                            fontSize: 12,
                            fontColor: AppColors.dottedBorderColor,
                            textDecoration: TextDecoration.underline,
                            decorationColor: AppColors.dottedBorderColor,
                          ),
                        )
                      ],
                    ),
                  ),
                )
              : Stack(
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(18),
                      child: Image.file(
                        File(imageFile),
                        height: 160,
                        width: isFullWidth ? double.infinity : 134,
                        fit: BoxFit.cover,
                      ),
                    ),
                    Positioned(
                      right: 6,
                      top: 6,
                      child: GestureDetector(
                        onTap: onRemove,
                        child: Container(
                          padding: const EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            color: Colors.black.withValues(alpha: 0.6),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.close,
                            color: Colors.white,
                            size: 16,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
        ).paddingOnly(bottom: 14),
        Text(
          label,
          style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.black),
        )
      ],
    );
  }
}
