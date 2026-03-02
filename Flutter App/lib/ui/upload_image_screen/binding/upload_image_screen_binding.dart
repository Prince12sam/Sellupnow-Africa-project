import 'package:get/get.dart';
import 'package:listify/ui/upload_image_screen/controller/upload_image_screen_controller.dart';

class UploadImageScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<UploadImageScreenController>(() => UploadImageScreenController());
  }
}
