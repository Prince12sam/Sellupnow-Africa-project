import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class TransactionHistoryShimmer extends StatelessWidget {
  const TransactionHistoryShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        shrinkWrap: true,
        itemCount: 7,
        padding: EdgeInsets.only(top: 6),
        itemBuilder: (context, index) {
        return  Container(
          height: 100,
          width: Get.width,
          padding: EdgeInsets.all(17),
          decoration: BoxDecoration(color: AppColors.white.withValues(alpha: 0.60), borderRadius: BorderRadius.circular(12)),
        ).paddingSymmetric(horizontal: 16,vertical: 5);
      },)
    );
  }
}
