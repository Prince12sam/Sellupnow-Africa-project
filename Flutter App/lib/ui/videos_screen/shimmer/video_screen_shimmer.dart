import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:shimmer/shimmer.dart';

class VideoScreenShimmer extends StatelessWidget {
  const VideoScreenShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Container(
            height: Get.height,
            width: Get.width,
            margin: const EdgeInsets.only(bottom: 4),
            decoration: BoxDecoration(
              color: AppColors.white.withValues(alpha: 0.50),
            ),
          ),
          Positioned(
            right: 16,
            bottom: 36,
            child: Column(
              children: [
                Container(
                  height: 42,
                  width: 42,
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.white,
                  ),
                ).paddingOnly(bottom: 24),
                Container(
                  height: 42,
                  width: 42,
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.white,
                  ),
                ).paddingOnly(bottom: 24),
                Container(
                  height: 42,
                  width: 42,
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.white,
                  ),
                ).paddingOnly(bottom: 24),
                Container(
                  height: 42,
                  width: 42,
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.white,
                  ),
                ).paddingOnly(bottom: 24),
                Container(
                  height: 42,
                  width: 42,
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.white,
                  ),
                ).paddingOnly(bottom: 24),
              ],
            ),
          ),
          Positioned(
              left: 16,
              bottom: 16,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    height: 26,
                    width: 147,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(55),
                      color: AppColors.white,
                    ),
                  ).paddingOnly(bottom: 14),
                  Container(
                    height: 98,
                    width: 276,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(15),
                      color: AppColors.white,
                    ),
                  ).paddingOnly(bottom: 14),
                  Row(
                    children: [
                      Container(
                        height: 46,
                        width: 46,
                        clipBehavior: Clip.hardEdge,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: AppColors.white,
                        ),
                      ).paddingOnly(right: 5),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            height: 11,
                            width: 100,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(15),
                              color: AppColors.white,
                            ),
                          ).paddingOnly(bottom: 4),
                          Container(
                            height: 11,
                            width: 170,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(15),
                              color: AppColors.white,
                            ),
                          ),
                        ],
                      )
                    ],
                  ).paddingOnly(bottom: 10),
                  Container(
                    height: 11,
                    width: 200,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(15),
                      color: AppColors.white,
                    ),
                  ).paddingOnly(bottom: 4),
                  Container(
                    height: 11,
                    width: 260,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(15),
                      color: AppColors.white,
                    ),
                  ).paddingOnly(bottom: 4),
                ],
              ))
        ],
      ),
    );
  }
}
