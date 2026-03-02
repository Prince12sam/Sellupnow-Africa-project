import 'package:get/get.dart';
import 'package:listify/ui/select_state_screen/controller/select_state_screen_controller.dart';

class SelectStateScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SelectStateScreenController>(() => SelectStateScreenController());
  }
}
