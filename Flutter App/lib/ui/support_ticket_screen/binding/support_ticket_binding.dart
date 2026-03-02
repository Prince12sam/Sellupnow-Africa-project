import 'package:get/get.dart';
import 'package:listify/ui/support_ticket_screen/controller/support_ticket_controller.dart';

class SupportTicketBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SupportTicketController>(() => SupportTicketController());
  }
}
