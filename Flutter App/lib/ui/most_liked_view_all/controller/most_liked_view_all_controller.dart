import 'dart:async';
import 'dart:convert';
import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/home_screen/api/most_liked_product_api.dart';
import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

enum ViewType { grid, list }

class MostLikedViewAllController extends GetxController {
  ViewType selectedView = ViewType.grid;
  final int limit = 10;

  //
  List<MostLikeData> favouriteAds = [];
  AllAdsResponseModel? allAdsResponseModel;
  MostLikeResponseModel? likeResponseModel;

  bool isLoading = false;
  bool isPaginationLoading = false;
  bool hasMoreData = true;
  bool hasMore = true;
  bool mostLike = false;
  Map<String, dynamic> arguments = Get.arguments ?? {};

  final ScrollController scrollController = ScrollController();

  @override
  void onInit() {
    init();
    mostLike = arguments["mostLike"] ?? false;

    log("mostlike all screen::::::$mostLike");
    super.onInit();
  }

  @override
  void onClose() {
    MostLikedProductApi.startPagination=0;
    scrollController.dispose();
    super.onClose();
  }

  void toggleView(ViewType viewType) {
    selectedView = viewType;
    update([Constant.idViewType]);
  }

  /// like/unlike optimistic
  bool isAdLiked(MostLikeData ad) {
    return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
  }

  Future<void> toggleLike(int index, String adId) async {

    HapticFeedback.lightImpact();
    if (index < 0 || index >= favouriteAds.length) return;

    final ad = favouriteAds[index];
    final currentState = isAdLiked(ad);
    final newState = !currentState;

    LikeManager.to.updateLikeState(adId, newState);
    ad.isLike = newState;
    update([Constant.idAllAds, Constant.idUserAds]);

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
        LikeManager.to.updateLikeState(adId, currentState);
        ad.isLike = currentState;
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
    } catch (e) {
      LikeManager.to.updateLikeState(adId, currentState);
      ad.isLike = currentState;
      Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
    } finally {
      update([Constant.idAllAds, Constant.idUserAds]);
    }
  }


  void init() {
    getMostLikeProduct(isRefresh: true);
    MostLikedProductApi.startPagination=0;
    scrollController.addListener(onTopPagination);
    searchController.clear();
  }

/// get all most like product\
  Future<void> getMostLikeProduct({String? search, bool isRefresh = false}) async {
    if (isRefresh) {
      favouriteAds.clear();
      hasMoreData = true;
      MostLikedProductApi.startPagination = 0;
    }

    if (!hasMoreData) return;

    if (isRefresh) {
      isLoading = true;
      isPaginationLoading = false;
    } else {
      isPaginationLoading = true;
    }

    update([Constant.idAllAds, Constant.favPagination]);

    try {
      final resp = await MostLikedProductApi.fetchMostLikedAds(
        userId: Database.getUserProfileResponseModel?.user?.id ?? "",
        search: search,
        isRefresh: isRefresh,
      );

      final newData = resp?.data ?? [];

      final newUnique = newData.where((item) =>
      !favouriteAds.any((e) => e.id == item.id)
      ).toList();

      if (newUnique.isNotEmpty) {
        favouriteAds.addAll(newUnique);
      }

      if (newData.length < limit) {
        hasMoreData = false;
      } else {
        hasMoreData = true;
      }

    } catch (e) {
      Utils.showLog("❌ Fetch error => $e");
      Utils.showToast(Get.context!, "Failed to load data");
    } finally {
      isLoading = false;
      isPaginationLoading = false;
      update([Constant.idAllAds, Constant.favPagination]);
    }
  }


  Future<void> onTopPagination() async {
    if (!scrollController.hasClients) return;

    final atBottom = scrollController.position.pixels >=
        scrollController.position.maxScrollExtent - 10;

    if (!atBottom) return;
    if (isPaginationLoading || !hasMoreData) return;

    await getMostLikeProduct(isRefresh: false);
  }


  ///sort
  String getSortKeyFromIndex(int index) {
    switch (index) {
      case 1:
        return 'new';
      case 2:
        return 'old';
      case 3:
        return 'high_price';
      case 4:
        return 'low_price';
      default:
        return '';
    }
  }

  void applySort(String sortKey) async {
    isLoading = true;
    update([Constant.idAllAds]);

    final result = await MostLikedProductApi.fetchMostLikedAds(
      isRefresh: true,
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      sort: sortKey,
    );



    if (result != null) {
      favouriteAds = result.data ?? [];
      hasMoreData = (result.data?.length ?? 0) >= limit;
    } else {
      favouriteAds.clear();
      hasMoreData = false;
    }
    update([Constant.idAllAds]);
  }

  ///search

  final TextEditingController searchController = TextEditingController();
  Timer? debounce;

  void onSearchChanged() {
    if (debounce?.isActive ?? false) debounce!.cancel();
    debounce = Timer(const Duration(milliseconds: 500), () {
      final query = searchController.text.trim();
      getMostLikeProduct(search: query.isNotEmpty ? query : null,isRefresh: true);
      update([Constant.idAllAds]);
    });
  }



  ///filter

  Future<void> openFilterAndApply() async {
    final result = await Get.toNamed(
      AppRoutes.productFilterScreen,
      arguments: {
        'mostLike': mostLike,
      },
    );

    if (result == null) return;

    try {
      final map = Map<String, dynamic>.from(result as Map);
      await _applyFilterFromMap(map);
    } catch (e) {
      Utils.showLog("Invalid filter payload: $e");
    }
  }

  Future<void> _applyFilterFromMap(Map<String, dynamic> map) async {
    isLoading = true;
    update([Constant.idAllAds]);

    final resp = await MostLikedProductApi.fetchMostLikedAds(
      isRefresh: true,
      userId: Database.getUserProfileResponseModel?.user?.id ?? '',
      categoryId: map['categoryId'] ?? "",
      country: map['country'],
      state: map['state'],
      city: map['city'],
      minPrice: (map['minPrice'] ?? ''),
      maxPrice: (map['maxPrice'] ?? ''),
      latitude: Database.selectedLocation['latitude'].toString(),
      longitude: Database.selectedLocation['longitude'].toString(),
      attributes: List<Map<String, dynamic>>.from(map['attributes'] ?? const []),
      postedSince: (map['postedSince'] ?? 'all_time').toString(),
      rangeInKm: (map['range'] ?? ''),
    );
    

    favouriteAds.clear();
    if (resp != null && resp.status == true) {
      favouriteAds.addAll(resp.data!);
      hasMoreData = (resp.data?.length ?? 0) >= limit;
    } else {
      hasMoreData = false;
    }
    update([Constant.idAllAds]);

    isLoading = false;
    update([Constant.idAllAds]);
  }
}
