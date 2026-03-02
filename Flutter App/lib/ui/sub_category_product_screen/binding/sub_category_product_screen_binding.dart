import 'package:get/get.dart';
import 'package:listify/ui/sub_categories_screen/service/select_bus_service.dart';
import 'package:listify/ui/sub_category_product_screen/controller/product_filter_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/controller/sub_category_product_screen_controller.dart';

class SubCategoryProductScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SubCategoryProductScreenController>(() => SubCategoryProductScreenController(), fenix: true);
    Get.lazyPut<ProductFilterScreenController>(() => ProductFilterScreenController(), fenix: true);
    Get.lazyPut<SelectionBus>(() => SelectionBus());
  }
}
