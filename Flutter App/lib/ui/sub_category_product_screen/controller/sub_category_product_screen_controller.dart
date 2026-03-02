import 'dart:async';
import 'dart:convert';
import 'dart:developer';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/add_product_screen/api/category_attributes_api.dart';
import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart' hide Attribute;
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/ui/sub_category_product_screen/api/category_wise_product_api.dart';
import 'package:listify/ui/sub_category_product_screen/model/add_like_reponse_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';
import 'gloable_controller.dart';

enum ViewType { grid, list }

class SubCategoryProductScreenController extends GetxController {
  String? categoryTitle;
  String? categoryId;
  bool isLoading = false;
  List<AllAds> categoryWiseProductList = [];
  AllAdsResponseModel? categoryWiseProductResponseModel;
  final TextEditingController searchController = TextEditingController();
  Timer? debounce;
  bool isPaginationLoading = false;
  ScrollController scrollController = ScrollController();
  List<bool> isLikedList = [];
  AddLikeResponseModel? addLikeResponseModel;
  // bool filterScreen = false;
  bool search = false;
  bool subcategory = false;
  bool hasMoreData = true;

  Worker? locationDataWorker;

  /// Local UI overrides for like state, keyed by ad id
  final Map<String, bool> _likedOverrides = {};

  bool isAdLikedOld(AllAds ad) {
    return _likedOverrides[ad.id] ?? ad.isLike ?? false;
  }

  /// filter
  TextEditingController locationController = TextEditingController();
  TextEditingController minPriceController = TextEditingController();
  TextEditingController maxPriceController = TextEditingController();

  @override
  void onInit() {
    super.onInit();
    init();
  }

  init() async {
    final args = Get.arguments as Map<String, dynamic>? ?? {};
    categoryTitle = args['categoryTitle'];
    categoryId = args['categoryId'];
    // filterScreen = args['filterScreen'] ?? true;
    search = args['search'] ?? false;
    subcategory = args['subcategory'] ?? false;
    Utils.showLog("search sub category screen :::$search");
    Utils.showLog("subcategory>>>>>>>>>>>:::$subcategory");

    // Utils.showLog("FilterScreen>>>>>>>>>>>>>>>$filterScreen");
    Utils.showLog("args>>>>>>>>>>>>>>>$args");
    Utils.showLog("categoryId>>>>>>>>>>>>>>>$categoryId");

    // attach scroll listener for pagination (works for both list & grid)
    scrollController.addListener(onProductPagination);

    CategoryWiseProductApi.startPagination = 0;
    await getCategoryWiseProduct();

    // locationDataWorker = ever(GlobalController.to.locationData, (data) {
    //   if (isClosed) {
    //     Utils.showLog("Controller disposed, skipping update");
    //     return;
    //   }
    //
    //   // if (data.isNotEmpty) {
    //   //   Utils.showLog("Received location data: $data");
    //   //   try {
    //   //     update([Constant.idLocationUpdate]);
    //   //     getCategoryWiseProduct();
    //   //     update([Constant.idAllAds]);
    //   //   } catch (e) {
    //   //     Utils.showLog("Error updating: $e");
    //   //   }
    //   // }
    // });

    locationDataWorker = ever(GlobalController.locationData, (data) {
      if (isClosed) return;
      // city/country/state બદલાયા ત્યારે માત્ર આ ID માટે UI update
      Utils.showLog("calllllllllllllllllllll");
      update([Constant.idLocationUpdate]);
    });

    getCategoryAttribute();
  }

  void onSearchChanged() {
    if (debounce?.isActive ?? false) debounce!.cancel();
    debounce = Timer(const Duration(milliseconds: 500), () {
      final query = searchController.text.trim();
      CategoryWiseProductApi.startPagination = 0; // ✅ reset pagination on search
      getCategoryWiseProduct(search: query.isNotEmpty ? query : null);
      update([Constant.idAllAds]);
    });
  }

  // By default grid view selected

  ViewType selectedView = ViewType.grid;

  void toggleView(ViewType viewType) {
    selectedView = viewType;
    update([Constant.idViewType]);
  }

  /// sub category product search api
  Future<void> getCategoryWiseProduct({String? search}) async {
    Utils.showLog("Fetching category-wise products...");
    isLoading = true;
    hasMoreData = true; // ✅ reset
    CategoryWiseProductApi.startPagination = 0;
    update([Constant.idAllAds]);

    categoryWiseProductResponseModel = await CategoryWiseProductApi.callApi(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      categoryId: categoryId ?? "",
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      search: search,
      resetPagination: true,
    );

    categoryWiseProductList.clear();
    categoryWiseProductList.addAll(categoryWiseProductResponseModel?.data ?? []);

    isLoading = false;
    update([Constant.idAllAds]);
  }


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
    CategoryWiseProductApi.startPagination = 0;

    final result = await CategoryWiseProductApi.callApi(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      // categoryId: categoryId ?? "",
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      resetPagination: true,
      sort: sortKey,
    );

    if (result != null) {
      categoryWiseProductList = result.data;
    } else {
      categoryWiseProductList.clear();
    }

    isLoading = false;
    update([Constant.idAllAds]);
  }

  /// Refresh action should be awaitable by RefreshIndicator
  Future<void> onRefresh() async {
    CategoryWiseProductApi.startPagination = 0;
    searchController.clear();
    await getCategoryWiseProduct();
    update([Constant.idAllAds]);
  }

  /// Pagination listener: appends next page
  Future<void> onProductPagination() async {
    if (!scrollController.hasClients) return;
    if (isPaginationLoading || !hasMoreData) return;

    final maxScroll = scrollController.position.maxScrollExtent;
    final currentScroll = scrollController.position.pixels;
    const threshold = 200.0;

    // Only trigger if scrolled close to bottom
    if (currentScroll < maxScroll - threshold) return;

    // Lock pagination immediately
    isPaginationLoading = true;
    update([Constant.idPagination]);

    try {
      CategoryWiseProductApi.startPagination += 1;

      final nextPageResponse = await CategoryWiseProductApi.callApi(
        userId: Database.getUserProfileResponseModel?.user?.id ?? "",
        categoryId: categoryId ?? "",
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
        search: searchController.text.trim().isEmpty
            ? null
            : searchController.text.trim(),
        resetPagination: false,
      );

      if (nextPageResponse != null && nextPageResponse.data.isNotEmpty) {
        final existingIds = categoryWiseProductList.map((e) => e.id).toSet();
        final newItems = nextPageResponse.data
            .where((e) => !existingIds.contains(e.id))
            .toList();

        categoryWiseProductList.addAll(newItems);
      } else {
        hasMoreData = false;
        CategoryWiseProductApi.startPagination -= 1; // rollback
      }
    } catch (e) {
      if (CategoryWiseProductApi.startPagination > 0) {
        CategoryWiseProductApi.startPagination -= 1;
      }
      Utils.showLog("Pagination error: $e");
    } finally {
      // Unlock
      isPaginationLoading = false;
      update([Constant.idPagination, Constant.idAllAds]);
    }
  }




  ///like unlike api
  Future<void> toggleLikeOld(int index, String adId) async {
    if (index < 0 || index >= categoryWiseProductList.length) return;

    final ad = categoryWiseProductList[index];
    final current = isAdLiked(ad); // computed state (override -> server value)

    // optimistic UI update
    _likedOverrides[adId] = !current;
    update([Constant.idAllAds]);

    try {
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      final serverIsLiked = resp?.like;
      if (serverIsLiked != null) {
        _likedOverrides[adId] = serverIsLiked;
        update([Constant.idAllAds]);
      }
    } catch (e) {
      // revert
      _likedOverrides[adId] = current;
      update([Constant.idAllAds]);
      Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
    }
  }



  bool isAdLiked(AllAds ad) {
    return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
  }

  Future<void> toggleLike(int index, String adId) async {

    HapticFeedback.lightImpact();
    if (index < 0 || index >= categoryWiseProductList.length) return;

    final ad = categoryWiseProductList[index];
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


  ///posted since all time today
  String postedSince = "all_time"; // default

  void setPostedSince(String value) {
    postedSince = value;
    update([Constant.idFilterUpdate]);
  }

  List<Map<String, dynamic>> selectedAttributes = [];

  ///categoryWise FilterProduct Api

  List<Attribute> attributeDataList = [];

  ///attribute get api
  CategoryAttributesResponseModel? categoryAttributesResponseModel;
  Future<void> getCategoryAttribute() async {
    isLoading = true;
    update([Constant.idAllAds]);

    Utils.showLog("last category id user for attribute api ::: $categoryId");
    categoryAttributesResponseModel = await CategoryAttributesApi.callApi(categoryId: categoryId);

    if (categoryAttributesResponseModel?.status == true) {
      attributeDataList = categoryAttributesResponseModel?.data ?? [];
    }

    Utils.showLog("fetch category vise attribute data : ${attributeDataList.length}");

    isLoading = false;
    update([Constant.idAllAds]);
  }

  /// radio selection
  /// selections (cleare  d on Reset)
  final Map<int, int> selectedRadioIndices = {}; // fieldType 4
  final Map<int, String> textFieldValues = {}; // fieldType 1/2
  final Map<int, List<String>> selectedChipValues = {}; // fieldType 6/7
  final Map<int, PlatformFile> selectedFiles = {}; // fieldType 3
  final Map<int, String> selectedDropdownValues = {}; // fieldType 5
  void radioSelection(int attributeIndex, int selectedIndex) {
    selectedRadioIndices[attributeIndex] = selectedIndex;
    update();
    Utils.showLog("Selected value for field $attributeIndex is index $selectedIndex → ${attributeDataList[attributeIndex].values?[selectedIndex]}");
  }

  /// text field values
  void updateTextValue(int attributeIndex, String value) {
    textFieldValues[attributeIndex] = value;
    update();
    Utils.showLog("Text input for $attributeIndex: $value");
  }

  void updateDropdownValue(int attributeIndex, String value) {
    selectedDropdownValues[attributeIndex] = value;
    update([Constant.idAllAds]);
    Utils.showLog("Dropdown value for $attributeIndex: $value");
  }

  ///

  Future<void> openFilterAndApply() async {
    final result = await Get.toNamed(
      AppRoutes.productFilterScreen,
      arguments: {
        // 'filterScreen': true,
        'categoryId': categoryId,
        'ad': categoryWiseProductList,
        'subcategory': subcategory,
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

  // actual API call અહીં centralised
  Future<void> _applyFilterFromMap(Map<String, dynamic> map) async {
    isLoading = true;
    update([Constant.idAllAds]);

    final resp = await CategoryWiseProductApi.callApi(
      userId: Database.getUserProfileResponseModel?.user?.id ?? '',
      categoryId: (map['categoryId'] ?? categoryId) ?? "",
      country: map['country'],
      state: map['state'],
      city: map['city'],
      minPrice: (map['minPrice'] ?? '').toString(),
      maxPrice: (map['maxPrice'] ?? '').toString(),
      latitude: (map['latitude'] ?? '').toString(),
      longitude: (map['longitude'] ?? '').toString(),
      attributes: List<Map<String, dynamic>>.from(map['attributes'] ?? const []),
      postedSince: (map['postedSince'] ?? 'all_time').toString(),
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      resetPagination: true,
    );

    categoryWiseProductList.clear();
    if (resp != null && resp.status == true) {

      Utils.showLog("resp >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>${jsonEncode(resp)}");
      categoryWiseProductList.addAll(resp.data);
    } else {
      // empty state handle
    }

    isLoading = false;
    update([Constant.idAllAds]);
  }

  @override
  void onClose() {
    debounce?.cancel();
    searchController.dispose();

    // dispose scroll controller
    try {
      scrollController.removeListener(onProductPagination);
      scrollController.dispose();
    } catch (_) {}

    locationDataWorker?.dispose();
    locationDataWorker = null;

    GlobalController.locationData['selectedCity'] = null;
    GlobalController.locationData['selectedCountry'] = null;
    GlobalController.locationData['selectedState'] = null;
    maxPriceController.clear();
    minPriceController.clear();
    super.onClose();
  }
}
