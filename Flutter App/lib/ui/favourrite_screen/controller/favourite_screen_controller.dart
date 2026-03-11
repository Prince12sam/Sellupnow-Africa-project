import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/ui/favourrite_screen/api/favourite_product_api.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

enum ViewType { grid, list }

class FavoriteScreenController extends GetxController {
  ViewType selectedView = ViewType.grid;

  final TextEditingController searchController = TextEditingController();
  String searchQuery = "";
  Timer? _debounce;

  List<AllAds> favouriteAds = [];
  AllAdsResponseModel? allAdsResponseModel;

  bool isLoading = false;
  bool isPaginationLoading = false;
  bool hasMoreData = true;

  int currentPage = 1;
  final int limit = 10;

  final ScrollController scrollController = ScrollController();

  @override
  void onInit() {
    searchController.addListener(() {
      final value = searchController.text.trim();
      _debounce?.cancel();
      _debounce = Timer(const Duration(milliseconds: 400), () {
        if (searchQuery != value) {
          searchQuery = value;
          Utils.showLog("🔍 Search query changed: '$value'");
          fetchFavouriteAds(isRefresh: true);
        }
      });
    });

    // Initial load
    fetchFavouriteAds(isRefresh: true);

    init();
    super.onInit();
  }

  init() async {
    scrollController.addListener(onTopPagination);
    FavouriteProductApi.startPagination = 0;
  }

  @override
  void onClose() {
    FavouriteProductApi.startPagination = 0;
    _debounce?.cancel();
    searchController.dispose();
    scrollController.dispose();
    super.onClose();
  }

  /// Toggle between grid and list view
  void toggleView(ViewType viewType) {
    selectedView = viewType;
    update([Constant.idViewType]);
  }

  /// Check if ad is liked
  bool isAdLiked(AllAds ad) {
    return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
  }

  /// Toggle like/unlike
  Future<void> toggleLike(int index, String adId) async {
    HapticFeedback.lightImpact();
    if (index < 0 || index >= favouriteAds.length) return;

    final ad = favouriteAds[index];
    final currentState = isAdLiked(ad);
    final newState = !currentState;

    // Optimistic update
    LikeManager.to.updateLikeState(adId, newState);
    ad.isLike = newState;
    update([Constant.idAllAds]);

    try {
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      );

      if (resp != null && resp.status == true) {
        final serverIsLiked = resp.like ?? newState;
        LikeManager.to.updateLikeState(adId, serverIsLiked);
        ad.isLike = serverIsLiked;

        // Remove from list if unliked
        if (!serverIsLiked) {
          favouriteAds.removeAt(index);
          Utils.showLog("🗑️ Removed ad from favorites at index $index");
        }
      } else {
        // Revert on failure
        LikeManager.to.updateLikeState(adId, currentState);
        ad.isLike = currentState;
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
    } catch (e) {
      // Revert on error
      LikeManager.to.updateLikeState(adId, currentState);
      ad.isLike = currentState;
      Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
    } finally {
      update([Constant.idAllAds]);
    }
  }

  /// 🔹 Fetch favourite ads
  Future<void> fetchFavouriteAds({bool isRefresh = false}) async {
    if (isRefresh) {
      Utils.showLog("🔄 Refreshing data, clearing list");
      favouriteAds.clear();
      FavouriteProductApi.startPagination = 0;
    }

    isLoading = true;
    update([Constant.idAllAds]);

    try {
      allAdsResponseModel = await FavouriteProductApi.callApi(
        userId: Database.getUserProfileResponseModel?.user?.id ?? Database.loginUserId,
        search: (searchQuery.isEmpty) ? "All" : searchQuery,
        isRefresh: isRefresh,
      );

      if (allAdsResponseModel?.data != null) {
        favouriteAds.addAll(allAdsResponseModel!.data);
        Utils.showLog("✅ Loaded ${allAdsResponseModel!.data.length} ads, Total: ${favouriteAds.length}");
      } else {
        Utils.showLog("⚠️ No data received from API");
      }
    } catch (e) {
      Utils.showLog("❌ Error fetching favourite ads: $e");
      Utils.showToast(Get.context!, "Failed to load favourite ads");
    } finally {
      isLoading = false;
      update([Constant.idAllAds]);
    }
  }

  /// Pull to refresh
  Future<void> onRefresh() async {
    Utils.showLog("🔄 onRefresh called");
    searchQuery = "";
    searchController.clear();
    await fetchFavouriteAds(isRefresh: true);
  }

  /// Pagination on scroll
  Future<void> onTopPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      if (isPaginationLoading || !hasMoreData) {
        Utils.showLog("⏸️ Pagination blocked: loading=$isPaginationLoading, hasMore=$hasMoreData");
        return;
      }

      Utils.showLog("📄 Loading more data (pagination)");

      isPaginationLoading = true;
      update([Constant.favPagination]);

      try {
        allAdsResponseModel = await FavouriteProductApi.callApi(
          userId: Database.getUserProfileResponseModel?.user?.id ?? Database.loginUserId,
          search: (searchQuery.isEmpty) ? "All" : searchQuery,
          isRefresh: false,
        );

        if (allAdsResponseModel?.data != null && allAdsResponseModel!.data.isNotEmpty) {
          favouriteAds.addAll(allAdsResponseModel!.data);
          Utils.showLog("✅ Paginated ${allAdsResponseModel!.data.length} ads, Total: ${favouriteAds.length}");

          // Check if there's more data
          if (allAdsResponseModel!.data.length < limit) {
            hasMoreData = false;
            Utils.showLog("🏁 No more data to load");
          }
        } else {
          hasMoreData = false;
          Utils.showLog("🏁 No more data available");
        }
      } catch (e) {
        Utils.showLog("❌ Error in pagination: $e");
      } finally {
        isPaginationLoading = false;
        update([Constant.favPagination]);
      }
    }
  }
}