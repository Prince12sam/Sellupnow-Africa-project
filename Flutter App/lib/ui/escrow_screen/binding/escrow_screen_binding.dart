import 'package:get/get.dart';
import 'package:listify/ui/escrow_screen/controller/escrow_screen_controller.dart';

class EscrowScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<EscrowScreenController>(() => EscrowScreenController());
  }
}
