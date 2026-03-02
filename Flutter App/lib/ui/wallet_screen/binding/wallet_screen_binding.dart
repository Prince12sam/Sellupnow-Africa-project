import 'package:get/get.dart';
import 'package:listify/ui/wallet_screen/controller/wallet_screen_controller.dart';

class WalletScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<WalletScreenController>(() => WalletScreenController());
  }
}
