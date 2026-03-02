import 'package:get/get.dart';
import 'package:listify/ui/user_verification_screen/controller/user_verification_screen_controller.dart';

class UserVerificationScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<UserVerificationScreenController>(() => UserVerificationScreenController());
  }
}
