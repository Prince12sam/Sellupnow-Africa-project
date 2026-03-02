import 'package:get/get.dart';
import 'package:listify/ui/home_screen/controller/home_screen_controller.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';

class HomeScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<HomeScreenController>(() => HomeScreenController());
    Get.lazyPut<MapController>(() => MapController());
  }
}
