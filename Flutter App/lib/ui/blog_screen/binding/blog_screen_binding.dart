import 'package:get/get.dart';
import 'package:listify/ui/blog_screen/controller/blog_screen_controller.dart';

class BlogScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<BlogScreenController>(() => BlogScreenController());
  }
}
