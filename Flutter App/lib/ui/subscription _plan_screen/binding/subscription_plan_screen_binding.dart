import 'package:get/get.dart';
import 'package:listify/ui/subscription%20_plan_screen/controller/subscription_plan_screen_controller.dart';

class SubscriptionPlanScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<SubscriptionPlanScreenController>(() => SubscriptionPlanScreenController());
  }
}
