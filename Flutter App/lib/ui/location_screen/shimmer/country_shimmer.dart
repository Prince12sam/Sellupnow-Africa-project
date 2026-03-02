import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class CountryShimmer extends StatelessWidget {
  const CountryShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        shrinkWrap: true,
        padding: EdgeInsets.zero,
        physics: NeverScrollableScrollPhysics(),
        itemCount: 10,
        itemBuilder: (context, index) {
          return Container(
            width: Get.width,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(16),
              color: AppColors.categoriesBgColor.withValues(alpha: 0.5),
              border: Border.all(color: AppColors.borderColor.withValues(alpha: 0.4)),
            ),
            child: Row(
              children: [
                Container(
                  height: 60,
                  width: 60,
                  padding: const EdgeInsets.all(9),
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(14),
                  ),
                ).paddingAll(5),
                Container(
                  height: 26,
                  width: 100,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.white),
                ).paddingOnly(left: 10),
                Spacer(),
                Container(
                  padding: EdgeInsets.all(4),
                  decoration: BoxDecoration(color: AppColors.white, shape: BoxShape.circle),
                  child: RotatedBox(
                    quarterTurns: 2,
                    child: SizedBox(
                      height: 22,
                      width: 22,
                    ),
                  ),
                ).paddingOnly(right: 16)
              ],
            ),
          ).paddingOnly(bottom: 14);
        },
      ).paddingOnly(left: 16, right: 16),
    );
  }
}
