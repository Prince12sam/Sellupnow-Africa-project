import 'package:get/get.dart';
import 'package:listify/ui/message_screen/controller/message_screen_controller.dart';

class MessageScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<MessageScreenController>(() => MessageScreenController());
  }
}
