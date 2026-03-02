import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/home_search_product_screen/controller/home_search_product_controller.dart';
import 'package:listify/ui/home_search_product_screen/widget/home_screen_product_screen_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class HomeScreenProductScreenView extends StatelessWidget {
  const HomeScreenProductScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: HomeSearchAppBar(
          title: EnumLocale.txtSearch.name.tr,
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
      floatingActionButton: Container(
        height: 55,
        width: Get.width * 0.6,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(30),
          color: AppColors.white,
          boxShadow: [
            BoxShadow(
              color: AppColors.black.withValues(alpha: 0.20),
              blurRadius: 18,
              spreadRadius: 0,
              offset: Offset(0, 0),
            ),
          ],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            GetBuilder<HomeSearchProductController>(builder: (controller) {
              return GestureDetector(
                onTap: () {
                  // Get.toNamed(
                  //   AppRoutes.productFilterScreen,
                  //   arguments: {
                  //     'filterScreen': true,
                  //     'search': controller.search,
                  //   },
                  // );

                  controller.openFilterAndApply();
                },
                child: Container(
                  padding: EdgeInsets.all(3),
                  color: Colors.transparent,
                  child: Row(
                    children: [
                      Image.asset(
                        AppAsset.filterIcon1,
                        height: 24,
                        width: 24,
                      ).paddingOnly(right: 10),
                      Text(
                        EnumLocale.txtFilter.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 14, fontColor: AppColors.black),
                      ),
                    ],
                  ),
                ),
              );
            }),
            Container(
              height: 30,
              width: 1,
              color: AppColors.borderColor,
            ).paddingSymmetric(horizontal: 20),
            GestureDetector(
              onTap: () {
                Get.bottomSheet(
                  SortByBottomSheet(),
                  isScrollControlled: true,
                  backgroundColor: Colors.transparent,
                  barrierColor: AppColors.black.withValues(alpha: 0.8),
                );
              },
              child: Container(
                padding: EdgeInsets.all(3),
                color: Colors.transparent,
                child: Row(
                  children: [
                    Image.asset(
                      AppAsset.sortByIcon,
                      height: 24,
                      width: 24,
                    ).paddingOnly(right: 10),
                    Text(
                      EnumLocale.txtSortby.name.tr,
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 14, fontColor: AppColors.black),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
      body: Column(
        children: [
          HomeSearchTopView(),
          ProductSHowView(),
        ],
      ),
    );
  }
}

///short bottom sheet
class SortByBottomSheet extends StatelessWidget {
  const SortByBottomSheet({super.key});

  @override
  Widget build(BuildContext context) {
    List sortByType = [
      EnumLocale.txtDefault.name.tr,
      EnumLocale.txtNewToOld.name.tr,
      EnumLocale.txtOldToNew.name.tr,
      EnumLocale.txtPriceHighToLow.name.tr,
      EnumLocale.txtPriceLowToHight.name.tr,
    ];
    return Container(
      // height: Get.height * 0.6,
      width: Get.width,
      // padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: Get.width,
            decoration: BoxDecoration(
              color: AppColors.lightGrey100,
              borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Spacer(),
                Text(
                  EnumLocale.txtSortby.name.tr,
                  style: AppFontStyle.fontStyleW700(
                    fontSize: 18,
                    fontColor: AppColors.black,
                  ),
                ).paddingOnly(left: 30, bottom: 19, top: 19),
                Spacer(),
                InkWell(
                  onTap: () {
                    Get.back();
                  },
                  child: Image.asset(
                    AppAsset.closeFillIcon,
                    width: 30,
                  ).paddingOnly(top: 14),
                )
              ],
            ).paddingSymmetric(horizontal: 16),
          ),
          GetBuilder<HomeSearchProductController>(builder: (controller) {
            return Column(
              children: List.generate(
                5,
                (index) {
                  return Column(
                    children: [
                      GestureDetector(
                        onTap: () {
                          final sortKey = controller.getSortKeyFromIndex(index);
                          controller.applySort(sortKey);
                          Get.back();
                        },
                        child: Container(
                          width: Get.width,
                          color: AppColors.transparent,
                          child: Text(
                            sortByType[index],
                            style: AppFontStyle.fontStyleW400(
                                fontSize: 17, fontColor: AppColors.black),
                          ).paddingSymmetric(vertical: 24, horizontal: 20),
                        ),
                      ),
                      Divider(
                        height: 0,
                        thickness: 0.8,
                        color: AppColors.lightGrey100,
                      ),
                    ],
                  );
                },
              ),
            );
          }),
          4.height,
        ],
      ),
    );
  }
}
