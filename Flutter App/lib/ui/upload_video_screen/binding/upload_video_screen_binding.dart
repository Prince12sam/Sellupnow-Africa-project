import 'package:get/get.dart';
import 'package:listify/ui/upload_video_screen/controller/upload_video_screen_controller.dart';

class UploadVideoScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<UploadVideoScreenController>(() => UploadVideoScreenController());
  }
}
