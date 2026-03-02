import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class SubscriptionPlanShimmer extends StatelessWidget {
  const SubscriptionPlanShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: Container(
        height: 475,
        width: Get.width,
        decoration: BoxDecoration(
          color: AppColors.transparent,
          border: Border.all(color: AppColors.grey),
          borderRadius: BorderRadius.circular(40),
        ),
        child: Column(
          children: [
            Spacer(),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  height: 24,
                  width: 300,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                ),
                5.height,
                Container(
                  height: 17,
                  width: Get.width * 0.7,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                ),
                5.height,
                Container(
                  height: 17,
                  width: Get.width * 0.7,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                ).paddingOnly(bottom: 19),

                // Feature list
                Row(
                  children: [
                    Container(
                      height: 23,
                      width: 23,
                      decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.lightGrey),
                    ).paddingOnly(right: 8),
                    Container(
                      height: 18,
                      width: 210,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                    ),
                  ],
                ).paddingOnly(bottom: 14),
                Row(
                  children: [
                    Container(
                      height: 23,
                      width: 23,
                      decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.lightGrey),
                    ).paddingOnly(right: 8),
                    Container(
                      height: 18,
                      width: 210,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                    ),
                  ],
                ).paddingOnly(bottom: 14),
                Row(
                  children: [
                    Container(
                      height: 23,
                      width: 23,
                      decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.lightGrey),
                    ).paddingOnly(right: 8),
                    Container(
                      height: 18,
                      width: 210,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                    ),
                  ],
                ).paddingOnly(bottom: 14),

                Container(
                  height: 50,
                  width: Get.width * 0.68,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), color: AppColors.lightGrey),
                ).paddingSymmetric(horizontal: 6),
                20.height,
              ],
            ).paddingOnly(left: 10),
          ],
        ),
      ).paddingOnly(top: 30, left: 40, right: 40),
    );
  }
}
