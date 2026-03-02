import 'package:get/get.dart';
import 'package:listify/ui/login_screen/controller/login_screen_controller.dart';
import 'package:listify/ui/mobile_number_screen/controller/mobile_number_controller.dart';
import 'package:listify/ui/verify_otp_screen/controller/verify_otp_controller.dart';

class VerifyOtpBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<VerifyOtpController>(() => VerifyOtpController());
    Get.lazyPut<MobileNumberController>(() => MobileNumberController());
    Get.lazyPut<LoginScreenController>(() => LoginScreenController());
  }
}
