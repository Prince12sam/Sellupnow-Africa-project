import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class VideoShowShimmer extends StatelessWidget {
  const VideoShowShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: GridView.builder(
        shrinkWrap: true,
        itemCount: 5,
        padding: EdgeInsets.only(top: 10),
        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 10,
          crossAxisSpacing: 10,
          childAspectRatio: 0.75,
        ),
        itemBuilder: (context, index) {
          return Container(
            decoration: BoxDecoration(
                color: AppColors.white.withValues(
                  alpha: 0,
                ),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Color(0xffECEEFA))),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              // mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  height: Get.height * 0.200,
                  decoration: BoxDecoration(color: AppColors.black, borderRadius: BorderRadius.vertical(top: Radius.circular(14))),
                ).paddingAll(2),
                9.height,
                Container(
                  height: 16,
                  width: 150,
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(25),
                  ),
                ).paddingOnly(left: 6, bottom: 10),
                Container(
                  height: 14,
                  width: 61,
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(25),
                  ),
                ).paddingOnly(left: 6, bottom: 9),
              ],
            ),
          );
        },
      ).paddingOnly(left: 15, right: 15),
    );
  }
}
