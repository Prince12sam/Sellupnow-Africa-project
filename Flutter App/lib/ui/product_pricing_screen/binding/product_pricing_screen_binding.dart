import 'package:get/get.dart';
import 'package:listify/ui/product_pricing_screen/controller/product_pricing_screen_controller.dart';

class ProductPricingScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<ProductPricingScreenController>(() => ProductPricingScreenController());
  }
}
