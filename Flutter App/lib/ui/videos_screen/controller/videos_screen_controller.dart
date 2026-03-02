import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/ui/bottom_bar/controller/bottom_bar_controller.dart';
import 'package:listify/ui/my_videos_screen/api/my_videos_api.dart';
import 'package:listify/ui/my_videos_screen/api/record_videi_view_api.dart';
import 'package:listify/ui/my_videos_screen/api/user_follow_unfollow_api.dart';
import 'package:listify/ui/my_videos_screen/api/video_like_api.dart';
import 'package:listify/ui/my_videos_screen/model/my_videos_response_model.dart';
import 'package:listify/ui/my_videos_screen/model/user_follow_unfollow_response_model.dart';
import 'package:listify/ui/product_detail_screen/api/report_reasons_api.dart';
import 'package:listify/ui/product_detail_screen/model/report_reasons_model.dart';
import 'package:listify/ui/product_detail_screen/model/safety_tips_response_model.dart';
import 'package:listify/ui/videos_screen/api/reel_report_api.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';
import 'package:preload_page_view/preload_page_view.dart';
import 'package:video_player/video_player.dart';

///
class VideosScreenController extends GetxController {
  PreloadPageController preloadPageController = PreloadPageController();
  int currentIndex = 0;
  bool isLoading = false;

  /// NEW: guards
  bool controllersReady = false; // players are initialized and ready
  bool isPaginationLoading = false;
  MyVideosResponseModel? myVideosResponseModel;
  List<MyVideo> myVideosList = [];
  final Map<int, bool> _likeInFlight = {};
  int _organicCounter = 0;
  int _imageAdIndex = 0;

  /// pagination
  void onPagination(int value) async {
    if (((myVideosList.length - 1) == value) && !isPaginationLoading) {
      isPaginationLoading = true;
      update([Constant.idPagination]);
      await getAllVideos(isPagination: true);
      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }

  /// Video players
  final Map<int, VideoPlayerController> videoControllers = {};

  /// UI States
  final Map<int, bool> isLikedMap = {};
  final Map<int, bool> isBookmarkedMap = {};
  final Map<int, bool> isFollowingMap = {};
  final Map<int, bool> isMutedMap = {};
  final Map<int, bool> isPlayingMap = {};

  /// fire-once-per-loop guard
  final Map<int, bool> firedThisLoop = {};

  /// ====== NEW: Global immersive state (affects all reels) ======
  bool isImmersive = false;

  void _applySystemUi({required bool immersive}) {
    if (immersive) {
      SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);
      SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
        systemNavigationBarColor: Colors.black,
        systemNavigationBarIconBrightness: Brightness.light,
      ));
    } else {
      SystemChrome.setEnabledSystemUIMode(
        SystemUiMode.manual,
        overlays: [SystemUiOverlay.top, SystemUiOverlay.bottom],
      );
      SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.dark,
        systemNavigationBarColor: Colors.white,
        systemNavigationBarIconBrightness: Brightness.dark,
      ));
    }
  }

  void setImmersive(bool on) {
    if (isImmersive == on) return;
    isImmersive = on;

    _applySystemUi(immersive: on);

    if (Get.isRegistered<BottomBarController>()) {
      Get.find<BottomBarController>().setBottomBarVisible(!on);
    }

    update([Constant.idAllAds, Constant.idBottomBar]);
  }

  void toggleImmersive() => setImmersive(!isImmersive);

  @override
  void onInit() {
    super.onInit();
    setImmersive(false);
    MyVideosApi.startPagination = 0;
    _organicCounter = 0;
    _imageAdIndex = 0;

    getAllVideos(isPagination: false); // first load
    getReportReason();
  }

  TextEditingController reasonController = TextEditingController();

  /// get all videos with pagination support
  /// ===== GET ALL VIDEOS (WITH PAGINATION) =====
  Future<void> getAllVideos({required bool isPagination}) async {
    if (!isPagination) {
      isLoading = true;
      controllersReady = false;
      MyVideosApi.startPagination = 0; // reset for first load
      update([Constant.idAllAds]);

      myVideosList.clear();
      _organicCounter = 0;
      _imageAdIndex = 0;
    }

    final response = await MyVideosApi.callApi();

    final raw = response?.data ?? [];
    if (raw.isEmpty) MyVideosApi.startPagination--;

    final prepared = _prepareReelFeed(raw);
    if (isPagination) {
      myVideosList.addAll(prepared);
    } else {
      myVideosList = prepared;
    }

    await _recreatePlayersFromApi(isPagination: isPagination);

    controllersReady = true;
    isLoading = false;
    update([Constant.idAllAds]);

    WidgetsBinding.instance.addPostFrameCallback((_) => resumeCurrent());
  }

  bool _isAdActive(MyVideo video) {
    if (video.isSponsored != true) return true;
    if (video.isActive == false) return false;
    final now = DateTime.now();
    if (video.startAt != null && now.isBefore(video.startAt!)) return false;
    if (video.endAt != null && now.isAfter(video.endAt!)) return false;
    return true;
  }

  List<MyVideo> _mergeSponsoredReels(
    List<MyVideo> organic,
    List<MyVideo> sponsored,
    int interval,
  ) {
    if (sponsored.isEmpty) return organic;
    final merged = <MyVideo>[];
    var adIndex = 0;
    for (var i = 0; i < organic.length; i++) {
      merged.add(organic[i]);
      if ((i + 1) % interval == 0 && adIndex < sponsored.length) {
        merged.add(sponsored[adIndex]);
        adIndex += 1;
      }
    }
    return merged;
  }

  List<MyVideo> _attachBottomImageAds(
    List<MyVideo> feed,
    List<MyVideo> imageAds,
    int interval,
  ) {
    if (imageAds.isEmpty) return feed;
    final result = <MyVideo>[];

    for (final item in feed) {
      if (item.isSponsored == true) {
        result.add(item);
        continue;
      }

      _organicCounter += 1;
      if (_organicCounter % interval == 0) {
        final ad = imageAds[_imageAdIndex % imageAds.length];
        _imageAdIndex += 1;

        item.bottomAd = BottomAd(
          imageUrl: ad.adImageUrl ?? ad.adDetails?.primaryImage ?? "",
          title: ad.adDetails?.title ?? ad.caption ?? "Sponsored",
          subtitle:
              ad.adDetails?.price != null ? "${ad.adDetails?.price}" : null,
          ctaText: ad.ctaText ?? "Learn More",
          ctaUrl: ad.ctaUrl,
        );
      }
      result.add(item);
    }

    return result;
  }

  List<MyVideo> _prepareReelFeed(List<MyVideo> raw) {
    final active = raw.where(_isAdActive).toList();
    final imageAds = active.where((v) => v.adType == "image").toList();
    final reels = active.where((v) => v.adType != "image").toList();
    final organic = reels.where((v) => v.isSponsored != true).toList();
    final sponsoredReels = reels.where((v) => v.isSponsored == true).toList();

    final merged = _mergeSponsoredReels(organic, sponsoredReels, 6);
    return _attachBottomImageAds(merged, imageAds, 3);
  }

  Future<void> _recreatePlayersFromApi({bool isPagination = false}) async {
    if (!isPagination) {
      for (final c in videoControllers.values) {
        try {
          await c.dispose();
        } catch (_) {}
      }
      videoControllers.clear();
      isLikedMap.clear();
      isBookmarkedMap.clear();
      isFollowingMap.clear();
      isMutedMap.clear();
      isPlayingMap.clear();
      firedThisLoop.clear();
      currentIndex = 0;
    }

    int startIndex = isPagination ? videoControllers.length : 0;

    for (int i = startIndex; i < myVideosList.length; i++) {
      final item = myVideosList[i];
      final String videoUrl = '${Api.baseUrl}${item.videoUrl ?? ' '}';
      final controller = VideoPlayerController.networkUrl(Uri.parse(videoUrl));
      videoControllers[i] = controller;

      isLikedMap[i] = item.isLike ?? false;
      isBookmarkedMap[i] = false;
      isFollowingMap[i] = false;
      isMutedMap[i] = false;
      isPlayingMap[i] = false;
      firedThisLoop[i] = false;

      try {
        await controller.initialize();
        controller.setLooping(true);
        controller.setVolume(1.0);

        controller.addListener(() {
          final value = controller.value;
          if (!value.isInitialized) return;

          final dur = value.duration;
          final pos = value.position;
          if (dur == Duration.zero) return;

          final bool nearEnd = pos >= dur - const Duration(milliseconds: 300);

          if (value.isPlaying && nearEnd && firedThisLoop[i] != true) {
            firedThisLoop[i] = true;
            recordVideoView(item.id?.toString());
          }

          if (firedThisLoop[i] == true &&
              pos <= const Duration(milliseconds: 150)) {
            firedThisLoop[i] = false;
          }
        });
      } catch (e) {
        Utils.showLog("Error initializing video $i: $e");
      }
    }

    update();
  }

  void togglePlayPause(int index) {
    if (isLoading || !controllersReady) return;
    final vc = videoControllers[index];
    if (vc != null && vc.value.isInitialized) {
      if (isPlayingMap[index] == true) {
        vc.pause();
        isPlayingMap[index] = false;
      } else {
        pauseExcept(index);
        vc.play();
        isPlayingMap[index] = true;
      }
      update();
    }
  }

  void onPageChanged(int index) {
    currentIndex = index;

    if (isLoading || !controllersReady) {
      pauseAll();
      return;
    }

    final cur = videoControllers[currentIndex];
    pauseExcept(currentIndex);
    if (cur != null && cur.value.isInitialized) {
      cur.play();
      isPlayingMap[currentIndex] = true;
    }
    update();
  }

  Future<void> onLikeTap(int index) async {
    if (index < 0 || index >= myVideosList.length) return;
    if (myVideosList[index].isSponsored == true) return;
    if (_likeInFlight[index] == true) return;
    _likeInFlight[index] = true;

    try {
      final prevLiked = isLikedMap[index] ?? false;
      final prevLikes = myVideosList[index].totalLikes ?? 0;

      isLikedMap[index] = !prevLiked;
      myVideosList[index].totalLikes = prevLikes + (prevLiked ? -1 : 1);
      update([Constant.idAllAds]);

      final adVideoId = myVideosList[index].id?.toString() ?? '';
      final res = await VideoLikeApi.callApi(adVideoId: adVideoId);

      if (res == null) {
        isLikedMap[index] = prevLiked;
        myVideosList[index].totalLikes = prevLikes;
        update([Constant.idAllAds]);
        return;
      }

      try {
        final bool? serverIsLike = res.like;
        if (serverIsLike != null) isLikedMap[index] = serverIsLike;
        try {
          myVideosList[index].isLike = isLikedMap[index] ?? prevLiked;
        } catch (_) {}
      } catch (_) {}

      update([Constant.idAllAds]);
    } catch (e) {
      Utils.showLog('Like toggle error: $e');
    } finally {
      _likeInFlight[index] = false;
    }
  }

  void toggleBookmark(int index) {
    isBookmarkedMap[index] = !(isBookmarkedMap[index] ?? false);
    update();
  }

  void toggleFollow(int index) {
    isFollowingMap[index] = !(isFollowingMap[index] ?? false);
    update();
  }

  void toggleMute(int index) {
    final vc = videoControllers[index];
    if (vc != null && vc.value.isInitialized) {
      isMutedMap[index] = !(isMutedMap[index] ?? false);
      vc.setVolume(isMutedMap[index]! ? 0.0 : 1.0);
      update();
    }
  }

  /// Follow/Unfollow API
  bool isFollowLoading = false;
  UserFollowUnFollowResponseModel? followResponse;

  Future<void> onToggleFollow({
    required String uid,
    required String toUserId,
  }) async {
    try {
      isFollowLoading = true;
      update([Constant.idFollow]);

      final result = await UserFollowUnFollowApi.toggleFollowStatus(
        uid: uid,
        toUserId: toUserId,
      );

      if (result != null) {
        followResponse = result;
        Utils.showLog("Follow API Success => ${result.message}");
        Utils.showLog("Is Follow => ${result.isFollow}");
      } else {
        Utils.showLog("Follow API Failed");
      }
    } catch (e) {
      Utils.showLog("Follow API Exception => $e");
    } finally {
      isFollowLoading = false;
      update([Constant.idFollow]);
    }
  }

  /// record video view api
  recordVideoView(String? videoId) async {
    if (videoId == null || videoId.isEmpty) return;
    final response = await RecordVideoViewApi.callApi(videoId: videoId);
    if (response != null && response.status == true) {
      Utils.showLog("✅ View Recorded Successfully");
      Utils.showLog("Video Id => ${response.view?.video}");
      Utils.showLog("User Id => ${response.view?.user}");
      Utils.showLog("Viewed At => ${response.view?.viewedAt}");
    } else {
      Utils.showLog("❌ Failed to record video view");
    }
  }

  ///report apis (unchanged)
  List<int> selectedReasons = [];
  bool isOtherSelected = false;
  ReportReasonsModel? reportReasonsModel;
  List<Datum> reportReasonList = [];
  List<SafetyTips> safetyTipsList = [];

  ///report user api (unchanged)
  reportReelApiOld(String? reelId) async {
    final result = await ReelReportApi.reportReel(
      reelId: reelId ?? "",
      reason: reasonController.text.toString(),
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );

    if (result != null && result.status == true) {
      Utils.showToast(
          Get.context!, result.message ?? "User reported successfully ✅");
    } else {
      reasonController.clear();
      Utils.showToast(
          Get.context!, result?.message ?? "Failed to report user ❌");
    }
  }

  ///ad report api (unchanged)
  reportReelApi(String? reelId) async {
    List<String> selectedTitles = selectedReasons
        .map((index) => reportReasonList[index].title ?? "")
        .where((title) => title.isNotEmpty)
        .toList();

    String extraReason = reasonController.text.trim();

    String finalReason = "";
    if (selectedTitles.isNotEmpty && extraReason.isNotEmpty) {
      finalReason = "${selectedTitles.join(", ")} | $extraReason";
    } else if (selectedTitles.isNotEmpty) {
      finalReason = selectedTitles.join(", ");
    } else if (extraReason.isNotEmpty) {
      finalReason = extraReason;
    }

    if (finalReason.isEmpty) {
      Utils.showToast(Get.context!, "Please select or enter a reason ❌");
      return;
    }

    final result = await ReelReportApi.reportReel(
      reelId: reelId ?? "",
      reason: finalReason,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );

    if (result != null && result.status == true) {
      Utils.showToast(
          Get.context!, result.message ?? "Ad reported successfully");
      Get.back();
    } else {
      Utils.showToast(Get.context!, result?.message ?? "Failed to report Ad");
      Get.back();
    }
  }

  void toggleOtherSelection() {
    isOtherSelected = !isOtherSelected;
    update();
  }

  void toggleSelection(int index) {
    if (selectedReasons.contains(index)) {
      selectedReasons.remove(index);
    } else {
      selectedReasons.add(index);
    }
    update();
  }

  getReportReason() async {
    isLoading = true;
    update([Constant.idReportReason]);
    reportReasonsModel = await ReportReasonsApi.callApi();
    reportReasonList
      ..clear()
      ..addAll(reportReasonsModel?.data ?? []);
    isLoading = false;
    update([Constant.idReportReason]);
  }

  /// ==== helpers to hard-stop playback ====
  void pauseAll() {
    for (final vc in videoControllers.values) {
      try {
        vc.pause();
      } catch (_) {}
    }
    isPlayingMap.updateAll((key, value) => false);
    update([Constant.idAllAds]);
  }

  void pauseExcept(int playingIndex) {
    videoControllers.forEach((i, vc) {
      if (i != playingIndex) {
        try {
          vc.pause();
        } catch (_) {}
      }
    });
    isPlayingMap.updateAll(
        (k, v) => k == playingIndex ? (isPlayingMap[k] ?? false) : false);
    update([Constant.idAllAds]);
  }

  void resumeCurrent() {
    if (isLoading || !controllersReady) return;
    final vc = videoControllers[currentIndex];
    if (vc != null && vc.value.isInitialized) {
      pauseExcept(currentIndex);
      vc.play();
      isPlayingMap[currentIndex] = true;
      update([Constant.idAllAds]);
    }
  }

  @override
  void onClose() {
    setImmersive(false);
    for (var controller in videoControllers.values) {
      try {
        controller.dispose();
      } catch (_) {}
    }
    preloadPageController.dispose();
    MyVideosApi.startPagination = 0;
    super.onClose();
  }
}
