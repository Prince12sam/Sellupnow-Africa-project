import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/ui/fashion_blog_screen/controller/fashion_blog_screen_controller.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class FashionBlogScreenAppBar extends StatelessWidget {
  final String? title;
  const FashionBlogScreenAppBar({super.key, this.title});

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

class FashionBlogScreenWidget extends StatelessWidget {
  const FashionBlogScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<FashionBlogScreenController>(
        id: Constant.idBlog,
        builder: (controller) {
          final blog = controller.blogByIdResponse?.data;
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              SizedBox(
                width: Get.width,
                height: Get.height * 0.32,
                child: CustomImageView(
                  image: controller.blogByIdResponse?.data?.image ?? '',
                ),
              ),
              Align(
                alignment: Alignment.centerRight,
                child: Text(
                  "${EnumLocale.txtPublished.name.tr} ${controller.formatBlogDate(blog?.createdAt.toString())}",
                  style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.popularProductText),
                ).paddingOnly(top: 10, bottom: 16, right: 10),
              ),
              Text(
                blog?.title ?? '',
                style: AppFontStyle.fontStyleW800(height: 0, fontSize: 30, fontColor: AppColors.black),
              ).paddingOnly(left: 14, bottom: 2),
              Text(
                blog?.description ?? '',
                style: AppFontStyle.fontStyleW500(fontSize: 11, fontColor: AppColors.popularProductText),
              ).paddingOnly(left: 14, right: 14, bottom: 18),
            ],
          );
        });
  }
}
