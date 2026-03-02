import 'dart:developer';

import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/api/specific_product_view_api.dart';
import 'package:listify/ui/product_detail_screen/model/specific_product_view_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class SpecificProductViewShowController extends GetxController {
  final Map<String, dynamic> arguments = Get.arguments ?? {};
  String? adId;
  bool isLoading = false;
  List<AdView> viewList = [];

  @override
  void onInit() {
    init();
    super.onInit();
  }

  init() {
    log("arguments///////////////${arguments}");
    adId = arguments['adId'];
    log("adId///////////////${adId}");
    specificProductView(adId ?? "");
  }

  Future<void> specificProductView(String adId) async {
    try {
      isLoading = true;
      update([Constant.productView]); // UI update karva mate ID change

      final viewResponse = await SpecificProductViewApi.getViewsForAd(
        adId: adId,
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      );

      if (viewResponse != null) {
        Utils.showLog("Total Views => ${viewResponse.adView}");

        /// list ma save karo
        viewList = viewResponse.adView ?? [];

        /// Debug
        for (var view in viewList) {
          Utils.showLog("User: ${view.user?.name}, Profile: ${view.user?.profileImage}");
        }
      }
    } catch (e) {
      Utils.showLog("specificProductView Error => $e");
    } finally {
      isLoading = false;
      update([Constant.productView]); // again update after loading done
    }
  }
}
