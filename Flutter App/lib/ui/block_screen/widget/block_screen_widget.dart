// ignore_for_file: must_be_immutable

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/block_screen/controller/block_screen_controller.dart';
import 'package:listify/ui/message_screen/shimmer/chat_list_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class BlockScreenAppBar extends StatelessWidget {
  final String? title;
  const BlockScreenAppBar({super.key, this.title});

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

class BlockListView extends StatelessWidget {
  const BlockListView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<BlockScreenController>(
      id: Constant.blockList,
      builder: (controller) {
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: () => controller.getBlockList(),
          child: controller.isLoading
              ? const ChatListShimmer()
              : controller.blockedUsers.isEmpty
                  ? ListView(
                      children: [
                        SizedBox(height: Get.height * 0.25),
                        NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr),
                        SizedBox(height: Get.height * 0.25),
                      ],
                    )
                  : ListView.builder(
                      itemCount: controller.blockedUsers.length,
                      itemBuilder: (context, index) {
                        final user = controller.blockedUsers[index];
                        return BlockListItemView(
                          name: user.blockedId?.name ?? "",
                          profileImage: user.blockedId?.profileImage ?? "",
                          id: user.blockedId?.profileId ?? "",
                          onTap: () {
                            controller.unBlockApi(controller.blockedUsers[index].blockedId!.id.toString());
                          },
                        );
                      },
                    ),
        );
      },
    );
  }
}

class BlockListItemView extends StatelessWidget {
  final String? name;
  final String? profileImage;
  final String? id;
  void Function()? onTap;
  BlockListItemView({super.key, this.name, this.profileImage, this.id, this.onTap});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.redColorBorder,
        borderRadius: BorderRadius.circular(10),
border: Border.all(color: AppColors.black.withValues(alpha: 0.06))
      ),
      child: Row(
        children: [
          Container(
            height: 48,
            width: 48,
            clipBehavior: Clip.hardEdge,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
            child: CustomProfileImage(image: profileImage ?? ""),
          ).paddingOnly(left: 8, top: 8, bottom: 8, right: 8),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                name ?? "",
                style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),
              ),
              Text(
                id ?? "",
                style: AppFontStyle.fontStyleW500(fontSize: 15, fontColor: AppColors.searchText),
              ),
            ],
          ),
          Spacer(),
          GestureDetector(
            onTap: onTap,
            child: Container(
              decoration: BoxDecoration(
                color: AppColors.appRedColor,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Row(
                children: [
                  Image.asset(AppAsset.unBlock,height: 20,).paddingOnly(right: 6),
                  Text(
                    "UnBlock",
                    style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.white),
                  ).paddingOnly( bottom: 10, top: 10,),
                ],
              ).paddingSymmetric(horizontal: 12),
            ).paddingOnly(right: 6),
          )
        ],
      ),
    ).paddingOnly(left: 16, right: 16, top: 16);
  }
}
