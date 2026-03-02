import 'dart:async';
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/home_screen/api/popular_product_api.dart';
import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

enum ViewType { grid, list }

class PopularProductScreenController extends GetxController {
  ViewType selectedView = ViewType.grid;
  bool isLoading = false;
  bool isPaginationLoading = false;
  bool hasMore = true;
  List<MostLikeData> popularProductList = [];
  final ScrollController scrollController = ScrollController();
  AllAdsResponseModel? allAdsResponseModel;
  MostLikeResponseModel? likeResponseModel;
  Map<String, dynamic> arguments = Get.arguments ?? {};
  bool popular = false;
  static final Map<String, bool> _globalLikeStates = {};
  static final Set<GetxController> listener = {};

  @override
  void onInit() {
    super.onInit();
    scrollController.addListener(_onScroll);
    PopularProductApi.startPagination = 0;
    popular = arguments["popular"] ?? false;
    Utils.showLog("popular::::::::::::$popular");
    getProduct();
    Utils.showLog("Database.getUserProfileResponseModel:::${Database.getUserProfileResponseModel}");
    Utils.showLog("Database.loginUserFirebaseId:::${Database.loginUserFirebaseId}");
  }

  @override
  void onClose() {
    // cleanup
    scrollController.removeListener(_onScroll);
    scrollController.dispose();
    searchController.dispose();
    super.onClose();
  }

  void toggleView(ViewType viewType) {
    selectedView = viewType;
    update([Constant.idViewType]);
  }

  /// initial load

  int startPagination = 0;
  final int limitPagination = 10;

  Future<void> getPopularProduct() async {
    try {
      isLoading = true;
      hasMore = true;
      update([Constant.idUserAds, Constant.idAllAds]);

      // ✅ Reset pagination
      startPagination = 0;

      likeResponseModel = await PopularProductApi.fetchPopularAds(
        categoryId: '',
        userId: Database.getUserProfileResponseModel?.user?.id ?? "",
        start: startPagination,
        limit: limitPagination,
      );

      final newData = likeResponseModel?.data ?? [];

      popularProductList.clear();
      popularProductList.addAll(newData);

      hasMore = newData.length >= limitPagination;
    } catch (e, st) {
      Utils.showLog("Most liked error: $e\n$st");
      Utils.showToast(Get.context!, "Failed to load products. Please try again.");
    } finally {
      isLoading = false;
      update([Constant.idUserAds, Constant.idAllAds]);
    }
  }

  /// pagination product

  /// 🟢 Scroll Listener (Single Trigger)
  void _onScroll() {
    if (!scrollController.hasClients) return;
    if (isPaginationLoading || !hasMore) return;

    final position = scrollController.position;
    if (position.pixels >= position.maxScrollExtent - 100) {
      // Debounce to prevent double call
      onPagination();
    }
  }

  /// 🟢 Load More Data
  Future<void> onPagination() async {
    if (isPaginationLoading || !hasMore) return;

    isPaginationLoading = true;
    update([Constant.idPagination]);

    final prevPage = startPagination;
    final nextPage = startPagination + 1;

    Utils.showLog("🔄 Pagination Start => Page: $nextPage");

    try {
      final response = await PopularProductApi.fetchPopularAds(
        userId: Database.getUserProfileResponseModel?.user?.id ?? "",
        start: nextPage,
        limit: limitPagination,
      );

      final newData = response?.data ?? [];

      Utils.showLog("📄 Page $nextPage => New Data: ${newData.length}");

      if (newData.isNotEmpty) {
        startPagination = nextPage;
        popularProductList.addAll(newData);
      } else {
        hasMore = false;
        startPagination = prevPage; // no change if empty
      }
    } catch (e) {
      Utils.showLog("❌ Pagination Error => $e");
      startPagination = prevPage;
    }

    isPaginationLoading = false;
    update([Constant.idPagination, Constant.idAllAds]);
  }

  /// Static method to get global like state
  static bool getGlobalLikeState(String adId, bool? fallback) {
    return _globalLikeStates[adId] ?? fallback ?? false;
  }


  /// popular product like api
  void updateGlobalLikeState(String adId, bool isLiked) {
    _globalLikeStates[adId] = isLiked;
    // Notify UI that shows likes
    update([Constant.idUserAds, Constant.idAllAds]);
  }


  bool isAdLiked(MostLikeData ad) {
    return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
  }

  Future<void> toggleLike(int index, String adId) async {
    HapticFeedback.lightImpact();

    if (index < 0 || index >= popularProductList.length) return;

    final ad = popularProductList[index];
    final currentState = isAdLiked(ad);
    final newState = !currentState;

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
        LikeManager.to.updateLikeState(adId, currentState);
        ad.isLike = currentState;
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
    } catch (e) {
      LikeManager.to.updateLikeState(adId, currentState);
      ad.isLike = currentState;
      Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
    } finally {
      update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);
    }
  }


  init() async {
    PopularProductApi.startPagination = 0;
    // getPopularProduct();
    getProduct();
    searchController.clear();
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
    PopularProductApi.startPagination = 0;

    final result = await PopularProductApi.fetchPopularAds(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      // categoryId: "id" ?? "",
      // uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      sort: sortKey,
    );

    if (result != null) {
      popularProductList = result.data!;
    } else {
      popularProductList.clear();
    }

    isLoading = false;
    update([Constant.idAllAds]);
  }

  ///
  Timer? debounce;
  final TextEditingController searchController = TextEditingController();

  void onSearchChanged() {
    if (debounce?.isActive ?? false) debounce!.cancel();
    debounce = Timer(const Duration(milliseconds: 500), () {
      final query = searchController.text.trim();
      getProduct(search: query.isNotEmpty ? query : null);
      update([Constant.idAllAds]);
    });
  }

  /// sub category product search api
  Future<void> getProductOld({String? search}) async {
    isLoading = true;
    update([Constant.idAllAds]);

    likeResponseModel = await PopularProductApi.fetchPopularAds(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      // categoryId: categoryId ?? "",
      // uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      search: search,
    );

    popularProductList.clear();
    popularProductList.addAll(likeResponseModel?.data ?? []);

    Utils.showLog("get category wise product list data $popularProductList");

    isLoading = false;
    update([Constant.idAllAds]);
  }

  // Future<void> getProduct({String? search}) async {
  //   isLoading = true;
  //   update([Constant.idAllAds]);
  //
  //   // ✅ Reset pagination on new search
  //   startPagination = 0;
  //
  //   likeResponseModel = await PopularProductApi.fetchPopularAds(
  //     userId: Database.getUserProfileResponseModel?.user?.id ?? "",
  //     search: search,
  //     start: 0,
  //     limit: 10,
  //   );
  //
  //   popularProductList.clear();
  //   popularProductList.addAll(likeResponseModel?.data ?? []);
  //
  //   hasMore = (likeResponseModel?.data?.length ?? 0) >= 10;
  //
  //   isLoading = false;
  //   update([Constant.idAllAds]);
  // }



  Future<void> getProduct({String? search}) async {
    isLoading = true;
    hasMore = true;
    startPagination = 1;
    update([Constant.idAllAds]);

    final response = await PopularProductApi.fetchPopularAds(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      start: startPagination,
      limit: limitPagination,
      search: search,
    );

    final newData = response?.data ?? [];

    popularProductList
      ..clear()
      ..addAll(newData);

    hasMore = newData.length >= limitPagination;
    isLoading = false;
    update([Constant.idAllAds]);
  }



  Future<void> openFilterAndApply() async {
    Utils.showLog(">>>>>>>>>>>>$popular");
    final result = await Get.toNamed(
      AppRoutes.productFilterScreen,
      arguments: {
        // 'filterScreen': true,
        'popular': popular,
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

    final resp = await PopularProductApi.fetchPopularAds(
      start: startPagination,
      limit: limitPagination,
      userId: Database.getUserProfileResponseModel?.user?.id ?? '',
      categoryId: map['categoryId'] ?? "",
      country: map['country'],
      state: map['state'],
      city: map['city'],
      minPrice: (map['minPrice'] ?? ''),
      maxPrice: (map['maxPrice'] ?? ''),
      latitude: (map['latitude'] ?? ''),
      longitude: (map['longitude'] ?? ''),
      attributes: List<Map<String, dynamic>>.from(map['attributes'] ?? const []),
      postedSince: (map['postedSince'] ?? 'all_time').toString(),
      rangeInKm: (map['range'] ?? ''),
    );

    popularProductList.clear();
    if (resp != null && resp.status == true) {

      Utils.showLog("res>>>>>>>>>>>>>>${jsonEncode(resp)}");
      popularProductList.addAll(resp.data!);
    } else {
      // empty state handle
    }

    isLoading = false;
    update([Constant.idAllAds]);
  }
}
