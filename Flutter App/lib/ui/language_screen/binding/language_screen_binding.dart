import 'package:get/get.dart';
import 'package:listify/ui/language_screen/controller/language_screen_controller.dart';

class LanguageScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<LanguageScreenController>(() => LanguageScreenController());
  }
}
