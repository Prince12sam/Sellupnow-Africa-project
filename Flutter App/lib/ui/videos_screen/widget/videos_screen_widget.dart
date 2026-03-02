import 'package:blurrycontainer/blurrycontainer.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/bottom_bar/controller/bottom_bar_controller.dart';
import 'package:listify/ui/videos_screen/controller/videos_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';
import 'package:video_player/video_player.dart';
import 'package:url_launcher/url_launcher.dart';

import 'full_screen_video.dart';

class VideoScreenWidget extends StatefulWidget {
  final int index;
  final bool isCurrentReel;

  const VideoScreenWidget({
    super.key,
    required this.index,
    required this.isCurrentReel,
  });

  @override
  VideoScreenWidgetState createState() => VideoScreenWidgetState();
}

/*
class VideoScreenWidgetState extends State<VideoScreenWidget>
    with SingleTickerProviderStateMixin {
  /// UI chrome (overlays) visibility
  bool _chromeVisible = true;

  void _enterImmersive() {
    setState(() => _chromeVisible = false);
    // status/nav bars hide
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);
  }

  void _exitImmersive() {
    setState(() => _chromeVisible = true);
    // status/nav bars show
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
  }

  void _toggleImmersive() {
    if (_chromeVisible) {
      _enterImmersive();
    } else {
      _exitImmersive();
    }
  }

  @override
  void dispose() {
    // restore bars on dispose
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VideosScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        final VideoPlayerController? videoController =
        controller.videoControllers[widget.index];

        final bool isMuted = controller.isMutedMap[widget.index] ?? false;
        final bool isPlaying = controller.isPlayingMap[widget.index] ?? false;

        final reel = controller.myVideosList[widget.index];
        final reelImage = reel.adDetails?.primaryImage;
        final price = reel.adDetails?.price.toString();
        final productName = reel.adDetails?.title;
        final description = reel.adDetails?.description;
        final location = reel.adDetails?.location?.country;
        final bool isLiked = reel.isLike ?? false;

        // Common wrapper to animate show/hide of any overlay block
        Widget _chromeWrap(Widget child) => IgnorePointer(
          ignoring: !_chromeVisible,
          child: AnimatedOpacity(
            opacity: _chromeVisible ? 1 : 0,
            duration: const Duration(milliseconds: 180),
            curve: Curves.easeInOut,
            child: child,
          ),
        );

        return Stack(
          children: [
            // ======== FULL-SCREEN VIDEO / THUMBNAIL BACKDROP ========
            GestureDetector(
              onTap: () {
                // If chrome hidden → first tap just reveal UI (don’t pause/play)
                if (!_chromeVisible) {
                  _exitImmersive();
                  HapticFeedback.lightImpact();
                  return;
                }
                // When chrome is visible, keep your existing play/pause toggle
                controller.togglePlayPause(widget.index);
                HapticFeedback.lightImpact();
              },
              child: LayoutBuilder(
                builder: (context, constraints) {
                  if (videoController != null &&
                      videoController.value.isInitialized) {
                    final videoSize = videoController.value.size;
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: ClipRect(
                          child: FittedBox(
                            fit: BoxFit.cover,
                            alignment: Alignment.center,
                            child: SizedBox(
                              width: videoSize.width,
                              height: videoSize.height,
                              child: VideoPlayer(videoController),
                            ),
                          ),
                        ),
                      ),
                    );
                  } else {
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: Stack(
                          fit: StackFit.expand,
                          children: [
                            ClipRect(
                              child: Image.network(
                                reel.thumbnailUrl ?? '',
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stack) {
                                  Utils.showLog(
                                      "Error loading thumbnail: $error");
                                  return const SizedBox();
                                },
                              ),
                            ),
                            const Center(child: CircularProgressIndicator()),
                          ],
                        ),
                      ),
                    );
                  }
                },
              ),
            ),

            // ======== BOTTOM GRADIENT ========
            _chromeWrap(
              Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  const Spacer(),
                  Container(
                    width: double.infinity,
                    height: Get.height * 0.200,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.bottomCenter,
                        end: Alignment.topCenter,
                        colors: [
                          Colors.black.withValues(alpha: 0.68),
                          Colors.transparent,
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // ======== CENTER PLAY ICON (only when paused) ========
            _chromeWrap(
              Center(
                child: AnimatedOpacity(
                  opacity: isPlaying ? 0.0 : 1.0,
                  duration: const Duration(milliseconds: 200),
                  child: Container(
                    padding: const EdgeInsets.all(15),
                    decoration: const BoxDecoration(
                      color: Colors.black54,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.play_arrow,
                        color: Colors.white, size: 30),
                  ),
                ),
              ),
            ),

            // ======== BOTTOM CONTENT ROW ========
            Positioned(
              bottom: 0,
              left: -4,
              right: -5,
              child: _chromeWrap(
                Container(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      // LEFT SIDE — badges, product, user, caption
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            // Chip
                            Container(
                              padding: const EdgeInsets.all(1),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(51),
                                gradient: LinearGradient(colors: [
                                  AppColors.blueColor,
                                  AppColors.yellowColor
                                ]),
                                boxShadow: [
                                  BoxShadow(
                                    color: AppColors.blueShadowColor
                                        .withValues(alpha: 0.25),
                                    blurRadius: 4,
                                  ),
                                ],
                              ),
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(51),
                                  gradient: LinearGradient(colors: [
                                    AppColors.pinkColor,
                                    AppColors.purpleColor,
                                    AppColors.lightBlueColor
                                  ]),
                                ),
                                child: Text(
                                  EnumLocale.txtOurBestProduct.name.tr,
                                  style: AppFontStyle.fontStyleW7002(
                                    fontSize: 12,
                                    fontColor: AppColors.white,
                                  ),
                                ).paddingSymmetric(
                                    vertical: 5, horizontal: 14),
                              ),
                            ).paddingOnly(bottom: 14),

                            // Product card
                            GestureDetector(
                              onTap: () {
                                controller
                                    .videoControllers[widget.index]
                                    ?.pause();
                                Get.toNamed(AppRoutes.productDetailScreen,
                                    arguments: {
                                      'sellerDetail': true,
                                      'relatedProduct': true,
                                      'viewLikeCount': true,
                                      'adId': reel.ad
                                    })?.then((value) {
                                  controller
                                      .videoControllers[widget.index]
                                      ?.play();
                                });
                              },
                              child: ProductCardView(
                                image: reelImage,
                                price: price,
                                productName: productName,
                                description: description,
                              ).paddingOnly(bottom: 14),
                            ),

                            // User + follow
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(1.5),
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    border:
                                    Border.all(color: AppColors.white),
                                  ),
                                  child: Container(
                                    width: 44,
                                    height: 44,
                                    decoration: const BoxDecoration(
                                      shape: BoxShape.circle,
                                      color: Colors.white,
                                    ),
                                    child: ClipOval(
                                      child: CustomImageView(
                                        fit: BoxFit.cover,
                                        image:
                                        reel.uploader?.profileImage ?? '',
                                      ),
                                    ),
                                  ),
                                ).paddingOnly(right: 8),
                                Column(
                                  crossAxisAlignment:
                                  CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Row(
                                      children: [
                                        Text(
                                          reel.uploader?.name ?? '',
                                          style:
                                          AppFontStyle.fontStyleW700(
                                            fontSize: 16,
                                            fontColor: AppColors.white,
                                          ),
                                        ).paddingOnly(bottom: 7, right: 6),
                                        Database
                                            .getUserProfileResponseModel
                                            ?.user
                                            ?.id ==
                                            reel.uploader?.id
                                            ? const Offstage()
                                            : GetBuilder<
                                            VideosScreenController>(
                                            id: Constant.idFollow,
                                            builder: (context) {
                                              return GestureDetector(
                                                onTap: () {
                                                  controller
                                                      .onToggleFollow(
                                                    uid: Database
                                                        .getUserProfileResponseModel
                                                        ?.user
                                                        ?.firebaseUid ??
                                                        "",
                                                    toUserId: controller
                                                        .myVideosList[
                                                    widget
                                                        .index]
                                                        .uploader
                                                        ?.id ??
                                                        "",
                                                  );
                                                  HapticFeedback
                                                      .lightImpact();
                                                },
                                                child: Container(
                                                  decoration:
                                                  BoxDecoration(
                                                    borderRadius:
                                                    BorderRadius
                                                        .circular(
                                                        32),
                                                    color: AppColors
                                                        .followBgColor,
                                                  ),
                                                  child: Row(
                                                    children: [
                                                      controller
                                                          .followResponse
                                                          ?.isFollow ==
                                                          true
                                                          ? const SizedBox()
                                                          : Image.asset(
                                                        AppAsset
                                                            .personAddIcon,
                                                        height: 15,
                                                        width: 15,
                                                      ).paddingOnly(
                                                          right: 5),
                                                      Text(
                                                        controller
                                                            .followResponse
                                                            ?.isFollow ==
                                                            true
                                                            ? EnumLocale
                                                            .txtFollowing
                                                            .name
                                                            .tr
                                                            : EnumLocale
                                                            .txtFollow
                                                            .name
                                                            .tr,
                                                        style: AppFontStyle
                                                            .fontStyleW500(
                                                          fontSize: 13,
                                                          fontColor:
                                                          AppColors
                                                              .white,
                                                        ),
                                                      ),
                                                    ],
                                                  ).paddingSymmetric(
                                                      horizontal: 10,
                                                      vertical: 3),
                                                ),
                                              );
                                            }),
                                      ],
                                    ),
                                    Row(
                                      children: [
                                        Image.asset(
                                          AppAsset.locationIcon,
                                          color: AppColors.white,
                                          height: 14,
                                          width: 14,
                                        ).paddingOnly(right: 6),
                                        Text(
                                          location ?? '',
                                          style: AppFontStyle.fontStyleW500(
                                            fontSize: 12,
                                            fontColor: AppColors.white,
                                          ),
                                        )
                                      ],
                                    ),
                                  ],
                                ),
                              ],
                            ),

                            // Caption
                            SizedBox(
                              width: double.infinity,
                              child: ExpandableText(
                                reel.caption ?? '',
                                trimLines: 2,
                                style: AppFontStyle.fontStyleW500(
                                  fontSize: 12,
                                  fontColor: AppColors.white,
                                ),
                                moreLabel: 'View more'.tr,
                                lessLabel: '  ${'Less'.tr}',
                              ),
                            ).paddingOnly(top: 10, bottom: 17),
                          ],
                        ),
                      ),

                      // RIGHT SIDE — actions
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          // LIKE
                          GetBuilder<VideosScreenController>(
                            id: Constant.idAllAds,
                            builder: (context) {
                              return GestureDetector(
                                onTap: () {
                                  controller.onLikeTap(widget.index);
                                  controller.update([Constant.idAllAds]);
                                  HapticFeedback.lightImpact();
                                },
                                child: _ActionCircle(
                                  size: 42,
                                  child: Image.asset(
                                    isLiked == false
                                        ? AppAsset.favouriteWhiteIcon
                                        : AppAsset.favouriteFillIcon,
                                    height: 24,
                                    width: 24,
                                  ),
                                ),
                              ).paddingOnly(bottom: 24);
                            },
                          ),

                          // SHARE
                          GestureDetector(
                            onTap: () {
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(
                                AppAsset.shareIcon,
                                height: 24,
                                width: 24,
                              ),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MUTE/UNMUTE
                          GestureDetector(
                            onTap: () {
                              controller.toggleMute(widget.index);
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: isMuted
                                  ? const Icon(CupertinoIcons.volume_off,
                                  color: Colors.white)
                                  : Image.asset(AppAsset.volumeOnIcon,
                                  height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),

                          // ZOOM → ENTER IMMERSIVE (hide UI)
                          GestureDetector(
                            onTap: () {
                              _enterImmersive();
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(
                                AppAsset.zoomOutIcon,
                                height: 24,
                                width: 24,
                              ),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MORE
                          GestureDetector(
                            onTap: () {
                              showModalBottomSheet(
                                context: context,
                                isScrollControlled: true,
                                backgroundColor: Colors.white,
                                barrierColor: AppColors.black
                                    .withValues(alpha: 0.8),
                                builder: (context) => ReportBottomSheet(
                                  submitOnTap: () {
                                    controller.reportReelApi(controller
                                        .myVideosList[widget.index].id);
                                    controller.reasonController.clear();
                                  },
                                ),
                              );
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(
                                AppAsset.moreViewHorizontalIcon,
                                height: 24,
                                width: 24,
                              ),
                            ),
                          ).paddingOnly(bottom: 24),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}
*/
///
/*class VideoScreenWidgetState extends State<VideoScreenWidget>
    with SingleTickerProviderStateMixin {
  /// In-video UI chrome (overlays) visibility
  bool _chromeVisible = true;

  /// ===== IMMERSIVE HELPERS =====
  void _enterImmersive() {
    // Hide in-video chrome
    setState(() => _chromeVisible = false);

    // Hide Android/iOS system UI
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);

    // Hide app BottomBar (GetX)
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(false);
    }
  }

  void _exitImmersive() {
    // Show in-video chrome
    setState(() => _chromeVisible = true);

    // Show Android/iOS system UI
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);

    // Show app BottomBar (GetX)
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(true);
    }
  }

  void _toggleImmersive() {
    if (_chromeVisible) {
      _enterImmersive();
    } else {
      _exitImmersive();
    }
  }

  @override
  void dispose() {
    // Always restore system bars
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);

    // Ensure BottomBar visible when video widget disposes
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(true);
    }
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // Common wrapper to animate show/hide of any overlay block
    Widget _chromeWrap(Widget child) => IgnorePointer(
      ignoring: !_chromeVisible,
      child: AnimatedOpacity(
        opacity: _chromeVisible ? 1 : 0,
        duration: const Duration(milliseconds: 180),
        curve: Curves.easeInOut,
        child: child,
      ),
    );

    return GetBuilder<VideosScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        final VideoPlayerController? videoController =
        controller.videoControllers[widget.index];

        final bool isMuted =
            controller.isMutedMap[widget.index] ?? false;
        final bool isPlaying =
            controller.isPlayingMap[widget.index] ?? false;

        final reel = controller.myVideosList[widget.index];
        final reelImage = reel.adDetails?.primaryImage;
        final price = reel.adDetails?.price.toString();
        final productName = reel.adDetails?.title;
        final description = reel.adDetails?.description;
        final location = reel.adDetails?.location?.country;
        final bool isLiked = reel.isLike ?? false;

        return Stack(
          children: [
            // ======== FULL-SCREEN VIDEO / THUMBNAIL BACKDROP ========
            GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTap: () {
                // If chrome hidden → first tap just reveal UI (don’t pause/play)
                if (!_chromeVisible) {
                  _exitImmersive();
                  HapticFeedback.lightImpact();
                  return;
                }
                // When chrome is visible, keep existing play/pause toggle
                controller.togglePlayPause(widget.index);
                HapticFeedback.lightImpact();
              },
              onDoubleTap: _toggleImmersive,
              child: LayoutBuilder(
                builder: (context, constraints) {
                  if (videoController != null &&
                      videoController.value.isInitialized) {
                    final videoSize = videoController.value.size;
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: ClipRect(
                          child: FittedBox(
                            fit: BoxFit.cover,
                            alignment: Alignment.center,
                            child: SizedBox(
                              width: videoSize.width,
                              height: videoSize.height,
                              child: VideoPlayer(videoController),
                            ),
                          ),
                        ),
                      ),
                    );
                  } else {
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: Stack(
                          fit: StackFit.expand,
                          children: [
                            ClipRect(
                              child: Image.network(
                                reel.thumbnailUrl ?? '',
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stack) {
                                  Utils.showLog(
                                      "Error loading thumbnail: $error");
                                  return const SizedBox();
                                },
                              ),
                            ),
                            const Center(child: CircularProgressIndicator()),
                          ],
                        ),
                      ),
                    );
                  }
                },
              ),
            ),

            // ======== BOTTOM GRADIENT ========
            _chromeWrap(
              Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  const Spacer(),
                  Container(
                    width: double.infinity,
                    height: Get.height * 0.200,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.bottomCenter,
                        end: Alignment.topCenter,
                        colors: [
                          Colors.black.withValues(alpha: 0.68),
                          Colors.transparent,
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // ======== CENTER PLAY ICON (only when paused) ========
            _chromeWrap(
              Center(
                child: AnimatedOpacity(
                  opacity: isPlaying ? 0.0 : 1.0,
                  duration: const Duration(milliseconds: 200),
                  child: Container(
                    padding: const EdgeInsets.all(15),
                    decoration: const BoxDecoration(
                      color: Colors.black54,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.play_arrow,
                      color: Colors.white,
                      size: 30,
                    ),
                  ),
                ),
              ),
            ),

            // ======== BOTTOM CONTENT ROW ========
            Positioned(
              bottom: 0,
              left: -4,
              right: -5,
              child: _chromeWrap(
                Container(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      // LEFT SIDE — badges, product, user, caption
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            // Chip
                            Container(
                              padding: const EdgeInsets.all(1),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(51),
                                gradient: LinearGradient(colors: [
                                  AppColors.blueColor,
                                  AppColors.yellowColor
                                ]),
                                boxShadow: [
                                  BoxShadow(
                                    color: AppColors.blueShadowColor
                                        .withValues(alpha: 0.25),
                                    blurRadius: 4,
                                  ),
                                ],
                              ),
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(51),
                                  gradient: LinearGradient(colors: [
                                    AppColors.pinkColor,
                                    AppColors.purpleColor,
                                    AppColors.lightBlueColor
                                  ]),
                                ),
                                child: Text(
                                  EnumLocale.txtOurBestProduct.name.tr,
                                  style: AppFontStyle.fontStyleW7002(
                                    fontSize: 12,
                                    fontColor: AppColors.white,
                                  ),
                                ).paddingSymmetric(
                                    vertical: 5, horizontal: 14),
                              ),
                            ).paddingOnly(bottom: 14),

                            // Product card
                            GestureDetector(
                              onTap: () {
                                controller
                                    .videoControllers[widget.index]
                                    ?.pause();
                                Get.toNamed(AppRoutes.productDetailScreen,
                                    arguments: {
                                      'sellerDetail': true,
                                      'relatedProduct': true,
                                      'viewLikeCount': true,
                                      'adId': reel.ad
                                    })?.then((value) {
                                  controller
                                      .videoControllers[widget.index]
                                      ?.play();
                                });
                              },
                              child: ProductCardView(
                                image: reelImage,
                                price: price,
                                productName: productName,
                                description: description,
                              ).paddingOnly(bottom: 14),
                            ),

                            // User + follow
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(1.5),
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    border: Border.all(
                                        color: AppColors.white),
                                  ),
                                  child: Container(
                                    width: 44,
                                    height: 44,
                                    decoration: const BoxDecoration(
                                      shape: BoxShape.circle,
                                      color: Colors.white,
                                    ),
                                    child: ClipOval(
                                      child: CustomImageView(
                                        fit: BoxFit.cover,
                                        image: reel.uploader?.profileImage ?? '',
                                      ),
                                    ),
                                  ),
                                ).paddingOnly(right: 8),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Row(
                                      children: [
                                        Text(
                                          reel.uploader?.name ?? '',
                                          style: AppFontStyle.fontStyleW700(
                                            fontSize: 16,
                                            fontColor: AppColors.white,
                                          ),
                                        ).paddingOnly(bottom: 7, right: 6),
                                        Database.getUserProfileResponseModel
                                            ?.user
                                            ?.id ==
                                            reel.uploader?.id
                                            ? const Offstage()
                                            : GetBuilder<VideosScreenController>(
                                            id: Constant.idFollow,
                                            builder: (context) {
                                              return GestureDetector(
                                                onTap: () {
                                                  controller.onToggleFollow(
                                                    uid: Database
                                                        .getUserProfileResponseModel
                                                        ?.user
                                                        ?.firebaseUid ??
                                                        "",
                                                    toUserId: controller
                                                        .myVideosList[
                                                    widget.index]
                                                        .uploader
                                                        ?.id ??
                                                        "",
                                                  );
                                                  HapticFeedback
                                                      .lightImpact();
                                                },
                                                child: Container(
                                                  decoration: BoxDecoration(
                                                    borderRadius:
                                                    BorderRadius
                                                        .circular(32),
                                                    color: AppColors
                                                        .followBgColor,
                                                  ),
                                                  child: Row(
                                                    children: [
                                                      controller
                                                          .followResponse
                                                          ?.isFollow ==
                                                          true
                                                          ? const SizedBox()
                                                          : Image.asset(
                                                        AppAsset
                                                            .personAddIcon,
                                                        height: 15,
                                                        width: 15,
                                                      ).paddingOnly(
                                                          right: 5),
                                                      Text(
                                                        controller
                                                            .followResponse
                                                            ?.isFollow ==
                                                            true
                                                            ? EnumLocale
                                                            .txtFollowing
                                                            .name
                                                            .tr
                                                            : EnumLocale
                                                            .txtFollow
                                                            .name
                                                            .tr,
                                                        style: AppFontStyle
                                                            .fontStyleW500(
                                                          fontSize: 13,
                                                          fontColor:
                                                          AppColors
                                                              .white,
                                                        ),
                                                      ),
                                                    ],
                                                  ).paddingSymmetric(
                                                      horizontal: 10,
                                                      vertical: 3),
                                                ),
                                              );
                                            }),
                                      ],
                                    ),
                                    Row(
                                      children: [
                                        Image.asset(
                                          AppAsset.locationIcon,
                                          color: AppColors.white,
                                          height: 14,
                                          width: 14,
                                        ).paddingOnly(right: 6),
                                        Text(
                                          location ?? '',
                                          style: AppFontStyle.fontStyleW500(
                                            fontSize: 12,
                                            fontColor: AppColors.white,
                                          ),
                                        )
                                      ],
                                    ),
                                  ],
                                ),
                              ],
                            ),

                            // Caption
                            SizedBox(
                              width: double.infinity,
                              child: ExpandableText(
                                reel.caption ?? '',
                                trimLines: 2,
                                style: AppFontStyle.fontStyleW500(
                                  fontSize: 12,
                                  fontColor: AppColors.white,
                                ),
                                moreLabel: 'View more'.tr,
                                lessLabel: '  ${'Less'.tr}',
                              ),
                            ).paddingOnly(top: 10, bottom: 17),
                          ],
                        ),
                      ),

                      // RIGHT SIDE — actions
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          // LIKE
                          GetBuilder<VideosScreenController>(
                            id: Constant.idAllAds,
                            builder: (context) {
                              return GestureDetector(
                                onTap: () {
                                  controller.onLikeTap(widget.index);
                                  controller.update([Constant.idAllAds]);
                                  HapticFeedback.lightImpact();
                                },
                                child: _ActionCircle(
                                  size: 42,
                                  child: Image.asset(
                                    isLiked == false
                                        ? AppAsset.favouriteWhiteIcon
                                        : AppAsset.favouriteFillIcon,
                                    height: 24,
                                    width: 24,
                                  ),
                                ),
                              ).paddingOnly(bottom: 24);
                            },
                          ),

                          // SHARE
                          GestureDetector(
                            onTap: () {
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(
                                AppAsset.shareIcon,
                                height: 24,
                                width: 24,
                              ),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MUTE/UNMUTE
                          GestureDetector(
                            onTap: () {
                              controller.toggleMute(widget.index);
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: isMuted
                                  ? const Icon(
                                CupertinoIcons.volume_off,
                                color: Colors.white,
                              )
                                  : Image.asset(
                                AppAsset.volumeOnIcon,
                                height: 24,
                                width: 24,
                              ),
                            ),
                          ).paddingOnly(bottom: 24),

                          // ZOOM → ENTER IMMERSIVE (hide UI + BottomBar)
                          GestureDetector(
                            onTap: () {
                              _enterImmersive();
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(
                                AppAsset.zoomOutIcon,
                                height: 24,
                                width: 24,
                              ),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MORE
                          GestureDetector(
                            onTap: () {
                              showModalBottomSheet(
                                context: context,
                                isScrollControlled: true,
                                backgroundColor: Colors.white,
                                barrierColor:
                                AppColors.black.withValues(alpha: 0.8),
                                builder: (context) => ReportBottomSheet(
                                  submitOnTap: () {
                                    controller.reportReelApi(
                                        controller.myVideosList[widget.index].id);
                                    controller.reasonController.clear();
                                  },
                                ),
                              );
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(
                                AppAsset.moreViewHorizontalIcon,
                                height: 24,
                                width: 24,
                              ),
                            ),
                          ).paddingOnly(bottom: 24),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}*/

///last
/*
class VideoScreenWidgetState extends State<VideoScreenWidget>
    with SingleTickerProviderStateMixin, WidgetsBindingObserver, RouteAware {
  /// In-video UI chrome (overlays) visibility
  bool _chromeVisible = true;

  VideosScreenController get _videos =>
      Get.find<VideosScreenController>();

  VideoPlayerController? get _vc =>
      _videos.videoControllers[widget.index];

  /// ===== IMMERSIVE HELPERS =====
  void _enterImmersive() {
    setState(() => _chromeVisible = false);
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);

    // Hide BottomBar/FAB
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(false);
    }
  }

  void _exitImmersive() {
    setState(() => _chromeVisible = true);
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);

    // Show BottomBar/FAB
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(true);
    }
  }

  void _toggleImmersive() {
    _chromeVisible ? _enterImmersive() : _exitImmersive();
  }

  /// ===== LIFECYCLE / ROUTE HOOKS =====
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    // subscribe for route changes
    final route = ModalRoute.of(context);
    if (route is PageRoute) {
      routeObserver.subscribe(this, route);
    }
  }

  @override
  void dispose() {
    // Restore system bars & bottom bar
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(true);
    }

    // Ensure current video's paused
    _vc?.pause();

    WidgetsBinding.instance.removeObserver(this);
    routeObserver.unsubscribe(this);
    super.dispose();
  }

  /// Widget treeમાંથી remove થાય ત્યારે પણ pause
  @override
  void deactivate() {
    _vc?.pause();
    super.deactivate();
  }

  /// App lifecycle: background/lock/inactive → pause
  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.inactive ||
        state == AppLifecycleState.paused ||
        state == AppLifecycleState.detached) {
      _vc?.pause();
    }
  }

  /// ===== RouteAware callbacks =====
  /// Another page pushed on top of this one
  @override
  void didPushNext() {
    _vc?.pause(); // <<< IMPORTANT: push any new screen → pause
  }

  /// Returned to this page from top page (pop)
  /// Keep paused (user will tap to resume); if you want auto-resume current only, uncomment below.
  @override
  void didPopNext() {
    // Optional: Auto-resume only if this index equals currentIndex
    // if (_videos.currentIndex == widget.index) {
    //   _vc?.play();
    //   _videos.isPlayingMap[widget.index] = true;
    //   _videos.update([Constant.idAllAds]);
    // }

    // આ widget જે index પર છે, તે જ current reel હોય ત્યારે જ auto-play
    if (_videos.currentIndex == widget.index) {
      // immersive UI restore કરવી હોય તો options પ્રમાણે:
      if (!_chromeVisible) _exitImmersive();

      _vc?.play();
      _videos.isPlayingMap[widget.index] = true;
      _videos.update([Constant.idAllAds]);
    }
  }

  @override
  Widget build(BuildContext context) {
    // Common wrapper to animate show/hide of any overlay block
    Widget _chromeWrap(Widget child) => IgnorePointer(
      ignoring: !_chromeVisible,
      child: AnimatedOpacity(
        opacity: _chromeVisible ? 1 : 0,
        duration: const Duration(milliseconds: 180),
        curve: Curves.easeInOut,
        child: child,
      ),
    );

    return GetBuilder<VideosScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        final videoController = controller.videoControllers[widget.index];

        final bool isMuted = controller.isMutedMap[widget.index] ?? false;
        final bool isPlaying = controller.isPlayingMap[widget.index] ?? false;

        final reel = controller.myVideosList[widget.index];
        final reelImage = reel.adDetails?.primaryImage;
        final price = reel.adDetails?.price.toString();
        final productName = reel.adDetails?.title;
        final description = reel.adDetails?.description;
        final location = reel.adDetails?.location?.country;
        final bool isLiked = reel.isLike ?? false;

        return Stack(
          children: [
            // ======== FULL-SCREEN VIDEO / THUMBNAIL BACKDROP ========
            GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTap: () {
                // Chrome hidden → first tap reveals UI (no play/pause)
                if (!_chromeVisible) {
                  _exitImmersive();
                  HapticFeedback.lightImpact();
                  return;
                }
                // Chrome visible → toggle play/pause
                controller.togglePlayPause(widget.index);
                HapticFeedback.lightImpact();
              },
              onDoubleTap: _toggleImmersive,
              child: LayoutBuilder(
                builder: (context, constraints) {
                  if (videoController != null &&
                      videoController.value.isInitialized) {
                    final videoSize = videoController.value.size;
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: ClipRect(
                          child: FittedBox(
                            fit: BoxFit.cover,
                            alignment: Alignment.center,
                            child: SizedBox(
                              width: videoSize.width,
                              height: videoSize.height,
                              child: VideoPlayer(videoController),
                            ),
                          ),
                        ),
                      ),
                    );
                  } else {
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: Stack(
                          fit: StackFit.expand,
                          children: [
                            ClipRect(
                              child: Image.network(
                                reel.thumbnailUrl ?? '',
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stack) {
                                  Utils.showLog("Error loading thumbnail: $error");
                                  return const SizedBox();
                                },
                              ),
                            ),
                            const Center(child: CircularProgressIndicator()),
                          ],
                        ),
                      ),
                    );
                  }
                },
              ),
            ),

            // ======== BOTTOM GRADIENT ========
            _chromeWrap(
              Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  const Spacer(),
                  Container(
                    width: double.infinity,
                    height: Get.height * 0.200,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.bottomCenter,
                        end: Alignment.topCenter,
                        colors: [
                          Colors.black.withOpacity(0.68),
                          Colors.transparent,
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // ======== CENTER PLAY ICON (only when paused) ========
            _chromeWrap(
              Center(
                child: AnimatedOpacity(
                  opacity: isPlaying ? 0.0 : 1.0,
                  duration: const Duration(milliseconds: 200),
                  child: Container(
                    padding: const EdgeInsets.all(15),
                    decoration: const BoxDecoration(
                      color: Colors.black54,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.play_arrow, color: Colors.white, size: 30),
                  ),
                ),
              ),
            ),

            // ======== BOTTOM CONTENT ROW ========
            Positioned(
              bottom: 0,
              left: -4,
              right: -5,
              child: _chromeWrap(
                Container(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      // LEFT SIDE — badges, product, user, caption
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            // Chip
                            Container(
                              padding: const EdgeInsets.all(1),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(51),
                                gradient: LinearGradient(colors: [
                                  AppColors.blueColor,
                                  AppColors.yellowColor
                                ]),
                                boxShadow: [
                                  BoxShadow(
                                    color: AppColors.blueShadowColor.withOpacity(0.25),
                                    blurRadius: 4,
                                  ),
                                ],
                              ),
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(51),
                                  gradient: LinearGradient(colors: [
                                    AppColors.pinkColor,
                                    AppColors.purpleColor,
                                    AppColors.lightBlueColor
                                  ]),
                                ),
                                child: Text(
                                  EnumLocale.txtOurBestProduct.name.tr,
                                  style: AppFontStyle.fontStyleW7002(
                                    fontSize: 12,
                                    fontColor: AppColors.white,
                                  ),
                                ).paddingSymmetric(vertical: 5, horizontal: 14),
                              ),
                            ).paddingOnly(bottom: 14),

                            // Product card
                            GestureDetector(
                              onTap: () {
                                _vc?.pause(); // ensure pause before push
                                Get.toNamed(
                                  AppRoutes.productDetailScreen,
                                  arguments: {
                                    'sellerDetail': true,
                                    'relatedProduct': true,
                                    'viewLikeCount': true,
                                    'adId': reel.ad
                                  },
                                );
                              },
                              child: ProductCardView(
                                image: reelImage,
                                price: price,
                                productName: productName,
                                description: description,
                              ).paddingOnly(bottom: 14),
                            ),

                            // User + follow
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(1.5),
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    border: Border.all(color: AppColors.white),
                                  ),
                                  child: Container(
                                    width: 44,
                                    height: 44,
                                    decoration: const BoxDecoration(
                                      shape: BoxShape.circle,
                                      color: Colors.white,
                                    ),
                                    child: ClipOval(
                                      child: CustomImageView(
                                        fit: BoxFit.cover,
                                        image: reel.uploader?.profileImage ?? '',
                                      ),
                                    ),
                                  ),
                                ).paddingOnly(right: 8),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Row(
                                      children: [
                                        Text(
                                          reel.uploader?.name ?? '',
                                          style: AppFontStyle.fontStyleW700(
                                            fontSize: 16,
                                            fontColor: AppColors.white,
                                          ),
                                        ).paddingOnly(bottom: 7, right: 6),
                                        Database.getUserProfileResponseModel?.user?.id ==
                                            reel.uploader?.id
                                            ? const Offstage()
                                            : GetBuilder<VideosScreenController>(
                                          id: Constant.idFollow,
                                          builder: (context) {
                                            return GestureDetector(
                                              onTap: () {
                                                _videos.onToggleFollow(
                                                  uid: Database
                                                      .getUserProfileResponseModel
                                                      ?.user
                                                      ?.firebaseUid ??
                                                      "",
                                                  toUserId: _videos
                                                      .myVideosList[widget.index]
                                                      .uploader
                                                      ?.id ??
                                                      "",
                                                );
                                                HapticFeedback.lightImpact();
                                              },
                                              child: Container(
                                                decoration: BoxDecoration(
                                                  borderRadius: BorderRadius.circular(32),
                                                  color: AppColors.followBgColor,
                                                ),
                                                child: Row(
                                                  children: [
                                                    _videos.followResponse?.isFollow == true
                                                        ? const SizedBox()
                                                        : Image.asset(
                                                      AppAsset.personAddIcon,
                                                      height: 15,
                                                      width: 15,
                                                    ).paddingOnly(right: 5),
                                                    Text(
                                                      _videos.followResponse?.isFollow == true
                                                          ? EnumLocale.txtFollowing.name.tr
                                                          : EnumLocale.txtFollow.name.tr,
                                                      style: AppFontStyle.fontStyleW500(
                                                        fontSize: 13,
                                                        fontColor: AppColors.white,
                                                      ),
                                                    ),
                                                  ],
                                                ).paddingSymmetric(horizontal: 10, vertical: 3),
                                              ),
                                            );
                                          },
                                        ),
                                      ],
                                    ),
                                    Row(
                                      children: [
                                        Image.asset(
                                          AppAsset.locationIcon,
                                          color: AppColors.white,
                                          height: 14,
                                          width: 14,
                                        ).paddingOnly(right: 6),
                                        Text(
                                          location ?? '',
                                          style: AppFontStyle.fontStyleW500(
                                            fontSize: 12,
                                            fontColor: AppColors.white,
                                          ),
                                        )
                                      ],
                                    ),
                                  ],
                                ),
                              ],
                            ),

                            // Caption
                            SizedBox(
                              width: double.infinity,
                              child: ExpandableText(
                                reel.caption ?? '',
                                trimLines: 2,
                                style: AppFontStyle.fontStyleW500(
                                  fontSize: 12,
                                  fontColor: AppColors.white,
                                ),
                                moreLabel: 'View more'.tr,
                                lessLabel: '  ${'Less'.tr}',
                              ),
                            ).paddingOnly(top: 10, bottom: 17),
                          ],
                        ),
                      ),

                      // RIGHT SIDE — actions
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          // LIKE
                          GetBuilder<VideosScreenController>(
                            id: Constant.idAllAds,
                            builder: (context) {
                              return GestureDetector(
                                onTap: () {
                                  _videos.onLikeTap(widget.index);
                                  _videos.update([Constant.idAllAds]);
                                  HapticFeedback.lightImpact();
                                },
                                child: _ActionCircle(
                                  size: 42,
                                  child: Image.asset(
                                    isLiked == false
                                        ? AppAsset.favouriteWhiteIcon
                                        : AppAsset.favouriteFillIcon,
                                    height: 24,
                                    width: 24,
                                  ),
                                ),
                              ).paddingOnly(bottom: 24);
                            },
                          ),

                          // SHARE
                          GestureDetector(
                            onTap: () {
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(AppAsset.shareIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MUTE/UNMUTE
                          GestureDetector(
                            onTap: () {
                              _videos.toggleMute(widget.index);
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: isMuted
                                  ? const Icon(CupertinoIcons.volume_off, color: Colors.white)
                                  : Image.asset(AppAsset.volumeOnIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),

                          // ZOOM → ENTER IMMERSIVE (hide UI + BottomBar)
                          GestureDetector(
                            onTap: () {
                              _enterImmersive();
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(AppAsset.zoomOutIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MORE
                          GestureDetector(
                            onTap: () {
                              showModalBottomSheet(
                                context: context,
                                isScrollControlled: true,
                                backgroundColor: Colors.white,
                                barrierColor: AppColors.black.withOpacity(0.8),
                                builder: (context) => ReportBottomSheet(
                                  submitOnTap: () {
                                    _videos.reportReelApi(
                                      _videos.myVideosList[widget.index].id,
                                    );
                                    _videos.reasonController.clear();
                                  },
                                ),
                              );
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(AppAsset.moreViewHorizontalIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}
*/
/*class VideoScreenWidgetState extends State<VideoScreenWidget>
    with SingleTickerProviderStateMixin, WidgetsBindingObserver, RouteAware {
  bool _chromeVisible = true;
  bool _isImmersiveMode = false; // Track if immersive mode is active
  bool _isScrolling = false; // Track if user is scrolling

  ScrollController _scrollController = ScrollController();

  VideosScreenController get _videos => Get.find<VideosScreenController>();
  VideoPlayerController? get _vc => _videos.videoControllers[widget.index];

  void _enterImmersive() {
    setState(() {
      _chromeVisible = false;
      _isImmersiveMode = true; // Immersive mode is on
    });
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);

    // Hide BottomBar/FAB
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(false);
    }
  }

  @override
  void didPopNext() {
    // Jyaare aa screen par pacha aaviye (next route pop thay),
    // current index no video auto-resume karo.
    if (Get.isRegistered<VideosScreenController>()) {
      final c = _videos;
      if (widget.index == c.currentIndex) {
        c.resumeCurrent(); // plays and updates UI
      }
    }
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    // subscribe for route changes
    final route = ModalRoute.of(context);
    if (route is PageRoute) {
      routeObserver.subscribe(this, route);
    }
  }

  void _exitImmersive() {
    setState(() {
      _chromeVisible = true;
      _isImmersiveMode = false; // Immersive mode is off
    });
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);

    // Show BottomBar/FAB
    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(true);
    }
  }

  void _toggleImmersive() {
    if (_isImmersiveMode) {
      _exitImmersive();
    } else {
      _enterImmersive();
    }
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);

    _scrollController.addListener(() {
      // When scrolling, hide UI
      if (_scrollController.position.userScrollDirection == ScrollDirection.reverse) {
        setState(() {
          _isScrolling = true;
        });
      } else if (_scrollController.position.userScrollDirection == ScrollDirection.forward) {
        setState(() {
          _isScrolling = false;
        });
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.inactive ||
        state == AppLifecycleState.paused ||
        state == AppLifecycleState.detached) {
      _vc?.pause();
    }
  }

  @override
  Widget build(BuildContext context) {
    // Common wrapper to animate show/hide of any overlay block
    Widget _chromeWrap(Widget child) => IgnorePointer(
      ignoring: !_chromeVisible,
      child: AnimatedOpacity(
        opacity: _chromeVisible ? 1 : 0,
        duration: const Duration(milliseconds: 180),
        curve: Curves.easeInOut,
        child: child,
      ),
    );

    return GetBuilder<VideosScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        final videoController = controller.videoControllers[widget.index];
        final bool isMuted = controller.isMutedMap[widget.index] ?? false;
        final bool isPlaying = controller.isPlayingMap[widget.index] ?? false;

        final reel = controller.myVideosList[widget.index];
        final reelImage = reel.adDetails?.primaryImage;
        final price = reel.adDetails?.price.toString();
        final productName = reel.adDetails?.title;
        final description = reel.adDetails?.description;
        final location = reel.adDetails?.location?.country;
        final bool isLiked = reel.isLike ?? false;

        return Stack(
          children: [
            // ======== FULL-SCREEN VIDEO / THUMBNAIL BACKDROP ========
            GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTap: () {
                // Chrome hidden → first tap reveals UI (no play/pause)
                if (!_chromeVisible) {
                  _exitImmersive();
                  HapticFeedback.lightImpact();
                  return;
                }
                // Chrome visible → toggle play/pause
                controller.togglePlayPause(widget.index);
                HapticFeedback.lightImpact();
              },
              onDoubleTap: _toggleImmersive,
              child: LayoutBuilder(
                builder: (context, constraints) {
                  if (videoController != null &&
                      videoController.value.isInitialized) {
                    final videoSize = videoController.value.size;
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: ClipRect(
                          child: FittedBox(
                            fit: BoxFit.cover,
                            alignment: Alignment.center,
                            child: SizedBox(
                              width: videoSize.width,
                              height: videoSize.height,
                              child: VideoPlayer(videoController),
                            ),
                          ),
                        ),
                      ),
                    );
                  } else {
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: Stack(
                          fit: StackFit.expand,
                          children: [
                            ClipRect(
                              child: Image.network(
                                reel.thumbnailUrl ?? '',
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stack) {
                                  Utils.showLog("Error loading thumbnail: $error");
                                  return const SizedBox();
                                },
                              ),
                            ),
                            const Center(child: CircularProgressIndicator()),
                          ],
                        ),
                      ),
                    );
                  }
                },
              ),
            ),

            // ======== BOTTOM GRADIENT ========
            _chromeWrap(
              Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  const Spacer(),
                  Container(
                    width: double.infinity,
                    height: Get.height * 0.200,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.bottomCenter,
                        end: Alignment.topCenter,
                        colors: [
                          Colors.black.withOpacity(0.68),
                          Colors.transparent,
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // ======== CENTER PLAY ICON (only when paused) ========
            _chromeWrap(
              Center(
                child: AnimatedOpacity(
                  opacity: isPlaying ? 0.0 : 1.0,
                  duration: const Duration(milliseconds: 200),
                  child: Container(
                    padding: const EdgeInsets.all(15),
                    decoration: const BoxDecoration(
                      color: Colors.black54,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.play_arrow, color: Colors.white, size: 30),
                  ),
                ),
              ),
            ),

            // ======== BOTTOM CONTENT ROW ========
            Positioned(
              bottom: 0,
              left: -4,
              right: -5,
              child: _chromeWrap(
                Container(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      // LEFT SIDE — badges, product, user, caption
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            // Chip
                            // Container(
                            //   padding: const EdgeInsets.all(1),
                            //   decoration: BoxDecoration(
                            //     borderRadius: BorderRadius.circular(51),
                            //     gradient: LinearGradient(colors: [
                            //       AppColors.blueColor,
                            //       AppColors.yellowColor
                            //     ]),
                            //     boxShadow: [
                            //       BoxShadow(
                            //         color: AppColors.blueShadowColor.withOpacity(0.25),
                            //         blurRadius: 4,
                            //       ),
                            //     ],
                            //   ),
                            //   child: Container(
                            //     decoration: BoxDecoration(
                            //       borderRadius: BorderRadius.circular(51),
                            //       gradient: LinearGradient(colors: [
                            //         AppColors.pinkColor,
                            //         AppColors.purpleColor,
                            //         AppColors.lightBlueColor
                            //       ]),
                            //     ),
                            //     child: Text(
                            //       EnumLocale.txtOurBestProduct.name.tr,
                            //       style: AppFontStyle.fontStyleW7002(
                            //         fontSize: 12,
                            //         fontColor: AppColors.white,
                            //       ),
                            //     ).paddingSymmetric(vertical: 5, horizontal: 14),
                            //   ),
                            // ).paddingOnly(bottom: 14),

                            // Product card
                            GestureDetector(
                              onTap: () {
                                _vc?.pause(); // ensure pause before push
                                Get.toNamed(
                                  AppRoutes.productDetailScreen,
                                  arguments: {
                                    'sellerDetail': true,
                                    'relatedProduct': true,
                                    'viewLikeCount': true,
                                    'adId': reel.ad
                                  },
                                );
                              },
                              child: ProductCardView(
                                image: reelImage,
                                price: price,
                                productName: productName,
                                description: description,
                              ).paddingOnly(bottom: 14),
                            ),
                          ],
                        ),
                      ),

                      // RIGHT SIDE — actions
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          // LIKE
                          GetBuilder<VideosScreenController>(
                            id: Constant.idAllAds,
                            builder: (context) {
                              return GestureDetector(
                                onTap: () {
                                  _videos.onLikeTap(widget.index);
                                  _videos.update([Constant.idAllAds]);
                                  HapticFeedback.lightImpact();
                                },
                                child: _ActionCircle(
                                  size: 42,
                                  child: Image.asset(
                                    isLiked == false
                                        ? AppAsset.favouriteWhiteIcon
                                        : AppAsset.favouriteFillIcon,
                                    height: 24,
                                    width: 24,
                                  ),
                                ),
                              ).paddingOnly(bottom: 24);
                            },
                          ),

                          // SHARE
                          GestureDetector(
                            onTap: () {
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(AppAsset.shareIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MUTE/UNMUTE
                          GestureDetector(
                            onTap: () {
                              _videos.toggleMute(widget.index);
                              HapticFeedback.lightImpact();
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: isMuted
                                  ? const Icon(CupertinoIcons.volume_off, color: Colors.white)
                                  : Image.asset(AppAsset.volumeOnIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),

                          // ZOOM → ENTER IMMERSIVE (hide UI + BottomBar)
                          GestureDetector(
                            onTap: () {
                              if (!_isImmersiveMode) {
                                _enterImmersive();
                                HapticFeedback.lightImpact();
                              }
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(AppAsset.zoomOutIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),

                          // MORE
                          GestureDetector(
                            onTap: () {
                              showModalBottomSheet(
                                context: context,
                                isScrollControlled: true,
                                backgroundColor: Colors.white,
                                barrierColor: AppColors.black.withOpacity(0.8),
                                builder: (context) => ReportBottomSheet(
                                  submitOnTap: () {
                                    controller.reportReelApi(controller
                                        .myVideosList[widget.index].id);
                                    controller.reasonController.clear();
                                  },

                                ),
                              );
                            },
                            child: _ActionCircle(
                              size: 42,
                              child: Image.asset(AppAsset.moreViewHorizontalIcon, height: 24, width: 24),
                            ),
                          ).paddingOnly(bottom: 24),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}*/
///
class VideoScreenWidgetState extends State<VideoScreenWidget>
    with SingleTickerProviderStateMixin, WidgetsBindingObserver, RouteAware {
  bool _isScrolling = false;
  final ScrollController _scrollController = ScrollController();

  // IMPORTANT: don't create a new controller here
  VideosScreenController get _videos => Get.find<VideosScreenController>();
  VideoPlayerController? get _vc => _videos.videoControllers[widget.index];

  void _enterImmersive() => _videos.setImmersive(true);
  void _exitImmersive() => _videos.setImmersive(false);
  void _toggleImmersive() => _videos.toggleImmersive();

  Future<void> _openUrl(String? url) async {
    if (url == null || url.trim().isEmpty) return;
    final uri = Uri.tryParse(url.trim());
    if (uri == null) return;
    await launchUrl(uri, mode: LaunchMode.externalApplication);
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);

    _scrollController.addListener(() {
      final dir = _scrollController.position.userScrollDirection;
      setState(() => _isScrolling = (dir == ScrollDirection.reverse));
    });
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    final route = ModalRoute.of(context);
    if (route is PageRoute) {
      routeObserver.subscribe(this, route);
    }
  }

  @override
  void dispose() {
    try {
      routeObserver.unsubscribe(this);
    } catch (_) {}
    _scrollController.dispose();
    super.dispose();
  }

  @override
  void didPushNext() {
    // ✅ Navigating AWAY from video screen → ensure immersive OFF
    if (Get.isRegistered<VideosScreenController>()) {
      Get.find<VideosScreenController>().setImmersive(false);
    }
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.inactive ||
        state == AppLifecycleState.paused ||
        state == AppLifecycleState.detached) {
      _vc?.pause();
    }
  }

  @override
  void didPopNext() {
    // back aavta auto-resume current
    if (Get.isRegistered<VideosScreenController>()) {
      final c = _videos;
      if (widget.index == c.currentIndex) c.resumeCurrent();
      c.setImmersive(false);
    }
  }

  // Common wrapper to animate show/hide of any overlay block using GLOBAL immersive
  Widget _chromeWrap(VideosScreenController controller, Widget child) =>
      IgnorePointer(
        ignoring: controller.isImmersive,
        child: AnimatedOpacity(
          opacity: controller.isImmersive ? 0 : 1,
          duration: const Duration(milliseconds: 180),
          curve: Curves.easeInOut,
          child: child,
        ),
      );

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VideosScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        final videoController = controller.videoControllers[widget.index];
        final bool isMuted = controller.isMutedMap[widget.index] ?? false;
        final bool isPlaying = controller.isPlayingMap[widget.index] ?? false;

        final reel = controller.myVideosList[widget.index];
        final reelImage = reel.adDetails?.primaryImage;
        final price = reel.adDetails?.price.toString();
        final productName = reel.adDetails?.title;
        final description = reel.adDetails?.description;
        final bool isLiked = reel.isLike ?? false;
        final location = reel.adDetails?.location?.country;
        final bool isSponsored = reel.isSponsored == true;
        final bottomAd = reel.bottomAd;

        return Stack(
          children: [
            // ======== FULL-SCREEN VIDEO / THUMBNAIL BACKDROP ========
            GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTap: () {
                // Immersive on → first tap exit immersive (global)
                if (controller.isImmersive) {
                  _exitImmersive();
                  HapticFeedback.lightImpact();
                  return;
                }
                // else toggle play/pause
                controller.togglePlayPause(widget.index);
                HapticFeedback.lightImpact();
              },
              onDoubleTap: () {
                if (controller.isLoading || !controller.controllersReady)
                  return;
                _toggleImmersive();
              },

              // _toggleImmersive,

              // double tap to toggle global immersive
              // child: LayoutBuilder(
              //   builder: (context, constraints) {
              //     if (videoController != null &&
              //         videoController.value.isInitialized) {
              //       final videoSize = videoController.value.size;
              //       return SizedBox.expand(
              //         child: ColoredBox(
              //           color: Colors.black,
              //           child: ClipRect(
              //             child: FittedBox(
              //               fit: BoxFit.cover,
              //               alignment: Alignment.center,
              //               child: SizedBox(
              //                 width: videoSize.width,
              //                 height: videoSize.height,
              //                 child: VideoPlayer(videoController),
              //               ),
              //             ),
              //           ),
              //         ),
              //       );
              //     } else {
              //       return SizedBox.expand(
              //         child: ColoredBox(
              //           color: Colors.black,
              //           child: Stack(
              //             fit: StackFit.expand,
              //             children: [
              //               ClipRect(
              //                 child: Image.network(
              //                   reel.thumbnailUrl ?? '',
              //                   fit: BoxFit.cover,
              //                   errorBuilder: (_, __, ___) => const SizedBox(),
              //                 ),
              //               ),
              //               const Center(child: CircularProgressIndicator()),
              //             ],
              //           ),
              //         ),
              //       );
              //     }
              //   },
              // ),

              child: LayoutBuilder(
                builder: (context, constraints) {
                  if (videoController != null &&
                      videoController.value.isInitialized) {
                    final videoSize = videoController.value.size;
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: ClipRect(
                          child: FittedBox(
                            fit: BoxFit.cover,
                            alignment: Alignment.center,
                            child: SizedBox(
                              width: videoSize.width,
                              height: videoSize.height,
                              child: VideoPlayer(videoController),
                            ),
                          ),
                        ),
                      ),
                    );
                  } else {
                    // ✅ initialize પહેલા shimmer બતાવો
                    return SizedBox.expand(
                      child: ColoredBox(
                        color: Colors.black,
                        child: Stack(
                          fit: StackFit.expand,
                          children: [
                            ClipRect(
                              child: Image.network(
                                reel.thumbnailUrl ?? '',
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => const SizedBox(),
                              ),
                            ),
                            const Center(child: CircularProgressIndicator()),
                          ],
                        ),
                      ),
                    );
                  }
                },
              ),
            ),

            // ======== BOTTOM GRADIENT ========
            _chromeWrap(
              controller,
              Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  const Spacer(),
                  Container(
                    width: double.infinity,
                    height: Get.height * 0.200,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.bottomCenter,
                        end: Alignment.topCenter,
                        colors: [
                          Colors.black.withOpacity(0.68),
                          Colors.transparent
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // ======== CENTER PLAY ICON (only when paused) ========
            _chromeWrap(
              controller,
              Center(
                child: AnimatedOpacity(
                  opacity: isPlaying ? 0.0 : 1.0,
                  duration: const Duration(milliseconds: 200),
                  child: Container(
                    padding: const EdgeInsets.all(15),
                    decoration: const BoxDecoration(
                      color: Colors.black54,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.play_arrow,
                        color: Colors.white, size: 30),
                  ),
                ),
              ),
            ),

            // ======== SPONSORED BADGE ========
            if (isSponsored)
              _chromeWrap(
                controller,
                Positioned(
                  top: 16,
                  left: 12,
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.amber.shade400,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      'Sponsored'.tr,
                      style: AppFontStyle.fontStyleW600(
                        fontSize: 11,
                        fontColor: AppColors.black,
                      ),
                    ),
                  ),
                ),
              ),

            // ======== BOTTOM CONTENT ROW ========
            Positioned(
              bottom: 0,
              left: 10,
              right: -5,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  GestureDetector(
                    onTap: () {
                      // optional: immersive off before navigation
                      _exitImmersive();
                      _vc?.pause();
                      Get.toNamed(
                        AppRoutes.productDetailScreen,
                        arguments: {
                          'sellerDetail': true,
                          'relatedProduct': true,
                          'viewLikeCount': true,
                          'adId': reel.ad
                        },
                      );
                    },
                    child: ProductCardView(
                      image: reelImage,
                      price: price,
                      productName: capitalizeWords(productName ?? ""),
                      description: description,
                    ).paddingOnly(bottom: 14),
                  ).paddingOnly(right: 35),

                  // User + follow
                  GestureDetector(
                    onTap: () {
                      _exitImmersive();
                      _vc?.pause();
                      Get.toNamed(
                        AppRoutes.sellerDetailScreenView,
                        arguments: {
                          'name': reel.uploader?.name,
                          'image': reel.uploader?.profileImage,
                          'register': reel.uploader?.registeredAt,
                          'userId': reel.uploader?.id,
                          // 'user': controller.productDetail?.data?.seller,
                        },
                      );
                    },
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(1.5),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            border: Border.all(color: AppColors.white),
                          ),
                          child: Container(
                            width: 44,
                            height: 44,
                            decoration: const BoxDecoration(
                              shape: BoxShape.circle,
                              color: Colors.white,
                            ),
                            child: ClipOval(
                              child: CustomImageView(
                                fit: BoxFit.cover,
                                image: reel.uploader?.profileImage ?? '',
                              ),
                            ),
                          ),
                        ).paddingOnly(right: 8),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text(
                                  reel.uploader?.name ?? '',
                                  style: AppFontStyle.fontStyleW700(
                                    fontSize: 16,
                                    fontColor: AppColors.white,
                                  ),
                                ).paddingOnly(bottom: 7, right: 6),
                                Database.getUserProfileResponseModel?.user
                                            ?.id ==
                                        reel.uploader?.id
                                    ? const Offstage()
                                    : GetBuilder<VideosScreenController>(
                                        id: Constant.idFollow,
                                        builder: (context) {
                                          return GestureDetector(
                                            onTap: () {
                                              controller.onToggleFollow(
                                                uid: Database
                                                        .getUserProfileResponseModel
                                                        ?.user
                                                        ?.firebaseUid ??
                                                    "",
                                                toUserId: controller
                                                        .myVideosList[
                                                            widget.index]
                                                        .uploader
                                                        ?.id ??
                                                    "",
                                              );
                                              HapticFeedback.lightImpact();
                                            },
                                            child: Container(
                                              decoration: BoxDecoration(
                                                borderRadius:
                                                    BorderRadius.circular(32),
                                                color: AppColors.followBgColor,
                                              ),
                                              child: Row(
                                                children: [
                                                  controller.followResponse
                                                              ?.isFollow ==
                                                          true
                                                      ? const SizedBox()
                                                      : Image.asset(
                                                          AppAsset
                                                              .personAddIcon,
                                                          height: 15,
                                                          width: 15,
                                                        ).paddingOnly(right: 5),
                                                  Text(
                                                    controller.followResponse
                                                                ?.isFollow ==
                                                            true
                                                        ? EnumLocale
                                                            .txtFollowing
                                                            .name
                                                            .tr
                                                        : EnumLocale
                                                            .txtFollow.name.tr,
                                                    style: AppFontStyle
                                                        .fontStyleW500(
                                                      fontSize: 13,
                                                      fontColor:
                                                          AppColors.white,
                                                    ),
                                                  ),
                                                ],
                                              ).paddingSymmetric(
                                                  horizontal: 10, vertical: 3),
                                            ),
                                          );
                                        }),
                              ],
                            ),
                            Row(
                              children: [
                                Image.asset(
                                  AppAsset.locationIcon,
                                  color: AppColors.white,
                                  height: 14,
                                  width: 14,
                                ).paddingOnly(right: 6),
                                Text(
                                  location ?? '',
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 12,
                                    fontColor: AppColors.white,
                                  ),
                                )
                              ],
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  // Caption
                  SizedBox(
                    width: double.infinity,
                    child: ExpandableText(
                      reel.caption ?? '',
                      trimLines: 2,
                      style: AppFontStyle.fontStyleW500(
                        fontSize: 12,
                        fontColor: AppColors.white,
                      ),
                      moreLabel: 'View more'.tr,
                      lessLabel: '  ${'Less'.tr}',
                    ),
                  ).paddingOnly(top: 10, bottom: 12, right: 50),

                  if (bottomAd != null && !isSponsored)
                    GestureDetector(
                      onTap: () => _openUrl(bottomAd.ctaUrl),
                      child: Container(
                        margin: const EdgeInsets.only(right: 50, bottom: 14),
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(12),
                          border:
                              Border.all(color: Colors.white.withOpacity(0.15)),
                        ),
                        child: Row(
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: Image.network(
                                bottomAd.imageUrl,
                                width: 52,
                                height: 52,
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => Container(
                                  width: 52,
                                  height: 52,
                                  color: Colors.white12,
                                ),
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    bottomAd.title,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: AppFontStyle.fontStyleW600(
                                      fontSize: 12,
                                      fontColor: AppColors.white,
                                    ),
                                  ),
                                  if ((bottomAd.subtitle ?? '').isNotEmpty)
                                    Text(
                                      bottomAd.subtitle!,
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                      style: AppFontStyle.fontStyleW500(
                                        fontSize: 11,
                                        fontColor:
                                            AppColors.white.withOpacity(0.7),
                                      ),
                                    ),
                                ],
                              ),
                            ),
                            if ((bottomAd.ctaText ?? '').isNotEmpty)
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 10, vertical: 6),
                                decoration: BoxDecoration(
                                  color: Colors.amber.shade400,
                                  borderRadius: BorderRadius.circular(18),
                                ),
                                child: Text(
                                  bottomAd.ctaText!,
                                  style: AppFontStyle.fontStyleW600(
                                    fontSize: 11,
                                    fontColor: AppColors.black,
                                  ),
                                ),
                              ),
                          ],
                        ),
                      ),
                    ),
                ],
              ),
            ),

            if (isSponsored && (reel.ctaUrl ?? '').isNotEmpty)
              Positioned(
                bottom: 28,
                right: 18,
                child: _chromeWrap(
                  controller,
                  GestureDetector(
                    onTap: () => _openUrl(reel.ctaUrl),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 8),
                      decoration: BoxDecoration(
                        color: Colors.amber.shade400,
                        borderRadius: BorderRadius.circular(22),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.25),
                            blurRadius: 8,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Text(
                        (reel.ctaText?.isNotEmpty ?? false)
                            ? reel.ctaText!
                            : 'Learn More'.tr,
                        style: AppFontStyle.fontStyleW600(
                          fontSize: 12,
                          fontColor: AppColors.black,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            Positioned(
              bottom: 20,
              right: 10,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  // LIKE
                  GestureDetector(
                    onTap: () {
                      _videos.onLikeTap(widget.index);
                      _videos.update([Constant.idAllAds]);
                      HapticFeedback.lightImpact();
                    },
                    child: _ActionCircle(
                      size: 42,
                      child: Image.asset(
                        isLiked
                            ? AppAsset.favouriteFillIcon
                            : AppAsset.favouriteWhiteIcon,
                        height: 24,
                        width: 24,
                      ),
                    ),
                  ).paddingOnly(bottom: 24),

                  // SHARE (placeholder)
                  GestureDetector(
                    onTap: () => HapticFeedback.lightImpact(),
                    child: _ActionCircle(
                      size: 42,
                      child: Image.asset(AppAsset.shareIcon,
                          height: 24, width: 24),
                    ),
                  ).paddingOnly(bottom: 24),

                  // MUTE/UNMUTE
                  GestureDetector(
                    onTap: () {
                      _videos.toggleMute(widget.index);
                      HapticFeedback.lightImpact();
                    },
                    child: _ActionCircle(
                      size: 42,
                      child: (isMuted)
                          ? const Icon(CupertinoIcons.volume_off,
                              color: Colors.white)
                          : Image.asset(AppAsset.volumeOnIcon,
                              height: 24, width: 24),
                    ),
                  ).paddingOnly(bottom: 24),

                  // ZOOM → ENTER IMMERSIVE (global hide)
                  GestureDetector(
                    onTap: () {
                      if (!controller.isImmersive) {
                        _enterImmersive();
                        HapticFeedback.lightImpact();
                      }
                    },
                    child: _ActionCircle(
                      size: 42,
                      child: Image.asset(AppAsset.zoomOutIcon,
                          height: 24, width: 24),
                    ),
                  ).paddingOnly(bottom: 24),

                  // MORE → Report sheet (optional: immersive off)
                  GestureDetector(
                    onTap: () {
                      _exitImmersive(); // keep UI visible in sheet
                      showModalBottomSheet(
                        context: context,
                        isScrollControlled: true,
                        backgroundColor: Colors.white,
                        barrierColor: AppColors.black.withOpacity(0.8),
                        builder: (context) => ReportBottomSheet(
                          submitOnTap: () {
                            if (Database.demoUser == true) {
                              Utils.showLog("This is demo app");
                            } else {
                              controller.reportReelApi(
                                controller.myVideosList[widget.index].id,
                              );
                              controller.reasonController.clear();
                            }
                          },
                        ),
                      );
                    },
                    child: _ActionCircle(
                      size: 42,
                      child: Image.asset(AppAsset.moreViewHorizontalIcon,
                          height: 24, width: 24),
                    ),
                  ).paddingOnly(bottom: 24),
                ],
              ),
            ),
          ],
        );
      },
    );
  }
}

///circle icons
class _ActionCircle extends StatelessWidget {
  final double size;
  final Widget child;
  const _ActionCircle({required this.size, required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: size,
      width: size,
      decoration: BoxDecoration(
        color: AppColors.black.withValues(alpha: 0.28),
        shape: BoxShape.circle,
      ),
      child: Center(child: child),
    );
  }
}

// reel_data.dart
class ReelData {
  final int id;
  final String userName;
  final String userAvatar;
  final String videoUrl;
  final String videoThumbnail;
  final String caption;
  final int likes;
  final int comments;
  final int shares;
  final bool isFollowing;
  final String music;
  final String duration;

  ReelData({
    required this.id,
    required this.userName,
    required this.userAvatar,
    required this.videoUrl,
    required this.videoThumbnail,
    required this.caption,
    required this.likes,
    required this.comments,
    required this.shares,
    required this.isFollowing,
    required this.music,
    required this.duration,
  });
}

class ProductCardView extends StatelessWidget {
  final String? image;
  final String? productName;
  final String? description;
  final String? price;
  const ProductCardView(
      {super.key, this.image, this.productName, this.description, this.price});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VideosScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          return BlurryContainer(
            // height: 103,
            // width: Get.width / 1.47,
            blur: 8,
            color: AppColors.black.withValues(alpha: 0.26),
            borderRadius: BorderRadius.circular(14),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: AppColors.white,
                  ),
                  height: 88,
                  width: 88,
                  child: CustomImageView(
                    image: image ?? '',
                    fit: BoxFit.contain,
                  ),
                ).paddingOnly(right: 8),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    SizedBox(
                      width: Get.width * 0.37,
                      child: Text(
                        productName!,
                        overflow: TextOverflow.ellipsis,
                        style: AppFontStyle.fontStyleW700(
                            fontSize: 15, fontColor: AppColors.white),
                      ),
                    ),
                    3.height,
                    SizedBox(
                      width: Get.width * 0.37,
                      child: Text(
                        description!,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 11, fontColor: AppColors.white),
                      ),
                    ).paddingOnly(bottom: 4),
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          '${Database.settingApiResponseModel?.data?.currency?.symbol} ${price!}',
                          style: AppFontStyle.fontStyleW800(
                              fontSize: 15,
                              fontColor: AppColors.lightGreenTxtColor),
                        ).paddingOnly(right: 44),
                        Container(
                          decoration: BoxDecoration(
                            color: AppColors.white,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            EnumLocale.txtViewMore.name.tr,
                            style: AppFontStyle.fontStyleW700(
                                fontSize: 11, fontColor: AppColors.black),
                          ).paddingSymmetric(horizontal: 11, vertical: 6),
                        )
                      ],
                    ),
                  ],
                )
              ],
            ),
          );
        });
  }
}

class ExpandableText extends StatefulWidget {
  final String text;
  final TextStyle? style;
  final int trimLines;
  final String moreLabel;
  final String lessLabel;

  const ExpandableText(
    this.text, {
    super.key,
    this.style,
    this.trimLines = 2,
    this.moreLabel = ' more',
    this.lessLabel = ' less',
  });

  @override
  State<ExpandableText> createState() => _ExpandableTextState();
}

class _ExpandableTextState extends State<ExpandableText> {
  bool _expanded = false;
  bool _overflow = false;

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        if (!_expanded) {
          final span = TextSpan(text: widget.text, style: widget.style);
          final tp = TextPainter(
            text: span,
            maxLines: widget.trimLines,
            textDirection: TextDirection.ltr,
          )..layout(maxWidth: constraints.maxWidth);
          _overflow = tp.didExceedMaxLines;
        }

        if (_expanded) {
          return _buildExpanded(context);
        } else {
          return _buildCollapsed(context, showMore: _overflow);
        }
      },
    );
  }

  Widget _buildCollapsed(BuildContext context, {required bool showMore}) {
    final style = widget.style ?? DefaultTextStyle.of(context).style;
    if (!showMore) {
      return Text(
        widget.text,
        style: style,
        maxLines: widget.trimLines,
        overflow: TextOverflow.ellipsis,
      );
    }

    return RichText(
      text: TextSpan(
        style: style,
        children: [
          TextSpan(
            text: _ellipsize(widget.text, style, widget.trimLines, context),
          ),
          TextSpan(
            text: widget.moreLabel,
            style: style.copyWith(
              fontWeight: FontWeight.w700,
              decoration: TextDecoration.underline,
            ),
            recognizer: TapGestureRecognizer()
              ..onTap = () {
                setState(() => _expanded = true);
              },
          ),
        ],
      ),
    );
  }

  Widget _buildExpanded(BuildContext context) {
    final style = widget.style ?? DefaultTextStyle.of(context).style;
    return RichText(
      text: TextSpan(
        style: style,
        children: [
          TextSpan(text: widget.text),
          TextSpan(
            text: widget.lessLabel,
            style: style.copyWith(
              fontWeight: FontWeight.w700,
              decoration: TextDecoration.underline,
            ),
            recognizer: TapGestureRecognizer()
              ..onTap = () {
                setState(() => _expanded = false);
              },
          ),
        ],
      ),
    );
  }

  String _ellipsize(
      String fullText, TextStyle style, int maxLines, BuildContext context) {
    final text = fullText.trim();
    if (text.isEmpty) return '';

    var low = 0;
    var high = text.length;
    var best = text;

    measure(String s) {
      final tp = TextPainter(
        text: TextSpan(text: '$s…', style: style),
        maxLines: maxLines,
        textDirection: TextDirection.ltr,
        ellipsis: '…',
      )..layout(maxWidth: MediaQuery.of(context).size.width * 0.45);
      return tp.didExceedMaxLines;
    }

    // Binary search to find longest prefix that fits
    while (low <= high) {
      final mid = (low + high) >> 1;
      final candidate = text.substring(0, mid);
      if (measure(candidate)) {
        high = mid - 1;
      } else {
        best = candidate;
        low = mid + 1;
      }
    }
    return '${best.trim()}…';
  }
}

class ReportBottomSheet extends StatelessWidget {
  final Function()? submitOnTap;
  const ReportBottomSheet({super.key, this.submitOnTap});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VideosScreenController>(
      // id: Constant.idReportReason,
      init: VideosScreenController(),
      builder: (controller) {
        return Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(28),
            color: AppColors.white,
          ),
          // height: Get.height * 0.8,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              // Header
              Row(
                children: [
                  GestureDetector(
                    onTap: () {
                      Get.back();
                    },
                    child: Container(
                      height: 44,
                      width: 44,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: AppColors.categoriesBgColor,
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(7),
                        child: Image.asset(
                          AppAsset.backArrowIcon,
                          color: AppColors.black,
                        ),
                      ),
                    ),
                  ).paddingOnly(right: 18),
                  Text(
                    EnumLocale.txtReportThisAds.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 22, fontColor: AppColors.black),
                  ).paddingOnly(right: 44),
                  Spacer(),
                ],
              ).paddingOnly(bottom: 24, left: 16, right: 16, top: 18),

              Flexible(
                child: ListView.builder(
                  shrinkWrap: true,
                  itemCount: controller.reportReasonList.length +
                      1, // +1 for Other Reason
                  itemBuilder: (_, index) {
                    bool isOther = index == controller.reportReasonList.length;

                    return Padding(
                      padding: const EdgeInsets.symmetric(vertical: 6),
                      child: InkWell(
                        onTap: () {
                          if (isOther) {
                            controller.toggleOtherSelection();
                          } else {
                            controller.toggleSelection(index);
                          }
                        },
                        borderRadius: BorderRadius.circular(12),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 20),
                          decoration: BoxDecoration(
                            border: Border.all(
                                color: AppColors.borderColor
                                    .withValues(alpha: 0.4)),
                            color: isOther
                                ? (controller.isOtherSelected
                                    ? AppColors.lightRed100
                                    : AppColors.reportAdContainer)
                                : (controller.selectedReasons.contains(index)
                                    ? AppColors.lightRed100
                                    : AppColors.reportAdContainer),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  isOther
                                      ? "Other Reason"
                                      : (controller
                                              .reportReasonList[index].title ??
                                          ''),
                                  style: AppFontStyle.fontStyleW400(
                                    fontSize: 16,
                                    fontColor: isOther
                                        ? (controller.isOtherSelected
                                            ? AppColors.appRedColor
                                            : AppColors.searchText)
                                        : (controller.selectedReasons
                                                .contains(index)
                                            ? AppColors.appRedColor
                                            : AppColors.searchText),
                                  ),
                                ),
                              ),
                              Container(
                                height: 21,
                                width: 21,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: isOther
                                        ? (controller.isOtherSelected
                                            ? AppColors.appRedColor
                                            : AppColors.grey300
                                                .withValues(alpha: 0.5))
                                        : (controller.selectedReasons
                                                .contains(index)
                                            ? AppColors.appRedColor
                                            : AppColors.grey300
                                                .withValues(alpha: 0.5)),
                                  ),
                                ),
                                child: (isOther
                                        ? controller.isOtherSelected
                                        : controller.selectedReasons
                                            .contains(index))
                                    ? Center(
                                        child: Container(
                                          decoration: const BoxDecoration(
                                            color: Colors.red,
                                            shape: BoxShape.circle,
                                          ),
                                        ).paddingAll(0.6),
                                      )
                                    : null,
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ).paddingOnly(left: 16, right: 16),
              ),

              // 8.height,
              // TextField and Buttons
              Container(
                decoration: BoxDecoration(
                  color: AppColors.white,
                  boxShadow: [
                    BoxShadow(
                      offset: Offset(0, -2),
                      color: AppColors.black.withValues(alpha: 0.1),
                      spreadRadius: 0,
                      blurRadius: 14,
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (controller.isOtherSelected) ...[
                      8.height,
                      Text(
                        EnumLocale.txtWriteHere.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 13,
                            fontColor: AppColors.popularProductText),
                      ).paddingOnly(top: 13, bottom: 8),
                      TextField(
                        controller: controller.reasonController,
                        maxLines: 3,
                        decoration: InputDecoration(
                          filled: true,
                          fillColor: AppColors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide:
                                BorderSide(color: AppColors.borderColor),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderSide:
                                BorderSide(color: AppColors.borderColor),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide:
                                BorderSide(color: AppColors.borderColor),
                          ),
                        ),
                      ).paddingOnly(bottom: 0),
                    ],

                    // Buttons
                    Row(
                      children: [
                        Expanded(
                          child: PrimaryAppButton(
                            onTap: () {
                              Get.back();
                            },
                            height: 52,
                            fontColor: AppColors.appRedColor,
                            color: AppColors.lightRed100,
                            text: EnumLocale.txtCancel.name.tr,
                          ),
                        ),
                        14.width,
                        Expanded(
                          child: PrimaryAppButton(
                            // onTap: () {
                            //   controller.adReportUserApi();
                            // },

                            onTap: submitOnTap,
                            height: 52,
                            text: EnumLocale.txtSubmit.name.tr,
                          ),
                        ),
                      ],
                    ).paddingOnly(bottom: 10, top: 17)
                  ],
                ).paddingOnly(left: 16, right: 16),
              ),
            ],
          ),
        );
      },
    );
  }
}
