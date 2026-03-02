import 'package:get/get.dart';
import 'package:listify/ui/faq_screen/controller/faq_screen_controller.dart';

class FaqScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<FaqScreenController>(() => FaqScreenController());
  }
}
