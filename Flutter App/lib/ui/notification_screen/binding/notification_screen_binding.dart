import 'package:get/get.dart';
import 'package:listify/ui/notification_screen/controller/notification_screen_controller.dart';

class NotificationScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<NotificationScreenController>(() => NotificationScreenController());
  }
}
