import 'package:get/get.dart';
import 'package:listify/ui/contact_us_screen/controller/contact_us_screen_controller.dart';

class ContactUsScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<ContactUsScreenController>(() => ContactUsScreenController());
  }
}
