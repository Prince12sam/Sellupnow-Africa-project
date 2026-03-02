import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class OurCategoryShimmer extends StatelessWidget {
  const OurCategoryShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: GridView.builder(
        scrollDirection: Axis.horizontal,
        shrinkWrap: true,
        physics: const AlwaysScrollableScrollPhysics(),
        itemCount: 10,
        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, mainAxisSpacing: 6, childAspectRatio: 1.5),
        itemBuilder: (context, index) {
          return Container(
            color: Colors.transparent,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  height: 76,
                  width: 76,
                  padding: EdgeInsets.symmetric(horizontal: 6),
                  decoration: BoxDecoration(color: AppColors.categoriesBgColor, borderRadius: BorderRadius.circular(12)),
                ).paddingOnly(top: 9),
                Container(
                  height: 16,
                  width: 60,
                  decoration: BoxDecoration(borderRadius: BorderRadius.circular(30), color: AppColors.grey),
                ).paddingOnly(top: 8),
              ],
            ),
          );
        },
      ).paddingOnly(bottom: 20),
    );
  }
}
