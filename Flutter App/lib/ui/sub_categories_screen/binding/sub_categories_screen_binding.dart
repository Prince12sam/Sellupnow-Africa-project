import 'package:get/get.dart';
import 'package:listify/ui/sub_categories_screen/controller/sub_categories_screen_controller.dart';
import 'package:listify/ui/sub_categories_screen/service/select_bus_service.dart';

class SubCategoriesScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SubCategoriesScreenController>(() => SubCategoriesScreenController(), fenix: true);
    Get.lazyPut<SelectionBus>(() => SelectionBus());
  }
}
