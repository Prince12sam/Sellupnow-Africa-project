import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class UserDataShimmer extends StatelessWidget {
  const UserDataShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        physics: NeverScrollableScrollPhysics(),
        shrinkWrap: true,
        itemCount: 7,
        itemBuilder: (context, index) {
          return Container(
            width: Get.width,
            padding: EdgeInsets.all(2),
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), color: AppColors.lightGrey.withValues(alpha: 0.40)
                // border: Border.all(color: AppColors.borderColor, width: 0.8),
                ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                Container(
                  height: 45,
                  width: 45,
                  decoration: BoxDecoration(color: AppColors.lightGrey, shape: BoxShape.circle),
                ).paddingOnly(left: 6, top: 8, bottom: 8, right: 6),
                Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      height: 13,
                      width: 160,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                    ).paddingOnly(bottom: 7),
                    Container(
                      height: 13,
                      width: 80,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.lightGrey),
                    ),
                  ],
                ),
              ],
            ),
          ).paddingOnly(left: 16, right: 16, top: 16);
        },
      ).paddingOnly(top: 6),
    );
  }
}
