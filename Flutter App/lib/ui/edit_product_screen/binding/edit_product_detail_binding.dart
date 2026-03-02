import 'package:get/get.dart';
import 'package:listify/ui/edit_product_screen/controller/edit_product_detail_controller.dart';

class EditProductDetailBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<EditProductDetailController>(() => EditProductDetailController(), fenix: true);
  }
}
