import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart' hide Seller;
import 'package:listify/ui/review_screen/api/get_review_api.dart';
import 'package:listify/ui/review_screen/model/get_review_response_model.dart';
import 'package:listify/ui/seller_detail_screen/api/seller_get_ads_api.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

class SellerDetailScreenController extends GetxController {
  String? name;
  String? image;
  String? register;
  String? userId;
  Seller? user;
  final Map<String, dynamic> arguments = Get.arguments ?? {};
  var rating = 0.0.obs;
  bool isReview = false;
  List<ReceivedReview> reviews = [];
  List<AllAds> favouriteAds = [];

  @override
  void onInit() {
    init();
    super.onInit();
  }

  ///get arguments

  init() {
    name = arguments['name'];
    image = arguments['image'];
    register = arguments['register'];
    userId = arguments['userId'];
    // user = arguments['user'];

    if (register != null) {
      register = formatRegisterDate(register);
    }
    getReview();
    fetchUserAds();

    Utils.showLog("name>>>>>>>>>>>>>>>>>>>>>$name");
    Utils.showLog("image>>>>>>>>>>>>>>>>>>>>>$image");
    Utils.showLog("register>>>>>>>>>>>>>>>>>>>>>$register");
    Utils.showLog("userId>>>>>>>>>>>>>>>>>>>>>$userId");
    Utils.showLog("user>>>>>>>>>>>>>>>>>>>>>$user");
  }

  ///date formate

  String formatRegisterDate(String? dateString) {
    if (dateString == null || dateString.isEmpty) return "";

    try {
      // Parse the incoming string "8/6/2025, 10:12:53 AM"
      final parsedDate = DateFormat("M/d/yyyy, h:mm:ss a").parse(dateString);

      // Format as "March 2025"
      return DateFormat("MMMM yyyy").format(parsedDate);
    } catch (e) {
      Utils.showLog("Date parsing error: $e");
      return "";
    }
  }

  ///get  review api

  // getReview() async {
  //   var reviewRes = await ReviewApi.getReviews(
  //     userId: userId ?? "",
  //     start: 1,
  //     limit: 20,
  //     uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
  //   );
  //
  //   if (reviewRes != null && reviewRes.status == true) {
  //     for (var review in reviewRes.receivedReviews ?? []) {
  //       Utils.showLog("Reviewer: ${review.reviewer?.name}");
  //       Utils.showLog("Rating: ${review.rating}");
  //       Utils.showLog("Review: ${review.reviewText}");
  //     }
  //   }
  // }

  Future<void> getReview() async {
    isReview = true;
    update([Constant.review]);
    var reviewRes = await ReviewApi.getReviews(
      userId: userId ?? "",
      start: 1,
      limit: 20,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
    );

    if (reviewRes != null && reviewRes.status == true) {
      // 🔹 Save data into list
      reviews = (reviewRes?.status == true) ? (reviewRes?.receivedReviews ?? []) : [];

      // Debug Utils.showLog
      for (var review in reviews) {
        Utils.showLog("Reviewer: ${review.reviewer?.name}");
        Utils.showLog("Rating: ${review.rating}");
        Utils.showLog("Review: ${review.reviewText}");
      }
      _rebuildBuckets();
      isReview = false;
      update([Constant.review]);
    } else {
      reviews = [];
    }
    isReview = false;
    update([Constant.review]);
  }

  /// format review time (only hh:mm a)
  String formatReviewTime(String? isoString) {
    if (isoString == null || isoString.isEmpty) return "";

    try {
      final dateTime = DateTime.parse(isoString).toLocal(); // convert UTC → local
      return DateFormat("hh:mm a").format(dateTime); // e.g. 10:24 AM
    } catch (e) {
      Utils.showLog("Review time parsing error: $e");
      return "";
    }
  }

  ///get all ads user

  bool isLoading = false;
  List<AllAds> userAllAds = [];

  int start = 1;
  int limit = 20;

  /// 📍 Fetch User Ads
  Future<void> fetchUserAds() async {
    try {
      isLoading = true;
      update([Constant.idAllAds]);

      final response = await SellerGetAdsApi.getAllSellerAds(
        sellerId: userId ?? "",
        start: start,
        limit: limit,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
        loginUserId: Database.getUserProfileResponseModel?.user?.id ?? Database.loginUserId,
      );

      if (response != null && response.status == true) {
        userAllAds.clear();
        userAllAds.addAll(response.data);
      }
    } finally {
      isLoading = false;
      update([Constant.idAllAds]);
    }
  }

  /// like/unlike api call
  final Map<String, bool> _likedOverrides = {};
  // bool isAdLiked(AllAds ad) => _likedOverrides[ad.id] ?? ad.isLike ?? false;

  // Future<void> toggleLike(int index, String adId) async {
  //   if (index < 0 || index >= favouriteAds.length || adId.isEmpty) return;
  //
  //   final ad = favouriteAds[index];
  //   final current = isAdLiked(ad);
  //
  //   // 1) Optimistic UI
  //   _likedOverrides[adId] = !current;
  //   update([Constant.idAllAds]);
  //
  //   try {
  //     // 2) API call
  //     final resp = await AddLikeApi.callApi(
  //       adId: adId,
  //       uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
  //     );
  //
  //     final serverIsLiked = resp?.like; // adjust per your DTO
  //     if (serverIsLiked != null) {
  //       _likedOverrides[adId] = serverIsLiked;
  //       update([Constant.idAllAds]);
  //     }
  //   } catch (e) {
  //     // 3) Revert on failure
  //     _likedOverrides[adId] = current;
  //     update([Constant.idAllAds]);
  //     if (Get.context != null) {
  //       Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
  //     }
  //   }
  // }

 /* Future<void> toggleLike(int index, String adId) async {
    if (index < 0 || index >= userAllAds.length || adId.isEmpty) return;

    final ad = userAllAds[index]; // ✅ correct list
    final current = isAdLiked(ad);

    // 1) Optimistic UI update
    _likedOverrides[adId] = !current;
    update([Constant.idUserAds]); // ✅ same ID use karo je builder ma use thay che

    try {
      // 2) API call
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      );

      final serverIsLiked = resp?.like;
      if (serverIsLiked != null) {
        _likedOverrides[adId] = serverIsLiked;
        update([Constant.idUserAds]);
      }
    } catch (e) {
      // 3) Revert on failure
      _likedOverrides[adId] = current;
      update([Constant.idUserAds]);
      if (Get.context != null) {
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
    }
  }*/

  bool isAdLiked(AllAds ad) {
    return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
  }

  Future<void> toggleLike(int index, String adId) async {
    if (index < 0 || index >= userAllAds.length) return;

    final ad = userAllAds[index];
    final currentState = isAdLiked(ad);
    final newState = !currentState;

    LikeManager.to.updateLikeState(adId, newState);
    ad.isLike = newState;
    update([Constant.idAllAds, Constant.idUserAds, Constant.idPopularAds]);

    try {
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
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

// in ReviewScreenController

  int get totalReviewsFromList => reviews.length;

  double get avgFromReviews =>
      reviews.isEmpty ? 0.0
          : reviews.fold<double>(0.0, (a, r) => a + ((r.rating ?? 0) as num).toDouble()) / reviews.length;

  double get avgForUI {
    final backendAvg = (user?.averageRating ?? 0).toDouble();
    return reviews.isNotEmpty ? avgFromReviews : backendAvg;
  }

  final Map<int, List<ReceivedReview>> reviewsByStar = {
    1: [], 2: [], 3: [], 4: [], 5: [],
  };

  void _rebuildBuckets() {
    for (final k in reviewsByStar.keys) {
      reviewsByStar[k] = [];
    }
    for (final r in reviews) {
      final raw = (r.rating ?? 0).toDouble();
      final s = raw.round().clamp(1, 5); // round to closest star
      reviewsByStar[s]!.add(r);
    }
  }

  int totalReviewsFromList1() => reviews.length;

  int countForStar(int star) => reviewsByStar[star]?.length ?? 0;

  double percentForStar(int star) {
    final total = totalReviewsFromList1();
    if (total == 0) return 0.0;
    return countForStar(star) / total;
  }


// controller માં (API response થી totalRatings મૂકી દો)
  int totalRatingsFromApi = Database.getUserProfileResponseModel?.user?.totalRating ?? 0;

  double percentForStarUsingApiTotal(int star) {
    final total = totalRatingsFromApi;
    if (total <= 0) return 0.0;
    return countForStar(star) / total; // NOTE: counts તમે list માંથી લઈ રહ્યા છો → આ ઓછું દેખાશે
  }
// α તમે tune કરી શકો: 0.5, 1.0… (મોટું α = વધારે સ્મૂધ, bars “શાંત” દેખાશે)
  double smoothedPercentForStar(int star, {double alpha = 1.0}) {
    final total = reviews.length;
    final count = countForStar(star);
    final denom = total + 5 * alpha;
    if (denom == 0) return 0.0;
    return (count + alpha) / denom;
  }

}
