import 'package:get/get.dart';
import 'package:listify/ui/fill_profile_screen/controller/fill_profile_screen_controller.dart';

class FillProfileScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<FillProfileScreenController>(() => FillProfileScreenController());
    // Get.lazyPut<EditProfileController>(() => EditProfileController());
  }
}
