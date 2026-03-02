import 'package:get/get.dart';
import 'package:listify/ui/select_city_screen/controller/select_city_screen_controller.dart';

class SelectCityScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SelectCityScreenController>(() => SelectCityScreenController());
  }
}
