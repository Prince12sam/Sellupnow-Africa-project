import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:shimmer/shimmer.dart';

class MyAdsScreenShimmer extends StatelessWidget {
  const MyAdsScreenShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Color(0xffEBEDF9),
      highlightColor: Color(0xffF3F5FD),
      child: ListView.builder(
        padding: EdgeInsets.only(bottom: 30),
        itemCount: 5,
        shrinkWrap: true,
        itemBuilder: (context, index) {
          return Column(
            children: [
              Container(
                decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(15),
                    border: Border.all(color: AppColors.lightGreyBorder),
                    color: AppColors.white.withValues(alpha: 0.3)),
                child: Row(
                  children: [
                    Container(
                      height: Get.height * 0.15,
                      width: Get.height * 0.16,
                      decoration: BoxDecoration(
                        color: AppColors.lightGrey,
                        borderRadius: BorderRadius.only(
                          bottomLeft: Radius.circular(12),
                          topLeft: Radius.circular(12),
                        ),
                      ),
                    ).paddingOnly(bottom: 2, left: 2, top: 2),
                    10.width,
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            height: 20,
                            width: 100,
                            decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(6)),
                          ).paddingOnly(bottom: 8, top: 6),
                          // 8.height,
                          Container(
                            height: 20,
                            width: 180,
                            decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(6)),
                          ).paddingOnly(bottom: 8, right: 10),
                          // 8.height,
                          Row(
                            children: [
                              Container(
                                height: 20,
                                width: 80,
                                decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(6)),
                              ).paddingOnly(right: 6),
                            ],
                          ).paddingOnly(bottom: 8),
                          // Row(
                          //   children: [
                          //     Image.asset(
                          //       AppAsset.eyeIcon,
                          //       height: 13,
                          //       width: 16,
                          //     ),
                          //     5.width,
                          //     Text(
                          //       "${EnumLocale.txtViews.name.tr} : ",
                          //       style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.grey),
                          //     ),
                          //     Text(
                          //       controller.allAdsList[index].viewsCount.toString(),
                          //       style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.black),
                          //     ),
                          //     14.width,
                          //     Image.asset(
                          //       AppAsset.favouriteIcon,
                          //       width: 14,
                          //       height: 14,
                          //     ),
                          //     5.width,
                          //     Text(
                          //       "${EnumLocale.txtLikes.name.tr} : ",
                          //       style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.grey),
                          //     ),
                          //     Text(
                          //       controller.allAdsList[index].likesCount.toString(),
                          //       style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.black),
                          //     ),
                          //   ],
                          // ).paddingOnly(bottom: 8)
                          Row(
                            children: [
                              Container(
                                height: 20,
                                width: 80,
                                decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(6)),
                              ).paddingOnly(right: 6),
                              Container(
                                height: 20,
                                width: 80,
                                decoration: BoxDecoration(color: AppColors.lightGrey, borderRadius: BorderRadius.circular(6)),
                              ),
                            ],
                          )
                        ],
                      ),
                    )
                  ],
                ),
              ).paddingOnly(bottom: 10),
            ],
          ).paddingSymmetric(horizontal: 14);
        },
      ),
    );
  }
}
