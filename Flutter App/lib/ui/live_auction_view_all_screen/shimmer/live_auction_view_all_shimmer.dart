import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/timer_widget/timer_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class LiveAuctionViewAllShimmer extends StatelessWidget {
  const LiveAuctionViewAllShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
        baseColor: Color(0xffEBEDF9),
        highlightColor: Color(0xffF3F5FD),
        child: GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: 6,
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 0.79,
          ),
          itemBuilder: (context, index) {
            return Container(
              decoration: BoxDecoration(
                color: AppColors.transparent,
                border: Border.all(color: AppColors.borderColor),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  SizedBox(
                    height: 147.5,
                    width: 130,
                    child: ClipRRect(
                      borderRadius: BorderRadius.vertical(top: Radius.circular(10)),
                      child: SizedBox(
                        height: 147.5,
                        width: 130,
                      ),
                    ).paddingAll(1),
                  ),
                  Container(
                    height: 10,
                    width: 80,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(30),
                      color: AppColors.grey,
                    ),
                  ).paddingOnly(left: 4, right: 4, bottom: 5, top: 12),
                  Container(
                    height: 10,
                    width: 90,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(30),
                      color: AppColors.grey,
                    ),
                  ).paddingOnly(left: 4, right: 4, bottom: 4.3),
                  TimerWidget(
                    padding: EdgeInsets.symmetric(vertical: 6),
                    width: Get.width * 0.65,
                    height: Get.height * 0.05,
                    endDate: "",
                  ),
                ],
              ),
            );
          },
        ).paddingOnly(left: 16, right: 16, top: 20, bottom: 20));
  }
}

class LiveAuctionShimmer extends StatelessWidget {
  const LiveAuctionShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
        baseColor: Color(0xffEBEDF9),
        highlightColor: Color(0xffF3F5FD),
        child: ListView.builder(itemBuilder: (context, index) {
          return Container(
            decoration: BoxDecoration(
              color: AppColors.transparent,
              border: Border.all(color: AppColors.borderColor),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(
                  height: 147.5,
                  width: 130,
                  child: ClipRRect(
                    borderRadius: BorderRadius.vertical(top: Radius.circular(10)),
                    child: SizedBox(
                      height: 147.5,
                      width: 130,
                    ),
                  ).paddingAll(1),
                ),
                Container(
                  height: 10,
                  width: 80,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(30),
                    color: AppColors.grey,
                  ),
                ).paddingOnly(left: 4, right: 4, bottom: 5, top: 12),
                Container(
                  height: 10,
                  width: 90,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(30),
                    color: AppColors.grey,
                  ),
                ).paddingOnly(left: 4, right: 4, bottom: 4.3),
                TimerWidget(
                  padding: EdgeInsets.symmetric(vertical: 6),
                  width: Get.width * 0.65,
                  height: Get.height * 0.05,
                  endDate: "",
                ),
              ],
            ),
          );
        },));
  }
}
