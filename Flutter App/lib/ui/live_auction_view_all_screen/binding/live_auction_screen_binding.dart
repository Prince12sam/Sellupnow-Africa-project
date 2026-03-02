import 'package:get/get.dart';
import 'package:listify/ui/live_auction_view_all_screen/controller/live_auction_screen_controller.dart';

class LiveAuctionScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<LiveAuctionScreenController>(() => LiveAuctionScreenController());
  }
}
