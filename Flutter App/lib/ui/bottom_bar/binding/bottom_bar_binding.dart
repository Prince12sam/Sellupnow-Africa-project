import 'package:get/get.dart';
import 'package:listify/ui/add_listing_screen/controller/add_listing_screen_controller.dart';
import 'package:listify/ui/bottom_bar/controller/bottom_bar_controller.dart';
import 'package:listify/ui/home_screen/controller/home_screen_controller.dart';
import 'package:listify/ui/message_screen/controller/message_screen_controller.dart';
import 'package:listify/ui/my_ads_screen/controller/my_ads_screen_controller.dart';

class BottomBarBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<BottomBarController>(() => BottomBarController());
    Get.lazyPut<HomeScreenController>(() => HomeScreenController(), fenix: true);
    Get.lazyPut<MyAdsScreenController>(() => MyAdsScreenController(), fenix: true);
    Get.lazyPut<AddListingScreenController>(() => AddListingScreenController(), fenix: true);
    Get.lazyPut<MessageScreenController>(() => MessageScreenController(), fenix: true);
    Get.lazyPut<MessageScreenController>(() => MessageScreenController(), fenix: true);
  }
}
