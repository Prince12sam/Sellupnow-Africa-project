import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';

class ProductDetailScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<ProductDetailScreenController>(() => ProductDetailScreenController());
  }
}
