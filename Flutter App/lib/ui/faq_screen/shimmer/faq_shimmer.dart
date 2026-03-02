import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class HelpCenterShimmer extends StatelessWidget {
  const HelpCenterShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        itemCount: 7,
        padding: EdgeInsets.zero,
        shrinkWrap: true,
        physics: NeverScrollableScrollPhysics(),
        itemBuilder: (context, index) => Padding(
          padding: const EdgeInsets.only(bottom: 10),
          child: Container(
            height: 60,
            width: Get.width,
            decoration: BoxDecoration(
              // color: AppColors.lightGrey,
              border: Border.all(color: AppColors.black),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  height: 20,
                  width: 140,
                  decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(30)),
                ).paddingOnly(left: 12),
              ],
            ),
          ).paddingOnly(left: 14, right: 14, bottom: 16),
        ),
      ),
    );
  }
}
