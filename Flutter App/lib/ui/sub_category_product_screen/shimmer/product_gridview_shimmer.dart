import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class ProductGridViewShimmer extends StatelessWidget {
  const ProductGridViewShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
        baseColor: Color(0xffEBEDF9),
        highlightColor: Color(0xffF3F5FD),
        // highlightColor: Color(0xffF3F5FD),
        child: GridView.builder(
          itemCount: 5,
          padding: EdgeInsets.only(top: 10),
          shrinkWrap: true,
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 0.70,
          ),
          itemBuilder: (context, index) {
            return Container(
              decoration: BoxDecoration(
                  color: AppColors.white.withValues(
                    alpha: 0,
                  ),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Color(0xffECEEFA))),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                // mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    height: 138,
                    decoration: BoxDecoration(color: AppColors.black, borderRadius: BorderRadius.vertical(top: Radius.circular(14))),
                  ).paddingAll(2),
                  9.height,

                  Container(
                    height: 16,
                    width: 150,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 10),
                  Container(
                    height: 14,
                    width: 61,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 9),
                  Container(
                    height: 49.5,
                    width: Get.width,
                    decoration: BoxDecoration(
                      color: Color(0xffF6F7FC).withValues(alpha: 0.30),
                      borderRadius: BorderRadius.only(bottomLeft: Radius.circular(14), bottomRight: Radius.circular(14)),
                    ),
                    child: Row(
                      children: [
                        Container(
                          height: 36,
                          width: 36,
                          decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.white),
                        ).paddingOnly(left: 6, right: 6),
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              height: 14,
                              width: 82,
                              decoration: BoxDecoration(color: AppColors.white, borderRadius: BorderRadius.circular(25)),
                            ).paddingOnly(bottom: 6),
                            Container(
                              height: 14,
                              width: 61,
                              decoration: BoxDecoration(color: AppColors.white, borderRadius: BorderRadius.circular(25)),
                            )
                          ],
                        )
                      ],
                    ),
                  )
                  // Row(
                  //   mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  //   children: [
                  //     Column(
                  //       crossAxisAlignment: CrossAxisAlignment.start,
                  //       children: [
                  //         Container(
                  //           height: 16,
                  //           width: 120,
                  //           margin: const EdgeInsets.only(bottom: 4),
                  //           decoration: BoxDecoration(
                  //             color: AppColors.black,
                  //             borderRadius: BorderRadius.circular(20),
                  //           ),
                  //         ),
                  //         3.height,
                  //         Container(
                  //           height: 16,
                  //           width: 100,
                  //           margin: const EdgeInsets.only(bottom: 5),
                  //           decoration: BoxDecoration(
                  //             color: AppColors.black,
                  //             borderRadius: BorderRadius.circular(20),
                  //           ),
                  //         ),
                  //         4.height,
                  //         Row(
                  //           children: [
                  //             Container(
                  //               height: 35,
                  //               width: 35,
                  //               decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.black),
                  //             ),
                  //             4.width,
                  //             Column(
                  //               crossAxisAlignment: CrossAxisAlignment.start,
                  //               children: [
                  //                 Container(
                  //                   height: 16,
                  //                   width: 80,
                  //                   margin: const EdgeInsets.only(bottom: 5),
                  //                   decoration: BoxDecoration(
                  //                     color: AppColors.black,
                  //                     borderRadius: BorderRadius.circular(20),
                  //                   ),
                  //                 ),
                  //                 Container(
                  //                   height: 15,
                  //                   width: 90,
                  //                   margin: const EdgeInsets.only(bottom: 5),
                  //                   decoration: BoxDecoration(
                  //                     color: AppColors.black,
                  //                     borderRadius: BorderRadius.circular(20),
                  //                   ),
                  //                 ),
                  //               ],
                  //             ),
                  //           ],
                  //         ),
                  //       ],
                  //     ),
                  //   ],
                  // ).paddingOnly(left: 6),
                ],
              ),
            );
          },
        ).paddingOnly(left: 13, right: 13));
  }
}

class UserProductGridViewShimmer extends StatelessWidget {
  const UserProductGridViewShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
        baseColor: Color(0xffEBEDF9),
        highlightColor: Color(0xffF3F5FD),
        // highlightColor: Color(0xffF3F5FD),
        child: GridView.builder(
          itemCount: 5,
          padding: EdgeInsets.only(top: 10),
          shrinkWrap: true,
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 0.68,
          ),
          itemBuilder: (context, index) {
            return Container(
              decoration: BoxDecoration(
                  color: AppColors.white.withValues(
                    alpha: 0,
                  ),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Color(0xffECEEFA))),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                // mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    height: 138,
                    decoration: BoxDecoration(color: AppColors.black, borderRadius: BorderRadius.vertical(top: Radius.circular(14))),
                  ).paddingAll(2),
                  9.height,
                  Container(
                    height: 16,
                    width: 150,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 10),
                  Container(
                    height: 14,
                    width: 61,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 9),
                  Container(
                    height: 14,
                    width: 100,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 9),
                  Container(
                    height: 14,
                    width: 100,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 9),
                ],
              ),
            );
          },
        ).paddingOnly(left: 15, right: 15));
  }
}

class RelatedProductGridViewShimmer extends StatelessWidget {
  const RelatedProductGridViewShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
        baseColor: Color(0xffEBEDF9),
        highlightColor: Color(0xffF3F5FD),
        // highlightColor: Color(0xffF3F5FD),
        child: GridView.builder(
          itemCount: 4,
          physics: NeverScrollableScrollPhysics(),
          padding: EdgeInsets.only(top: 10),
          shrinkWrap: true,
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 0.70,
          ),
          itemBuilder: (context, index) {
            return Container(
              decoration: BoxDecoration(
                  color: AppColors.white.withValues(
                    alpha: 0,
                  ),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Color(0xffECEEFA))),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                // mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    height: 138,
                    decoration: BoxDecoration(color: AppColors.black, borderRadius: BorderRadius.vertical(top: Radius.circular(14))),
                  ).paddingAll(2),
                  9.height,

                  Container(
                    height: 16,
                    width: 150,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 10),
                  Container(
                    height: 14,
                    width: 61,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                  ).paddingOnly(left: 6, bottom: 9),
                  Container(
                    height: 49.5,
                    width: Get.width,
                    decoration: BoxDecoration(
                      color: Color(0xffF6F7FC).withValues(alpha: 0.30),
                      borderRadius: BorderRadius.only(bottomLeft: Radius.circular(14), bottomRight: Radius.circular(14)),
                    ),
                    child: Row(
                      children: [
                        Container(
                          height: 36,
                          width: 36,
                          decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.white),
                        ).paddingOnly(left: 6, right: 6),
                        Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              height: 14,
                              width: 82,
                              decoration: BoxDecoration(color: AppColors.white, borderRadius: BorderRadius.circular(25)),
                            ).paddingOnly(bottom: 6),
                            Container(
                              height: 14,
                              width: 61,
                              decoration: BoxDecoration(color: AppColors.white, borderRadius: BorderRadius.circular(25)),
                            )
                          ],
                        )
                      ],
                    ),
                  )
                  // Row(
                  //   mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  //   children: [
                  //     Column(
                  //       crossAxisAlignment: CrossAxisAlignment.start,
                  //       children: [
                  //         Container(
                  //           height: 16,
                  //           width: 120,
                  //           margin: const EdgeInsets.only(bottom: 4),
                  //           decoration: BoxDecoration(
                  //             color: AppColors.black,
                  //             borderRadius: BorderRadius.circular(20),
                  //           ),
                  //         ),
                  //         3.height,
                  //         Container(
                  //           height: 16,
                  //           width: 100,
                  //           margin: const EdgeInsets.only(bottom: 5),
                  //           decoration: BoxDecoration(
                  //             color: AppColors.black,
                  //             borderRadius: BorderRadius.circular(20),
                  //           ),
                  //         ),
                  //         4.height,
                  //         Row(
                  //           children: [
                  //             Container(
                  //               height: 35,
                  //               width: 35,
                  //               decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.black),
                  //             ),
                  //             4.width,
                  //             Column(
                  //               crossAxisAlignment: CrossAxisAlignment.start,
                  //               children: [
                  //                 Container(
                  //                   height: 16,
                  //                   width: 80,
                  //                   margin: const EdgeInsets.only(bottom: 5),
                  //                   decoration: BoxDecoration(
                  //                     color: AppColors.black,
                  //                     borderRadius: BorderRadius.circular(20),
                  //                   ),
                  //                 ),
                  //                 Container(
                  //                   height: 15,
                  //                   width: 90,
                  //                   margin: const EdgeInsets.only(bottom: 5),
                  //                   decoration: BoxDecoration(
                  //                     color: AppColors.black,
                  //                     borderRadius: BorderRadius.circular(20),
                  //                   ),
                  //                 ),
                  //               ],
                  //             ),
                  //           ],
                  //         ),
                  //       ],
                  //     ),
                  //   ],
                  // ).paddingOnly(left: 6),
                ],
              ),
            );
          },
        ).paddingOnly(left: 15, right: 15));
  }
}
