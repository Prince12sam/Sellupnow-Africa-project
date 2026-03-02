import 'package:get/get.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/sub_category_product_screen/api/add_like_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class SellerDetailProductAllViewController extends GetxController {
  final Map<String, dynamic> arguments = Get.arguments ?? {};

  List<AllAds> adProduct = [];

  @override
  void onInit() {
    init();
    super.onInit();
  }

  init() {
    adProduct = arguments['adProduct'];
    Utils.showLog("adProduct::::::::::::::::::::$adProduct");
  }

  /// Local UI overrides for like state, keyed by ad id
  final Map<String, bool> _likedOverrides = {};

  bool isAdLiked(AllAds ad) {
    return _likedOverrides[ad.id] ?? ad.isLike ?? false;
  }

  ///like unlike api
  Future<void> toggleLike(int index, String adId) async {
    if (index < 0 || index >= adProduct.length || adId.isEmpty) return;

    final ad = adProduct[index];
    final current = isAdLiked(ad);

    // Optimistic update
    _likedOverrides[adId] = !current;
    update([Constant.idUserAds]); // ✅ Changed to match UI id

    try {
      final resp = await AddLikeApi.callApi(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      final serverIsLiked = resp?.like;
      if (serverIsLiked != null) {
        _likedOverrides[adId] = serverIsLiked;
        update([Constant.idUserAds]); // ✅ Changed to match UI id
      }
    } catch (e) {
      // Revert if API fails
      _likedOverrides[adId] = current;
      update([Constant.idUserAds]); // ✅ Changed to match UI id
      if (Get.context != null) {
        Utils.showToast(Get.context!, "Couldn't update like. Please try again.");
      }
    }
  }
}
