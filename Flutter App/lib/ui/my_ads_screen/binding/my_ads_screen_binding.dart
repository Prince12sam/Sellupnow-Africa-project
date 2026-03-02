import 'package:get/get.dart';
import 'package:listify/ui/my_ads_screen/controller/my_ads_screen_controller.dart';

class MyAdsScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<MyAdsScreenController>(() => MyAdsScreenController(), fenix: true);
  }
}
