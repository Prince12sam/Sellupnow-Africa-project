import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/ui/verify_otp_screen/controller/verify_otp_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';
import 'package:pinput/pinput.dart';

/// =================== Description =================== ///
class VerifyOtpDescriptionView extends StatelessWidget {
  const VerifyOtpDescriptionView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          height: 42,
          width: 42,
          decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.categoriesBgColor),
          child: Center(
              child: Image.asset(
            AppAsset.backArrowIcon,
            height: 26,
          )),
        ).paddingOnly(top: 25),
        Text(
          EnumLocale.txtEnterOtpWithRegisterNumber.name.tr,
          // textAlign: TextAlign.center,
          style: AppFontStyle.fontStyleW900(
            fontSize: 42,
            fontColor: AppColors.appRedColor,
          ),
        ).paddingOnly(top: 50),
        Text(
          EnumLocale.txtVerifyOtpDescription.name.tr, // textAlign: TextAlign.center,
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
class VerifyOtpView extends StatelessWidget {
  const VerifyOtpView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VerifyOtpController>(
      id: Constant.idResendOtp,
      builder: (logic) {
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              EnumLocale.txtEnterOtp.name.tr,
              style: AppFontStyle.fontStyleW500(fontSize: 15, fontColor: AppColors.popularProductText),
            ).paddingOnly(top: 42),
            Pinput(
              cursor: Container(
                width: 1,
                height: 15,
                decoration: BoxDecoration(
                  color: AppColors.black,
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              length: 6,
              defaultPinTheme: PinTheme(
                width: 56,
                height: 56,
                textStyle: AppFontStyle.fontStyleW700(
                  fontSize: 20,
                  fontColor: AppColors.black,
                ),
                decoration: BoxDecoration(
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.black.withValues(alpha: 0.08),
                      offset: Offset(0, 0),
                      blurRadius: 16,
                      spreadRadius: 0,
                    ),
                  ],
                  border: Border.all(color: AppColors.txtFieldBorder),
                  color: AppColors.white,
                  borderRadius: BorderRadius.circular(14),
                ),
              ),
              pinAnimationType: PinAnimationType.fade,
              controller: logic.otpController,
              focusedPinTheme: PinTheme(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  border: Border.all(
                    color: AppColors.black,
                    width: 1,
                  ),
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
            ).paddingOnly(top: 16, bottom: 18),
            // if (logic.countdown > 0)
            Text(
              logic.formattedCountdown,
              style: AppFontStyle.fontStyleW500(
                fontSize: 14,
                fontColor: AppColors.appRedColor,
              ),
            ),
          ],
        );
      },
    );
  }
}

/// =================== Complete Registration Resend OTP =================== ///
class VerifyOtpResendOTPView extends StatelessWidget {
  const VerifyOtpResendOTPView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VerifyOtpController>(
        id: Constant.idResendOtp,
        builder: (logic) {
          final isCountdownActive = logic.countdown > 0;

          return Column(
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    EnumLocale.txtYouHaveNotGetOtp.name.tr,
                    textAlign: TextAlign.center,
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 14,
                      fontColor: AppColors.popularProductText,
                    ),
                  ).paddingOnly(top: 0, right: 3),
                  if (!isCountdownActive)
                    InkWell(
                      onTap: () {
                        logic.onResendOtpClick(context);
                      },
                      overlayColor: WidgetStatePropertyAll(AppColors.transparent),
                      child: Text(
                        EnumLocale.txtResendOtp.name.tr,
                        textAlign: TextAlign.center,
                        style: AppFontStyle.fontStyleW700(
                          fontSize: 14,
                          fontColor: AppColors.appRedColor,
                          textDecoration: TextDecoration.underline,
                          decorationColor: AppColors.appRedColor,
                        ),
                      ).paddingOnly(top: 12),
                    ),
                  if (isCountdownActive)
                    InkWell(
                      onTap: () {},
                      overlayColor: WidgetStatePropertyAll(AppColors.transparent),
                      child: Text(
                        EnumLocale.txtResendOtp.name.tr,
                        textAlign: TextAlign.center,
                        style: AppFontStyle.fontStyleW700(
                          fontSize: 13,
                          fontColor: AppColors.grey,
                          textDecoration: TextDecoration.underline,
                          decorationColor: AppColors.grey,
                        ),
                      ),
                    ),
                ],
              ).paddingOnly(top: 0),
            ],
          );
        });
  }
}

/// =================== Complete Registration Button =================== ///
class VerifyOtpButtonView extends StatelessWidget {
  const VerifyOtpButtonView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VerifyOtpController>(
      id: Constant.idVerifyOtp,
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
          // width: Get.width * 0.75,
          onTap: () {
            if (logic.isLoading) {
              Utils.showToast(context, "Please wait...");
            } else {
              logic.verifyOtp();
            }
          },

          text: EnumLocale.txtSubmit.name.tr,
          textStyle: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.white),
        ));
      },
    );
  }
}
