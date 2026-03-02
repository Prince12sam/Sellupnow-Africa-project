import 'package:get/get.dart';
import 'package:listify/ui/review_screen/controller/review_screen_controller.dart';

class ReviewScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<ReviewScreenController>(() => ReviewScreenController());
  }
}
