import 'package:get/get.dart';
import 'package:listify/ui/banner_ad_screen/controller/banner_ad_screen_controller.dart';

class BannerAdScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<BannerAdScreenController>(() => BannerAdScreenController());
  }
}
