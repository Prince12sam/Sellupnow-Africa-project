import 'package:get/get.dart';
import 'package:listify/ui/seller_detail_product_all_view/controller/seller_detail_product_all_view_controller.dart';

class SellerDetailProductAllViewBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SellerDetailProductAllViewController>(() => SellerDetailProductAllViewController());
  }
}
