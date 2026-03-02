// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:listify/ui/my_ads_screen/api/all_ads_api.dart';
// import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/utils.dart';
//
// class MyAdsScreenController extends GetxController {
//   bool isLoading = false;
//   AllAdsResponseModel? allAdsResponseModel;
//   List<AllAds> allAdsList = [];
//   ScrollController scrollController = ScrollController();
//   bool isPaginationLoading = false;
//
//   @override
//   void onInit() {
//     init();
//     super.onInit();
//   }
//
//   init() async {
//     Utils.showLog("Enter all ads Controller");
//     scrollController.addListener(onAllAdsPagination);
//
//     AllAdsApi.startPagination = 0;
//     await getAllAds();
//   }
//
//   ///tabs
//
//   final List<String> tabs = [
//     'All Ads',
//     'Featured',
//     'Live',
//     'Deactivate',
//     'Under Review',
//     'Sold Out',
//     'Permanent Rejected',
//     'Soft Rejected',
//     'Resubmitted',
//     'Expired',
//   ];
//   final List<String> type = [
//     '',
//     'FEATURED',
//     'APPROVED',
//     'DEACTIVATED',
//     'PENDING',
//     'SOLD_OUT',
//     'PERMANENT_REJECTED',
//     'SOFT_REJECTED',
//     'RESUBMITTED',
//     'EXPIRED',
//   ];
//
//   /// get all ads
//   Future<void> getAllAds({String adType = ""}) async {
//     isLoading = true;
//     update([Constant.idAllAds]);
//     allAdsResponseModel = await AllAdsApi.callApi(adType);
//     allAdsList.clear();
//     allAdsList.addAll(allAdsResponseModel?.data ?? []);
//
//     Utils.showLog(" get all Ads list data $allAdsList");
//     Utils.showLog("Ads fetched for type: $adType -> ${allAdsList.length} items");
//
//     isLoading = false;
//     update([Constant.idAllAds]);
//   }
//
//   /// pagination
//   Future<void> onAllAdsPagination() async {
//     if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
//       isPaginationLoading = true;
//       update([Constant.idPagination]);
//
//       allAdsResponseModel = await AllAdsApi.callApi("");
//       allAdsList.clear();
//       allAdsList.addAll(allAdsResponseModel?.data ?? []);
//
//       Utils.showLog("cityList pagination ::::: $allAdsList");
//
//       isPaginationLoading = false;
//       update([Constant.idPagination]);
//     }
//   }
// }

import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/my_ads_screen/api/all_ads_api.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class MyAdsScreenController extends GetxController {
  bool isLoading = false;
  AllAdsResponseModel? allAdsResponseModel;
  List<AllAds> allAdsList = [];
  ScrollController scrollController = ScrollController();
  bool isPaginationLoading = false;

  // Add current tab index and type
  int currentTabIndex = 0;
  String currentAdType = "";

  @override
  void onInit() {
    init();
    log("jhkgjhkhhkithikijijijijiojioj");
    super.onInit();
  }

  init() async {
    Utils.showLog("Enter all ads Controller");
    scrollController.addListener(onAllAdsPagination);

    AllAdsApi.startPagination = 0;
    await getAllAds(adType: currentAdType);
  }

  ///tabs
  final List<String> tabs = [
    'All Ads',
    'Featured',
    'Live',
    'Deactivate',
    'Under Review',
    'Sold Out',
    'Permanent Rejected',
    'Soft Rejected',
    'Resubmitted',
    'Expired',
  ];

  final List<String> type = [
    'ALL',
    'FEATURED',
    'LIVE',
    'DEACTIVATED',
    'UNDER_REVIEW',
    'SOLD_OUT',
    'PERMANENT_REJECTED',
    'SOFT_REJECTED',
    'RESUBMITTED',
    'EXPIRED',
  ];

  /// Method to handle tab change from UI
  void onTabChanged(int tabIndex) {
    if (currentTabIndex != tabIndex) {
      currentTabIndex = tabIndex;
      currentAdType = type[tabIndex];

      // Reset pagination when tab changes
      AllAdsApi.startPagination = 0;

      // Call API with new type
      getAllAds(adType: currentAdType);

      Utils.showLog("Tab changed to index: $tabIndex, type: $currentAdType");
    }
  }

  /// get all ads
  Future<void> getAllAds({String adType = "", bool isRefresh = false}) async {
    isLoading = true;
    currentAdType = adType; // Update current type
    update([Constant.idAllAds]);

    allAdsResponseModel = await AllAdsApi.callApi(adType);
    allAdsList.clear();
    allAdsList.addAll(allAdsResponseModel?.data ?? []);

    Utils.showLog("Get all Ads list data $allAdsList");
    Utils.showLog("Ads fetched for type: $adType -> ${allAdsList.length} items");

    isLoading = false;
    update([Constant.idAllAds]);
  }

  /// pagination - Updated to use current ad type
  Future<void> onAllAdsPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      if (isPaginationLoading) return; // Prevent multiple calls

      isPaginationLoading = true;
      update([Constant.idPagination]);

      // Use current ad type for pagination
      allAdsResponseModel = await AllAdsApi.callApi(currentAdType);

      // Add new data to existing list (don't clear for pagination)
      if (allAdsResponseModel?.data != null) {
        allAdsList.addAll(allAdsResponseModel!.data);
      }

      Utils.showLog("Ads pagination ::::: ${allAdsList.length} total items");

      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }

  ///refresh data

  Future<void> refreshCurrentTab(int tabIndex) async {
    // Add your refresh logic here
    // For example:
    // - Reload data for the current tab
    // - Reset pagination
    // - Clear cache if needed

    // Example implementation:
    try {
      isLoading = true;
      update();

      // Your API call or data refresh logic here
      await getAllAds(adType: tabIndex.toString());

      isLoading = false;
      update();
    } catch (e) {
      isLoading = false;
      update();
      // Handle error
    }
  }

  @override
  void onClose() {
    scrollController.dispose();
    super.onClose();
  }
}
