import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/controller/specific_product_like_show_controller.dart';

class SpecificProductLikeViewBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SpecificProductLikeShowController>(() => SpecificProductLikeShowController());
  }
}
