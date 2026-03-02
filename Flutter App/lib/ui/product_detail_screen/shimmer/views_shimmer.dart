import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class ViewsShimmer extends StatelessWidget {
  const ViewsShimmer({super.key});

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
        itemBuilder: (context, index) {
          return Row(
            children: [
              Container(
                height: 45,
                width: 45,
                clipBehavior: Clip.hardEdge,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.white,
                ),
                child: SizedBox(
                  height: 45,
                  width: 45,
                ),
              ).paddingOnly(left: 6, top: 8, bottom: 8, right: 6),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    height: 10,
                    width: 120,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(30),
                      color: AppColors.grey,
                    ),
                  )
                  // Text(
                  //   id ?? "",
                  //   style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.black),
                  // ),
                ],
              ),
            ],
          ).paddingOnly(left: 8,);
        },
      ).paddingOnly(top: 6),
    );
  }
}
