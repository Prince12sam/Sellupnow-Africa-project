import 'dart:developer';

import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/api/specific_product_like_get_api.dart';
import 'package:listify/ui/product_detail_screen/model/specific_product_like_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class SpecificProductLikeShowController extends GetxController {
  final Map<String, dynamic> arguments = Get.arguments ?? {};
  String? adId;
  bool isLoading = false;
  List<Like> likeList = [];

  @override
  void onInit() {
    init();
    super.onInit();
  }

  init() {
    log("arguments///////////////${arguments}");
    adId = arguments['adId'];
    log("adId///////////////${adId}");
    specificProductLike(adId ?? "");
  }

  ///specific product like api
  Future<void> specificProductLike(String adId) async {
    try {
      isLoading = true;
      update([Constant.productLike]);

      final likesResponse = await SpecificProductLikeApi.getLikesForAd(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (likesResponse != null) {
        Utils.showLog("Total Likes => ${likesResponse.total}");

        /// list ma save karo
        likeList = likesResponse.likes ?? [];

        /// Debug
        for (var like in likeList) {
          Utils.showLog("User: ${like.user?.name}, Profile: ${like.user?.profileImage}");
        }
      }
    } catch (e) {
      Utils.showLog("specificProductLike Error => $e");
    } finally {
      isLoading = false;
      update([Constant.productLike]);
    }
  }
}
