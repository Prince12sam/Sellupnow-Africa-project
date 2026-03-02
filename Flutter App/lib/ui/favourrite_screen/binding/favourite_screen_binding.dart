import 'package:get/get.dart';
import 'package:listify/ui/favourrite_screen/controller/favourite_screen_controller.dart';

class FavoriteScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<FavoriteScreenController>(() => FavoriteScreenController());
  }
}
