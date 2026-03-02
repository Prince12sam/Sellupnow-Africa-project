import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class NotificationShimmer extends StatelessWidget {
  const NotificationShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        physics: AlwaysScrollableScrollPhysics(),
        padding: EdgeInsets.zero,
        shrinkWrap: true,
        itemCount: 10,
        itemBuilder: (context, index) {
          return Container(
            decoration: BoxDecoration(
                boxShadow: [
                  BoxShadow(color: AppColors.black.withValues(alpha: 0.06), blurRadius: 14, offset: Offset(0, 0), spreadRadius: 0),
                ],
                color: AppColors.transparent,
                border: Border.all(
                  color: AppColors.notificationBorderColor,
                ),
                borderRadius: BorderRadius.circular(16)),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                Container(
                  height: 70,
                  width: 70,

                  // padding: EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: AppColors.greyTxtColor2,
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Center(
                    child: SizedBox(
                      height: 70,
                      width: 70,
                    ),
                  ),
                ).paddingOnly(left: 8, top: 8, right: 12, bottom: 8),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        height: 12,
                        width: Get.width,
                        decoration: BoxDecoration(
                          color: AppColors.grey,
                          borderRadius: BorderRadius.circular(30),
                        ),
                      ).paddingOnly(bottom: 4),
                      SizedBox(
                        // width: Get.width * 0.72,
                        child: Container(
                          height: 15,
                          width: 200,
                          decoration: BoxDecoration(
                            color: AppColors.grey,
                            borderRadius: BorderRadius.circular(30),
                          ),
                        ),
                      ).paddingOnly(bottom: 6),
                      Container(
                        height: 12,
                        width: 120,
                        decoration: BoxDecoration(
                          color: AppColors.grey,
                          borderRadius: BorderRadius.circular(30),
                        ),
                      ).paddingOnly(bottom: 7)
                    ],
                  ).paddingOnly(top: 9, right: 10),
                )
              ],
            ),
          ).paddingOnly(top: 14);
        },
      ),
    );
  }
}
