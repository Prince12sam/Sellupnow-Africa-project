import 'package:carousel_slider/carousel_options.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/ui/home_screen/api/all_category_api.dart';
import 'package:listify/ui/home_screen/api/banner_api.dart';
import 'package:listify/ui/home_screen/api/live_auction_list_api.dart';
import 'package:listify/ui/home_screen/api/most_liked_product_api.dart';
import 'package:listify/ui/home_screen/api/popular_product_api.dart';
import 'package:listify/ui/home_screen/model/banner_api_response_model.dart';
import 'package:listify/ui/home_screen/model/category_api_model.dart';
import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
import 'package:listify/ui/home_screen/model/popular_product_response_model.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/model/sub_category_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

class HomeScreenController extends GetxController {
  int currentIndex = 0;
  final List<String> imageList = [
    AppAsset.offerImage,
    AppAsset.offerImage,
    AppAsset.offerImage,
  ];
  bool isLoading = true;
  bool subcategory = true;
  bool isPaginationLoading = false;
  BannerResponseModel? bannerResponseModel;
  AllCategoryResponseModel? allCategoryResponseModel;
  List<BannerList> bannerList = [];
  List<AllCategory> allCategoryList = [];
  GetUserProfileResponseModel? getUserProfileResponseModel;
  PopularProductResponseModel? popularProductResponseModel;
  AllAdsResponseModel? allAdsResponseModel;
  List<MostLikeData> popularProductList = [];
  // List<AllAds> popularProductList = [];
  List<AllAds> liveAuctionProductList = [];
  ScrollController scrollController = ScrollController();
  // Paging
  int currentPage = 1;
  final int limit = 20;
  bool hasMoreData = true;
  List<MostLikeData> favouriteAds = [];
  MostLikeResponseModel? likeResponseModel;
  // List<AllAds> favouriteAds = [];
  int startPagination = 1;
  final int limitPagination = 10;

  @override
  void onInit() {
    listener.add(this);

    if (!Get.isRegistered<LikeManager>()) {
      Get.put(LikeManager(), permanent: true);
    }
    init();
    super.onInit();
    Utils.showLog("Database.getUserProfileResponseModel:::${Database.getUserProfileResponseModel}");
    Utils.showLog("Database.getUserProfileResponseModel:::${Database.loginUserFirebaseId}");
  }

  @override
  void onClose() {
    listener.remove(this);
    super.onClose();
  }

  init() async {
    scrollController.addListener(onProductPagination);
    PopularProductApi.startPagination = 0;
    LiveAuctionListApi.startPagination = 1;
    MostLikedProductApi.startPagination = 1;

    //
    getUserProfileResponseModel = await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
    Database.getUserProfileResponseModel = getUserProfileResponseModel;
    update([Constant.idProfile]);

    //
    getBanner();
    getAllCategory();
    getPopularProduct();
    liveAuctionProductListApi();

    //
    await fetchFavouriteAds();
  }

  /// slider change
  void onPageChanged(int index, CarouselPageChangedReason reason) {
    currentIndex = index;
    update();
  }

  /// banner api
  bool isBanner = true;
  getBanner() async {
    isBanner = true;
    update([Constant.idBanner]);
    bannerResponseModel = await BannerApi.callApi();
    bannerList.clear();
    bannerList.addAll(bannerResponseModel?.data ?? []);

    Utils.showLog("Banner list data $bannerList");

    isBanner = false;
    update([Constant.idBanner]);
  }

  /// get all category api
  bool isCategory = true;
  getAllCategory() async {
    isCategory = true;
    update([Constant.idAllCategory]);
    allCategoryResponseModel = await AllCategoryApi.callApi();
    allCategoryList.clear();
    allCategoryList.addAll(allCategoryResponseModel?.data ?? []);

    Utils.showLog("All category list data $allCategoryList");

    isCategory = false;
    update([Constant.idAllCategory]);
  }

  /// popular product api
  getPopularProduct() async {
    isLoading = true;
    update();
    likeResponseModel = await PopularProductApi.fetchPopularAds(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      // uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );
    popularProductList.clear();
    popularProductList.addAll(likeResponseModel?.data ?? []);

    Utils.showLog("popular product list data $popularProductList");

    isLoading = false;
    update();
  }

  /// refresh
  onRefresh() async {
    PopularProductApi.startPagination = 0;
    LiveAuctionListApi.startPagination = 1;
    MostLikedProductApi.startPagination = 1;
    getUserProfileResponseModel = await GetUserProfileApi.callApi(
      loginUserId: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );
    Database.getUserProfileResponseModel = getUserProfileResponseModel;
    update([Constant.idProfile]);

    getBanner();
    getAllCategory();
    liveAuctionProductListApi();
    getPopularProduct();
    await fetchFavouriteAds();
  }

  /// live auction product api
  bool isAuction = true;
  liveAuctionProductListApi() async {
    isAuction = true;
    update([Constant.idAuction]);
    allAdsResponseModel = await LiveAuctionListApi.callApi();
    liveAuctionProductList.clear();
    liveAuctionProductList.addAll(allAdsResponseModel?.data ?? []);

    Utils.showLog("live auction product list data $liveAuctionProductList");

    isAuction = false;
    update([Constant.idAuction]);
  }

  /// pagination
  Future<void> onProductPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      isPaginationLoading = true;
      update([Constant.idPagination]);

      likeResponseModel = await PopularProductApi.fetchPopularAds(
        userId: Database.getUserProfileResponseModel?.user?.id ?? "",
        // uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
      );
      popularProductList.clear();
      popularProductList.addAll(likeResponseModel?.data ?? []);

      isPaginationLoading = false;
      update([
        Constant.idGetCountry,
        Constant.idPagination,
        Constant.idAllAds,
      ]);
    }
  }

  /// popular product like api
  static void updateGlobalLikeState(String adId, bool isLiked) {
    _globalLikeStates[adId] = isLiked;
    for (var controller in listener) {
      controller.update();
    }
  }

  static bool getGlobalLikeState(String adId, bool? fallback) {
    return _globalLikeStates[adId] ?? fallback ?? false;
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

  Future<void> toggleMostLike(int index, String adId) async {

    HapticFeedback.lightImpact();
    if (index < 0 || index >= favouriteAds.length) return;

    final ad = favouriteAds[index];
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


  static final Map<String, bool> _globalLikeStates = {};
  static final Set<GetxController> listener = {};

  ///fetch fav ads

  /// Fetch most like data api
  Future<void> fetchFavouriteAds({String? search}) async {
    isLoading = true;
    update([Constant.idAllAds]);

    likeResponseModel = await MostLikedProductApi.fetchMostLikedAds(
      isRefresh: true,
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      // search: search,
      // start: startPagination,
      // limit: limitPagination,
    );

    favouriteAds.clear();
    favouriteAds.addAll(likeResponseModel?.data ?? []);

    Utils.showLog("get category wise product list data $favouriteAds");

    isLoading = false;
    update([Constant.idAllAds]);
  }

  ///
bool homeLocation= true;


  ///sub category api
  SubCategoryResponseModel? subCategoryResponseModel;
  List<Datum> subCategoryList = [];

  Future<bool> getSubCategoryApi(String categoryId) async {
    isLoading = true;
    update();

    subCategoryResponseModel = await SubCategoryApi.callApi(parentId: categoryId);
    subCategoryList.clear();
    subCategoryList.addAll(subCategoryResponseModel?.data ?? []);

    Utils.showLog("sub category list data: ${subCategoryList.map((e) => e.name).toList()}");

    isLoading = false;
    update();
    update(['appbar']); // For AppBar title update

    return subCategoryList.isEmpty;
  }
}
