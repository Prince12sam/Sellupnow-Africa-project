import 'package:get/get.dart';
import 'package:listify/ui/categories_screen/controller/categories_screen_controller.dart';

class CategoriesScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<CategoriesScreenController>(() => CategoriesScreenController());
  }
}
