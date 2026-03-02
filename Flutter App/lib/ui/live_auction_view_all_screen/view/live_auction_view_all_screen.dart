import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/live_auction_view_all_screen/controller/live_auction_screen_controller.dart';
import 'package:listify/ui/live_auction_view_all_screen/widget/live_auction_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class LiveAuctionViewAllScreen extends StatelessWidget {
  const LiveAuctionViewAllScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<LiveAuctionScreenController>(builder: (controller) {
      return Scaffold(
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: LiveAuctionScreenAppBar(
            title: "${EnumLocale.txtLiveAuction.name.tr}(${controller.liveAuctionProductList.length})",
          ),
        ),
        backgroundColor: AppColors.white,
        body: RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: controller.onRefresh,
          child: SingleChildScrollView(
            controller: controller.scrollController,
            physics: AlwaysScrollableScrollPhysics(),
            child: Column(
              children: [
                LiveAuctionScreenView(),
              ],
            ),
          ),
        ),
      );
    });
  }
}
