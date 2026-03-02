import 'package:get/get.dart';
import 'package:listify/ui/most_liked_view_all/controller/most_liked_view_all_controller.dart';

class MostLikedViewAllBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<MostLikedViewAllController>(() => MostLikedViewAllController());
  }
}
