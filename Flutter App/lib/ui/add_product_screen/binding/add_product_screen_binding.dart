import 'package:get/get.dart';
import 'package:listify/ui/add_product_screen/controller/add_product_screen_controller.dart';

class AddProductScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<AddProductScreenController>(() => AddProductScreenController(), fenix: true);
  }
}
