import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_background/app_background.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/login_screen/controller/login_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class LoginScreenView extends StatelessWidget {
  const LoginScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<LoginScreenController>(
      builder: (controller) {
        return LoginBg(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    height: 65,
                    width: 65,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(20),
                      color: AppColors.appRedColor,
                    ),
                    child: Center(
                      child: Image.asset(
                        AppAsset.loginBasketImage,
                        height: 37,
                      ),
                    ),
                  ).paddingOnly(right: 16),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        EnumLocale.txtAppName.name.tr,
                        style: AppFontStyle.fontStyleW800(
                            fontSize: 28, fontColor: AppColors.appRedColor),
                      ),
                      Text(
                        EnumLocale.txtECommerceBuySaleGoods.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 15, fontColor: AppColors.black),
                      ),
                    ],
                  ),
                ],
              ).paddingOnly(bottom: 32, top: Get.height * 0.067),
              Text(
                EnumLocale.txtEnterYourEmailId.name.tr,
                style: AppFontStyle.fontStyleW500(
                    fontSize: 15, fontColor: AppColors.popularProductText),
              ).paddingOnly(bottom: 10),
              LogInTextField(
                textInputAction: TextInputAction.next,
                maxLines: 1,
                cursorColor: AppColors.black,
                controller: controller.emailTxtController,
                filled: true,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return EnumLocale.desEnterEmail.name.tr;
                  } else if (!controller.isEmailValid(value)) {
                    return EnumLocale.desEnterValidEmailAddress.name.tr;
                  }

                  return null;
                },
                hintText: EnumLocale.txtEnterYourEmailId.name.tr,
              ).paddingOnly(bottom: 20),
              Text(
                EnumLocale.txtEnterYourPassword.name.tr,
                style: AppFontStyle.fontStyleW500(
                    fontSize: 15, fontColor: AppColors.popularProductText),
              ).paddingOnly(bottom: 10),
              LogInTextField(
                textInputAction: TextInputAction.done,
                cursorColor: AppColors.black,
                controller: controller.passwordTxtController,
                maxLines: 1,
                obscureText: controller.isObscure,
                suffixIcon: controller.isObscure
                    ? InkWell(
                        onTap: () {
                          controller.onClickObscure();
                        },
                        child: Image.asset(
                          AppAsset.purpleEyeIcon,
                          height: 10,
                          width: 10,
                        ).paddingAll(12),
                      )
                    : InkWell(
                        onTap: () {
                          controller.onClickObscure();
                        },
                        child: Image.asset(
                          AppAsset.purpleHideEyeIcon,
                          height: 10,
                          width: 10,
                        ).paddingAll(12),
                      ),
                filled: true,
                hintText: EnumLocale.txtEnterYourPassword.name.tr,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return EnumLocale.desEnterPassword.name.tr;
                  } else if (value.length < 8) {
                    return 'Password must be at least 8 characters long';
                  } else if (!RegExp(r'[A-Z]').hasMatch(value)) {
                    return 'Include at least one uppercase letter';
                  } else if (!RegExp(r'[a-z]').hasMatch(value)) {
                    return 'Include at least one lowercase letter';
                  } else if (!RegExp(r'\d').hasMatch(value)) {
                    return 'Include at least one number';
                  } else if (!RegExp(r'[!@#\$&*~%^()-_+=<>?]')
                      .hasMatch(value)) {
                    return 'Include at least one special character';
                  }
                  return null;
                },
              ),
              InkWell(
                onTap: () {
                  ///
                  Get.toNamed(AppRoutes.forgotPasswordScreen, arguments: {
                    'email': controller.emailTxtController.text.trim()
                  });
                },
                child: Align(
                  alignment: Alignment.centerRight,
                  child: Text(
                    EnumLocale.txtForgotPassword.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        textDecoration: TextDecoration.underline,
                        decorationColor: AppColors.appRedColor,
                        fontSize: 14,
                        fontColor: AppColors.appRedColor),
                  ).paddingOnly(top: 10, bottom: 23),
                ),
              ),
              Center(
                child: PrimaryAppButton(
                  onTap: () {
                    if (controller.validateLogin()) {
                      controller.onClickSignIn();
                    }

                    // controller.loginUser();
                  },
                  color: AppColors.appRedColor,
                  height: Get.height * 0.058,
                  width: Get.width,
                  text: EnumLocale.txtContinue.name.tr,
                  textStyle: AppFontStyle.fontStyleW500(
                      fontSize: 17, fontColor: AppColors.white),
                ),
              ).paddingOnly(bottom: 25),

              Row(
                children: [
                  Text(
                    EnumLocale.txtIfYouHaveANewUser.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 14, fontColor: AppColors.popularProductText),
                  ).paddingOnly(right: 2),
                  InkWell(
                    onTap: () {
                      Get.toNamed(AppRoutes.register,
                          arguments: controller.registerArguments);
                    },
                    child: Text(
                      EnumLocale.txtGetRegister.name.tr,
                      style: AppFontStyle.fontStyleW500(
                        textDecoration: TextDecoration.underline,
                        decorationColor: AppColors.appRedColor,
                        fontSize: 14,
                        fontColor: AppColors.appRedColor,
                      ),
                    ),
                  )
                ],
              ).paddingOnly(bottom: 25),

              Row(
                children: [
                  Expanded(
                    child: DottedLine(
                      // dashGapRadius: 10,
                      // dashRadius: 20,

                      dashColor: AppColors.popularProductText,
                    ).paddingOnly(right: 10),
                  ),
                  Text(
                    EnumLocale.txtOrSignInWith.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 12, fontColor: AppColors.popularProductText),
                  ),
                  Expanded(
                    child: DottedLine(
                      dashColor: AppColors.popularProductText,
                    ).paddingOnly(left: 10),
                  ),
                ],
              ),
              

              GestureDetector(
                onTap: () async {
                  await controller.onGoogleLogin();
                },
                child: Container(
                  height: 54,
                  decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(60),
                      border: Border.all(
                        color: AppColors.txtFieldBorder,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.black.withValues(alpha: 0.08),
                          offset: Offset(0, 0),
                          blurRadius: 16,
                          spreadRadius: 0,
                        ),
                      ]),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        height: 47,
                        width: 47,
                        padding: EdgeInsets.all(7),
                        decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: AppColors.loginIconBgColor),
                        child: Center(
                          child: Image.asset(
                            AppAsset.googleLoginIcon,
                            height: 28,
                          ),
                        ),
                      ).paddingAll(3),
                      Text(
                        EnumLocale.txtGoogleLogin.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            decorationColor: AppColors.black,
                            fontSize: 15,
                            fontColor: AppColors.black),
                      ).paddingOnly(left: 2)
                    ],
                  ),
                ),
              ).paddingOnly(top: 25, bottom: 19),

              GestureDetector(
                onTap: () {
                  Get.toNamed(AppRoutes.mobileLogIn);
                },
                child: Container(
                  height: 54,
                  decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(60),
                      border: Border.all(
                        color: AppColors.txtFieldBorder,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.black.withValues(alpha: 0.08),
                          offset: Offset(0, 0),
                          blurRadius: 16,
                          spreadRadius: 0,
                        ),
                      ]),
                  child: Row(
                    children: [
                      Container(
                        height: 47,
                        width: 47,
                        padding: EdgeInsets.all(7),
                        decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: AppColors.loginIconBgColor),
                        child: Center(
                          child: Image.asset(
                            AppAsset.mobileLoginIcon,
                            height: 27,
                          ),
                        ),
                      ).paddingAll(3),
                      Text(
                        EnumLocale.txtMobileLogin.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            decorationColor: AppColors.black,
                            fontSize: 15,
                            fontColor: AppColors.black),
                      ).paddingOnly(left: 40)
                    ],
                  ),
                ),
              ),
              // PrimaryAppButton(
              //   onTap: () {
              //     // controller.onQuickLogin();
              //     // Get.offAllNamed(AppRoutes.bottomBar);
              //   },
              //   borderRadius: 60,
              //   color: AppColors.black,
              //   child: Row(
              //     children: [
              //       Container(
              //         padding: EdgeInsets.all(8),
              //         decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.white),
              //         child: Center(
              //           child: Image.asset(
              //             AppAsset.quickLoginIcon,
              //             height: 25,
              //           ),
              //         ),
              //       ).paddingAll(4),
              //       Spacer(),
              //       Text(
              //         EnumLocale.txtQuickLogin.name.tr,
              //         style: AppFontStyle.fontStyleW600(decorationColor: AppColors.black, fontSize: 18, fontColor: AppColors.white),
              //       ).paddingOnly(right: 50),
              //       Spacer()
              //     ],
              //   ),
              // ),
              GetBuilder<LoginScreenController>(
                id: Constant.radioButton,
                builder: (controller) {
                  final isSelected = controller.selectedValue == 1;
                  return Container(
                    color: AppColors.transparent,
                    child: Row(
                      children: [
                        GestureDetector(
                          onTap: () {
                            controller.toggleValue(1);
                          },
                          child: Container(
                            width: 18,
                            height: 18,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: isSelected
                                    ? AppColors.appRedColor
                                    : AppColors.popularProductText,
                                width: 1,
                              ),
                              color: isSelected
                                  ? AppColors.appRedColor
                                  : Colors.transparent,
                            ),
                            child: isSelected
                                ? Container(
                                    decoration: BoxDecoration(
                                      color: AppColors.appRedColor,
                                      shape: BoxShape.circle,
                                    ),
                                    child: Container(
                                      height: 18,
                                      width: 18,
                                      decoration: BoxDecoration(
                                        shape: BoxShape.circle,
                                        border:
                                            Border.all(color: AppColors.white),
                                        color: AppColors.appRedColor,
                                      ),
                                    ).paddingAll(0.5),
                                  )
                                : null,
                          ).paddingOnly(right: 10, left: 5),
                        ),
                        Row(
                          children: [
                            GestureDetector(
                              onTap: () {
                                controller.onClickPrivacyPolicy();
                              },
                              child: Container(
                                color: AppColors.transparent,
                                child: Text(
                                  EnumLocale.txtPrivacyPolicy.name.tr,
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 13,
                                    fontColor: AppColors.popularProductText,
                                  ),
                                ).paddingOnly(top: 4, bottom: 4),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ).paddingOnly(top: 24, bottom: 6),
                  );
                },
              ),
            ],
          ).paddingOnly(top: 9, left: 18, right: 18),
        );
      },
    );
  }
}
