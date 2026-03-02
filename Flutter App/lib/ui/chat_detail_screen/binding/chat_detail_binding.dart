import 'package:get/get.dart';
import 'package:listify/ui/chat_detail_screen/controller/chat_detail_controller.dart';

class ChatDetailBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<ChatDetailController>(() => ChatDetailController());
  }
}
