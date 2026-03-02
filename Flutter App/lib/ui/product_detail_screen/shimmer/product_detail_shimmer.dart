import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class ProductDetailShimmer extends StatelessWidget {
  const ProductDetailShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: SingleChildScrollView(
        child: Stack(
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  height: Get.height * 0.32,
                  width: Get.width,
                  color: AppColors.white.withValues(alpha: 0.50),
                ),
                Container(
                  // height: Get.height * 0.32,
                  width: Get.width,
                  color: AppColors.white.withValues(alpha: 0.30),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        height: Get.height * 0.014,
                        width: Get.width * 0.8,
                        decoration: BoxDecoration(
                          color: AppColors.white.withValues(alpha: 0.50),
                          borderRadius: BorderRadius.circular(20),
                        ),
                      ).paddingOnly(left: 14, top: 10, bottom: 2),
                      Container(
                        height: Get.height * 0.014,
                        width: Get.width * 0.4,
                        decoration: BoxDecoration(
                          color: AppColors.white.withValues(alpha: 0.50),
                          borderRadius: BorderRadius.circular(20),
                        ),
                      ).paddingOnly(left: 14, top: 10, bottom: 2),
                      Container(
                        height: Get.height * 0.014,
                        width: Get.width * 0.7,
                        decoration: BoxDecoration(
                          color: AppColors.white.withValues(alpha: 0.50),
                          borderRadius: BorderRadius.circular(20),
                        ),
                      ).paddingOnly(left: 14, top: 10, bottom: 10),
                    ],
                  ),
                ).paddingOnly(bottom: 18),
                Row(
                  children: [
                    Expanded(
                      child: Container(
                        height: Get.height * 0.06,
                        width: Get.width,
                        decoration: BoxDecoration(
                          color: AppColors.white.withValues(alpha: 0.50),
                        ),
                      ).paddingOnly(right: 8),
                    ),
                    Expanded(
                      child: Container(
                        height: Get.height * 0.06,
                        width: Get.width,
                        decoration: BoxDecoration(
                          color: AppColors.white.withValues(alpha: 0.50),
                        ),
                      ).paddingOnly(left: 8),
                    )
                  ],
                ).paddingOnly(left: 14, right: 14, bottom: 20),
                Container(
                  height: Get.height * 0.014,
                  width: Get.width * 0.4,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: AppColors.white.withValues(alpha: 0.50),
                  ),
                ).paddingOnly(left: 14, bottom: 8),
                Container(
                  height: Get.height * 0.014,
                  width: Get.width,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: AppColors.white.withValues(alpha: 0.50),
                  ),
                ).paddingOnly(left: 14, right: 14, bottom: 8),
                Container(
                  height: Get.height * 0.014,
                  width: Get.width,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: AppColors.white.withValues(alpha: 0.50),
                  ),
                ).paddingOnly(left: 14, right: 14, bottom: 8),
                Container(
                  height: Get.height * 0.014,
                  width: Get.width,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: AppColors.white.withValues(alpha: 0.50),
                  ),
                ).paddingOnly(left: 14, right: 14, bottom: 8),
                Container(
                  height: Get.height * 0.014,
                  width: Get.width * 0.6,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: AppColors.white.withValues(alpha: 0.50),
                  ),
                ).paddingOnly(left: 14, right: 14, bottom: 20),
                Container(
                  // height: Get.height * 0.32,
                  width: Get.width,
                  color: AppColors.white.withValues(alpha: 0.30),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        height: Get.height * 0.014,
                        width: Get.width * 0.4,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(10),
                          color: AppColors.white.withValues(alpha: 0.50),
                        ),
                      ).paddingOnly(left: 14, bottom: 30, top: 14),
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              height: Get.height * 0.07,
                              decoration: BoxDecoration(
                                color: AppColors.white.withValues(alpha: 0.30),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    height: 70,
                                    width: 45,
                                    decoration: BoxDecoration(color: AppColors.white.withValues(alpha: 0.50)),
                                  ).paddingAll(7),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.25,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      ).paddingOnly(bottom: 7),
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.15,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      )
                                    ],
                                  ).paddingOnly(left: 7)
                                ],
                              ),
                            ).paddingOnly(right: 14, left: 8),
                          ),
                          Expanded(
                            child: Container(
                              height: Get.height * 0.07,
                              decoration: BoxDecoration(
                                color: AppColors.white.withValues(alpha: 0.30),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    height: 70,
                                    width: 45,
                                    decoration: BoxDecoration(color: AppColors.white.withValues(alpha: 0.50)),
                                  ).paddingAll(7),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.25,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      ).paddingOnly(bottom: 7),
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.15,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      )
                                    ],
                                  ).paddingOnly(left: 7)
                                ],
                              ),
                            ).paddingOnly(right: 14, left: 8),
                          ),
                        ],
                      ).paddingOnly(bottom: 25),
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              height: Get.height * 0.07,
                              decoration: BoxDecoration(
                                color: AppColors.white.withValues(alpha: 0.30),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    height: 70,
                                    width: 45,
                                    decoration: BoxDecoration(color: AppColors.white.withValues(alpha: 0.50)),
                                  ).paddingAll(7),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.25,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      ).paddingOnly(bottom: 7),
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.15,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      )
                                    ],
                                  ).paddingOnly(left: 7)
                                ],
                              ),
                            ).paddingOnly(right: 14, left: 8),
                          ),
                          Expanded(
                            child: Container(
                              height: Get.height * 0.07,
                              decoration: BoxDecoration(
                                color: AppColors.white.withValues(alpha: 0.30),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    height: 70,
                                    width: 45,
                                    decoration: BoxDecoration(color: AppColors.white.withValues(alpha: 0.50)),
                                  ).paddingAll(7),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.25,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      ).paddingOnly(bottom: 7),
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.15,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      )
                                    ],
                                  ).paddingOnly(left: 7)
                                ],
                              ),
                            ).paddingOnly(right: 14, left: 8),
                          ),
                        ],
                      ).paddingOnly(bottom: 25),
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              height: Get.height * 0.07,
                              decoration: BoxDecoration(
                                color: AppColors.white.withValues(alpha: 0.30),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    height: 70,
                                    width: 45,
                                    decoration: BoxDecoration(color: AppColors.white.withValues(alpha: 0.50)),
                                  ).paddingAll(7),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.25,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      ).paddingOnly(bottom: 7),
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.15,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      )
                                    ],
                                  ).paddingOnly(left: 7)
                                ],
                              ),
                            ).paddingOnly(right: 14, left: 8),
                          ),
                          Expanded(
                            child: Container(
                              height: Get.height * 0.07,
                              decoration: BoxDecoration(
                                color: AppColors.white.withValues(alpha: 0.30),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    height: 70,
                                    width: 45,
                                    decoration: BoxDecoration(color: AppColors.white.withValues(alpha: 0.50)),
                                  ).paddingAll(7),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.25,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      ).paddingOnly(bottom: 7),
                                      Container(
                                        height: Get.height * 0.014,
                                        width: Get.width * 0.15,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(10),
                                          color: AppColors.white.withValues(alpha: 0.50),
                                        ),
                                      )
                                    ],
                                  ).paddingOnly(left: 7)
                                ],
                              ),
                            ).paddingOnly(right: 14, left: 8),
                          ),
                        ],
                      ).paddingOnly(bottom: 25),
                    ],
                  ),
                ),
              ],
            ),
            // Positioned(
            //   bottom: -10,
            //   child: Container(
            //     height: Get.height * 0.08,
            //     width: Get.width,
            //     color: AppColors.white.withValues(alpha: 0.50),
            //   ),
            // )
          ],
        ),
      ),
    );
  }
}

class ProductDetailBottomShimmer extends StatelessWidget {
  const ProductDetailBottomShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: Container(
        height: Get.height * 0.08,
        width: Get.width,
        color: AppColors.white.withValues(alpha: 0.80),
      ),
    );
  }
}
