import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class AllCategoryShimmer extends StatelessWidget {
  const AllCategoryShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
        baseColor: Color(0xffEBEDF9),
        highlightColor: Color(0xffF3F5FD),
        child: GridView.builder(
          itemCount: 6,
          shrinkWrap: true,
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 3,
              crossAxisSpacing: 10,
              mainAxisSpacing: 10,
              childAspectRatio: 0.9),
          itemBuilder: (context, index) {
            return Container(
              decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(20),
                  color:  AppColors.grey.withValues(alpha: 0.50)),

              child: Column(
                children: [
                  Container(
                    height: 100,
                    width: Get.width,
                    decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(20),
                        color:AppColors.grey),
                  ).paddingOnly(top: 3, left: 3, right: 3),
                  Container(
                    height: 10,
                    width: Get.width,
                    decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(20),
                        color:AppColors.grey),
                  ).paddingOnly(top: 7, left: 10, right: 10)
                ],
              ),
              // child: Column(
              //   children: [
              //     Column(
              //       crossAxisAlignment: CrossAxisAlignment.start,
              //       children: [
              //         Row(
              //           children: [
              //             Container(
              //               padding: EdgeInsets.symmetric(
              //                   horizontal: 10, vertical: 5),
              //               decoration: BoxDecoration(
              //                   color: AppColors.appRedColor
              //                       .withValues(alpha: 0.12),
              //                   borderRadius: BorderRadius.circular(6)),
              //               child: Center(),
              //             ).paddingOnly(top: 9, bottom: 6, right: 80),
              //             Container(
              //               height: 27,
              //               width: 27,
              //               decoration: BoxDecoration(
              //                 borderRadius: BorderRadius.circular(7),
              //                 color: AppColors.white,
              //                 border:
              //                 Border.all(color: AppColors.appRedColor),
              //               ),
              //               child: Center(
              //                 child: Image.asset(
              //                   AppAsset.paymentChainIcon,
              //                   height: 18,
              //                   width: 18,
              //                 ),
              //               ),
              //             )
              //           ],
              //         ),
              //         SizedBox(
              //           width: 190,
              //           height: 18,
              //         ).paddingOnly(bottom: 5),
              //         Container(
              //           height: 18,
              //           width: 1209,
              //           decoration: BoxDecoration(
              //               borderRadius: BorderRadius.circular(30),
              //               color: AppColors.grey),
              //         )
              //       ],
              //     ).paddingOnly(left: 38),
              //   ],
              // ),
            );
          },
        ).paddingOnly(right: 10, left: 10, top: 10));
  }
}
