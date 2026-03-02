import 'dart:developer';
import 'dart:io';

import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl_phone_field/country_picker_dialog.dart';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/ui/edit_profile_screen/controller/edit_profile_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class EditProfileAppBar extends StatelessWidget {
  final String? title;
  const EditProfileAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(300),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class EditImageView extends StatelessWidget {
  const EditImageView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<EditProfileController>(builder: (controller) {
      return Stack(
        clipBehavior: Clip.none,
        children: [
          DottedBorder(
            borderType: BorderType.Circle,
            color: AppColors.black,
            dashPattern: [3, 2],
            strokeWidth: 1,
            child: Container(
              clipBehavior: Clip.hardEdge,
              height: 116,
              width: 116,
              decoration: BoxDecoration(
                color: AppColors.white,
                shape: BoxShape.circle,
              ),
              // child: controller.pickImage != null
              //     ? Image.file(
              //         File(controller.pickImage!),
              //         fit: BoxFit.cover,
              //       )
              //     : Image.asset(
              //         AppAsset.profilePlaceHolder,
              //         fit: BoxFit.cover,
              //       ),

              child: controller.pickImage != null
                  ? Image.file(
                      File(controller.pickImage!),
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return Image.asset(
                          AppAsset.profilePlaceHolder,
                          fit: BoxFit.cover,
                        );
                      },
                    )
                  : CustomProfileImage(
                      image: Database.getUserProfileResponseModel?.user?.profileImage ?? '',
                    ),
            ),
          ),
          Positioned(
            bottom: 0,
            right: 0,
            child: GestureDetector(
              // onTap: () {
              //   controller.imageShow();
              // },

              onTap: () {
                Get.defaultDialog(
                    backgroundColor: AppColors.white,
                    title: EnumLocale.changeYourImage.name.tr,
                    titlePadding: const EdgeInsets.only(top: 30),
                    titleStyle: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),
                    content: GetBuilder<EditProfileController>(
                      builder: (controller) {
                        return Column(
                          children: [
                            Padding(
                              padding: const EdgeInsets.symmetric(vertical: 8),
                              child: Divider(
                                thickness: 1,
                                color: Colors.grey.shade100,
                              ),
                            ),
                            GestureDetector(
                              onTap: () {
                                Get.back();
                                controller.takePhoto();
                              },
                              child: Container(
                                height: 60,
                                decoration: BoxDecoration(borderRadius: BorderRadius.circular(8)),
                                child: Row(
                                  children: [
                                    Padding(
                                      padding: const EdgeInsets.symmetric(horizontal: 20),
                                      child: Image(
                                        color: AppColors.black,
                                        image: AssetImage(AppAsset.cameraFlipIcon),
                                        height: 20,
                                      ),
                                    ),
                                    Text(
                                      EnumLocale.txtTakeAphoto.name.tr,
                                      style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
                                    )
                                  ],
                                ),
                              ),
                            ),
                            Padding(
                              padding: const EdgeInsets.only(bottom: 10),
                              child: GestureDetector(
                                onTap: () {
                                  Get.back();
                                  controller.imageShow();
                                },
                                child: Container(
                                  height: 60,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Row(
                                    children: [
                                      Padding(
                                        padding: const EdgeInsets.symmetric(horizontal: 20),
                                        child: Image(
                                          color: AppColors.black,
                                          image: AssetImage(AppAsset.chatIcon),
                                          height: 20,
                                        ),
                                      ),
                                      Text(
                                        EnumLocale.txtChooseFromYourFile.name.tr,
                                        style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
                                      )
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          ],
                        );
                      },
                    ));
              },
              child: Container(
                padding: EdgeInsets.all(1.5),
                decoration: BoxDecoration(
                  color: AppColors.white,
                  shape: BoxShape.circle,
                ),
                child: Container(
                  height: 30,
                  width: 30,
                  decoration: BoxDecoration(
                    color: AppColors.editRedColor,
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: Image.asset(
                      AppAsset.cameraIcon,
                      width: 20,
                      height: 20,
                    ),
                  ),
                ),
              ),
            ),
          )
        ],
      ).paddingOnly(bottom: 36);
    });
  }
}

class EditTextFieldView extends StatelessWidget {
  const EditTextFieldView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<EditProfileController>(builder: (controller) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            EnumLocale.txtFullName.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
          ).paddingOnly(bottom: 12),
          CustomTextField(
            textInputAction: TextInputAction.next,
            maxLines: 1,
            controller: controller.name,
            fontSize: 16,
            cursorColor: AppColors.black,
            filled: true,
            fillColor: AppColors.editTextFieldColor,
            borderColor: AppColors.editTextFieldColor,
          ).paddingOnly(bottom: 22),
          Text(
            EnumLocale.txtEmailAddress.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
          ).paddingOnly(bottom: 12),
          CustomTextField(
            textInputAction: TextInputAction.next,
            maxLines: 1,
            controller: controller.email,
            fontSize: 16,
            cursorColor: AppColors.black,
            filled: true,
            readOnly: Database.loginType == 4 || Database.loginType == 2 ? true : false,
            fillColor: AppColors.editTextFieldColor,
            borderColor: AppColors.editTextFieldColor,
            // readOnly: Database.loginType == 4,
          ).paddingOnly(bottom: 22),
          Text(
            EnumLocale.txtPhoneNumber.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
          ).paddingOnly(bottom: 12),
          // CustomTextField(
          //   fontSize: 16,
          //   cursorColor: AppColors.black,
          //   filled: true,
          //   fillColor: AppColors.editTextFieldColor,
          //   borderColor: AppColors.editTextFieldColor,
          // ).paddingOnly(bottom: 22),

          // GetBuilder<EditProfileController>(
          //   builder: (controller) => IntlPhoneField(
          //     textInputAction: TextInputAction.next,
          //
          //     showDropdownIcon: false,
          //     cursorColor: AppColors.black,
          //     keyboardType: TextInputType.phone,
          //     controller: controller.number,
          //     style: TextStyle(
          //       color: AppColors.black,
          //     ),
          //     invalidNumberMessage: controller.mobileNumberValidate.value ? "phoneNumber Can Not Be Empty" : null,
          //     flagsButtonPadding: const EdgeInsets.only(left: 15),
          //     dropdownIcon: Icon(
          //       Icons.keyboard_arrow_down_sharp,
          //       color: Colors.grey.shade400,
          //     ),
          //     dropdownTextStyle: TextStyle(
          //       color: AppColors.black,
          //     ),
          //     initialCountryCode: 'IN',
          //     // favorite: const ['IN', 'SE'],
          //     onCountryChanged: (country) {
          //       controller.countryCode = country.dialCode;
          //       debugPrint("country code: ${controller.countryCode}");
          //     },
          //     decoration: InputDecoration(
          //       filled: true,
          //       fillColor: AppColors.editTextFieldColor,
          //       hintStyle: GoogleFonts.plusJakartaSans(
          //         color: Colors.grey.shade400,
          //         fontSize: 16,
          //       ),
          //       enabledBorder: OutlineInputBorder(
          //         borderSide: BorderSide.none,
          //         borderRadius: BorderRadius.circular(12),
          //       ),
          //       border: OutlineInputBorder(
          //         borderSide: BorderSide.none,
          //         borderRadius: BorderRadius.circular(12),
          //       ),
          //     ),
          //   ),
          // ),

          GetBuilder<EditProfileController>(
            builder: (logic) {
              return Form(
                key: logic.formKey,
                child: AbsorbPointer(
                  absorbing: Database.loginType == 1 ? true : false,
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
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 16,
                      fontColor: AppColors.black,
                    ),
                    readOnly: Database.loginType == 1 ? true : false,
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
                        hintStyle: AppFontStyle.fontStyleW500(
                          fontSize: 16,
                          fontColor: AppColors.black.withValues(alpha: 0.2),
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
                        fontColor: AppColors.black,
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
                        fontColor: AppColors.black,
                      ),
                    ),
                    // onCountryChanged: (value) {
                    //   log("Database.loginType${Database.loginType}");
                    //   log("message================= ${value.code}");
                    //   Database.onSetSelectedCountryCode(value.code);
                    //   Database.getDialCode();
                    //   log("Database.selectedCountryCode message================= ${Database.selectedCountryCode}");
                    // },

                    onCountryChanged: (value) {
                      logic.selectedCountryCode = value.code; // only in controller, not in DB
                      log("TEMP selectedCountryCode: ${logic.selectedCountryCode}");
                    },

                    initialCountryCode: Database.selectedCountryCode,
                    onChanged: (phone) {
                      logic.dialCode = phone.countryCode; // example: +91
                      logic.number.text = phone.number; // only number part
                    },
                  ),
                ),
              );
            },
          ).paddingOnly(bottom: 22),
          Text(
            EnumLocale.txtAddress.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
          ).paddingOnly(bottom: 12),
          CustomTextField(
            textInputAction: TextInputAction.next,
            maxLines: 1,
            controller: controller.address,
            fontSize: 16,
            cursorColor: AppColors.black,
            filled: true,
            fillColor: AppColors.editTextFieldColor,
            borderColor: AppColors.editTextFieldColor,
          ).paddingOnly(bottom: 22),
          Text(
            EnumLocale.txtNotification.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
          ).paddingOnly(bottom: 12),

          Container(
            height: 58,
            decoration: BoxDecoration(
              color: AppColors.editTextFieldColor,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              children: [
                Image.asset(
                  AppAsset.notificationIcon,
                  height: 28,
                  width: 28,
                ).paddingOnly(left: 14, right: 26),
                Text(
                  EnumLocale.txtEnabled.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
                ),
                Spacer(),
                GetBuilder<EditProfileController>(
                  id: Constant.idNotification,
                  builder: (controller) {
                    return CupertinoSwitch(
                      value: controller.isNotificationSwitch,
                      onChanged: (value) {
                        controller.notificationChange(value);
                      },
                    ).paddingOnly(right: 14);
                  },
                ),
              ],
            ),
          ).paddingOnly(bottom: 22),
          Text(
            EnumLocale.txtShowContactInfo.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
          ).paddingOnly(bottom: 12),
          Container(
            height: 58,
            decoration: BoxDecoration(
              color: AppColors.editTextFieldColor,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              children: [
                Image.asset(
                  AppAsset.callMailIcon,
                  height: 28,
                  width: 28,
                ).paddingOnly(left: 14, right: 26),
                Text(
                  EnumLocale.txtDisabled.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
                ),
                Spacer(),
                GetBuilder<EditProfileController>(
                    id: Constant.idContact,
                    builder: (controller) {
                  return CupertinoSwitch(
                    value: controller.isContactInfoSwitch,
                    onChanged: (value) {
                      controller.contactInfoChange(value);
                    },
                  ).paddingOnly(right: 14);
                }),
              ],
            ),
          ).paddingOnly(bottom: 22),
        ],
      ).paddingOnly(left: 16, right: 16);
    });
  }
}

class EditProfileBottomBar extends StatelessWidget {
  const EditProfileBottomBar({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<EditProfileController>(builder: (controller) {
      return Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            decoration: BoxDecoration(
              color: AppColors.white,
              boxShadow: [
                BoxShadow(
                  color: AppColors.black.withValues(alpha: 0.12),
                  blurRadius: 12,
                  offset: Offset(0, -1),
                ),
              ],
            ),
            child: PrimaryAppButton(
              onTap: () async {
                Utils.showLog("Database.demoUser${Database.demoUser}");
                if(Database.demoUser==true){
                  Utils.showToast(context, "This is Demo User");
                }else{
                await controller.callEditApi();}
              },
              height: 56,
              width: Get.width,
              child: Center(
                child: Text(
                  EnumLocale.txtUpdateProfile.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.white),
                ),
              ),
            ).paddingOnly(bottom: 10, right: 16, left: 10, top: 10),
          ),
        ],
      );
    });
  }
}
