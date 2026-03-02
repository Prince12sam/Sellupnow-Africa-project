import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/ui/product_detail_screen/controller/specific_product_view_show_controller.dart';
import 'package:listify/ui/product_detail_screen/shimmer/views_shimmer.dart';
import 'package:listify/ui/product_detail_screen/widget/specific_product_view_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SpecificAdLikeBottomSheet extends StatelessWidget {
  final ProductDetailScreenController controller;
  const SpecificAdLikeBottomSheet({super.key, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(28),
        color: AppColors.white,
      ),
      child: SafeArea(
        top: false,
        child: SizedBox( // ❗️Give a bounded height so Expanded works
          height: Get.height * 0.80,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header
              Container(
                width: Get.width,
                decoration: BoxDecoration(
                  color: AppColors.lightGrey100,
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Spacer(),
                    Text(
                      EnumLocale.txtFavorites.name.tr,
                      style: AppFontStyle.fontStyleW700(
                        fontSize: 18,
                        fontColor: AppColors.black,
                      ),
                    ).paddingOnly(left: 30, bottom: 15, top: 15),
                    const Spacer(),
                    InkWell(
                      onTap: () => Get.back(),
                      child: Image.asset(
                        AppAsset.closeFillIcon,
                        width: 30,
                      ).paddingOnly(top: 14),
                    ),
                  ],
                ).paddingSymmetric(horizontal: 16),
              ),

              // Body
              Expanded(
                child: GetBuilder<ProductDetailScreenController>(
                  id: Constant.productLike,
                  builder: (controller) {
                    return RefreshIndicator(
                      color: AppColors.appRedColor,
                      onRefresh: () async {
                        await controller.init();
                      },
                      child: controller.isLikeLoading
                          ? ViewsShimmer()
                          : controller.likeList.isEmpty
                          ? SizedBox(
                        height: Get.height * 0.76,
                        child: Center(
                          child: NoDataFound(image: AppAsset.noProductFound, imageHeight: 160, text: EnumLocale.txtNoDataFound.name.tr),
                        ),
                      )
                          : ListView.builder(
                        padding: EdgeInsets.only(top: 6),
                        physics: const AlwaysScrollableScrollPhysics(), // scroll + refresh enable
                        shrinkWrap: true,
                        itemCount: controller.likeList.length,
                        itemBuilder: (context, index) {
                          return SpecificAdViewItemView(
                            name: controller.likeList[index].user?.name,
                            profileImage: controller.likeList[index].user?.profileImage,
                            id: controller.likeList[index].ad,
                          );
                        },
                      ),
                    );
                  },
                ),
              ),

              // GetBuilder<ProductDetailScreenController>(builder: (controller) {
              //   return ListView.builder(
              //               physics: const AlwaysScrollableScrollPhysics(), // scroll + refresh enable
              //               shrinkWrap: true,
              //               itemCount: controller.viewList.length,
              //               itemBuilder: (context, index) {
              //                 return SpecificAdViewItemView(
              //                   name: controller.viewList[index].user?.name,
              //                   profileImage: controller.viewList[index].user?.profileImage,
              //                   id: controller.viewList[index].ad,
              //                 );
              //               },
              //             );
              // },)
            ],
          ),
        ),
      ),
    );
  }
}
