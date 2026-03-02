import 'dart:io';

import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/ui/upload_image_screen/controller/upload_image_screen_controller.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class UploadImageScreenAppBar extends StatelessWidget {
  final String? title;
  const UploadImageScreenAppBar({super.key, this.title});

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

class UploadImageScreenTopView extends StatelessWidget {
  const UploadImageScreenTopView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          EnumLocale.txtUploadProductImages.name.tr,
          style: AppFontStyle.fontStyleW700(
              fontSize: 18, fontColor: AppColors.appRedColor),
        ).paddingOnly(top: 18, left: 12),
        Text(
          EnumLocale.txtUploadImageTxt.name.tr,
          style: AppFontStyle.fontStyleW500(
              fontSize: 12, fontColor: AppColors.searchText),
        ).paddingOnly(top: 6, left: 12, right: 12, bottom: 28),
      ],
    );
  }
}

class MainCoverImageView extends StatelessWidget {
  const MainCoverImageView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              EnumLocale.txtMainCoverPicture.name.tr,
              style: AppFontStyle.fontStyleW700(
                  fontSize: 16, fontColor: AppColors.black),
            ).paddingOnly(right: 6),
            Text(
              "( ${EnumLocale.txtMax.name.tr} 3MB )",
              style: AppFontStyle.fontStyleW500(
                  fontSize: 14, fontColor: AppColors.black),
            )
          ],
        ).paddingOnly(left: 12, bottom: 8),
        Text(
          "(${EnumLocale.txtRecommendedSize.name.tr}1020 x 600)",
          style: AppFontStyle.fontStyleW400(
              fontSize: 12, fontColor: AppColors.searchText),
        ).paddingOnly(left: 12, bottom: 20),
        SingleImageUploadView().paddingOnly(bottom: 38),
        Row(
          children: [
            Text(
              EnumLocale.txtOtherPicture.name.tr,
              style: AppFontStyle.fontStyleW700(
                  fontSize: 16, fontColor: AppColors.black),
            ).paddingOnly(right: 6),
            Text(
              "( ${EnumLocale.txtMax.name.tr} 5 images )",
              style: AppFontStyle.fontStyleW500(
                  fontSize: 14, fontColor: AppColors.black),
            )
          ],
        ).paddingOnly(left: 12, bottom: 8),
        Text(
          "(${EnumLocale.txtRecommendedSize.name.tr}1020 x 600)",
          style: AppFontStyle.fontStyleW400(
              fontSize: 12, fontColor: AppColors.searchText),
        ).paddingOnly(left: 12, bottom: 20),
        ImageUploadGrid(),
      ],
    );
  }
}

class SingleImageUploadView extends StatelessWidget {
  const SingleImageUploadView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UploadImageScreenController>(
      init: UploadImageScreenController(),
      builder: (controller) {
        return Row(
          children: [
            if (controller.mainImage != null || controller.apiMainImage != null)
              Container(
                width: Get.height * 0.150,
                height: Get.height * 0.180,
                margin: const EdgeInsets.only(right: 12),
                child: Stack(
                  children: [
                    Container(
                      width: double.infinity,
                      height: double.infinity,
                      clipBehavior: Clip.antiAlias,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(18),
                        border: Border.all(color: Colors.grey.shade300),
                      ),
                      child: controller.mainImage != null
                          ? ClipRRect(
                        borderRadius: BorderRadiusGeometry.circular(18),
                            child: Image.file(File(controller.mainImage!),
                                fit: BoxFit.cover),
                          ) // ✅ Picked image
                          : ClipRRect(
                              borderRadius: BorderRadiusGeometry.circular(18),
                              child: CustomImageView(
                                  image: controller.apiMainImage!),
                            ),

                      // Image.network("${Api.baseUrl}${controller.apiMainImage!}", fit: BoxFit.cover), // ✅ API image
                    ),
                    Positioned(
                      top: 6,
                      right: 6,
                      child: GestureDetector(
                        onTap: () {
                          if (controller.mainImage != null) {
                            controller.removeImage(
                                fromApi: false); // remove picked
                          } else {
                            controller.removeImage(fromApi: true); // remove api
                          }
                        },
                        child: Container(
                          width: 22,
                          height: 22,
                          decoration: BoxDecoration(
                              color: AppColors.black, shape: BoxShape.circle),
                          child: const Icon(Icons.close,
                              size: 16, color: Colors.white),
                        ),
                      ),
                    ),
                  ],
                ),
              )
            else
              GestureDetector(
                onTap: () => controller.pickSingleImage(),
                child: Container(
                  width: Get.height * 0.150,
                  height: Get.height * 0.180,
                  margin: const EdgeInsets.only(right: 12),
                  child: DottedBorder(
                    stackFit: StackFit.expand,
                    strokeCap: StrokeCap.butt,
                    dashPattern: [3, 2],
                    radius: Radius.circular(18),
                    borderType: BorderType.RRect,
                    color: AppColors.dottedBorderColor,
                    child: Container(
                      clipBehavior: Clip.hardEdge,
                      decoration: BoxDecoration(
                        color: AppColors.lightColor,
                        borderRadius: BorderRadius.circular(13),
                      ),
                      child: Center(
                        child: Image.asset(
                          AppAsset.purpleAddIcon,
                          width: 32,
                          height: 32,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
          ],
        ).paddingOnly(left: 12);
      },
    );
  }
}

class ImageUploadGrid extends StatelessWidget {
  const ImageUploadGrid({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UploadImageScreenController>(
      init: UploadImageScreenController(),
      builder: (controller) {
        return Row(
          children: [
            if (controller.totalCount < 5)
              Container(
                width: Get.height * 0.150,
                height: Get.height * 0.180,
                margin: const EdgeInsets.only(right: 12),
                child: GestureDetector(
                  onTap: () {
                    controller.pickOtherImages();
                  },
                  child: DottedBorder(
                    stackFit: StackFit.expand,
                    strokeCap: StrokeCap.butt,
                    dashPattern: [3, 2],
                    radius: Radius.circular(18),
                    borderType: BorderType.RRect,
                    color: AppColors.dottedBorderColor,
                    child: Container(
                      clipBehavior: Clip.hardEdge,
                      decoration: BoxDecoration(
                        color: AppColors.lightColor,
                        borderRadius: BorderRadius.circular(13),
                      ),
                      child: Center(
                        child: Image.asset(
                          AppAsset.purpleAddIcon,
                          width: 32,
                          height: 32,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            Expanded(
              child: (controller.finalGalleryImages.isEmpty)
                  ? const SizedBox.shrink()
                  : SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: List.generate(
                          controller.finalGalleryImages.length,
                          (index) {
                            final image = controller.finalGalleryImages[index];
                            final isApiImage =
                                index < controller.apiGalleryImages.length;

                            return Container(
                              width: Get.height * 0.150,
                              height: Get.height * 0.180,
                              margin: const EdgeInsets.only(right: 12),
                              child: Stack(
                                children: [
                                  Container(
                                    width: double.infinity,
                                    height: double.infinity,
                                    clipBehavior: Clip.hardEdge,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(18),
                                      border: Border.all(
                                          color: Colors.grey.shade300),
                                    ),
                                    child: (image.startsWith('http') ||
                                            image.startsWith('https'))
                                        ? CustomImageView(
                                            image: controller
                                                .getFullImageUrl(image),
                                            fit: BoxFit.cover,
                                          )
                                        : (image.startsWith('/storage') ||
                                                image.startsWith('/data'))
                                            ? ClipRRect(
                                      borderRadius: BorderRadiusGeometry.circular(18),
                                              child: Image.file(
                                                  File(image),
                                                  fit: BoxFit.cover,
                                                ),
                                            )
                                            : ClipRRect(
                                      borderRadius: BorderRadiusGeometry.circular(18),
                                              child: CustomImageView(
                                                  image: "$image",
                                                  fit: BoxFit.cover,
                                                ),
                                            ),
                                  ),
                                  Positioned(
                                    top: 6,
                                    right: 6,
                                    child: GestureDetector(
                                      onTap: () {
                                        controller.removeOtherImage(
                                          isApiImage
                                              ? index
                                              : index -
                                                  controller
                                                      .apiGalleryImages.length,
                                          // fromApi: isApiImage,
                                        );
                                      },
                                      child: Container(
                                        width: 22,
                                        height: 22,
                                        decoration: BoxDecoration(
                                          color: AppColors.black,
                                          shape: BoxShape.circle,
                                        ),
                                        child: const Icon(Icons.close,
                                            size: 16, color: Colors.white),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                      ),
                    ),
            ),
          ],
        ).paddingOnly(left: 12);
      },
    );
  }
}

class UploadImageBottomView extends StatelessWidget {
  const UploadImageBottomView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UploadImageScreenController>(builder: (controller) {
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
                  offset: Offset(0, -2),
                  spreadRadius: 0,
                ),
              ],
            ),
            child: PrimaryAppButton(
              color: ((controller.mainImage?.isEmpty ?? true) && (controller.apiMainImage?.isEmpty ?? true)) ||
                  controller.finalGalleryImages.isEmpty
                  ? AppColors.grey.withValues(alpha: 0.4)
                  : AppColors.appRedColor,

              onTap: () async {
                controller.validationForImage();
              },
              text: EnumLocale.txtNext.name.tr,
              height: 54,
            ).paddingSymmetric(vertical: 12, horizontal: 16),
          ),
        ],
      );
    });
  }
}
