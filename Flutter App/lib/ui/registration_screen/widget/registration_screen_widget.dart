import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/custom/upper_case_formatter/upper_case_formatter_class.dart';
import 'package:listify/ui/registration_screen/controller/registration_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

/// =================== App Bar =================== ///
class RegistrationAppBarView extends StatelessWidget {
  const RegistrationAppBarView({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomAppBar(
      title: EnumLocale.txtRegister.name.tr,
      showLeadingIcon: true,
    );
  }
}

/// =================== Add Information(TextFormField) =================== ///
class RegistrationAddInfoView extends StatelessWidget {
  const RegistrationAddInfoView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<RegistrationController>(
      builder: (logic) {
        return Form(
          key: logic.formKey,
          child: Column(
            children: [
              CustomTitle(
                title: EnumLocale.txtEnterName.name.tr,
                method: LogInTextField(
                  filled: true,
                  hintText: EnumLocale.txtEnterName.name.tr,
                  controller: logic.nameController,
                  fillColor: AppColors.white,
                  cursorColor: AppColors.black,
                  fontColor: AppColors.black,
                  fontSize: 15,
                  textInputAction: TextInputAction.next,
                  inputFormatters: [UpperCaseTextFormatter()],
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return EnumLocale.desEnterFullName.name.tr;
                    }
                    return null;
                  },
                ),
              ).paddingOnly(bottom: 30),
              CustomTitle(
                title: EnumLocale.txtEnterMail.name.tr,
                method: LogInTextField(

                  filled: true,
                  hintText: EnumLocale.txtEnterYourEmailId.name.tr,
                  controller: logic.emailController,
                  fillColor: AppColors.white,
                  cursorColor: AppColors.black,
                  fontColor: AppColors.black,
                  fontSize: 15,
                  textInputAction: TextInputAction.next,
                  textInputType: TextInputType.emailAddress,
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return EnumLocale.desEnterEmail.name.tr;
                    } else if (!logic.isEmailValid(value)) {
                      return EnumLocale.desEnterValidEmailAddress.name.tr;
                    }
                    return null;
                  },
                ),
              ).paddingOnly(bottom: 30),
              CustomTitle(
                title: EnumLocale.txtPassword.name.tr,
                method: LogInTextField(
                    filled: true,
                    hintText: EnumLocale.txtEnterYourPassword.name.tr,
                    controller: logic.passwordController,
                    fillColor: AppColors.white,
                    cursorColor: AppColors.black,
                    fontColor: AppColors.black,
                    fontSize: 15,
                    textInputAction: TextInputAction.next,
                    maxLines: 1,
                    obscureText: logic.isObscure,
                    suffixIcon: logic.isObscure
                        ? InkWell(
                            onTap: () {
                              logic.onClickObscure();
                            },
                            child: Image.asset(
                              AppAsset.purpleEyeIcon,
                              height: 10,
                              width: 10,
                            ).paddingAll(12),
                          )
                        : InkWell(
                            onTap: () {
                              logic.onClickObscure();
                            },
                            child: Image.asset(
                              AppAsset.purpleHideEyeIcon,
                              height: 10,
                              width: 10,
                            ).paddingAll(12),
                          ),
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
                      } else if (!RegExp(r'[!@#\$&*~%^()_+\-=\[\]{};:"\\|,.<>\/?]').hasMatch(value)) {
                        return 'Include at least one special character';
                      }
                      return null;
                    }),
              ).paddingOnly(bottom: 30),
              CustomTitle(
                title: EnumLocale.txtConfirmPassword.name.tr,
                method: LogInTextField(
                  filled: true,
                  hintText: EnumLocale.txtEnterConfirmPassword.name.tr,
                  controller: logic.confirmPassController,
                  fillColor: AppColors.white,
                  cursorColor: AppColors.black,
                  fontColor: AppColors.black,
                  fontSize: 15,
                  textInputAction: TextInputAction.next,
                  maxLines: 1,
                  obscureText: logic.isObscure1,
                  suffixIcon: logic.isObscure1
                      ? InkWell(
                          onTap: () {
                            logic.onClickObscure1();
                          },
                          child: Image.asset(
                            AppAsset.purpleEyeIcon,
                            height: 10,
                            width: 10,
                          ).paddingAll(12),
                        )
                      : InkWell(
                          onTap: () {
                            logic.onClickObscure1();
                          },
                          child: Image.asset(
                            AppAsset.purpleHideEyeIcon,
                            height: 10,
                            width: 10,
                          ).paddingAll(12),
                        ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return EnumLocale.desReEnterPassword.name.tr;
                    } else if (value != logic.passwordController.text) {
                      return EnumLocale.desPasswordNotMatch.name.tr;
                    }
                    return null;
                  },
                ),
              )
              // submitButton().paddingOnly(bottom: 33),
            ],
          ),
        );
      },
    ).paddingOnly(right: 18, left: 18);
  }
}

/// =================== submit button =================== ///

class SubMitBottomButton extends StatelessWidget {
  const SubMitBottomButton({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<RegistrationController>(
      builder: (controller) {
        return Container(
          decoration: BoxDecoration(
            color: AppColors.white,
            image: DecorationImage(
              image: AssetImage(AppAsset.loginBgImage),
              fit: BoxFit.cover,
            ),
          ),
          child: PrimaryAppButton(
            onTap: () async {
              FocusScope.of(Get.context!).unfocus();

              if (controller.validateRegistration()) {
                await controller.signUpWithEmailPassword();
              }
            },
            color: AppColors.appRedColor,
            height: 50,
            // width: Get.width * 0.75,
            text: EnumLocale.txtSubmit.name.tr,
            widget: Image.asset(
              AppAsset.arrowUpIcon,
              height: 15,
              width: 15,
            ),
            textStyle: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.white),
          ).paddingOnly(left: 20,top: 20,right: 20),
        );
      },
    );
  }
}
