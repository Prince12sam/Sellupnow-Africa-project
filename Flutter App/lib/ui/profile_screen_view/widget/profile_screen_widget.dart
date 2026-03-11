import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/dialog/delete_account_dialog.dart';
import 'package:listify/custom/dialog/log_out_dialog.dart';
import 'package:listify/custom/dialog/user_verification_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/profile_screen_view/controller/profile_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class ProfileAppBar extends StatelessWidget {
  final String? title;
  const ProfileAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class ProfileTopView extends StatelessWidget {
  const ProfileTopView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProfileScreenController>(
        id: Constant.idProfile,
        builder: (controller) {
          final isApprovedUser =
              Database.getUserProfileResponseModel?.user?.isVerified == true ||
              Database.loginUserVerified == true;

          return Row(
            children: [
              DottedBorder(
                borderType: BorderType.Circle,
                color: AppColors.black,
                dashPattern: [3, 2],
                strokeWidth: 1,
                child: Container(
                  clipBehavior: Clip.hardEdge,
                  height: 90,
                  width: 90,
                  decoration: BoxDecoration(
                    // color: AppColors.appRedColor,
                    shape: BoxShape.circle,
                  ),
                  // child: Image.asset(
                  //   AppAsset.personImage,
                  //   fit: BoxFit.cover,
                  // ),
                  child: CustomProfileImage(
                    image: Database.getUserProfileResponseModel?.user?.profileImage
                        ?? Database.loginUserProfilePic,
                  ),
                ),
              ).paddingOnly(right: 14),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        maxLines: 1,
                        Database.getUserProfileResponseModel?.user?.name
                            ?? (Database.loginUserName.isNotEmpty ? Database.loginUserName : "User Name"),
                        overflow: TextOverflow.ellipsis,
                        style: AppFontStyle.fontStyleW700(fontSize: 20, fontColor: AppColors.black),
                      ).paddingOnly(right: 5, bottom: 9),
                      isApprovedUser ? Image.asset(AppAsset.verificationRightIcon,height: 22,width: 22,) : SizedBox(),
                    ],
                  ),
                    isApprovedUser
                      ? Container(
                          decoration: BoxDecoration(
                            color: AppColors.green,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Row(
                            children: [
                              Image.asset(
                                AppAsset.verificationIcon,
                                width: 16,
                                height: 16,
                              ).paddingOnly(right: 5),
                              Text(
                                EnumLocale.txtVerificationApproved.name.tr,
                                style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.white),
                              )
                            ],
                          ).paddingSymmetric(vertical: 5, horizontal: 6),
                        )
                        : Database.hasPendingVerification
                          ? GetBuilder<ProfileScreenController>(builder: (controller) {
                              return GestureDetector(
                                onTap: () {
                                  Get.dialog(
                                    barrierColor: AppColors.black.withValues(alpha: 0.8),
                                    Dialog(
                                      insetPadding: EdgeInsets.symmetric(horizontal: 32),
                                      backgroundColor: AppColors.transparent,
                                      shadowColor: Colors.transparent,
                                      surfaceTintColor: Colors.transparent,
                                      elevation: 0,
                                      child: UserVerificationDialog(id: controller.userVerificationResponseModel?.data.uniqueId ?? ""),
                                    ),
                                  );
                                },
                                child: Container(
                                  decoration: BoxDecoration(
                                    color: AppColors.verifyRed,
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Row(
                                    children: [
                                      Image.asset(
                                        AppAsset.verificationPending,
                                        width: 16,
                                        height: 16,
                                      ).paddingOnly(right: 5),
                                      Text(
                                        EnumLocale.txtVerificationPending.name.tr,
                                        style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.white),
                                      )
                                    ],
                                  ).paddingSymmetric(vertical: 5, horizontal: 6),
                                ),
                              );
                            })
                          : GestureDetector(
                              onTap: () {
                                Get.toNamed(AppRoutes.userVerificationView);
                              },
                              child: Container(
                                decoration: BoxDecoration(
                                  color: AppColors.badgeColor,
                                  borderRadius: BorderRadius.circular(6),
                                ),
                                child: Row(
                                  children: [
                                    Image.asset(
                                      AppAsset.verificationIcon,
                                      width: 16,
                                      height: 16,
                                    ).paddingOnly(right: 5),
                                    Text(
                                      EnumLocale.txtMyGetVerificationBadge.name.tr,
                                      style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.white),
                                    )
                                  ],
                                ).paddingSymmetric(vertical: 5, horizontal: 6),
                              ),
                            )
                ],
              ),
            ],
          ).paddingOnly(top: 22, left: 16, bottom: 29);
        });
  }
}

class ProfileGeneralView extends StatelessWidget {
  const ProfileGeneralView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          EnumLocale.txtMyGeneral.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
        ).paddingOnly(left: 14, bottom: 18),
        Container(
          decoration: BoxDecoration(color: AppColors.profileItemBgColor, borderRadius: BorderRadius.circular(14)),
          child: Column(
            children: [
              GetBuilder<ProfileScreenController>(builder: (controller) {
                return ProfileItemView(
                  onTap: () {
                    Get.toNamed(AppRoutes.editProfileView)?.then(
                      (value) {
                        controller.profileApi();
                        // controller.update();
                      },
                    );
                  },
                  title: EnumLocale.txtMyProfile.name.tr,
                  imageIcon: AppAsset.myProfileIcon,
                );
              }),
              if (Database.loginType == 4)
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
              if (Database.loginType == 4)
                ProfileItemView(
                  onTap: () {
                    Get.toNamed(AppRoutes.changePasswordScreen);
                  },
                  title: 'Change Password',
                  imageIcon: AppAsset.privacyPolicyIcon,
                ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.transactionHistoryScreenView);
                },
                title: EnumLocale.txtMyTransactionHistory.name.tr,
                imageIcon: AppAsset.transactionHistory,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.walletScreen);
                },
                title: 'My Wallet',
                imageIcon: AppAsset.transactionHistory,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.withdrawScreen);
                },
                title: 'Withdraw Funds',
                imageIcon: AppAsset.transactionHistory,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.supportTicketScreen);
                },
                title: 'Support Tickets',
                imageIcon: AppAsset.transactionHistory,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.escrowOrdersScreen);
                },
                title: 'Escrow Orders',
                imageIcon: AppAsset.transactionHistory,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.bannerAdScreen);
                },
                title: 'Banner Ads',
                imageIcon: AppAsset.transactionHistory,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.favoriteScreen);
                },
                title: EnumLocale.txtMyFavorites.name.tr,
                imageIcon: AppAsset.blueFavouriteIcon,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.reviewScreenView);
                },
                title: EnumLocale.txtMyReviews.name.tr,
                imageIcon: AppAsset.myReviewIcon,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.myVideosScreen);
                },
                title: EnumLocale.txtMyVideos.name.tr,
                imageIcon: AppAsset.myVideoIcon,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.blockScreenView);
                },
                title: EnumLocale.txtBlocked.name.tr,
                imageIcon: AppAsset.blockUser,
                color: AppColors.faqTxt,
              ),
            ],
          ),
        ).paddingOnly(right: 14, left: 14, bottom: 30),
      ],
    );
  }
}

class ProfileItemView extends StatelessWidget {
  final String? imageIcon;
  final String? title;
  final Color? color;
  final Color? txtColor;
  final void Function()? onTap;
  const ProfileItemView({super.key, this.imageIcon, this.title, this.color, this.txtColor, this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        color: Colors.transparent,
        child: Row(
          children: [
            Image.asset(
              "$imageIcon",
              height: 28,
              width: 28,
            ).paddingOnly(right: 20),
            Text(
              "$title",
              style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: txtColor ?? AppColors.profileTxtColor),
            ),
            Spacer(),
            Image.asset(
              AppAsset.forwardIcon,
              height: 18,
              width: 18,
              color: color ?? AppColors.profileTxtColor,
            ),
          ],
        ).paddingSymmetric(horizontal: 20, vertical: 17),
      ),
    );
  }
}

class SubscriptionView extends StatelessWidget {
  const SubscriptionView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          EnumLocale.txtSubscription.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
        ).paddingOnly(left: 14, bottom: 18),
        Container(
          decoration: BoxDecoration(color: AppColors.profileItemBgColor, borderRadius: BorderRadius.circular(14)),
          child: Column(
            children: [
              Database.getUserProfileResponseModel?.user?.isSubscriptionExpired == true
                  ? SizedBox()
                  : ProfileItemView(
                      onTap: () {
                        Get.toNamed(AppRoutes.featuredAdsScreen);
                      },
                      title: EnumLocale.txtMyFeaturedAds.name.tr,
                      imageIcon: AppAsset.myFeaturedAdsIcon,
                    ),
              Database.getUserProfileResponseModel?.user?.isSubscriptionExpired == true
                  ? SizedBox()
                  : Divider(
                      color: AppColors.white,
                      height: 0,
                      indent: 7,
                      endIndent: 7,
                    ),
              ProfileItemView(
                onTap: () {
                  Get.toNamed(AppRoutes.subscriptionPlanScreen);
                },
                title: EnumLocale.txtSubscription.name.tr,
                imageIcon: AppAsset.subscriptionIcon,
              ),
              Divider(
                color: AppColors.white,
                height: 0,
                indent: 7,
                endIndent: 7,
              ),
            ],
          ),
        ).paddingOnly(right: 14, left: 14, bottom: 30),
      ],
    );
  }
}

class SettingView extends StatelessWidget {
  const SettingView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProfileScreenController>(builder: (controller) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            EnumLocale.txtSettings.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
          ).paddingOnly(left: 14, bottom: 18),
          Container(
            decoration: BoxDecoration(color: AppColors.profileItemBgColor, borderRadius: BorderRadius.circular(14)),
            child: Column(
              children: [
                ProfileItemView(
                  title: EnumLocale.txtLanguage.name.tr,
                  imageIcon: AppAsset.languageIcon,
                  onTap: () {
                    Get.toNamed(AppRoutes.languageScreenView);
                  },
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  title: EnumLocale.txtNotification.name.tr,
                  imageIcon: AppAsset.notificationIcon,
                  onTap: () {
                    Get.toNamed(AppRoutes.notificationScreenView);
                  },
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  onTap: () {
                    Get.toNamed(AppRoutes.blogScreen);
                  },
                  title: EnumLocale.txtBlogs.name.tr,
                  imageIcon: AppAsset.blogsIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  onTap: () {
                    Get.toNamed(AppRoutes.faqScreen);
                  },
                  title: EnumLocale.txtFAQs.name.tr,
                  imageIcon: AppAsset.faqIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  title: EnumLocale.txtShareThisAPP.name.tr,
                  imageIcon: AppAsset.blueShareIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  title: EnumLocale.txtRateUs.name.tr,
                  imageIcon: AppAsset.rateUsIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  onTap: () {
                    Get.toNamed(AppRoutes.contactUsScreen);
                  },
                  title: EnumLocale.txtContactUs.name.tr,
                  imageIcon: AppAsset.contactUsIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  onTap: () {
                    controller.onClickAboutUs();
                    // Get.toNamed(AppRoutes.aboutUsScreen);
                  },
                  title: EnumLocale.txtAboutUs.name.tr,
                  imageIcon: AppAsset.aboutUsIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  onTap: () {
                    controller.onClickTermsConditions();
                  },
                  title: EnumLocale.txtTermsConditions.name.tr,
                  imageIcon: AppAsset.termsAndConditionIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  onTap: () {
                    controller.onClickPrivacyPolicy();
                  },
                  title: EnumLocale.txtPrivacyPolicyTxt.name.tr,
                  imageIcon: AppAsset.privacyPolicyIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                ProfileItemView(
                  onTap: () {
                    Get.dialog(
                      barrierColor: AppColors.black.withValues(alpha: 0.8),
                      Dialog(
                        insetPadding: EdgeInsets.symmetric(horizontal: 32),
                        backgroundColor: AppColors.transparent,
                        shadowColor: Colors.transparent,
                        surfaceTintColor: Colors.transparent,
                        elevation: 0,
                        child: LogOutDialog(),
                      ),
                    );
                  },
                  title: EnumLocale.txtLogOut.name.tr,
                  imageIcon: AppAsset.logOutIcon,
                ),
                Divider(
                  color: AppColors.white,
                  height: 0,
                  indent: 7,
                  endIndent: 7,
                ),
                GestureDetector(
                  onTap: () {
                    Get.dialog(
                      barrierColor: AppColors.black.withValues(alpha: 0.8),
                      Dialog(
                        backgroundColor: AppColors.transparent,
                        shadowColor: Colors.transparent,
                        surfaceTintColor: Colors.transparent,
                        elevation: 0,
                        child: DeleteAccountDialog(
                          onTap: () {
                            controller.onDeleteAccount();
                          },
                        ),
                      ),
                    );
                  },
                  child: ProfileItemView(
                    title: EnumLocale.txtDeleteAccount.name.tr,
                    imageIcon: AppAsset.deleteAccount,
                    txtColor: AppColors.redColor,
                  ),
                ),
              ],
            ),
          ).paddingOnly(right: 14, left: 14, bottom: 30),
        ],
      );
    });
  }
}
