import 'package:get/get.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/ui/near_by_listing_screen/controller/near_by_listing_screen_controller.dart';

class NearByListingScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<NearByListingScreenController>(() => NearByListingScreenController());
    Get.lazyPut<MapController>(() => MapController());
  }
}
