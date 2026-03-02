import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class GetReviewShimmer extends StatelessWidget {
  const GetReviewShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
          itemCount: 9,
          padding: EdgeInsets.zero,
          shrinkWrap: true,
          physics: NeverScrollableScrollPhysics(),
          itemBuilder: (context, index) => Container(
                decoration: BoxDecoration(
                  border: Border.all(
                    color: AppColors.lightGrey,
                  ),
                  color: AppColors.white.withValues(alpha: 0.30),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(
                          height: 48,
                          width: 48,
                          clipBehavior: Clip.hardEdge,
                          decoration: BoxDecoration(color: AppColors.white, shape: BoxShape.circle),
                        ).paddingOnly(top: 8, left: 16, right: 10, bottom: 10),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              height: 11,
                              width: 200,
                              decoration: BoxDecoration(borderRadius: BorderRadius.circular(10), color: AppColors.white),
                            ).paddingOnly(bottom: 5),
                            Container(
                              height: 11,
                              width: 150,
                              decoration: BoxDecoration(borderRadius: BorderRadius.circular(10), color: AppColors.white),
                            )
                          ],
                        )
                      ],
                    ),
                    Container(
                      height: 11,
                      width: 300,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(10), color: AppColors.white),
                    ).paddingOnly(bottom: 5, left: 16),
                    Container(
                      height: 11,
                      width: 250,
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(10), color: AppColors.white),
                    ).paddingOnly(bottom: 5, left: 16),
                  ],
                ),
              ).paddingOnly(bottom: 10, right: 14, left: 14)),
    );
  }
}
