import 'package:get/get.dart';
import 'package:listify/ui/popular_poduct_screen/controller/popular_product_screen_controller.dart';

class PopularProductScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<PopularProductScreenController>(() => PopularProductScreenController());
  }
}
