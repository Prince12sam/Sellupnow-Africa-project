import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class ProductListViewShimmer extends StatelessWidget {
  const ProductListViewShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        itemCount: 4,
        padding: EdgeInsets.zero,
        shrinkWrap: true,
        physics: AlwaysScrollableScrollPhysics(),
        itemBuilder: (context, index) => Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: Container(
            decoration: BoxDecoration(
              color: AppColors.appRedColor.withValues(alpha: 0.2),
              border: Border.all(color: AppColors.borderColor.withValues(alpha: 0.4)),
              borderRadius: BorderRadius.circular(14),
            ),
            padding: EdgeInsets.all(1),
            child: Row(
              children: [
                Container(
                  height: Get.height * 0.15,
                  width: Get.width * 0.36,
                  decoration: BoxDecoration(
                      color: AppColors.black, borderRadius: BorderRadius.only(bottomLeft: Radius.circular(16), topLeft: Radius.circular(16))),
                ),
                6.width,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          height: 16,
                          width: 180,
                          margin: const EdgeInsets.only(bottom: 4),
                          decoration: BoxDecoration(
                            color: AppColors.black,
                            borderRadius: BorderRadius.circular(20),
                          ),
                        ),
                        3.height,
                        Container(
                          height: 16,
                          width: 160,
                          margin: const EdgeInsets.only(bottom: 4),
                          decoration: BoxDecoration(
                            color: AppColors.black,
                            borderRadius: BorderRadius.circular(20),
                          ),
                        ),
                        3.height,
                        Container(
                          height: 16,
                          width: 130,
                          margin: const EdgeInsets.only(bottom: 5),
                          decoration: BoxDecoration(
                            color: AppColors.black,
                            borderRadius: BorderRadius.circular(20),
                          ),
                        ),
                        4.height,
                        Row(
                          children: [
                            Container(
                              height: 35,
                              width: 35,
                              decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.black),
                            ),
                            4.width,
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Container(
                                  height: 16,
                                  width: 80,
                                  margin: const EdgeInsets.only(bottom: 5),
                                  decoration: BoxDecoration(
                                    color: AppColors.black,
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                ),
                                Container(
                                  height: 15,
                                  width: 90,
                                  margin: const EdgeInsets.only(bottom: 5),
                                  decoration: BoxDecoration(
                                    color: AppColors.black,
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ],
                    ),
                  ],
                ).paddingOnly(left: 6),
              ],
            ),
          ).paddingOnly(left: 14, right: 14),
        ),
      ),
    );
  }
}

class ProductViewShimmer extends StatelessWidget {
  const ProductViewShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        itemCount: 4,
        padding: EdgeInsets.zero,
        shrinkWrap: true,
        physics: AlwaysScrollableScrollPhysics(),
        itemBuilder: (context, index) => Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: Container(
            decoration: BoxDecoration(
              color: AppColors.appRedColor.withValues(alpha: 0.2),
              border: Border.all(color: AppColors.borderColor.withValues(alpha: 0.4)),
              borderRadius: BorderRadius.circular(8),
            ),
            padding: EdgeInsets.all(6),
            child: Row(
              children: [
                Container(
                  height: Get.height * 0.09,
                  width: Get.width * 0.24,
                  decoration: BoxDecoration(
                    color: AppColors.black,
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                6.width,
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          height: 16,
                          width: 180,
                          margin: const EdgeInsets.only(bottom: 4),
                          decoration: BoxDecoration(
                            color: AppColors.black,
                            borderRadius: BorderRadius.circular(20),
                          ),
                        ),
                        3.height,
                        Container(
                          height: 16,
                          width: 160,
                          margin: const EdgeInsets.only(bottom: 4),
                          decoration: BoxDecoration(
                            color: AppColors.black,
                            borderRadius: BorderRadius.circular(20),
                          ),
                        ),
                        3.height,
                        Container(
                          height: 16,
                          width: 130,
                          margin: const EdgeInsets.only(bottom: 5),
                          decoration: BoxDecoration(
                            color: AppColors.black,
                            borderRadius: BorderRadius.circular(20),
                          ),
                        ),
                        4.height,
                      ],
                    ),
                  ],
                ).paddingOnly(left: 6),
              ],
            ),
          ).paddingOnly(left: 14, right: 14),
        ),
      ),
    );
  }
}
