import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/controller/specific_product_view_show_controller.dart';

class SpecificProductViewBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SpecificProductViewShowController>(() => SpecificProductViewShowController());
  }
}
