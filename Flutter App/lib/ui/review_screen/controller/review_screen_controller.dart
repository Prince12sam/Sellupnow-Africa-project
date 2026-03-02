// import 'package:get/get.dart';
// import 'package:intl/intl.dart';
// import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
// import 'package:listify/ui/review_screen/api/get_review_api.dart';
// import 'package:listify/ui/review_screen/api/user_product_api.dart';
// import 'package:listify/ui/review_screen/model/get_review_response_model.dart';
// import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/database.dart';
// import 'package:listify/utils/utils.dart';
//
// class ReviewScreenController extends GetxController {
//   var rating = 0.0.obs;
//   final Map<String, dynamic> arguments = Get.arguments ?? {};
//   List<ReceivedReview> reviews = [];
//   String? name;
//   String? image;
//   String? register;
//   bool isReview = false;
//   List<AllAds> favouriteAds = [];
//
//   @override
//   void onInit() {
//     getReview();
//     init();
//     super.onInit();
//   }
//
//   ///get arguments
//
//   init() {
//     final registeredAt = Database.getUserProfileResponseModel?.user?.registeredAt;
//
//     register = formatRegisterDate(registeredAt);
//
//     fetchUserAds();
//
//     Utils.showLog("name>>>>>>>>>>>>>>>>>>>>>$name");
//     Utils.showLog("image>>>>>>>>>>>>>>>>>>>>>$image");
//     Utils.showLog("register>>>>>>>>>>>>>>>>>>>>>$register");
//     Utils.showLog("register>>>>>>>>>>>>>>>>>>>>>${Database.getUserProfileResponseModel?.user?.registeredAt}");
//   }
//
//   ///date formate
//
//   String formatRegisterDate(String? dateString) {
//     if (dateString == null || dateString.isEmpty) return "";
//
//     try {
//       // Parse the incoming string "8/6/2025, 10:12:53 AM"
//       final parsedDate = DateFormat("M/d/yyyy, h:mm:ss a").parse(dateString);
//
//       // Format as "March 2025"
//       return DateFormat("MMMM yyyy").format(parsedDate);
//     } catch (e) {
//       Utils.showLog("Date parsing error: $e");
//       return "";
//     }
//   }
//
//   /// API call
//   Future<void> getReview() async {
//     isReview = true;
//     update([Constant.review]);
//     var reviewRes = await ReviewApi.getReviews(
//       userId: Database.getUserProfileResponseModel?.user?.id ?? "",
//       start: 1,
//       limit: 20,
//       uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
//     );
//
//     if (reviewRes != null && reviewRes.status == true) {
//       // 🔹 Save data into list
//       reviews = reviewRes.receivedReviews ?? [];
//
//       // Debug Utils.showLog
//       for (var review in reviews) {
//         Utils.showLog("Reviewer: ${review.reviewer?.name}");
//         Utils.showLog("Rating: ${review.rating}");
//         Utils.showLog("Review: ${review.reviewText}");
//       }
//     } else {
//       reviews = [];
//     }
//     isReview = false;
//     update([Constant.review]);
//   }
//
//   /// format review time (only hh:mm a)
//   String formatReviewTime(String? isoString) {
//     if (isoString == null || isoString.isEmpty) return "";
//
//     try {
//       final dateTime = DateTime.parse(isoString).toLocal(); // convert UTC → local
//       return DateFormat("hh:mm a").format(dateTime); // e.g. 10:24 AM
//     } catch (e) {
//       Utils.showLog("Review time parsing error: $e");
//       return "";
//     }
//   }
//
//   ///get all ads user
//
//   bool isLoading = false;
//   List<AllAds> userAllAds = [];
//
//   int start = 1;
//   int limit = 20;
//
//   /// 📍 Fetch User Ads
//   Future<void> fetchUserAds() async {
//     try {
//       isLoading = true;
//       update([Constant.idUserAds]);
//
//       final response = await UserProductApi.getAllAdsOfUser(
//         loginUserId: Database.getUserProfileResponseModel?.user?.id ?? "",
//         start: start,
//         limit: limit,
//         uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
//       );
//
//       if (response != null && response.status == true) {
//         userAllAds.clear();
//         userAllAds.addAll(response.data);
//       }
//     } finally {
//       isLoading = false;
//       update([Constant.idUserAds]);
//     }
//   }
//
//   /// like/unlike api call
//   final Map<String, bool> _likedOverrides = {};
//   bool isAdLiked(AllAds ad) => _likedOverrides[ad.id] ?? ad.isLike ?? false;
//
//   Future<void> toggleLike(int index, String adId) async {
//     if (index < 0 || index >= favouriteAds.length || adId.isEmpty) return;
//
//     final ad = favouriteAds[index];
//     final current = isAdLiked(ad);
//
//     // 1) Optimistic UI
//     _likedOverrides[adId] = !current;
//     update([Constant.idAllAds]);
//
//     try {
//       // 2) API call
//       final resp = await AddLikeApi.callApi(
//         adId: adId,
//         uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
//       );
//
//       final serverIsLiked = resp?.like; // adjust per your DTO
//       if (serverIsLiked != null) {
//         _likedOverrides[adId] = serverIsLiked;
//         update([Constant.idAllAds]);
//       }
//     } catch (e) {
//       // 3) Revert on failure
//       _likedOverrides[adId] = current;
//       update([Constant.idAllAds]);
//       if (Get.context != null) {
//         Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
//       }
//     }
//   }
// }

/*
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/review_screen/api/get_review_api.dart';
import 'package:listify/ui/review_screen/api/user_product_api.dart';
import 'package:listify/ui/review_screen/model/get_review_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';
import 'package:listify/ui/home_screen/controller/home_screen_controller.dart'; // Import this

class ReviewScreenController extends GetxController {
  var rating = 0.0.obs;
  final Map<String, dynamic> arguments = Get.arguments ?? {};
  List<ReceivedReview> reviews = [];
  String? name;
  String? image;
  String? register;
  bool isReview = false;
  List<AllAds> favouriteAds = [];

  @override
  void onInit() {
    getReview();
    init();
    // Register this controller as a listener to HomeScreenController
    HomeScreenController.listener.add(this);
    super.onInit();
  }

  @override
  void onClose() {
    // Unregister when disposing
    HomeScreenController.listener.remove(this);
    super.onClose();
  }

  ///get arguments
  init() {
    final registeredAt = Database.getUserProfileResponseModel?.user?.registeredAt;
    register = formatRegisterDate(registeredAt);
    fetchUserAds();

    Utils.showLog("name>>>>>>>>>>>>>>>>>>>>>$name");
    Utils.showLog("image>>>>>>>>>>>>>>>>>>>>>$image");
    Utils.showLog("register>>>>>>>>>>>>>>>>>>>>>$register");
    Utils.showLog("register>>>>>>>>>>>>>>>>>>>>>${Database.getUserProfileResponseModel?.user?.registeredAt}");
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

  /// API call
  Future<void> getReview() async {
    isReview = true;
    update([Constant.review]);
    var reviewRes = await ReviewApi.getReviews(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      start: 1,
      limit: 20,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );

    if (reviewRes != null && reviewRes.status == true) {
      // 🔹 Save data into list
      reviews = reviewRes.receivedReviews ?? [];

      // Debug Utils.showLog
      for (var review in reviews) {
        Utils.showLog("Reviewer: ${review.reviewer?.name}");
        Utils.showLog("Rating: ${review.rating}");
        Utils.showLog("Review: ${review.reviewText}");
      }
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
      update([Constant.idUserAds, Constant.idAllAds]);

      final response = await UserProductApi.getAllAdsOfUser(
        loginUserId: Database.getUserProfileResponseModel?.user?.id ?? "",
        start: start,
        limit: limit,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (response != null && response.status == true) {
        userAllAds.clear();
        userAllAds.addAll(response.data);
      }
    } finally {
      isLoading = false;
      update([Constant.idUserAds, Constant.idAllAds]);
    }
  }

  /// 🔹 NEW: Use centralized like state from HomeScreenController
  bool isAdLiked(AllAds ad) {
    return HomeScreenController.getGlobalLikeState(ad.id ?? "", ad.isLike);
  }

  /// 🔹 UPDATED: Like/unlike with centralized state management
  Future<void> toggleLike(int index, String adId) async {
    if (index < 0 || index >= userAllAds.length || adId.isEmpty) return;

    final ad = userAllAds[index];
    final currentState = isAdLiked(ad);
    final newState = !currentState;

    // 1) Update global state immediately (optimistic UI)
    HomeScreenController.updateGlobalLikeState(adId, newState);

    try {
      // 2) API call
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
      // 3) Revert on error
      HomeScreenController.updateGlobalLikeState(adId, currentState);
      if (Get.context != null) {
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
      Utils.showLog("Add like failed: $e");
    }
  }

  /// 🔹 REMOVED: Local _likedOverrides map (no longer needed)
// We're now using HomeScreenController's centralized state management
}*/
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/review_screen/api/get_review_api.dart';
import 'package:listify/ui/review_screen/api/user_product_api.dart';
import 'package:listify/ui/review_screen/model/get_review_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';
import 'package:listify/ui/home_screen/controller/home_screen_controller.dart';

class ReviewScreenController extends GetxController {
  var rating = 0.0.obs;
  final Map<String, dynamic> arguments = Get.arguments ?? {};
  List<ReceivedReview> reviews = [];
  String? name;
  String? image;
  String? register;
  bool isReview = false;
  List<AllAds> favouriteAds = [];

  bool isLoading = false;
  List<AllAds> userAllAds = [];

  int start = 1;
  int limit = 20;

  @override
  void onInit() {
    getReview();
    init();
    HomeScreenController.listener.add(this);
    super.onInit();
  }

  @override
  void onClose() {
    HomeScreenController.listener.remove(this);
    super.onClose();
  }

  init() {
    final registeredAt = Database.getUserProfileResponseModel?.user?.registeredAt;
    register = formatRegisterDate(registeredAt);
    fetchUserAds();

    Utils.showLog("name>>>>>>>>>>>>>>>>>>>>>$name");
    Utils.showLog("image>>>>>>>>>>>>>>>>>>>>>$image");
    Utils.showLog("register>>>>>>>>>>>>>>>>>>>>>$register");
    Utils.showLog("register>>>>>>>>>>>>>>>>>>>>>${Database.getUserProfileResponseModel?.user?.registeredAt}");
  }

  String formatRegisterDate(String? dateString) {
    if (dateString == null || dateString.isEmpty) return "";
    try {
      final parsedDate = DateFormat("M/d/yyyy, h:mm:ss a").parse(dateString);
      return DateFormat("MMMM yyyy").format(parsedDate);
    } catch (e) {
      Utils.showLog("Date parsing error: $e");
      return "";
    }
  }

  Future<void> getReview() async {
    isReview = true;
    update([Constant.review]);
    var reviewRes = await ReviewApi.getReviews(
      userId: Database.getUserProfileResponseModel?.user?.id ?? "",
      start: 1,
      limit: 20,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
    );

    if (reviewRes != null && reviewRes.status == true) {
      reviews = (reviewRes?.status == true) ? (reviewRes?.receivedReviews ?? []) : [];
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

  String formatReviewTime(String? isoString) {
    if (isoString == null || isoString.isEmpty) return "";
    try {
      final dateTime = DateTime.parse(isoString).toLocal();
      return DateFormat("hh:mm a").format(dateTime);
    } catch (e) {
      Utils.showLog("Review time parsing error: $e");
      return "";
    }
  }

  /// Load ads
  Future<void> fetchUserAds() async {
    try {
      isLoading = true;
      update([Constant.idUserAds, Constant.idAllAds]);

      final response = await UserProductApi.callApi(
        loginUserId: Database.getUserProfileResponseModel?.user?.id ?? "",
        start: start,
        limit: limit,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      userAllAds.clear();
      if (response != null && response.status == true) {
        userAllAds.addAll(response.data);
      }
    } finally {
      isLoading = false;
      update([Constant.idUserAds, Constant.idAllAds]);
    }
  }

  /// Pull-to-refresh helper
  Future<void> refreshUserAds() async {
    start = 1;
    await fetchUserAds();
  }

  /// Centralized like state
  bool isAdLiked(AllAds ad) {
    return LikeManager.to.getLikeState(ad.id ?? "", fallback: ad.isLike);
  }

  /// Toggle like with optimistic UI + proper rebuilds
  Future<void> toggleMostLike(int index, String adId) async {

    HapticFeedback.lightImpact();
    if (index < 0 || index >= userAllAds.length) return;

    final ad = userAllAds[index];
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
  Map<int, int>? ratingBreakdown;




  ///
  int get totalReviewsFromList => reviews.length;

  double get avgFromReviews =>
      reviews.isEmpty ? 0.0
          : reviews.fold<double>(0.0, (a, r) => a + ((r.rating ?? 0) as num).toDouble()) / reviews.length;

  double get avgForUI {
    final backendAvg = (Database.getUserProfileResponseModel?.user?.averageRating ?? 0).toDouble();
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
