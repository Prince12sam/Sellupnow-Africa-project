import 'package:get/get.dart';
import 'package:listify/ui/fashion_blog_screen/controller/fashion_blog_screen_controller.dart';

class FashionBlogScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<FashionBlogScreenController>(() => FashionBlogScreenController());
  }
}
