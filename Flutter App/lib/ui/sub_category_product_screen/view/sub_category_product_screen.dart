import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/sub_category_product_screen/controller/sub_category_product_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/widget/product_filter_screen_widget.dart';
import 'package:listify/ui/sub_category_product_screen/widget/sub_category_product_screen_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SubCategoryProductScreen extends StatelessWidget {
  const SubCategoryProductScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SubCategoryProductScreenController>(builder: (controller) {
      return Scaffold(
        backgroundColor: AppColors.white,
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: SubCategoriesProductScreenAppBar(
            title: capitalizeWords(controller.categoryTitle??''),
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
                color: AppColors.black.withValues(alpha: 0.1),
                blurRadius: 18,
                spreadRadius: 0,
                offset: Offset(0, 0),
              ),
            ],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              GestureDetector(
                onTap: () {
                  // Get.toNamed(
                  //   AppRoutes.productFilterScreen,
                  //   arguments: {
                  //     'filterScreen': true,
                  //     'categoryId': controller.categoryId,
                  //     'ad': controller.categoryWiseProductList,
                  //   },
                  // );

                  // onTap:
                  controller.openFilterAndApply();
                },
                child: Row(
                  children: [
                    Image.asset(
                      AppAsset.filterIcon1,
                      height: 24,
                      width: 24,
                    ).paddingOnly(right: 10),
                    Text(
                      EnumLocale.txtFilter.name.tr,
                      style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.black),
                    ),
                  ],
                ),
              ),
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
                child: Row(
                  children: [
                    Image.asset(
                      AppAsset.sortByIcon,
                      height: 24,
                      width: 24,
                    ).paddingOnly(right: 10),
                    Text(
                      EnumLocale.txtSortby.name.tr,
                      style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.black),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
        body: GetBuilder<SubCategoryProductScreenController>(
            id: Constant.idViewType,
            builder: (controller) {
              return Column(
                children: [
                  SearchView(),
                  controller.selectedView == ViewType.grid ? const GridProductView() : const ListProductView(),
                ],
              );
            }),
      );
    });
  }
}
