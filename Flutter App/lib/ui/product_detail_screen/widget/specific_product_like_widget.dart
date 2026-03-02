import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/ui/message_screen/shimmer/chat_list_shimmer.dart';
import 'package:listify/ui/product_detail_screen/controller/specific_product_like_show_controller.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/font_style.dart';

class SpecificLikeShow extends StatelessWidget {
  const SpecificLikeShow({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SpecificProductLikeShowController>(
      id: Constant.productLike,
      builder: (controller) {
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: () async {
            await controller.init();
          },
          child: controller.isLoading
              ? ChatListShimmer()
              : controller.likeList.isEmpty
                  ? ListView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      children: [
                        SizedBox(height: Get.height * 0.4),
                        Center(
                          child: Text(
                            "No Like User",
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w500,
                              color: Colors.grey,
                            ),
                          ),
                        ),
                      ],
                    )
                  : ListView.builder(
                      physics: const AlwaysScrollableScrollPhysics(), // scroll + refresh enable
                      shrinkWrap: true,
                      itemCount: controller.likeList.length,
                      itemBuilder: (context, index) {
                        return SpecificAdLikeItemView(
                          name: controller.likeList[index].user?.name,
                          profileImage: controller.likeList[index].user?.profileImage,
                          id: controller.likeList[index].ad,
                        );
                      },
                    ),
        );
      },
    );
  }
}

class SpecificAdLikeItemView extends StatefulWidget {
  final String? name;
  final String? profileImage;
  final String? id;

  SpecificAdLikeItemView({super.key, this.name, this.profileImage, this.id});

  @override
  State<SpecificAdLikeItemView> createState() => _SpecificAdLikeItemViewState();
}

class _SpecificAdLikeItemViewState extends State<SpecificAdLikeItemView> {
  void Function()? onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: AppColors.black.withValues(alpha: 0.05),
            spreadRadius: 2,
            blurRadius: 7,
            offset: Offset(0, 0),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            height: 45,
            width: 45,
            clipBehavior: Clip.hardEdge,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
            child: CustomProfileImage(image: widget.profileImage ?? ""),
          ).paddingOnly(left: 6, top: 8, bottom: 8, right: 6),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                widget.name ?? "",
                style: AppFontStyle.fontStyleW700(fontSize: 14, fontColor: AppColors.black),
              ),
              Text(
                widget.id ?? "",
                style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.black),
              ),
            ],
          ),
        ],
      ),
    ).paddingOnly(left: 16, right: 16, top: 16);
  }
}

class SpecificAdLikeShowAppBar extends StatelessWidget {
  final String? title;
  const SpecificAdLikeShowAppBar({super.key, this.title});

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
