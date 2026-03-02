import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/ui/my_videos_screen/api/my_videos_api.dart';
import 'package:listify/ui/my_videos_screen/api/specific_seller_video_api.dart';
import 'package:listify/ui/my_videos_screen/api/video_delete_api.dart';
import 'package:listify/ui/my_videos_screen/model/specific_seller_video_list_model.dart';
import 'package:listify/ui/my_videos_screen/model/video_delete_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class MyVideosScreenController extends GetxController {
  bool isLoading = false;
  // MyVideosResponseModel? myVideosResponseModel;
  // List<MyVideo> myVideosList = [];
  TextEditingController productName = TextEditingController();
  TextEditingController productDetails = TextEditingController();
  TextEditingController productPrice = TextEditingController();
  TextEditingController productDescription = TextEditingController();
  MyVideoDeleteResponseModel? myVideoDeleteResponseModel;
  SpecificSellerVideoListResponseModel? specificSellerVideoListResponseModel;
  List<Datum> sellerVideoList = [];

  @override
  onInit() {
    super.onInit();
    // MyVideosApi.startPagination = 0;
    SpecificSellerVideoApi.startPagination = 0;
    scrollController.addListener(onTopPagination);
    // getAllVideos();
    getSpecificSellerVideos();
  }

  // /// get all videos
  // getAllVideos() async {
  //   isLoading = true;
  //   update([Constant.idAllAds]);
  //   myVideosResponseModel = await MyVideosApi.callApi();
  //   myVideosList.clear();
  //   myVideosList.addAll(myVideosResponseModel?.data ?? []);
  //
  //   Utils.showLog("get user videos list data $myVideosList");
  //
  //   isLoading = false;
  //   update([Constant.idAllAds]);
  // }

  /// on refresh
  onRefresh() {
    // MyVideosApi.startPagination = 0;
    SpecificSellerVideoApi.startPagination = 0;
    // getAllVideos();
    getSpecificSellerVideos();
  }

  ///  delete video method
  Future<void> deleteMyVideo({
    required String videoId,
    required int index,
  }) async {
    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      myVideoDeleteResponseModel = await VideoDeleteApi.callApi(id: videoId);
      Get.back();
      if (myVideoDeleteResponseModel != null) {
        if (index >= 0 && index < sellerVideoList.length) {
          sellerVideoList.removeAt(index);
        } else {
          sellerVideoList.removeWhere((v) => v.id == videoId);
        }
        update([Constant.idAllAds]);
        Utils.showToast(Get.context!, myVideoDeleteResponseModel?.message ?? '');
      } else {
        Utils.showToast(Get.context!, myVideoDeleteResponseModel?.message ?? '');
      }
      Get.back();
    } catch (e) {
      Get.back();
      Utils.showLog("deleteMyVideo() error => $e");
    }
  }

  /// get specific seller videos
  getSpecificSellerVideos() async {
    isLoading = true;
    update([Constant.idAllAds]);
    specificSellerVideoListResponseModel = await SpecificSellerVideoApi.callApi();
    sellerVideoList.clear();
    sellerVideoList.addAll(specificSellerVideoListResponseModel?.data ?? []);

    Utils.showLog("get specific seller videos list data $sellerVideoList");

    isLoading = false;
    update([Constant.idAllAds]);
  }

  ScrollController scrollController = ScrollController();
  bool isPaginationLoading= false;

  Future<void> onTopPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      isPaginationLoading = true;
      update([Constant.idPagination]);

      specificSellerVideoListResponseModel = await SpecificSellerVideoApi.callApi();
      sellerVideoList.addAll(specificSellerVideoListResponseModel?.data ?? []);
      log("topListenersModel ::::: $sellerVideoList");

      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }
  @override
  void onClose() {
    // TODO: implement onClose

    scrollController.removeListener(onTopPagination);
  }
}
