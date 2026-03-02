import 'package:get/get.dart';
import 'package:listify/ui/confirm_location/controller/confirm_location_controller.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';

class ConfirmLocationBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<ConfirmLocationScreenController>(() => ConfirmLocationScreenController());
    Get.lazyPut<MapController>(() => MapController());
  }
}
