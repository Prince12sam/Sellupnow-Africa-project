import 'package:get/get.dart';
import 'package:listify/ui/home_search_product_screen/controller/home_search_product_controller.dart';

class HomeSearchProductBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<HomeSearchProductController>(() => HomeSearchProductController());
  }
}
