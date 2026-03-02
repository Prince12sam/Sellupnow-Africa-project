import 'package:get/get.dart';
import 'package:listify/ui/featured_ads_screen/controller/featured_ads_show_screen_controller.dart';

class FeaturedAdsShowScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<FeaturedAdsShowScreenController>(() => FeaturedAdsShowScreenController());
  }
}
