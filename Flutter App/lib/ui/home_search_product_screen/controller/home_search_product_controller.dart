// import 'dart:async';
//
// import 'package:flutter/cupertino.dart';
// import 'package:flutter/services.dart';
// import 'package:get/get.dart';
// import 'package:listify/routes/app_routes.dart';
// import 'package:listify/ui/home_screen/api/popular_product_api.dart';
// import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
// import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/database.dart';
// import 'package:listify/utils/like_manager.dart';
// import 'package:listify/utils/utils.dart';
//
// class HomeSearchProductController extends GetxController {
//   bool isLoading = false;
//   MostLikeResponseModel? likeResponseModel;
//   List<MostLikeData> popularProductList = [];
//   int startPagination = 1;
//   final int limitPagination = 10;
//   bool hasMore = true;
//   final ScrollController scrollController = ScrollController();
//   bool isPaginationLoading = false;
//
//
//   // keep current query to reuse for pagination after search/sort/filter
//   String? _qSearch;
//   String? _qCategoryId;
//   String? _qCountry;
//   String? _qState;
//   String? _qCity;
//   String? _qMinPrice;
//   String? _qMaxPrice;
//   String? _qPostedSince;
//   String? _qLatitude;
//   String? _qLongitude;
//   String? _qRangeInKm;
//   String? _qSort;
//   List<Map<String, dynamic>> _qAttributes = const [];
//
//   Map<String, dynamic> arguments = Get.arguments ?? {};
//   bool search = false;
//
//   @override
//   void onInit() {
//     scrollController.addListener(_onScroll);
//
//     getPopularProduct();
//     init();
//     super.onInit();
//   }
//
//   init() {
//     search = arguments["search"] ?? false;
//
//     Utils.showLog("search home screen :::$search");
//   }
//
//   Future<void> onRefresh() async {
//     FocusScope.of(Get.context!).unfocus(); // hide keyboard
//     searchController.clear();
//     _qSearch = null;
//     hasMore = true;
//     PopularProductApi.startPagination = 0;
//     await getPopularProduct();
//   }
//
//
//
//   Future<void> getPopularProduct() async {
//     try {
//       isLoading = true;
//       hasMore = true;
//       startPagination = 0;
//       update([Constant.idAllAds, Constant.idUserAds]);
//
//       likeResponseModel = await PopularProductApi.fetchPopularAds(
//         categoryId: _qCategoryId,
//         userId: Database.getUserProfileResponseModel?.user?.id ?? "",
//         country: _qCountry,
//         state: _qState,
//         city: _qCity,
//         minPrice: _qMinPrice,
//         maxPrice: _qMaxPrice,
//         postedSince: _qPostedSince,
//         search: _qSearch,
//         latitude: _qLatitude,
//         longitude: _qLongitude,
//         rangeInKm: _qRangeInKm,
//         sort: _qSort,
//         attributes: _qAttributes,
//         start: startPagination,
//         limit: limitPagination,
//       );
//
//       final newData = likeResponseModel?.data ?? [];
//       popularProductList
//         ..clear()
//         ..addAll(newData);
//
//       hasMore = newData.length >= limitPagination;
//     } catch (e, st) {
//       Utils.showLog("Most liked error: $e\n$st");
//     } finally {
//       isLoading = false;
//       update([Constant.idAllAds, Constant.idUserAds]);
//     }
//   }
//
//   void _onScroll() {
//     if (!scrollController.hasClients) return;
//
//     final threshold = 150; // distance from bottom to trigger
//     final position = scrollController.position;
//     final isNearBottom = position.pixels >= (position.maxScrollExtent - threshold);
//
//     // ✅ Only trigger if:
//     // - Not already loading
//     // - Still has more data
//     // - Near bottom
//     if (isNearBottom && !isPaginationLoading && hasMore) {
//       onPagination();
//     }
//   }
//
//   Future<void> onPagination() async {
//     if (isPaginationLoading || !hasMore) return;
//
//     // ✅ Prevent double call immediately
//     isPaginationLoading = true;
//     update([Constant.idPagination, Constant.idAllAds]);
//
//     final prevPage = startPagination;
//     final nextPage = startPagination + 1;
//     Utils.showLog("📄 Pagination: Trying Page $nextPage");
//
//     try {
//       final resp = await PopularProductApi.fetchPopularAds(
//         userId: Database.getUserProfileResponseModel?.user?.id ?? "",
//         categoryId: _qCategoryId,
//         country: _qCountry,
//         state: _qState,
//         city: _qCity,
//         minPrice: _qMinPrice,
//         maxPrice: _qMaxPrice,
//         postedSince: _qPostedSince,
//         search: _qSearch,
//         latitude: _qLatitude,
//         longitude: _qLongitude,
//         rangeInKm: _qRangeInKm,
//         sort: _qSort,
//         attributes: _qAttributes,
//         start: nextPage,
//         limit: limitPagination,
//       );
//
//       final newData = resp?.data ?? [];
//
//       // ✅ Only increase page if data found
//       if (newData.isNotEmpty) {
//         startPagination = nextPage;
//         popularProductList.addAll(newData);
//         hasMore = newData.length >= limitPagination;
//         Utils.showLog("✅ Page $nextPage Loaded (${newData.length} items)");
//       } else {
//         hasMore = false;
//         startPagination = prevPage;
//         Utils.showLog("⚠️ No more data, stopping pagination");
//       }
//     } catch (e) {
//       Utils.showLog("❌ Pagination Error => $e");
//       startPagination = prevPage;
//     } finally {
//       // 🟢 Small delay prevents second trigger
//       await Future.delayed(const Duration(milliseconds: 300));
//       isPaginationLoading = false;
//       update([Constant.idPagination, Constant.idAllAds]);
//     }
//   }
//
//   /// like/unlike optimistic
//
//   bool isAdLiked(MostLikeData ad) {
//     return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
//   }
//
//   Future<void> toggleLike(int index, String adId) async {
//
//     HapticFeedback.lightImpact();
//     if (index < 0 || index >= popularProductList.length) return;
//
//     final ad = popularProductList[index];
//     final currentState = isAdLiked(ad);
//     final newState = !currentState;
//
//     // ✅ Optimistic update
//     LikeManager.to.updateLikeState(adId, newState);
//     ad.isLike = newState;
//     update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);
//
//     try {
//       final resp = await AddLikeApi.callApi(
//         adId: adId,
//         uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
//       );
//
//       if (resp != null && resp.status == true) {
//         final serverIsLiked = resp.like ?? newState;
//         LikeManager.to.updateLikeState(adId, serverIsLiked);
//         ad.isLike = serverIsLiked;
//       } else {
//         // ❌ rollback
//         LikeManager.to.updateLikeState(adId, currentState);
//         ad.isLike = currentState;
//         Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
//       }
//     } catch (e) {
//       // ❌ rollback
//       LikeManager.to.updateLikeState(adId, currentState);
//       ad.isLike = currentState;
//       Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
//     } finally {
//       // ✅ Always refresh all relevant screens
//       update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);
//     }
//   }
//
//   Timer? debounce;
//   final TextEditingController searchController = TextEditingController();
//
//   void onSearchChanged() {
//     if (debounce?.isActive ?? false) debounce!.cancel();
//     debounce = Timer(const Duration(milliseconds: 500), () {
//       final query = searchController.text.trim();
//       getProduct(search: query.isNotEmpty ? query : null);
//       update([Constant.idAllAds]);
//     });
//   }
//
//
//
//
//   Future<void> getProduct({String? search}) async {
//     _qSearch = search;
//     _qSort = null; // (optional) clear sort when searching
//     startPagination = 0;
//     hasMore = true;
//
//     isLoading = true;
//     update([Constant.idAllAds]);
//
//     final resp = await PopularProductApi.fetchPopularAds(
//       userId: Database.getUserProfileResponseModel?.user?.id ?? "",
//       search: _qSearch,
//       sort: _qSort,
//       start: startPagination,
//       limit: limitPagination,
//     );
//
//     popularProductList
//       ..clear()
//       ..addAll(resp?.data ?? []);
//
//     hasMore = (resp?.data?.length ?? 0) >= limitPagination;
//
//     isLoading = false;
//     update([Constant.idAllAds]);
//   }
//   ///sort
//   String getSortKeyFromIndex(int index) {
//     switch (index) {
//       case 1:
//         return 'new';
//       case 2:
//         return 'old';
//       case 3:
//         return 'high_price';
//       case 4:
//         return 'low_price';
//       default:
//         return '';
//     }
//   }
//
//
//   void applySort(String sortKey) async {
//     _qSort = sortKey;
//     startPagination = 0;
//     hasMore = true;
//
//     isLoading = true;
//     update([Constant.idAllAds]);
//
//     final result = await PopularProductApi.fetchPopularAds(
//       userId: Database.getUserProfileResponseModel?.user?.id ?? "",
//       search: _qSearch,
//       sort: _qSort,
//       start: startPagination,
//       limit: limitPagination,
//     );
//
//     popularProductList = result?.data ?? [];
//     hasMore = (result?.data?.length ?? 0) >= limitPagination;
//
//     isLoading = false;
//     update([Constant.idAllAds]);
//   }
//   ///
//   Future<void> openFilterAndApply() async {
//     final result = await Get.toNamed(
//       AppRoutes.productFilterScreen,
//       arguments: {
//         // 'filterScreen': true,
//         'search': search,
//       },
//     );
//
//     if (result == null) return;
//
//     try {
//       final map = Map<String, dynamic>.from(result as Map);
//       await _applyFilterFromMap(map);
//     } catch (e) {
//       Utils.showLog("Invalid filter payload: $e");
//     }
//   }
//
//
//
//
//
//   Future<void> _applyFilterFromMap(Map<String, dynamic> map) async {
//     isLoading = true;
//     update([Constant.idAllAds]);
//
//     _qCategoryId = map['categoryId'] ?? "";
//     _qCountry = map['country'];
//     _qState = map['state'];
//     _qCity = map['city'];
//     _qMinPrice = map['minPrice']?.toString();
//     _qMaxPrice = map['maxPrice']?.toString();
//     _qPostedSince = (map['postedSince'] ?? 'all_time').toString();
//     _qAttributes = List<Map<String, dynamic>>.from(map['attributes'] ?? const []);
//     _qRangeInKm = map['range']?.toString();
//     _qLatitude = Database.selectedLocation['latitude'].toString();
//     _qLongitude = Database.selectedLocation['longitude'].toString();
//
//     startPagination = 0;
//     hasMore = true;
//
//     final resp = await PopularProductApi.fetchPopularAds(
//       userId: Database.getUserProfileResponseModel?.user?.id ?? '',
//       categoryId: _qCategoryId,
//       country: _qCountry,
//       state: _qState,
//       city: _qCity,
//       minPrice: _qMinPrice,
//       maxPrice: _qMaxPrice,
//       latitude: _qLatitude,
//       longitude: _qLongitude,
//       attributes: _qAttributes,
//       postedSince: _qPostedSince,
//       rangeInKm: _qRangeInKm,
//       start: startPagination,
//       limit: limitPagination,
//     );
//
//     popularProductList
//       ..clear()
//       ..addAll(resp?.data ?? []);
//
//     hasMore = (resp?.data?.length ?? 0) >= limitPagination;
//
//     isLoading = false;
//     update([Constant.idAllAds]);
//   }
//
//
//   @override
//   void onClose() {
//     // cleanup
//     scrollController.removeListener(_onScroll);
//     scrollController.dispose();
//     searchController.dispose();
//     super.onClose();
//   }
// }
import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/home_screen/api/popular_product_api.dart';
import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

class HomeSearchProductController extends GetxController {
  bool isLoading = false;
  bool isPaginationLoading = false;
  bool hasMore = true;
  bool isApiInitialized = false;   bool paginationLock = false;


  MostLikeResponseModel? likeResponseModel;
  List<MostLikeData> popularProductList = [];

  int startPagination = 0;
  final int limitPagination = 10;
  final ScrollController scrollController = ScrollController();

  Map<String, dynamic> arguments = Get.arguments ?? {};
  bool search = false;

  // keep query params
  String? _qSearch,
      _qCategoryId,
      _qCountry,
      _qState,
      _qCity,
      _qMinPrice,
      _qMaxPrice,
      _qPostedSince,
      _qLatitude,
      _qLongitude,
      _qRangeInKm,
      _qSort;

  List<Map<String, dynamic>> _qAttributes = const [];

  Timer? debounce;
  final TextEditingController searchController = TextEditingController();

  @override
  void onInit() {
    super.onInit();
    scrollController.addListener(_onScroll);
    search = arguments["search"] ?? false;
  }

  @override
  void onReady() {
    super.onReady();
    if (!isApiInitialized) {
      isApiInitialized = true;
      getPopularProduct();
    }
  }
  void init() {
    search = arguments["search"] ?? false;
    Utils.showLog("search home screen: $search");
  }

  /// -------------------- Refresh --------------------
  Future<void> onRefresh() async {
    FocusScope.of(Get.context!).unfocus();
    searchController.clear();
    _qSearch = null;
    hasMore = true;
    startPagination = 0;
    popularProductList.clear();
    await getPopularProduct();
  }

  /// -------------------- Initial Load --------------------
  Future<void> getPopularProduct() async {
    if (isLoading) return;
    isLoading = true;
    hasMore = true;
    startPagination = 0;

    update([Constant.idAllAds, Constant.idUserAds]);

    try {
      likeResponseModel = await PopularProductApi.fetchPopularAds(
        userId: Database.getUserProfileResponseModel?.user?.id ?? "",
        categoryId: _qCategoryId,
        country: _qCountry,
        state: _qState,
        city: _qCity,
        minPrice: _qMinPrice,
        maxPrice: _qMaxPrice,
        postedSince: _qPostedSince,
        search: _qSearch,
        latitude: _qLatitude,
        longitude: _qLongitude,
        rangeInKm: _qRangeInKm,
        sort: _qSort,
        attributes: _qAttributes,
        start: startPagination,
        limit: limitPagination,
      );

      final newData = likeResponseModel?.data ?? [];
      popularProductList
        ..clear()
        ..addAll(newData);

      hasMore = newData.length >= limitPagination;
      Utils.showLog("✅ Initial load: ${newData.length}");
    } catch (e, st) {
      Utils.showLog("❌ Initial load error: $e\n$st");
    } finally {
      isLoading = false;
      update([Constant.idAllAds, Constant.idUserAds]);
    }
  }

  /// -------------------- Scroll Listener --------------------
  void _onScroll() {
    if (!scrollController.hasClients ||
        paginationLock ||
        isPaginationLoading ||
        isLoading ||
        !hasMore) return;

    final position = scrollController.position;
    if (position.pixels >= position.maxScrollExtent - 200) {
      paginationLock = true;
      onPagination();
    }
  }

  /// -------------------- Pagination --------------------
  Future<void> onPagination() async {
    if (isPaginationLoading || !hasMore) {
      paginationLock = false;
      return;
    }

    isPaginationLoading = true;
    update([Constant.idPagination, Constant.idAllAds]);

    final nextPage = startPagination + 1;
    Utils.showLog("📄 Pagination request: page=$nextPage (startPagination=$startPagination)");

    try {
      final resp = await PopularProductApi.fetchPopularAds(
        userId: Database.getUserProfileResponseModel?.user?.id ?? "",
        categoryId: _qCategoryId,
        country: _qCountry,
        state: _qState,
        city: _qCity,
        minPrice: _qMinPrice,
        maxPrice: _qMaxPrice,
        postedSince: _qPostedSince,
        search: _qSearch,
        latitude: _qLatitude,
        longitude: _qLongitude,
        rangeInKm: _qRangeInKm,
        sort: _qSort,
        attributes: _qAttributes,
        start: nextPage,
        limit: limitPagination,
      );

      final newData = resp?.data ?? [];

      if (newData.isNotEmpty) {
        // ✅ Only increase page if data received
        startPagination = nextPage;
        popularProductList.addAll(newData);
        hasMore = newData.length >= limitPagination;
        Utils.showLog("✅ Page $nextPage loaded (${newData.length} items)");
      } else {
        hasMore = false;
        Utils.showLog("⚠️ No more data available — pagination stopped");
      }
    } catch (e) {
      Utils.showLog("❌ Pagination error: $e");
    } finally {
      await Future.delayed(const Duration(milliseconds: 400));
      isPaginationLoading = false;
      paginationLock = false; // unlock after load
      update([Constant.idPagination, Constant.idAllAds]);
    }
  }



  // 🔹 Like/unlike optimistic
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
        ad.isLike = serverIsLiked;
        LikeManager.to.updateLikeState(adId, serverIsLiked);
      } else {
        ad.isLike = currentState;
        LikeManager.to.updateLikeState(adId, currentState);
        Utils.showToast(Get.context!, "Couldn't update like.");
      }
    } catch (e) {
      ad.isLike = currentState;
      LikeManager.to.updateLikeState(adId, currentState);
      Utils.showToast(Get.context!, "Couldn't update like.");
    } finally {
      update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);
    }
  }

  /// -------------------- Search --------------------
  void onSearchChanged() {
    if (debounce?.isActive ?? false) debounce!.cancel();
    debounce = Timer(const Duration(milliseconds: 500), () {
      final query = searchController.text.trim();
      getProduct(search: query.isNotEmpty ? query : null);
    });
  }


  Future<void> getProduct({String? search}) async {
    _qSearch = search;
    _qSort = null;
    startPagination = 0;
    hasMore = true;

    isLoading = true;
    update([Constant.idAllAds]);

    final resp = await PopularProductApi.fetchPopularAds(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      search: _qSearch,
      sort: _qSort,
      start: startPagination,
      limit: limitPagination,
    );

    popularProductList
      ..clear()
      ..addAll(resp?.data ?? []);

    hasMore = (resp?.data?.length ?? 0) >= limitPagination;

    isLoading = false;
    update([Constant.idAllAds]);
  }
  // 🔹 Sort
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
    _qSort = sortKey;
    startPagination = 0;
    hasMore = true;

    isLoading = true;
    update([Constant.idAllAds]);

    final result = await PopularProductApi.fetchPopularAds(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      search: _qSearch,
      sort: _qSort,
      start: startPagination,
      limit: limitPagination,
    );

    popularProductList = result?.data ?? [];
    hasMore = (result?.data?.length ?? 0) >= limitPagination;

    isLoading = false;
    update([Constant.idAllAds]);
  }

  // 🔹 Filter
  Future<void> openFilterAndApply() async {
    final result = await Get.toNamed(
      AppRoutes.productFilterScreen,
      arguments: {'search': search},
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

    _qCategoryId = map['categoryId'] ?? "";
    _qCountry = map['country'];
    _qState = map['state'];
    _qCity = map['city'];
    _qMinPrice = map['minPrice']?.toString();
    _qMaxPrice = map['maxPrice']?.toString();
    _qPostedSince = (map['postedSince'] ?? 'all_time').toString();
    _qAttributes = List<Map<String, dynamic>>.from(map['attributes'] ?? const []);
    _qRangeInKm = map['range']?.toString();
    _qLatitude = Database.selectedLocation['latitude'].toString();
    _qLongitude = Database.selectedLocation['longitude'].toString();

    startPagination = 0;
    hasMore = true;

    final resp = await PopularProductApi.fetchPopularAds(
      userId: Database.getUserProfileResponseModel?.user?.id ?? '',
      categoryId: _qCategoryId,
      country: _qCountry,
      state: _qState,
      city: _qCity,
      minPrice: _qMinPrice,
      maxPrice: _qMaxPrice,
      latitude: _qLatitude,
      longitude: _qLongitude,
      attributes: _qAttributes,
      postedSince: _qPostedSince,
      rangeInKm: _qRangeInKm,
      start: startPagination,
      limit: limitPagination,
    );

    popularProductList
      ..clear()
      ..addAll(resp?.data ?? []);

    hasMore = (resp?.data?.length ?? 0) >= limitPagination;

    isLoading = false;
    update([Constant.idAllAds]);
  }

  @override
  void onClose() {
    scrollController.removeListener(_onScroll);
    scrollController.dispose();
    searchController.dispose();
    super.onClose();
  }
}
