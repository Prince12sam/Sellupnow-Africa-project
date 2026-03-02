import 'package:get/get.dart';
import 'package:listify/ui/location_screen/controller/location_screen_controller.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';

class LocationScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<LocationScreenController>(() => LocationScreenController());
    Get.lazyPut<MapController>(() => MapController());
  }
}
