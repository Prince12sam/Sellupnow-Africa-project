import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class AllBlogShimmer extends StatelessWidget {
  const AllBlogShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        physics: NeverScrollableScrollPhysics(),
        shrinkWrap: true,
        itemCount: 2,
        itemBuilder: (context, index) {
          return Container(
            width: Get.width,
            padding: EdgeInsets.all(2),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.borderColor, width: 0.8),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(
                  width: Get.width,
                  height: 220,
                  child: Container(
                    decoration: BoxDecoration(borderRadius: BorderRadius.circular(10), color: AppColors.lightGrey),
                  ),
                ),
                Container(
                  height: 13,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                ).paddingOnly(left: 6, right: 8, bottom: 1, top: 3),
                Container(
                  height: 13,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                ).paddingOnly(left: 6, right: 8),
                1.height,
                Container(
                  height: 13,
                  width: 180,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                ).paddingOnly(left: 6, right: 8),
              ],
            ),
          ).paddingOnly(left: 14, right: 14, bottom: 12);
        },
      ).paddingOnly(top: 20),
    );
  }
}
