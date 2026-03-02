import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/timer_widget/timer_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class AuctionShimmer extends StatelessWidget {
  const AuctionShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        shrinkWrap: true,
        scrollDirection: Axis.horizontal,
        physics: const AlwaysScrollableScrollPhysics(),
        itemCount: 5,
        itemBuilder: (context, index) {
          return Container(
            decoration: BoxDecoration(
              color: AppColors.transparent,
              border: Border.all(color: AppColors.borderColor),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                SizedBox(
                  height: 110,
                  width: 132,
                  child: ClipRRect(
                    borderRadius: BorderRadius.vertical(top: Radius.circular(10)),

                    child: Container(
                      height: 110,
                      width: 132,
                        color: AppColors.grey
                    ),
                  ).paddingAll(1),
                ),
                Container(
                  height: 8,
                  width: 70,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.grey),
                ).paddingOnly(left: 4, right: 4, bottom: 3, top: 8),
                Container(
                  height: 8,
                  width: 60,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.grey),
                ).paddingOnly(left: 4, right: 4, bottom: 4.9),
                Spacer(),
                TimerWidget(
                  padding: EdgeInsets.symmetric(vertical: 6),
                  width: 132,
                  endDate: "",
                ),
              ],
            ),
          ).paddingOnly(left: 5, right: 5);
        },
      ),
    );
  }
}
