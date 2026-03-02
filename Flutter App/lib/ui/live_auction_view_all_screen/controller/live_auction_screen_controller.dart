import 'dart:developer';

import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:listify/ui/home_screen/api/live_auction_list_api.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';



// class LiveAuctionScreenController extends GetxController {
//   bool isLoading = false;
//   AllAdsResponseModel? allAdsResponseModel;
//   List<AllAds> liveAuctionProductList = [];
//
//   ScrollController scrollController = ScrollController();
//   bool isPaginationLoading = false;
//
//   @override
//   void onInit() {
//     scrollController.addListener(onTopPagination);
//     LiveAuctionListApi.startPagination = 0;
//     init();
//     super.onInit();
//   }
//
//   init() {
//     liveAuctionProductListApi();
//   }
//
//   /// Live auction product API
//   liveAuctionProductListApi() async {
//     isLoading = true;
//     update();
//
//     allAdsResponseModel = await LiveAuctionListApi.callApi();
//     liveAuctionProductList.clear();
//
//     /// --- DEMO: Duplicate API data 20 times for testing pagination ---
//     if (allAdsResponseModel?.data != null && allAdsResponseModel!.data!.isNotEmpty) {
//       for (int i = 0; i < 20; i++) {
//         liveAuctionProductList.addAll(allAdsResponseModel!.data!);
//       }
//     }
//     /// ---------------------------------------------------------------
//
//     Utils.showLog("Live auction product list (demo expanded): ${liveAuctionProductList.length}");
//
//     isLoading = false;
//     update();
//   }
//
//   /// Pull to refresh
//   Future<void> onRefresh() async {
//     LiveAuctionListApi.startPagination = 0;
//     await liveAuctionProductListApi();
//   }
//
//   /// Pagination listener
//   Future<void> onTopPagination() async {
//     if (scrollController.position.pixels == scrollController.position.maxScrollExtent &&
//         !isPaginationLoading) {
//       isPaginationLoading = true;
//       update([Constant.idPagination]);
//
//       allAdsResponseModel = await LiveAuctionListApi.callApi();
//
//       /// --- DEMO: Duplicate next-page data 20 times too ---
//       if (allAdsResponseModel?.data != null && allAdsResponseModel!.data!.isNotEmpty) {
//         for (int i = 0; i < 20; i++) {
//           liveAuctionProductList.addAll(allAdsResponseModel!.data!);
//         }
//       }
//       /// ---------------------------------------------------
//
//       log("Pagination loaded: ${liveAuctionProductList.length} total items");
//
//       isPaginationLoading = false;
//       update([Constant.idPagination]);
//     }
//   }
//
//   @override
//   void onClose() {
//     scrollController.removeListener(onTopPagination);
//     super.onClose();
//   }
// }




class LiveAuctionScreenController extends GetxController {
  bool isLoading = false;
  AllAdsResponseModel? allAdsResponseModel;
  List<AllAds> liveAuctionProductList = [];

  ScrollController scrollController = ScrollController();
  bool isPaginationLoading = false;

  @override
  void onInit() {
    scrollController.addListener(onTopPagination);
    LiveAuctionListApi.startPagination = 0;
    init();
    super.onInit();
  }

  init() {
    liveAuctionProductListApi();
  }

  /// Live auction product API
  liveAuctionProductListApi() async {
    isLoading = true;
    update();

    allAdsResponseModel = await LiveAuctionListApi.callApi();
    liveAuctionProductList.clear();

    /// Add real API data only
    if (allAdsResponseModel?.data != null && allAdsResponseModel!.data!.isNotEmpty) {
      liveAuctionProductList.addAll(allAdsResponseModel!.data!);
    }

    Utils.showLog("Live auction product list: ${liveAuctionProductList.length}");

    isLoading = false;
    update();
  }

  /// Pull to refresh
  Future<void> onRefresh() async {
    LiveAuctionListApi.startPagination = 0;
    await liveAuctionProductListApi();
  }

  /// Pagination listener
  Future<void> onTopPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent &&
        !isPaginationLoading) {
      isPaginationLoading = true;
      update([Constant.idPagination]);

      allAdsResponseModel = await LiveAuctionListApi.callApi();

      /// Add real API data only
      if (allAdsResponseModel?.data != null && allAdsResponseModel!.data!.isNotEmpty) {
        liveAuctionProductList.addAll(allAdsResponseModel!.data!);
      }

      log("Pagination loaded: ${liveAuctionProductList.length} total items");

      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }

  @override
  void onClose() {
    scrollController.removeListener(onTopPagination);
    super.onClose();
  }
}
