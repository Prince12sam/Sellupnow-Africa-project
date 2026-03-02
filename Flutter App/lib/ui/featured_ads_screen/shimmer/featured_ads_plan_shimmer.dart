import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class FeaturedAdsPlanShimmer extends StatelessWidget {
  const FeaturedAdsPlanShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        itemCount: 3,
        shrinkWrap: true,
        physics: NeverScrollableScrollPhysics(),
        itemBuilder: (context, index) {
          return Container(
            width: Get.width,
            margin: EdgeInsets.only(left: 14, right: 14, bottom: 14),
            decoration: BoxDecoration(
              color: AppColors.transparent,
              border: Border.all(color: AppColors.appRedColor),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Row(
              children: [
                Container(
                  // height: 74,
                  // width: 74,
                  padding: EdgeInsets.all(10),
                  decoration: BoxDecoration(color: AppColors.categoriesBgColor, borderRadius: BorderRadius.circular(14)),
                  // child: Image.asset(
                  //   AppAsset.freeTrialImage,
                  //   height: 50,
                  //   width: 50,
                  // ),
                  child: SizedBox(
                    height: 50,
                    width: 50,
                  ),
                ).paddingAll(6),
                5.width,
                Expanded(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        height: 24,
                        width: 120,
                        decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.grey),
                      ).paddingOnly(bottom: 6),
                      Container(
                        height: 18,
                        width: 150,
                        decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.grey),
                      ).paddingOnly(bottom: 8),
                      Row(
                        children: [
                          Container(
                            height: 17,
                            width: 17,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(4),
                              color: AppColors.grey,
                            ),
                          ).paddingOnly(right: 5),
                          Container(
                            height: 16,
                            width: 50,
                            decoration: BoxDecoration(color: AppColors.grey, borderRadius: BorderRadius.circular(30)),
                          ).paddingOnly(right: 16),
                          Container(
                            height: 17,
                            width: 17,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(4),
                              color: AppColors.grey,
                            ),
                          ).paddingOnly(right: 5),
                          Container(
                            height: 16,
                            width: 50,
                            decoration: BoxDecoration(color: AppColors.grey, borderRadius: BorderRadius.circular(30)),
                          ).paddingOnly(right: 16),
                        ],
                      ),
                    ],
                  ),
                ),
                6.width,
                Container(
                  height: 34,
                  width: 70,
                  padding: EdgeInsets.symmetric(horizontal: 11, vertical: 4),
                  decoration: BoxDecoration(color: AppColors.lightRed100.withValues(alpha: 0.5), borderRadius: BorderRadius.circular(8)),
                ).paddingOnly(right: 14)
              ],
            ),
          );
        },
      ),
    );
  }
}
