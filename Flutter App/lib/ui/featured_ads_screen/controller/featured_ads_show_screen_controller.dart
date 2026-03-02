import 'package:get/get.dart';
import 'package:listify/ui/featured_ads_screen/api/ads_promoted_api.dart';
import 'package:listify/ui/upload_video_screen/api/seller_ad_listing_upload_video.dart';
import 'package:listify/ui/upload_video_screen/model/seller_product_info_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';
//
// class FeaturedAdsShowScreenController extends GetxController {
//   List<SellerProductInfo> allAdsList = [];
//   SellerProductInfoModel? sellerProductInfoModel;
//   bool isLoading = false;
//   Map<String, dynamic> arguments = Get.arguments ?? {};
//
//   num? plan ;
//
//   @override
//   void onInit() {
//     sellerProductInfo();
//     plan = arguments['plan'];
//     Utils.showLog("plan:::::::::::::::$plan");
//     super.onInit();
//   }
//
//   /// seller product info
//   Future<void> sellerProductInfo() async {
//     isLoading = true;
//     update([Constant.idAllAds]);
//
//     sellerProductInfoModel = await SellerAdListingUploadVideo.callApi();
//     allAdsList
//       ..clear()
//       ..addAll(sellerProductInfoModel?.data ?? []);
//
//     isLoading = false;
//     update([Constant.idAllAds]);
//   }
//
//   // List<AdModel> allAdsList = []; // tamaru API thi aave chhe
//   List<String> selectedIds = []; // selected product ids store thase
//
//   void toggleSelection(String id) {
//     if (selectedIds.contains(id)) {
//       // already selected -> remove
//       selectedIds.remove(id);
//     } else {
//       // limit check
//       if (plan != null && selectedIds.length >= plan!) {
//         Utils.showToast(Get.context!, "You can select only $plan ads.");
//         return; // more select નહિ થાય
//       }
//       selectedIds.add(id);
//     }
//     update([Constant.idAllAds]); // UI refresh
//   }
//
//
//   bool isSelected(String id) {
//     return selectedIds.contains(id);
//   }
//
//   promoteAdsApi() async {
//     final response = await AdsPromotedApi.callApi(
//       adIds: selectedIds,
//     );
//
//     if (response != null) {
//       if (response.status == true) {
//         Utils.showToast(Get.context!, response.message ?? "Success");
//
//         Get.back();
//         Get.back();
//         // Get.close(3);
//       } else {
//         Utils.showToast(Get.context!, response.message ?? "Something went wrong");
//       }
//     }
//   }
//
//   @override
//   void dispose() {
//     selectedIds = [];
//     super.dispose();
//   }
// }
class FeaturedAdsShowScreenController extends GetxController {
  List<SellerProductInfo> allAdsList = [];
  SellerProductInfoModel? sellerProductInfoModel;
  bool isLoading = false;
  Map<String, dynamic> arguments = Get.arguments ?? {};

  num? plan ;

  @override
  void onInit() {
    sellerProductInfo();
    plan = arguments['plan'];
    Utils.showLog("plan:::::::::::::::$plan");
    super.onInit();
  }

  /// seller product info
  Future<void> sellerProductInfo() async {
    isLoading = true;
    update([Constant.idAllAds]);

    sellerProductInfoModel = await SellerAdListingUploadVideo.callApi();
    allAdsList
      ..clear()
      ..addAll(sellerProductInfoModel?.data ?? []);

    isLoading = false;
    update([Constant.idAllAds]);
  }

  // Selected product ids
  List<String> selectedIds = [];

  void toggleSelection(String id) {
    if (selectedIds.contains(id)) {
      selectedIds.remove(id);
    } else {
      if (plan != null && selectedIds.length >= plan!) {
        Utils.showToast(Get.context!, "You can select only $plan ads.");
        return;
      }
      selectedIds.add(id);
    }
    update([Constant.idAllAds]);
  }

  bool isSelected(String id) {
    return selectedIds.contains(id);
  }

  /// ---- NEW: Selected products total price ----
  double get selectedTotal {
    double sum = 0.0;
    for (final p in allAdsList) {
      if (selectedIds.contains(p.id.toString())) {
        sum += _toDouble(p.price);
      }
    }
    return sum;
  }

  String get selectedTotalString => selectedTotal.toStringAsFixed(2);

  double _toDouble(dynamic v) {
    if (v is num) return v.toDouble();
    if (v is String) return double.tryParse(v) ?? 0.0;
    return 0.0;
  }
  /// -------------------------------------------

  promoteAdsApi() async {
    final response = await AdsPromotedApi.callApi(
      adIds: selectedIds,
    );

    if (response != null) {
      if (response.status == true) {
        Utils.showToast(Get.context!, response.message ?? "Success");
        Get.back();
        Get.back();
      } else {
        Utils.showToast(Get.context!, response.message ?? "Something went wrong");
      }
    }
  }

  @override
  void dispose() {
    selectedIds = [];
    super.dispose();
  }
}
