import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class TrendingBlogShimmer extends StatelessWidget {
  const TrendingBlogShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: SizedBox(
        height: 200,
        child: ListView.builder(
          shrinkWrap: true,
          physics: BouncingScrollPhysics(),
          itemCount: 4,
          scrollDirection: Axis.horizontal,
          itemBuilder: (context, index) {
            return Container(
              width: 165,
              padding: EdgeInsets.all(1),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.borderColor, width: 0.8),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    height: 114,
                    decoration: BoxDecoration(borderRadius: BorderRadius.vertical(top: Radius.circular(10)), color: AppColors.lightGrey),
                  ),
                  3.height,
                  Container(
                    height: 14,
                    width: 90,
                    decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(30)),
                  ),
                  3.height,
                  Container(
                    height: 12,
                    width: 150,
                    decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(30)),
                  ),
                  1.height,
                  Container(
                    height: 12,
                    width: 150,
                    decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(30)),
                  ),
                  1.height,
                  Container(
                    height: 12,
                    width: 120,
                    decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(30)),
                  ),
                ],
              ),
            ).paddingOnly(right: 8);
          },
        ).paddingOnly(left: 14, bottom: 20),
      ),
    );
  }
}
