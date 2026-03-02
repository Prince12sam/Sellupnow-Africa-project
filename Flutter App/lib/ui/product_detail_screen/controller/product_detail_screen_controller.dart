import 'dart:convert';

import 'package:flutter/cupertino.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart' hide Location;
import 'package:listify/ui/product_detail_screen/api/ad_report_api.dart';
import 'package:listify/ui/product_detail_screen/api/ad_view_api.dart';
import 'package:listify/ui/product_detail_screen/api/place_bid_api.dart';
import 'package:listify/ui/product_detail_screen/api/product_detail_api.dart';
import 'package:listify/ui/product_detail_screen/api/related_product_api.dart';
import 'package:listify/ui/product_detail_screen/api/remove_ad_listing_api.dart';
import 'package:listify/ui/product_detail_screen/api/report_reasons_api.dart';
import 'package:listify/ui/product_detail_screen/api/safety_tips_api.dart';
import 'package:listify/ui/product_detail_screen/api/specific_product_like_get_api.dart';
import 'package:listify/ui/product_detail_screen/api/specific_product_view_api.dart';
import 'package:listify/ui/product_detail_screen/model/place_bid_response_model.dart';
import 'package:listify/ui/product_detail_screen/model/product_detail_response_model.dart';
import 'package:listify/ui/product_detail_screen/model/remove_ad_listing_response_model.dart';
import 'package:listify/ui/product_detail_screen/model/report_reasons_model.dart';
import 'package:listify/ui/product_detail_screen/model/safety_tips_response_model.dart';
import 'package:listify/ui/product_detail_screen/model/specific_product_like_response_model.dart';
import 'package:listify/ui/product_detail_screen/model/specific_product_view_response_model.dart';
import 'package:listify/ui/review_screen/api/get_review_api.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../home_screen/controller/home_screen_controller.dart';

class ProductDetailScreenController extends GetxController {
  PlaceBidResponseModel? placeBidResponseModel;

  bool isEdit = false;
  bool sellerDetail = false;
  bool relatedProduct = false;
  bool viewLikeCount = false;
  bool liveAuctionTime = false;
  bool isLoading = false;
  List<int> selectedReasons = [];
  SafetyTipsApiResponseModel? safetyTipsApiResponseModel;
  ReportReasonsModel? reportReasonsModel;
  List<SafetyTips> safetyTipsList = [];
  List<Datum> reportReasonList = [];
  // AllAds? adsData;
  AllAdsResponseModel? allAdsResponseModel;
  bool isDetailLoading = false;
  ProductDetailResponseModel? productDetail;
  List<AllAds> relatedProductList = [];
  final TextEditingController bidController = TextEditingController();
  String? adId;
  String? id;
  bool isFeaturedSeller = false;

  RemoveAdListingResponseModel? removeAdListingResponseModel;
  TextEditingController reasonController = TextEditingController();

  final List<String> reasons = [
    "Item Not as Described",
    "Misleading Description",
    "Incomplete Information",
    "Poor Quality Images",
    "Pricing Discrepancies",
    "Unresponsive Seller",
    "Fake items",
    "Other Reason",
  ];

  @override
  onInit() {
    init();
    HomeScreenController.listener.add(this);
    super.onInit();
  }

  @override
  void onClose() {
    // Unregister when disposing
    HomeScreenController.listener.remove(this);
    super.onClose();
  }

  /// 🔹 Current ad like state using centralized management
/*  bool get isCurrentAdLiked {
    return HomeScreenController.getGlobalLikeState(productDetail?.data?.id ?? "", productDetail?.data?.isLike);
  }

  /// 🔹 Toggle current ad like state
  Future<void> toggleCurrentAdLike() async {
    if (productDetail?.data?.id == null) return;

    final currentState = isCurrentAdLiked;
    final newState = !currentState;

    // Optimistic update + UI refresh
    HomeScreenController.updateGlobalLikeState(productDetail!.data!.id!, newState);
    productDetail?.data?.isLike = newState;
    update();

    try {
      final res = await AddLikeApi.callApi(
        adId: productDetail!.data!.id!,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (res != null && res.status == true) {
        final serverIsLiked = res.like ?? newState;
        HomeScreenController.updateGlobalLikeState(productDetail?.data?.id ?? "", serverIsLiked);
        productDetail?.data?.isLike = serverIsLiked;
        update();
      } else {
        // Revert
        HomeScreenController.updateGlobalLikeState(productDetail?.data?.id ?? "", currentState);
        productDetail?.data?.isLike = currentState;
        update();
        Utils.showLog("Add like failed");
      }
    } catch (e) {
      // Revert on error
      HomeScreenController.updateGlobalLikeState(productDetail!.data!.id!, currentState);
      productDetail?.data?.isLike = currentState;
      update();
      Utils.showLog("Add like failed: $e");
    }
  }*/
  bool get isCurrentAdLiked {
    return LikeManager.to.getLikeState(
      productDetail?.data?.id ?? "",
      fallback: productDetail?.data?.isLike,
    );
  }

  Future<void> toggleCurrentAdLike() async {
    if (productDetail?.data?.id == null) return;

    final adId = productDetail!.data!.id!;
    final currentState = isCurrentAdLiked;
    final newState = !currentState;

    LikeManager.to.updateLikeState(adId, newState);
    productDetail?.data?.isLike = newState;
    update();

    try {
      final res = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (res != null && res.status == true) {
        final serverIsLiked = res.like ?? newState;
        LikeManager.to.updateLikeState(adId, serverIsLiked);
        productDetail?.data?.isLike = serverIsLiked;
      } else {
        LikeManager.to.updateLikeState(adId, currentState);
        productDetail?.data?.isLike = currentState;
      }
    } catch (e) {
      LikeManager.to.updateLikeState(adId, currentState);
      productDetail?.data?.isLike = currentState;
    } finally {
      update();
    }
  }



  init() {
    final args = Get.arguments;
    if (args is Map<String, dynamic>) {
      isEdit = args['edit'] ?? false;
      sellerDetail = args['sellerDetail'] ?? false;
      relatedProduct = args['relatedProduct'] ?? false;
      viewLikeCount = args['viewLikeCount'] ?? false;
      liveAuctionTime = args['liveAuctionTime'] ?? false;
      adId = args['adId'] ?? '';
    } else {
      Utils.showLog("⚠️ Get.arguments is not Map: $args");
    }
    fetchProductDetail();

    Utils.showLog('hasAuctionEnded ::::: $hasAuctionEnded');
    Utils.showLog('isFeaturedSeller ::::: $isFeaturedSeller');

    Utils.showLog("ad.id detail screen ...................${adId}");


    Utils.showLog('productDetail?.data?.seller?.isFeaturedSeller ::::: ${productDetail?.data?.seller?.isFeaturedSeller}');



    RelatedProductApi.startPagination = 0;

    getSafetyTips();
    getReportReason();
    specificProductView(adId ?? "");
    specificProductLike(adId ?? "");

    Utils.showLog("edit button :::::$isEdit");
    Utils.showLog("All Ads Dataaaaaaaa :::::${jsonEncode(productDetail?.data)}");
    Utils.showLog("adsData?.isLike :::::${productDetail?.data?.isLike}");
    Utils.showLog("liveAuctionTime :::::$liveAuctionTime");
  }

  bool get hasAuctionEnded {
    try {
      final auctionEndDate = productDetail?.data?.auctionEndDate;
      final endDate = DateTime.parse(auctionEndDate.toString());
      return DateTime.now().isAfter(endDate);
    } catch (e) {
      return true;
    }
  }

  void toggleSelection(int index) {
    if (selectedReasons.contains(index)) {
      selectedReasons.remove(index); // Deselect if already selected
    } else {
      selectedReasons.add(index); // Add to list if not selected
    }
    update(); // Rebuild the UI
  }

  /// get safety tips list
  getSafetyTips() async {
    isLoading = true;
    update([Constant.idSafetyTips]); // notify UI
    safetyTipsApiResponseModel = await SafetyTipsApi.callApi();
    safetyTipsList.clear();
    safetyTipsList.addAll(safetyTipsApiResponseModel?.data ?? []);

    Utils.showLog("subscriptionPlan list data $safetyTipsList");

    isLoading = false;
    update([Constant.idSafetyTips]); // notify UI
  }

  /// get report reasons
  getReportReason() async {
    isLoading = true;
    update([Constant.idReportReason]);
    reportReasonsModel = await ReportReasonsApi.callApi();
    reportReasonList.clear();
    reportReasonList.addAll(reportReasonsModel?.data ?? []);

    Utils.showLog("subscriptionPlan list data $safetyTipsList");

    isLoading = false;
    update([Constant.idReportReason]);
  }

  String formatAddress(Location? loc, {bool includeCountry = true}) {
    if (loc == null) return '';

    final parts = <String?>[
      loc.fullAddress,
      loc.city,
      loc.state,
      if (includeCountry) loc.country,
    ]
        .whereType<String>() // drop nulls
        .map((s) => s.trim())
        .where((s) => s.isNotEmpty) // drop empty
        .toList();

    if (parts.isNotEmpty) return parts.join(', ');

    // Fallback: only coordinates available
    final hasCoords = (loc.latitude != null && loc.longitude != null);
    return hasCoords ? '${loc.latitude!.toStringAsFixed(4)}, ${loc.longitude!.toStringAsFixed(4)}' : '';
  }

  String formatCreated(DateTime? dt, {bool longMonth = false}) {
    if (dt == null) return '-';
    final local = dt.toLocal(); // Z (UTC) → device local
    final pattern = longMonth ? 'd MMMM yyyy' : 'd MMM yyyy';
    return DateFormat(pattern).format(local);
  }

  /// popular product api
  getPopularProduct(String id) async {
    isLoading = true;
    update([Constant.idAllAds]);
    allAdsResponseModel = await RelatedProductApi.callApi(categoryId: id, userId: Database.getUserProfileResponseModel?.user?.id ?? "");
    relatedProductList.clear();
    relatedProductList.addAll(allAdsResponseModel?.data ?? []);

    Utils.showLog("Related product list data $relatedProductList");
    // Utils.showLog("Related product list data like ${relatedProductList[0].isLike}");

    isLoading = false;
    update([Constant.idAllAds]);
  }

  /// REMOVE AD LISTING API
  removeAdListing() async {
    removeAdListingResponseModel = await RemoveAdListingApi.callApi(
      adId: "${productDetail?.data?.id}",
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );

    if (removeAdListingResponseModel != null) {
      if (removeAdListingResponseModel?.status ?? false) {
        Utils.showToast(Get.context!, removeAdListingResponseModel?.message ?? "Ad removed successfully.");
        Get.back();
        Get.back();
      } else {
        Utils.showToast(Get.context!, removeAdListingResponseModel?.message ?? "Failed to remove ad.");
      }
    } else {
      Utils.showToast(Get.context!, "Something went wrong. Please try again.");
    }
  }

  /// 🔹 Related products like state using centralized management
  bool isAdLiked(AllAds ad) {
    return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
  }

  /// 🔹 Toggle like for related products with centralized state
  Future<void> toggleLike(int index, String adId) async {
    HapticFeedback.lightImpact();

    if (index < 0 || index >= relatedProductList.length) return;

    final ad = relatedProductList[index];
    final currentState = isAdLiked(ad);
    final newState = !currentState;

    // 1) Update global state immediately (optimistic UI)
    HomeScreenController.updateGlobalLikeState(adId, newState);

    try {
      // 2) Call API
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (resp != null && resp.status == true) {
        final serverIsLiked = resp.like ?? newState;
        // Update global state with server response
        HomeScreenController.updateGlobalLikeState(adId, serverIsLiked);
        // Update local model
        ad.isLike = serverIsLiked;
      } else {
        // Revert on failure
        HomeScreenController.updateGlobalLikeState(adId, currentState);
        Utils.showLog("Add like failed");
      }
    } catch (e) {
      // 3) Revert on failure
      HomeScreenController.updateGlobalLikeState(adId, currentState);
      Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      Utils.showLog("Add like failed: $e");
    }
  }

  ///ad report api
  adReportUserApi() async {
    // 1) Collect selected reasons titles
    List<String> selectedTitles = selectedReasons.map((index) => reportReasonList[index].title ?? "").where((title) => title.isNotEmpty).toList();

    // 2) Collect text from textfield
    String extraReason = reasonController.text.trim();

    // 3) Combine both
    String finalReason = "";
    if (selectedTitles.isNotEmpty && extraReason.isNotEmpty) {
      finalReason = "${selectedTitles.join(", ")} | $extraReason";
    } else if (selectedTitles.isNotEmpty) {
      finalReason = selectedTitles.join(", ");
    } else if (extraReason.isNotEmpty) {
      finalReason = extraReason;
    }

    // 4) Validation → if nothing selected/written
    if (finalReason.isEmpty) {
      Utils.showToast(Get.context!, "Please select or enter a reason ❌");
      return;
    }

    // 5) Call API
    final result = await AdReportApi.reportAd(
      adId: productDetail?.data?.id.toString() ?? "",
      reason: finalReason,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );

    if (result != null && result.status == true) {
      Utils.showToast(Get.context!, result.message ?? "Ad reported successfully");
      Get.back(); // Close bottom sheet
    } else {
      Utils.showToast(Get.context!, result?.message ?? "Failed to report Ad");
      Get.back(); // Close bottom sheet
    }
  }

  bool isOtherSelected = false;

  void toggleOtherSelection() {
    isOtherSelected = !isOtherSelected;
    update();
  }

  ///get  review api
  getReview() async {
    var reviewRes = await ReviewApi.getReviews(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      start: 1,
      limit: 20,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );

    if (reviewRes != null && reviewRes.status == true) {
      for (var review in reviewRes.receivedReviews ?? []) {
        Utils.showLog("Reviewer: ${review.reviewer?.name}");
        Utils.showLog("Rating: ${review.rating}");
        Utils.showLog("Review: ${review.reviewText}");
      }
    }
  }

  /// PLACE BID API
  Future<bool> placeBidApiCall() async {
    Get.dialog(const LoadingWidget(), barrierDismissible: false);

    Utils.showLog("👉 PlaceBidApiCall started...");

    try {
      final List<Map<String, dynamic>> slimAttributes = (productDetail?.data?.attributes ?? []).map((attr) {
        final dynamic v = attr.value;

        dynamic cleanedValue;
        if (v is Map && v['name'] != null) {
          cleanedValue = v['name'];
        } else if (v is List) {
          cleanedValue = v.map((e) => e.toString()).toList();
        } else {
          cleanedValue = v?.toString();
        }

        return {
          'name': attr.name,
          'value': cleanedValue,
          'image': attr.image,
        };
      }).toList();

      Utils.showLog("ads data attribute :::::$slimAttributes");

      placeBidResponseModel = await PlaceBidApi.callApi(
        adId: productDetail?.data?.id.toString() ?? "",
        bidAmount: bidController.text.trim(),
        attributes: slimAttributes,
      );

      Get.back();
      Get.back();

      final ok = placeBidResponseModel?.status == true;
      Utils.showToast(Get.context!, placeBidResponseModel?.message ?? (ok ? "Bid placed successfully ✅" : "Failed to place bid ❌"));
      return ok;
    } catch (e) {
      Utils.showToast(Get.context!, "Something went wrong: $e");
      return false;
    }
  }

  ///product detail api

  Future<void> fetchProductDetail() async {
    isDetailLoading = true;
    update([Constant.idProductDetail]);

    final response = await ProductDetailApi.callApi(adId: adId.toString(),userId: Database.getUserProfileResponseModel?.user?.id ?? "");
    if (response != null) {
      productDetail = response;

      Utils.showLog("detail:::::::::::::::${jsonEncode(productDetail)}");

      id = productDetail?.data?.category?.id;
      isFeaturedSeller = productDetail?.data?.seller?.isFeaturedSeller??false;
      Utils.showLog("id:::::::::::::::${id}");
      getPopularProduct(productDetail?.data?.category?.id ?? "");

      ///AD VIEW COUNT API

      if (isEdit == false) {
        Utils.showLog("productDetail?.data?.id${productDetail?.data?.id}");

        AdViewsApi.callApi(adId: productDetail?.data?.id ?? "");
      }else{

        Utils.showLog("isEdit::::::::::::::::::::${isEdit}");


      }
    }

    isDetailLoading = false;
    update([Constant.idProductDetail]);
  }

  ///specific ad view api
  List<AdView> viewList = [];
  bool isViewLoading=false;
  Future<void> specificProductView(String adId) async {
    try {
      isViewLoading = true;
      update([Constant.productView]); // UI update karva mate ID change

      final viewResponse = await SpecificProductViewApi.getViewsForAd(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (viewResponse != null) {
        Utils.showLog("Total Views => ${viewResponse.adView}");

        /// list ma save karo
        viewList = viewResponse.adView ?? [];

        /// Debug
        for (var view in viewList) {
          Utils.showLog("User: ${view.user?.name}, Profile: ${view.user?.profileImage}");
        }
      }
    } catch (e) {
      Utils.showLog("specificProductView Error => $e");
    } finally {
      isViewLoading = false;
      update([Constant.productView]); // again update after loading done
    }
  }
  ///specific product like api
  List<Like> likeList = [];
bool isLikeLoading = false;
  Future<void> specificProductLike(String adId) async {
    try {
      isLikeLoading = true;
      update([Constant.productLike]);

      final likesResponse = await SpecificProductLikeApi.getLikesForAd(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (likesResponse != null) {
        Utils.showLog("Total Likes => ${likesResponse.total}");

        /// list ma save karo
        likeList = likesResponse.likes ?? [];

        /// Debug
        for (var like in likeList) {
          Utils.showLog("User: ${like.user?.name}, Profile: ${like.user?.profileImage}");
        }
      }
    } catch (e) {
      Utils.showLog("specificProductLike Error => $e");
    } finally {
      isLikeLoading = false;
      update([Constant.productLike]);
    }
  }

  Future<void> openDialer(String rawNumber) async {
    // નંબરમાંથી space/dash કાઢી દેતાં; + રહેવા દો
    final cleaned = rawNumber.replaceAll(RegExp(r'[\s\-]'), '');


    Utils.showLog("Cleaned::::::$cleaned");

    if (cleaned.isEmpty) {
      // તમારું error UI/Toast બતાવો
      throw 'Empty phone number';
    }

    final uri = Uri(
      scheme: 'tel',
      path: cleaned, // e.g. +919876543210
    );

    // external app (Dialer) જ ખોલો
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
      throw 'Could not open dialer';
    }
  }


  Future<void> toggleMostLike1(int index, String adId) async {

    HapticFeedback.lightImpact();
    if (index < 0 || index >= relatedProductList.length) return;

    final ad = relatedProductList[index];
    final currentState = isAdLiked(ad); // current like state
    final newState = !currentState;

    // ✅ Optimistic update (UI ma fast reflect karva mate)
    LikeManager.to.updateLikeState(adId, newState);
    ad.isLike = newState;
    update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);
    try {
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (resp != null && resp.status == true) {
        // ✅ Server state confirm
        final serverIsLiked = resp.like ?? newState;

        LikeManager.to.updateLikeState(adId, serverIsLiked);
        ad.isLike = serverIsLiked;
      } else {
        // ❌ Fail → rollback
        LikeManager.to.updateLikeState(adId, currentState);
        ad.isLike = currentState;
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
    } catch (e) {
      // ❌ Exception → rollback
      LikeManager.to.updateLikeState(adId, currentState);
      ad.isLike = currentState;
      Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
    } finally {
      // ✅ Always refresh UI
      update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);    }
  }

  Future<void> toggleMostLike(int index, String adId) async {

    HapticFeedback.lightImpact();
    if (index < 0 || index >= relatedProductList.length) return;

    final ad = relatedProductList[index];
    final currentState = isAdLiked(ad);
    final newState = !currentState;

    // ✅ Optimistic update
    LikeManager.to.updateLikeState(adId, newState);
    ad.isLike = newState;
    update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);

    try {
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (resp != null && resp.status == true) {
        final serverIsLiked = resp.like ?? newState;
        LikeManager.to.updateLikeState(adId, serverIsLiked);
        ad.isLike = serverIsLiked;
      } else {
        // ❌ rollback
        LikeManager.to.updateLikeState(adId, currentState);
        ad.isLike = currentState;
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
    } catch (e) {
      // ❌ rollback
      LikeManager.to.updateLikeState(adId, currentState);
      ad.isLike = currentState;
      Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
    } finally {
      // ✅ Always refresh all relevant screens
      update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);
    }
  }


}

class ProductDetailItem {
  final String iconPath;
  final String title;
  final String? value;

  ProductDetailItem({
    required this.iconPath,
    required this.title,
    required this.value,
  });
}
