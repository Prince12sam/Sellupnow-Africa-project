import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/add_listing_screen/controller/add_listing_screen_controller.dart';
import 'package:listify/ui/categories_screen/shimmer/all_category_shimmer.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class AddListingScreenAppBar extends StatelessWidget {
  final String? title;
  const AddListingScreenAppBar({super.key, this.title});

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

class SelectCategoriesView extends StatelessWidget {
  const SelectCategoriesView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<AddListingScreenController>(
        id: Constant.idAllCategory,
        builder: (controller) {
          return Expanded(
            child: SingleChildScrollView(
              physics: AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    "${EnumLocale.txtSelectCategory.name.tr}...",
                    style: AppFontStyle.fontStyleW700(
                      fontSize: 18,
                      fontColor: AppColors.black,
                    ),
                  ).paddingOnly(top: 18, left: 12, bottom: 3),
                  Text(
                    EnumLocale.txtAdListingDesText.name.tr,
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 12,
                      fontColor: AppColors.searchText,
                    ),
                  ).paddingOnly(left: 12),
                  controller.isLoading
                      ? AllCategoryShimmer()
                      : GridView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: controller
                                  .allCategoryResponseModel?.data?.length ??
                              0,
                          gridDelegate:
                              SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 3,
                            mainAxisSpacing: 8,
                            crossAxisSpacing: 8,
                            childAspectRatio: 0.9,
                          ),
                          itemBuilder: (context, index) {
                            return GestureDetector(
                              // onTap: () {
                              //   // Get.toNamed(AppRoutes.subCategoriesScreen, arguments: {"addListingScreen": true});
                              //   final category = controller.allCategoryResponseModel?.data?[index];
                              //   if (category != null) {
                              //     Get.toNamed(
                              //       AppRoutes.subCategoriesScreen,
                              //       arguments: {
                              //         "categories": category.children,
                              //         "addListingScreen": true,
                              //         "title": category.name,
                              //       },
                              //     );
                              //   }
                              // },

                              // onTap: () {
                              //   Utils.showLog("Category ID: ${controller.allCategoryResponseModel?.data?[index].id}");
                              //   Utils.showLog("Category name: ${controller.allCategoryResponseModel?.data?[index].name}");
                              //
                              //   Get.toNamed(
                              //     AppRoutes.subCategoriesScreen,
                              //     arguments: {
                              //       "categoryId": controller.allCategoryResponseModel?.data?[index].id,
                              //       "categoryTitle": controller.allCategoryResponseModel?.data?[index].name,
                              //       "addListingScreen": true,
                              //     },
                              //   );
                              //   // Database.onSetCategoryId(controller.allCategoryResponseModel?.data?[index].id ?? '');
                              //   // Utils.showLog("Database save category id  ::: ${controller.allCategoryResponseModel?.data?[index].id}");
                              // },

                              onTap: () async {

                                SubCategoryApi.startPagination = 0;
                                final isEmpty =
                                    await controller.getSubCategoryApi(
                                        controller.allCategoryList[index].id ??
                                            ""); // returns true if empty

                                // If no children, then navigate to EditProductView
                                if (isEmpty) {
                                  if (controller.addListingScreen == true) {
                                    Get.toNamed(AppRoutes.editProductView,
                                        arguments: {
                                          'categoryId': controller.allCategoryList[index].id,
                                          'categoryTitle': controller.allCategoryList[index].name,
                                        })?.then((value) {
                                      SubCategoryApi.startPagination = 0;
                                    });
                                  } else {
                                    Get.toNamed(AppRoutes.subCategoriesScreen,
                                        arguments: {
                                          "categoryId": controller
                                              .allCategoryResponseModel
                                              ?.data?[index]
                                              .id,
                                          "categoryTitle": controller
                                              .allCategoryResponseModel
                                              ?.data?[index]
                                              .name,
                                          "addListingScreen": true,
                                        })?.then((value) {
                                      SubCategoryApi.startPagination = 0;
                                    });
                                  }
                                } else {
                                  Utils.showLog('addListingScreen is false');
                                  controller.update(['appbar']);
                                  Get.toNamed(AppRoutes.subCategoriesScreen,
                                      arguments: {
                                        "categoryId": controller
                                            .allCategoryResponseModel
                                            ?.data?[index]
                                            .id,
                                        "categoryTitle": controller
                                            .allCategoryResponseModel
                                            ?.data?[index]
                                            .name,
                                        "addListingScreen": true,
                                      })?.then((value) {
                                    SubCategoryApi.startPagination = 0;
                                  });

                                }
                              },

                              child: Container(
                                decoration: BoxDecoration(
                                  color: AppColors.categoriesBgColor,
                                  borderRadius: BorderRadius.circular(16),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.center,
                                  children: [
                                    Container(
                                      height: 90,
                                      width: 110,
                                      padding: EdgeInsets.all(10),
                                      decoration: BoxDecoration(
                                          color: AppColors.white,
                                          borderRadius:
                                              BorderRadius.circular(12)),
                                      // child: CustomImageView(
                                      //   image: "",
                                      //   // height: 110,
                                      //   // width: 130,
                                      // ),
                                      child: ClipRRect(
                                        borderRadius: BorderRadius.circular(12),
                                        child: CustomImageView(
                                          image: controller
                                                  .allCategoryResponseModel
                                                  ?.data?[index]
                                                  .image ??
                                              "",
                                          fit: BoxFit.cover,
                                        ),
                                      ),
                                    ).paddingAll(4),
                                    Expanded(
                                      child: Text(
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        controller.allCategoryResponseModel
                                                ?.data?[index].name ??
                                            "",
                                        style: AppFontStyle.fontStyleW500(
                                            fontSize: 14,
                                            fontColor: AppColors.darkGrey),
                                      ).paddingOnly(
                                          left: 8, right: 8, top: 2, bottom: 0),
                                    )
                                  ],
                                ),
                              ),
                            );
                          },
                        ).paddingOnly(left: 12, right: 12, top: 20, bottom: 20),
                ],
              ),
            ),
          );
        });
  }
}
