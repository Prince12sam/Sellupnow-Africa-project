import 'package:get/get.dart';
import 'package:listify/ui/add_listing_screen/controller/add_listing_screen_controller.dart';

class AddListingScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<AddListingScreenController>(() => AddListingScreenController());
  }
}
