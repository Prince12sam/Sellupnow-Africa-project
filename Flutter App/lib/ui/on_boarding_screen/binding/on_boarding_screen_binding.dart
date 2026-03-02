import 'package:get/get.dart';
import 'package:listify/ui/on_boarding_screen/controller/on_boarding_screen_controller.dart';

class OnBoardingScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<OnBoardingScreenController>(() => OnBoardingScreenController());
  }
}
