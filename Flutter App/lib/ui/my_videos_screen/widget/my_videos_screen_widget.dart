import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/dialog/remove_video_dialog.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/my_videos_screen/controller/my_videos_screen_controller.dart';
import 'package:listify/ui/my_videos_screen/shimmer/video_show_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class MyVideosScreenAppBar extends StatelessWidget {
  final String? title;
  const MyVideosScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class MyVideosScreenWidget extends StatelessWidget {
  const MyVideosScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MyVideosScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          return controller.isLoading
              ? const VideoShowShimmer()
              : controller.sellerVideoList.isEmpty
                  ? SizedBox(
                      height: Get.height * 0.67,
                      child: Center(child: NoDataFound(image: AppAsset.noProductFound, imageHeight: 160, text: EnumLocale.txtNoDataFound.name.tr)))
                  : Column(
                    children: [
                      GridView.builder(
                          padding: EdgeInsets.zero,
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: controller.sellerVideoList.length,
                          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            childAspectRatio: 0.76,
                            crossAxisSpacing: 6,
                            mainAxisSpacing: 6,
                          ),
                          itemBuilder: (context, index) {
                            // return ProductShimmer();
                            return GestureDetector(
                              onTap: () {
                                // Get.find<BottomBarController>().onClick(2);
                                // Get.to(VideosScreenView());
                              },
                              child: Container(
                                decoration: BoxDecoration(
                                  border: Border.all(color: AppColors.borderColor),
                                  color: AppColors.white,
                                  borderRadius: BorderRadius.circular(18),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    // Product image
                                    Stack(
                                      children: [
                                        SizedBox(
                                          height: 180,
                                          width: Get.width,
                                          child: ClipRRect(
                                            borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
                                            child: CustomImageView(
                                              image: controller.sellerVideoList[index].thumbnailUrl ?? '',
                                              fit: BoxFit.fill,
                                            ),
                                          ).paddingAll(1),
                                        ),
                                        Positioned(
                                          top: 6,
                                          right: 6,
                                          child: GestureDetector(
                                            onTap: () {
                                              Get.dialog(
                                                barrierColor: AppColors.black.withValues(alpha: 0.8),
                                                Dialog(
                                                  insetPadding: EdgeInsets.symmetric(horizontal: 32),
                                                  backgroundColor: AppColors.transparent,
                                                  shadowColor: Colors.transparent,
                                                  surfaceTintColor: Colors.transparent,
                                                  elevation: 0,
                                                  child: RemoveVideoDialog(
                                                    onTap: () {
                                                      controller.deleteMyVideo(
                                                        videoId: controller.sellerVideoList[index].id ?? "",
                                                        index: index,
                                                      );
                                                    },
                                                  ),
                                                ),
                                              );
                                            },
                                            child: Container(
                                              decoration: BoxDecoration(
                                                shape: BoxShape.circle,
                                                color: AppColors.black.withValues(alpha: 0.23),
                                              ),
                                              child: Image.asset(
                                                AppAsset.deleteIcon,
                                                height: 20,
                                                width: 20,
                                              ).paddingAll(6),
                                            ),
                                          ),
                                        ),
                                        Positioned(
                                          top: 6,
                                          left: 6,
                                          child: Container(
                                            padding: EdgeInsets.symmetric(vertical: 3, horizontal: 6),
                                            decoration: BoxDecoration(
                                              borderRadius: BorderRadius.circular(30),
                                              color: AppColors.black.withValues(alpha: 0.23),
                                            ),
                                            child: Row(
                                              children: [
                                                Image.asset(
                                                  AppAsset.eyeFillIcon,
                                                  color: AppColors.white,
                                                  height: 15,
                                                  width: 15,
                                                ).paddingOnly(right: 3),
                                                Text(
                                                  controller.sellerVideoList[index].totalViews.toString(),
                                                  style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.white),
                                                )
                                              ],
                                            ),
                                          ),
                                        )
                                      ],
                                    ),
                                    Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(controller.sellerVideoList[index].adDetails?.title ?? '',
                                                style: AppFontStyle.fontStyleW700(fontSize: 14, fontColor: AppColors.black))
                                            .paddingOnly(top: 6, bottom: 1),
                                        Text(
                                          controller.sellerVideoList[index].adDetails?.subTitle ?? '',
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.grey),
                                        ).paddingOnly(right: 8),
                                      ],
                                    ).paddingOnly(left: 6)
                                  ],
                                ),
                              ),
                            );
                          },
                        ).paddingOnly(top: 20, left: 16, right: 16, bottom: 20),

                      GetBuilder<MyVideosScreenController>(
                        id: Constant.idPagination,
                        builder: (controller) => Visibility(
                          visible: controller.isPaginationLoading,
                          child: CircularProgressIndicator(color: AppColors.appRedColor),
                        ),
                      ),
                    ],
                  );
        });
  }
}

class MyVideosBottomButton extends StatelessWidget {
  const MyVideosBottomButton({super.key});

  @override
  Widget build(BuildContext context) {
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
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          PrimaryAppButton(
            widget: Image.asset(
              AppAsset.uploadImageIcon,
              height: 26,
              width: 26,
            ),
            text: EnumLocale.txtUploadVideo.name.tr,
            height: 54,
            onTap: () {
              Get.toNamed(AppRoutes.uploadVideoScreen);
            },
          ).paddingSymmetric(vertical: 12, horizontal: 16),
        ],
      ),
    );
  }
}
