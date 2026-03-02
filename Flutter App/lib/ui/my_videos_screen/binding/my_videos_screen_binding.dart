import 'package:get/get.dart';
import 'package:listify/ui/my_videos_screen/controller/my_videos_screen_controller.dart';

class MyVideosScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<MyVideosScreenController>(() => MyVideosScreenController());
  }
}
