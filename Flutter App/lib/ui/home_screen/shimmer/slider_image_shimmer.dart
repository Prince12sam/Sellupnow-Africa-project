import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class SliderImageShimmer extends StatelessWidget {
  const SliderImageShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: SizedBox(
        height: 160,
        child: ListView.builder(
          physics: AlwaysScrollableScrollPhysics(),
          scrollDirection: Axis.horizontal,
          shrinkWrap: true,
          itemCount: 3,
          itemBuilder: (context, index) {
            return Container(
              height: 160,
              width: 300,
              decoration: BoxDecoration(color: AppColors.grey, borderRadius: BorderRadius.circular(25)),
            ).paddingOnly(left: 12);
          },
        ),
      ).paddingOnly(top: 30, bottom: 20),
    );
  }
}
