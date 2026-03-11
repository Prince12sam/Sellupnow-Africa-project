import 'package:get/get.dart';
import 'package:listify/ui/videos_screen/controller/videos_screen_controller.dart';

class VideosScreenBinding extends Bindings {
  @override
  void dependencies() {
    if (!Get.isRegistered<VideosScreenController>()) {
      Get.lazyPut<VideosScreenController>(() => VideosScreenController());
    }
  }
}
