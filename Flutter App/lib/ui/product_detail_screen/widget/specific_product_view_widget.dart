import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/product_detail_screen/controller/specific_product_view_show_controller.dart';
import 'package:listify/ui/product_detail_screen/shimmer/views_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SpecificViewShow extends StatelessWidget {
  const SpecificViewShow({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SpecificProductViewShowController>(
      id: Constant.productView,
      builder: (controller) {
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: () async {
            await controller.init();
          },
          child: controller.isLoading
              ? ViewsShimmer()
              : controller.viewList.isEmpty
                  ? SizedBox(
                      height: Get.height * 0.76,
                      child: Center(
                        child: NoDataFound(image: AppAsset.noProductFound, imageHeight: 160, text: EnumLocale.txtNoDataFound.name.tr),
                      ),
                    )
                  : ListView.builder(
                      physics: const AlwaysScrollableScrollPhysics(), // scroll + refresh enable
                      shrinkWrap: true,
                      itemCount: controller.viewList.length,
                      itemBuilder: (context, index) {
                        return SpecificAdViewItemView(
                          name: controller.viewList[index].user?.name,
                          profileImage: controller.viewList[index].user?.profileImage,
                          id: controller.viewList[index].ad,
                        );
                      },
                    ),
        );
      },
    );
  }
}

class SpecificAdViewItemView extends StatefulWidget {
  final String? name;
  final String? profileImage;
  final String? id;

  const SpecificAdViewItemView({super.key, this.name, this.profileImage, this.id});

  @override
  State<SpecificAdViewItemView> createState() => _SpecificAdViewItemViewState();
}

class _SpecificAdViewItemViewState extends State<SpecificAdViewItemView> {
  void Function()? onTap;

  @override
  Widget build(BuildContext context) {
    return Row(
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
            // Text(
            //   id ?? "",
            //   style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.black),
            // ),
          ],
        ),
      ],
    ).paddingOnly(left: 8 );
  }
}

class SpecificAdViewShowAppBar extends StatelessWidget {
  final String? title;
  const SpecificAdViewShowAppBar({super.key, this.title});

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
