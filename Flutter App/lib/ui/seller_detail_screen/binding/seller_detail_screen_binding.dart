import 'package:get/get.dart';
import 'package:listify/ui/seller_detail_screen/controller/seller_detail_screen_controller.dart';

class SellerDetailScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SellerDetailScreenController>(() => SellerDetailScreenController());
  }
}
