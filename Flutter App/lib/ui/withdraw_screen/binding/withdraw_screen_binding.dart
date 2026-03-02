import 'package:get/get.dart';
import 'package:listify/ui/withdraw_screen/controller/withdraw_screen_controller.dart';

class WithdrawScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<WithdrawScreenController>(() => WithdrawScreenController());
  }
}
