import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/timer_widget/timer_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/live_auction_view_all_screen/controller/live_auction_screen_controller.dart';
import 'package:listify/ui/live_auction_view_all_screen/shimmer/live_auction_view_all_shimmer.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/font_style.dart';

class LiveAuctionScreenAppBar extends StatelessWidget {
  final String? title;
  const LiveAuctionScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class LiveAuctionScreenView extends StatelessWidget {
  const LiveAuctionScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<LiveAuctionScreenController>(builder: (controller) {
      const cross = 2;
      const tileHeight = 240.0;

      return controller.isLoading
          ? LiveAuctionViewAllShimmer()
          : Column(
            children: [
              GridView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: controller.liveAuctionProductList.length,
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: cross,
                    mainAxisExtent: tileHeight,        // <- key change (no childAspectRatio)
                    crossAxisSpacing: 10,
                    mainAxisSpacing: 10,
                  ),
                  itemBuilder: (context, index) {
                    final auctionItem = controller.liveAuctionProductList[index];

                    return GestureDetector(
                      onTap: () {
                        Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                          'liveAuctionTime': true,
                          'sellerDetail': true,
                          'relatedProduct': true,
                          'adId': auctionItem.id,
                          // 'ad': auctionItem,
                        });
                      },
                      child: Container(
                        decoration: BoxDecoration(
                          color: AppColors.white,
                          border: Border.all(color: AppColors.borderColor),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            SizedBox(
                              height: 147.5,
                              width: Get.width,
                              child: Container(
                                // color: Colors.red,
                                child: ClipRRect(
                                  borderRadius: BorderRadius.vertical(top: Radius.circular(10)),
                                  child: CustomImageView(image: controller.liveAuctionProductList[index].primaryImage ?? ''),
                                ).paddingAll(1),
                              ),
                            ),
                            SizedBox(
                              width: 170,
                              child: Text(
                                auctionItem.title ?? '',
                                style: AppFontStyle.fontStyleW700(fontSize: 14, fontColor: AppColors.black),
                                overflow: TextOverflow.ellipsis,
                              ).paddingOnly(left: 4, right: 4),
                            ), SizedBox(
                              width: 170,
                              child: Text(
                                auctionItem.subTitle ?? '',
                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.grey),
                                overflow: TextOverflow.ellipsis,
                              ).paddingOnly(left: 4, right: 4),
                            ).paddingOnly(left: 4, right: 4, bottom: 4.3),

                            TimerWidget(
                              padding: EdgeInsets.symmetric(vertical: 6),
                              width: Get.width * 0.65,
                              height: Get.height * 0.05,
                              endDate: "${controller.liveAuctionProductList[index].auctionEndDate}",
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ).paddingOnly(left: 16, right: 16, top: 20, bottom: 20),

              GetBuilder<LiveAuctionScreenController>(
                id: Constant.idPagination,
                builder: (controller) => Visibility(
                  visible: controller.isPaginationLoading,
                  child: CircularProgressIndicator(color: AppColors.appRedColor),
                ),
              ),
            ],
          );
    });
  }
}
