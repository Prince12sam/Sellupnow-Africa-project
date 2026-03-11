import 'dart:io';

import 'package:dotted_border/dotted_border.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/sub_category_product_screen/shimmer/product_list_view_shimmer.dart';
import 'package:listify/ui/upload_video_screen/controller/upload_video_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';
import 'package:video_player/video_player.dart';

class UploadVideoScreenAppBar extends StatelessWidget {
  final String? title;
  const UploadVideoScreenAppBar({super.key, this.title});

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

class UploadVideoScreenWidget extends StatelessWidget {
  const UploadVideoScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          EnumLocale.txtUploadProductVideo.name.tr,
          style: AppFontStyle.fontStyleW700(fontSize: 18, fontColor: AppColors.appRedColor),
        ).paddingOnly(top: 18, left: 12),
        Text(
          EnumLocale.txtUploadImageTxt.name.tr,
          style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText),
        ).paddingOnly(top: 6, left: 12, right: 12, bottom: 15),
        Row(
          children: [
            Text(
              EnumLocale.txtProductPicture.name.tr,
              style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),
            ).paddingOnly(right: 6),
            Text(
              "( ${EnumLocale.txtMax.name.tr} 3MB )",
              style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.black),
            )
          ],
        ).paddingOnly(left: 12, bottom: 8, top: 10),
        Text(
          "(${EnumLocale.txtRecommendedSize.name.tr}1020 x 600)",
          style: AppFontStyle.fontStyleW400(fontSize: 12, fontColor: AppColors.searchText),
        ).paddingOnly(left: 12, bottom: 20),
        VideoUploadView(),
        UploadVideoDetailScreenWidget(),
      ],
    );
  }
}

class VideoUploadView extends StatelessWidget {
  const VideoUploadView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UploadVideoScreenController>(
      init: UploadVideoScreenController(),
      builder: (controller) {
        return Row(
          children: [
            // 1) When no video selected: picker tile
            if (controller.videoFile == null)
              Container(
                width: Get.height * 0.170,
                height: Get.height * 0.220,
                margin: const EdgeInsets.only(right: 12),
                child: GestureDetector(
                  onTap: () => controller.pickVideo(),
                  child: DottedBorder(
                    stackFit: StackFit.expand,
                    strokeCap: StrokeCap.butt,
                    dashPattern: const [3, 2],
                    radius: const Radius.circular(18),
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
                          AppAsset.uploadVideoIcon,
                          width: 51,
                          height: 48,
                        ),
                      ),
                    ),
                  ),
                ),
              ),

            // 2) When video selected: show generated THUMBNAIL
            if (controller.videoFile != null)
              Container(
                width: Get.height * 0.170,
                height: Get.height * 0.220,
                margin: const EdgeInsets.only(right: 12),
                child: Stack(
                  children: [
                    // whole tile is tappable => opens full-screen player
                    Positioned.fill(
                      child: GestureDetector(
                        onTap: () {
                          final path = controller.videoFile!.path;
                          Get.to(() => FullScreenVideoPage(videoPath: path));
                        },
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(18),
                          child: controller.videoThumbPath != null
                              ? Image.file(
                                  File(controller.videoThumbPath!),
                                  fit: BoxFit.cover,
                                )
                              : Container(
                                  color: Colors.black12,
                                  child: Center(
                                    child: CupertinoActivityIndicator(
                                      color: AppColors.appRedColor,
                                    ),
                                  ),
                                ),
                        ),
                      ),
                    ),

                    Positioned(
                      top: 6,
                      right: 6,
                      child: GestureDetector(
                        onTap: () => controller.removeVideo(),
                        child: Container(
                          width: 22,
                          height: 22,
                          decoration: const BoxDecoration(
                            color: Colors.black,
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.close, size: 16, color: Colors.white),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
          ],
        ).paddingOnly(left: 12);
      },
    );
  }
}

class FullScreenVideoPage extends StatefulWidget {
  final String videoPath;
  const FullScreenVideoPage({super.key, required this.videoPath});

  @override
  State<FullScreenVideoPage> createState() => _FullScreenVideoPageState();
}

class _FullScreenVideoPageState extends State<FullScreenVideoPage> {
  late final VideoPlayerController _controller;
  bool _ready = false;

  @override
  void initState() {
    super.initState();
    _controller = VideoPlayerController.file(File(widget.videoPath))
      ..initialize().then((_) {
        setState(() => _ready = true);
        _controller.play();
      });
    _controller.setLooping(true);
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _toggle() {
    if (_controller.value.isPlaying) {
      _controller.pause();
    } else {
      _controller.play();
    }
    setState(() {}); // center icon refresh
  }

  @override
  Widget build(BuildContext context) {
    final double topPad = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        clipBehavior: Clip.none,
        children: [
          Center(
            child: _ready
                ? AspectRatio(
                    aspectRatio: _controller.value.aspectRatio,
                    child: VideoPlayer(_controller),
                  )
                : CircularProgressIndicator(
                    color: AppColors.appRedColor,
                  ),
          ),
          Positioned.fill(
            child: GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTap: _toggle,
              child: const SizedBox.expand(),
            ),
          ),
          if (_ready)
            IgnorePointer(
              ignoring: false, // visual only; taps pass-through
              child: Center(
                child: Icon(
                  _controller.value.isPlaying ? Icons.pause_circle_filled : Icons.play_circle_filled,
                  size: 60,
                  color: Colors.white70,
                ),
              ),
            ),
          Positioned(
            top: topPad + 8,
            left: 17,
            child: GestureDetector(
              onTap: () {
                Utils.showLog("Back tapped from fullscreen");
                Get.back();
              },
              child: Container(
                height: 40,
                width: 40,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.categoriesBgColor,
                ),
                child: Padding(
                  padding: const EdgeInsets.all(6),
                  child: Image.asset(
                    AppAsset.backArrowIcon,
                    color: AppColors.black,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class UploadVideoDetailBottomButton extends StatelessWidget {
  const UploadVideoDetailBottomButton({super.key});

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
            text: EnumLocale.txtUpload.name.tr,
            height: 54,
            onTap: () {
              Get.toNamed(AppRoutes.uploadVideoDetailScreen);
            },
          ).paddingSymmetric(vertical: 12, horizontal: 16),
        ],
      ),
    );
  }
}

/// detail views

class UploadVideoDetailScreenWidget extends StatelessWidget {
  const UploadVideoDetailScreenWidget({super.key});

  Future<void> _openProductPicker(BuildContext context, UploadVideoScreenController controller) async {
    final picked = await showModalBottomSheet<dynamic>(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (context) => const ProductBottomSheet(),
    );
    if (picked != null) {
      controller.setSelectedProduct(picked);
    }
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UploadVideoScreenController>(builder: (controller) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          GetBuilder<UploadVideoScreenController>(builder: (controller) {
            final p = controller.selectedProduct;

            // Title
            return Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  EnumLocale.txtSelectedRelatedProduct.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.popularProductText),
                ).paddingOnly(left: 14, right: 14, top: 23, bottom: 12),
                if (p == null)
                  GestureDetector(
                    onTap: () => _openProductPicker(context, controller),
                    child: Container(
                      height: 50,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(10),
                        color: AppColors.editTextFieldColor,
                        border: Border.all(color: AppColors.textFieldBorderColor),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.add, color: AppColors.appRedColor, size: 24).paddingOnly(right: 6),
                          Text(
                            EnumLocale.txtSelectProduct.name.tr,
                            style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.appRedColor),
                          )
                        ],
                      ),
                    ).paddingOnly(left: 14, right: 14, bottom: 22),
                  )
                else
                  Container(
                    width: double.infinity,
                    decoration: BoxDecoration(
                      color: AppColors.editTextFieldColor,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: AppColors.textFieldBorderColor),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                EnumLocale.txtSelectedRelatedProduct.name.tr,
                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.darkGrey.withValues(alpha: 0.8)),
                              ).paddingOnly(left: 6, top: 6),
                              Row(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                children: [
                                  Container(
                                    width: 70,
                                    height: 70,
                                    margin: const EdgeInsets.all(6),
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(8),
                                      color: AppColors.grey,
                                    ),
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(8),
                                      child: CustomImageView(image: p.primaryImage ?? ''),
                                    ),
                                  ).paddingOnly(right: 10),
                                  // title + price
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                                      children: [
                                        Text(p.title ?? '', style: AppFontStyle.fontStyleW600(fontSize: 15, fontColor: AppColors.darkGrey))
                                            .paddingOnly(bottom: 3),
                                        Text(p.subTitle ?? '',
                                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.darkGrey.withValues(alpha: 0.7)))
                                            .paddingOnly(bottom: 3),
                                        Text(
                                          '${Database.settingApiResponseModel?.data?.currency?.symbol}${p.price ?? ''}',
                                          style: AppFontStyle.fontStyleW600(fontSize: 14, fontColor: AppColors.appRedColor),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        // Change / Clear
                        TextButton(
                          onPressed: () => _openProductPicker(context, controller),
                          child: Container(
                              padding: EdgeInsets.all(4),
                              decoration: BoxDecoration(color: AppColors.lightRed100, borderRadius: BorderRadius.circular(6)),
                              child: Text(EnumLocale.txtChange.name.tr,
                                  style: AppFontStyle.fontStyleW600(fontSize: 12, fontColor: AppColors.appRedColor))),
                        ),
                      ],
                    ),
                  ).paddingOnly(left: 14, right: 14, bottom: 22),
              ],
            );
          }),
          CustomTitle(
            title: EnumLocale.txtProductDetail.name.tr,
            method: CustomTextField(
              controller: controller.productDetails,
              filled: true,
              fillColor: AppColors.editTextFieldColor,
              borderColor: AppColors.textFieldBorderColor,
            ),
          ).paddingOnly(left: 14, right: 14, bottom: 22),
        ],
      );
    });
  }
}

class ProductBottomSheet extends StatelessWidget {
  const ProductBottomSheet({super.key});

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      top: false,
      child: GetBuilder<UploadVideoScreenController>(
          id: Constant.idAllAds,
          builder: (controller) {
            return Container(
              constraints: BoxConstraints(
                maxHeight: MediaQuery.of(context).size.height * 0.67,
              ),
              width: Get.width,
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  // Header
                  Container(
                    width: Get.width,
                    decoration: BoxDecoration(
                      color: AppColors.lightGrey100,
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Spacer(),
                        Text(
                          EnumLocale.txtSelectProduct.name.tr,
                          style: AppFontStyle.fontStyleW700(
                            fontSize: 18,
                            fontColor: AppColors.black,
                          ),
                        ).paddingOnly(left: 30, bottom: 16, top: 16),
                        const Spacer(),
                        InkWell(
                          onTap: () => Get.back(),
                          child: Image.asset(
                            AppAsset.closeFillIcon,
                            width: 30,
                          ).paddingOnly(top: 14),
                        )
                      ],
                    ).paddingSymmetric(horizontal: 16),
                  ),
                  Expanded(
                    child: controller.isLoading
                        ? ProductViewShimmer().paddingOnly(top: 10)
                        : controller.allAdsList.isEmpty
                            ? NoDataFound(image: AppAsset.noProductFound, imageHeight: 150, text: "No Data Found")
                            : ListView.builder(
                                physics: const AlwaysScrollableScrollPhysics(),
                                itemCount: controller.allAdsList.length,
                                itemBuilder: (context, index) {
                                  return GestureDetector(
                                    onTap: () {
                                      final selected = controller.allAdsList[index];
                                      Navigator.of(context).pop(selected);
                                    },
                                    child: Container(
                                      height: 80,
                                      width: Get.width,
                                      decoration: BoxDecoration(
                                        color: AppColors.txtFieldBorder.withValues(alpha: 0.5),
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Row(
                                        children: [
                                          Container(
                                            width: 80,
                                            height: 80,
                                            decoration: BoxDecoration(
                                              color: AppColors.grey,
                                              borderRadius: BorderRadius.circular(8),
                                            ),
                                            child: ClipRRect(
                                                borderRadius: BorderRadius.circular(8),
                                                child: CustomImageView(image: controller.allAdsList[index].primaryImage ?? '')),
                                          ).paddingOnly(right: 16),
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                                              children: [
                                                Text(controller.allAdsList[index].title ?? '',
                                                    maxLines: 1,
                                                    overflow: TextOverflow.ellipsis,
                                                    style: AppFontStyle.fontStyleW600(fontSize: 15, fontColor: AppColors.darkGrey)),
                                                Text(controller.allAdsList[index].subTitle ?? '',
                                                    maxLines: 1,
                                                    overflow: TextOverflow.ellipsis,
                                                    style:
                                                        AppFontStyle.fontStyleW600(fontSize: 12, fontColor: AppColors.darkGrey.withValues(alpha: 0.7))),
                                                Text(
                                                    '${Database.settingApiResponseModel?.data?.currency?.symbol}${controller.allAdsList[index].price ?? ' '}',
                                                    style: AppFontStyle.fontStyleW600(fontSize: 15, fontColor: AppColors.appRedColor)),
                                              ],
                                            ),
                                          )
                                        ],
                                      ),
                                    ).paddingOnly(left: 12, right: 12, bottom: 14, top: index == 0 ? 14 : 0),
                                  );
                                },
                              ),
                  ),

                  4.height,
                ],
              ),
            );
          }),
    );
  }
}

class UploadVideoBottomButton extends StatelessWidget {
  const UploadVideoBottomButton({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<UploadVideoScreenController>(builder: (controller) {
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
              text: EnumLocale.txtUpload.name.tr,
              height: 54,
              onTap: () {
                controller.uploadVideo();
              },
            ).paddingSymmetric(vertical: 12, horizontal: 16),
          ],
        ),
      );
    });
  }
}
