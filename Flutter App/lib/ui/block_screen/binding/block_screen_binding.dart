import 'package:get/get.dart';
import 'package:listify/ui/block_screen/controller/block_screen_controller.dart';

class BlockScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<BlockScreenController>(() => BlockScreenController());
  }
}
