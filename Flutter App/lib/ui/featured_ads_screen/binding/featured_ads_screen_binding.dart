import 'package:get/get.dart';
import 'package:listify/ui/featured_ads_screen/controller/featured_ads_screen_controller.dart';

class FeaturedAdsScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<FeaturedAdsScreenController>(() => FeaturedAdsScreenController());
  }
}
