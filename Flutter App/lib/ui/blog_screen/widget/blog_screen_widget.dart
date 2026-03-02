import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/blog_screen/controller/blog_screen_controller.dart';
import 'package:listify/ui/blog_screen/shimmer/all_blog_shimmer.dart';
import 'package:listify/ui/blog_screen/shimmer/trending_blog_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class BlogScreenAppBar extends StatelessWidget {
  final String? title;
  const BlogScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class BlogScreenWidget extends StatelessWidget {
  const BlogScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<BlogScreenController>(
        id: Constant.idBlog,
        builder: (controller) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // trending blog
              Text(
                EnumLocale.txtTrendingBlog.name.tr,
                style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
              ).paddingOnly(left: 14, right: 14, top: 16, bottom: 14),
              SizedBox(
                height: 200,
                child: ListView.builder(
                  shrinkWrap: true,
                  physics: BouncingScrollPhysics(),
                  itemCount: controller.trendingBlogResponse?.data?.length,
                  scrollDirection: Axis.horizontal,
                  itemBuilder: (context, index) {
                    final trendingBlog = controller.trendingBlogResponse?.data?[index];

                    return Container(
                      width: 165,
                      padding: EdgeInsets.all(1),
                      decoration:
                          BoxDecoration(borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.borderColor, width: 0.8)),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Center(
                            child: SizedBox(
                              height: 114,
                              child: ClipRRect(
                                borderRadius: BorderRadius.vertical(top: Radius.circular(10)),
                                // child: CustomImageView(
                                //   image: trendingBlog?.image ?? '',
                                //   fit: BoxFit.fill,
                                // ),
                              ),
                            ),
                          ),
                          Text(
                            trendingBlog?.title ?? '',
                            style: AppFontStyle.fontStyleW700(fontSize: 11, fontColor: AppColors.black),
                          ).paddingOnly(left: 4, right: 5, top: 2, bottom: 1),
                          Text(
                            trendingBlog?.description ?? '',
                            style: AppFontStyle.fontStyleW500(fontSize: 8, fontColor: AppColors.searchText),
                          ).paddingOnly(left: 4, right: 8, bottom: 3),
                        ],
                      ),
                    ).paddingOnly(right: 8);
                  },
                ).paddingOnly(left: 14, bottom: 20),
              ),
              // our fashion blog
              Text(
                EnumLocale.txtOurFashionBlog.name.tr,
                style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
              ).paddingOnly(left: 14, bottom: 16),
              // controller.blogResponseModel?.data?.isEmpty == false
              //     ? NoDataFound(image: AppAsset.noBlogFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr)
              //     : ListView.builder(
              //         physics: NeverScrollableScrollPhysics(),
              //         shrinkWrap: true,
              //         itemCount: controller.blogResponseModel?.data?.length,
              //         itemBuilder: (context, index) {
              //           final blog = controller.blogResponseModel?.data?[index];
              //           return GestureDetector(
              //             onTap: () {
              //               Get.toNamed(AppRoutes.fashionBlogScreen);
              //             },
              //             child: Container(
              //               width: Get.width,
              //               padding: EdgeInsets.all(2),
              //               decoration:
              //                   BoxDecoration(borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.borderColor, width: 0.8)),
              //               child: Column(
              //                 crossAxisAlignment: CrossAxisAlignment.start,
              //                 children: [
              //                   SizedBox(
              //                     width: Get.width,
              //                     height: 220,
              //                     child: ClipRRect(
              //                       borderRadius: BorderRadius.circular(10),
              //                       child: CustomImageView(
              //                         image: blog?.image ?? '',
              //                         fit: BoxFit.cover,
              //                       ),
              //                     ),
              //                   ),
              //                   Text(
              //                     blog?.description ?? '',
              //                     style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.searchText),
              //                   ).paddingOnly(left: 6, right: 8, bottom: 5, top: 3),
              //                 ],
              //               ),
              //             ).paddingOnly(left: 14, right: 14, bottom: 12),
              //           );
              //         },
              //       )
            ],
          );
        });
  }
}

class BlogScreenWidget1 extends StatelessWidget {
  const BlogScreenWidget1({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<BlogScreenController>(
      id: Constant.idBlog,
      builder: (controller) {
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              EnumLocale.txtTrendingBlog.name.tr,
              style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
            ).paddingOnly(left: 14, right: 14, top: 16, bottom: 14),

            // Trending Blog List...
            controller.isLoading
                ? TrendingBlogShimmer()
                : controller.blogResponseModel?.data?.isEmpty == true
                    ? SizedBox(
                        height: 200,
                        child: Center(child: NoDataFound(image: AppAsset.noBlogFound, imageHeight: 110, text: EnumLocale.txtNotFoundBlog.name.tr)))
                    : SizedBox(
                        height: 200,
                        child: ListView.builder(
                          shrinkWrap: true,
                          physics: BouncingScrollPhysics(),
                          itemCount: controller.trendingBlogResponse?.data?.length ?? 0,
                          scrollDirection: Axis.horizontal,
                          itemBuilder: (context, index) {
                            final trendingBlog = controller.trendingBlogResponse?.data?[index];
                            return Container(
                              width: 165,
                              padding: EdgeInsets.all(1),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: AppColors.borderColor, width: 0.8),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Center(
                                    child: SizedBox(
                                      height: 114,
                                      child: ClipRRect(
                                        borderRadius: BorderRadius.vertical(top: Radius.circular(10)),
                                        child: CustomImageView(
                                          image: trendingBlog?.image ?? '',
                                          fit: BoxFit.fill,
                                        ),
                                      ),
                                    ),
                                  ),
                                  Text(
                                    trendingBlog?.title ?? '',
                                    style: AppFontStyle.fontStyleW700(fontSize: 11, fontColor: AppColors.black),
                                  ).paddingOnly(left: 4, right: 5, top: 2, bottom: 1),
                                  Text(
                                    trendingBlog?.description ?? '',
                                    style: AppFontStyle.fontStyleW500(fontSize: 8, fontColor: AppColors.popularProductText),
                                  ).paddingOnly(left: 4, right: 8, bottom: 3),
                                ],
                              ),
                            ).paddingOnly(right: 8);
                          },
                        ).paddingOnly(left: 14, bottom: 20),
                      ),

            // Our Fashion Blog List...
            Text(
              EnumLocale.txtOurFashionBlog.name.tr,
              style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
            ).paddingOnly(left: 14, bottom: 16),

            controller.isLoading
                ? AllBlogShimmer()
                : controller.blogResponseModel?.data?.isEmpty == true
                    ? SizedBox(
                        height: Get.height * 0.5,
                        child: NoDataFound(image: AppAsset.noBlogFound, imageHeight: 140, text: EnumLocale.txtNotFoundBlog.name.tr))
                    : ListView.builder(
                        physics: NeverScrollableScrollPhysics(),
                        shrinkWrap: true,
                        itemCount: controller.blogResponseModel?.data?.length ?? 0,
                        itemBuilder: (context, index) {
                          final blog = controller.blogResponseModel?.data?[index];
                          return GestureDetector(
                            onTap: () {
                              Get.toNamed(AppRoutes.fashionBlogScreen, arguments: blog?.id);
                            },
                            child: Container(
                              width: Get.width,
                              padding: EdgeInsets.all(2),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: AppColors.borderColor, width: 0.8),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SizedBox(
                                    width: Get.width,
                                    height: 220,
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(10),
                                      child: CustomImageView(
                                        image: blog?.image ?? '',
                                        fit: BoxFit.cover,
                                      ),
                                    ),
                                  ),
                                  Text(
                                    blog?.description ?? '',
                                    style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.popularProductText),
                                  ).paddingOnly(left: 6, right: 8, bottom: 5, top: 3),
                                ],
                              ),
                            ).paddingOnly(left: 14, right: 14, bottom: 12),
                          );
                        },
                      )
          ],
        );
      },
    );
  }
}
