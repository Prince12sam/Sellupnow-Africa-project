import 'package:get/get.dart';
import 'package:listify/ui/start_user_verification_screen/controller/start_user_verification_controller.dart';

class StartUserVerificationBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<StartUserVerificationController>(() => StartUserVerificationController());
  }
}
