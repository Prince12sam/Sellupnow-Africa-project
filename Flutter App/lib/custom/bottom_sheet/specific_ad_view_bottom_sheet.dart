import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/ui/product_detail_screen/shimmer/views_shimmer.dart';
import 'package:listify/ui/product_detail_screen/widget/specific_product_view_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SpecificAdViewBottomSheet extends StatelessWidget {
  final ProductDetailScreenController controller;
  const SpecificAdViewBottomSheet({super.key, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: Get.height * 0.95,
      color: Colors.transparent,
      child: DraggableScrollableSheet(
        expand: false,
        minChildSize: 0.30, // 30% of parent
        initialChildSize: 0.80, // open at 80%
        maxChildSize: 0.95, // up
        builder: (context, scrollController) {
          return Container(
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(28),
              color: AppColors.white,
            ),
            child: SafeArea(
              top: false,
              child: SizedBox(
                // ❗️Give a bounded height so Expanded works
                height: Get.height * 0.80,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header
                    Container(
                      width: Get.width,
                      decoration: BoxDecoration(
                        color: AppColors.lightGrey100,
                        borderRadius:
                            const BorderRadius.vertical(top: Radius.circular(20)),
                      ),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Spacer(),
                          Text(
                            EnumLocale.txtViews.name.tr,
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
                        id: Constant.productView,
                        builder: (controller) {
                          return RefreshIndicator(
                            color: AppColors.appRedColor,
                            onRefresh: () async {
                              await controller.init();
                            },
                            child: controller.isViewLoading
                                ? ViewsShimmer()
                                : controller.viewList.isEmpty
                                    ? SizedBox(
                                        height: Get.height * 0.76,
                                        child: Center(
                                          child: NoDataFound(
                                              image: AppAsset.noProductFound,
                                              imageHeight: 160,
                                              text: EnumLocale
                                                  .txtNoDataFound.name.tr),
                                        ),
                                      )
                                    : ListView.builder(
                              padding: EdgeInsets.only(top: 6),
                                        physics:
                                            const AlwaysScrollableScrollPhysics(), // scroll + refresh enable
                                        shrinkWrap: true,
                                        itemCount: controller.viewList.length,
                                        itemBuilder: (context, index) {
                                          return SpecificAdViewItemView(
                                            name: controller
                                                .viewList[index].user?.name,
                                            profileImage: controller
                                                .viewList[index]
                                                .user
                                                ?.profileImage,
                                            id: controller.viewList[index].ad,
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
        },
      ),
    );
  }
}

// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
// import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
// import 'package:listify/ui/product_detail_screen/shimmer/views_shimmer.dart';
// import 'package:listify/ui/product_detail_screen/widget/specific_product_view_widget.dart';
// import 'package:listify/utils/app_asset.dart';
// import 'package:listify/utils/app_color.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/enums.dart';
// import 'package:listify/utils/font_style.dart';
//
// class SpecificAdViewBottomSheet extends StatelessWidget {
//   final ProductDetailScreenController controller;
//   const SpecificAdViewBottomSheet({super.key, required this.controller});
//
//   @override
//   Widget build(BuildContext context) {
//     // Full-height container so sheet can expand up to 95%:
//     return ClipRRect(
//       borderRadius: BorderRadius.circular(28),
//       child: Container(
//         color: Colors.transparent, // keep bg transparent for rounded corners
//         child: SizedBox(
//           height: Get.height * 0.95,
//           child: DraggableScrollableSheet(
//             expand: false,
//             minChildSize: 0.30, // 30% of parent
//             initialChildSize: 0.80, // open at 80%
//             maxChildSize: 0.95, // up to 95%
//             builder: (context, scrollController) {
//               return Container(
//                 decoration: BoxDecoration(
//                   color: AppColors.white,
//                   borderRadius:
//                   const BorderRadius.vertical(top: Radius.circular(28)),
//                 ),
//                 child: SafeArea(
//                   top: false,
//                   child: Column(
//                     crossAxisAlignment: CrossAxisAlignment.start,
//                     children: [
//                       // Handle + Header
//                       Padding(
//                         padding: const EdgeInsets.only(top: 0),
//                         child: Center(
//                           child: Container(
//                             width: 40,
//                             height: 4,
//                             decoration: BoxDecoration(
//                               color: AppColors.lightGrey100,
//                               borderRadius: BorderRadius.circular(28),
//                             ),
//                           ),
//                         ),
//                       ),
//                       Container(
//                         width: Get.width,
//                         decoration: BoxDecoration(
//                           color: AppColors.lightGrey100,
//                           borderRadius: const BorderRadius.vertical(
//                               top: Radius.circular(28)),
//                         ),
//                         child: Row(
//                           crossAxisAlignment: CrossAxisAlignment.start,
//                           children: [
//                             const Spacer(),
//                             Text(
//                               EnumLocale.txtViews.name.tr,
//                               style: AppFontStyle.fontStyleW700(
//                                 fontSize: 18,
//                                 fontColor: AppColors.black,
//                               ),
//                             ).paddingOnly(left: 30, bottom: 19, top: 19),
//                             const Spacer(),
//                             InkWell(
//                               onTap: () => Get.back(),
//                               child: Image.asset(
//                                 AppAsset.closeFillIcon,
//                                 width: 30,
//                               ).paddingOnly(top: 14),
//                             ),
//                           ],
//                         ).paddingSymmetric(horizontal: 16),
//                       ),
//
//                       // Body (assign the provided scrollController here!)
//                       Expanded(
//                         child: GetBuilder<ProductDetailScreenController>(
//                           id: Constant.productView,
//                           builder: (_) {
//                             return RefreshIndicator(
//                               color: AppColors.appRedColor,
//                               onRefresh: () async {
//                                 await controller
//                                     .specificProductView(controller.adId ?? "");
//                               },
//                               child: controller.isViewLoading
//                                   ? const ViewsShimmer()
//                                   : (controller.viewList.isEmpty)
//                                   ? SizedBox(
//                                 height: Get.height * 0.76,
//                                 child: Center(
//                                   child: NoDataFound(
//                                     image: AppAsset.noProductFound,
//                                     imageHeight: 160,
//                                     text: EnumLocale
//                                         .txtNoDataFound.name.tr,
//                                   ),
//                                 ),
//                               )
//                                   : ListView.builder(
//                                 controller:
//                                 scrollController, // << important!
//                                 physics:
//                                 const AlwaysScrollableScrollPhysics(),
//                                 itemCount: controller.viewList.length,
//                                 itemBuilder: (context, index) {
//                                   final v =
//                                   controller.viewList[index];
//                                   return SpecificAdViewItemView(
//                                     name: v.user?.name,
//                                     profileImage:
//                                     v.user?.profileImage,
//                                     id: v.ad,
//                                   );
//                                 },
//                               ),
//                             );
//                           },
//                         ),
//                       ),
//                     ],
//                   ),
//                 ),
//               );
//             },
//           ),
//         ),
//       ),
//     );
//   }
// }
