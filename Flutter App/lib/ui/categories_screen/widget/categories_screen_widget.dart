import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/categories_screen/controller/categories_screen_controller.dart';
import 'package:listify/ui/categories_screen/shimmer/all_category_shimmer.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/service/select_bus_service.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class CategoriesScreenAppBar extends StatelessWidget {
  final String? title;
  const CategoriesScreenAppBar({super.key, this.title});

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

class CategoriesScreenWidget extends StatelessWidget {
  const CategoriesScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<CategoriesScreenController>(
        id: Constant.idAllCategory,
        builder: (controller) {
          return controller.isLoading
              ? AllCategoryShimmer()
              : GridView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: controller.allCategoryList.length,
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 3,
                    mainAxisSpacing: 8,
                    crossAxisSpacing: 8,
                    childAspectRatio: 0.92,
                  ),
                  itemBuilder: (context, index) {
                    return GestureDetector(
                      // onTap: () {
                      //   Get.toNamed(
                      //     AppRoutes.subCategoriesScreen,
                      //     arguments: {
                      //       "categoryId": controller.allCategoryResponseModel?.data?[index].id,
                      //       "title": controller.allCategoryResponseModel?.data?[index].name,
                      //       "search": controller.search,
                      //       "popular": controller.popular,
                      //       "mostLike": controller.mostLike,
                      //     },
                      //   );
                      //   // Get.toNamed(AppRoutes.subCategoriesScreen);
                      // },

                      onTap: () async {

                        Utils.showLog("controller.allCategoryList[index].id${controller.allCategoryList[index].id}");
                        SubCategoryApi.startPagination = 0;
                        final isEmpty = await controller.getSubCategoryApi(
                            controller.allCategoryList[index].id ?? "");

                        if (isEmpty) {
                          if (controller.search == true ||
                              controller.popular == true ||
                              controller.mostLike == true) {
                            // Get.close(2);
                            // Utils.showLog("select id:::::::::::::${selectedId}");

                            // Get.find<SelectionBus>().setSelection(
                            //   id: selectedId,
                            //   title: selectedTitle,
                            //   image: selectedImage, // 👈 pass image
                            // );

                            Get.find<SelectionBus>().setSelection(
                                id: controller.allCategoryList[index].id ?? "",
                                title: controller.allCategoryList[index].name ??
                                    "",
                                image:
                                    controller.allCategoryList[index].image ??
                                        "");
                            isEmpty?Get.close(1): Get.close(2); // 2 screen back
                          } else {
                            Utils.showLog("empty enter.................");

                            Get.toNamed(AppRoutes.subCategoryProductScreen,
                                arguments: {
                                  'categoryId': controller
                                      .allCategoryResponseModel
                                      ?.data?[index]
                                      .id,
                                  'categoryTitle': controller
                                      .allCategoryResponseModel
                                      ?.data?[index]
                                      .name,
                                  "search": controller.search,
                                  "popular": controller.popular,
                                  "mostLike": controller.mostLike,
                                })?.then((value) {
                              SubCategoryApi.startPagination = 0;
                            });
                          }
                        } else {
                          Utils.showLog(
                              "categoryId enter.................${controller.allCategoryResponseModel?.data?[index].id}");
                          Utils.showLog(
                              "categoryTitle enter.................${controller.allCategoryResponseModel?.data?[index].name}");

                          Get.toNamed(
                            AppRoutes.subCategoriesScreen,
                            arguments: {
                              "categoryId": controller
                                  .allCategoryResponseModel?.data?[index].id,
                              "categoryTitle": controller
                                  .allCategoryResponseModel?.data?[index].name,
                              "search": controller.search,
                              "popular": controller.popular,
                              "mostLike": controller.mostLike,
                            },
                          );
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
                              padding: EdgeInsets.all(17),
                              decoration: BoxDecoration(
                                  color: AppColors.white,
                                  borderRadius: BorderRadius.circular(12)),
                              child: CustomImageView(
                                image:
                                    controller.allCategoryList[index].image ??
                                        '',
                                // height: 110,
                                // width: 130,
                              ),
                            ).paddingAll(4),
                            Expanded(
                              child: Text(
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                capitalizeWords(
                                    controller.allCategoryList[index].name ??
                                        ""),
                                style: AppFontStyle.fontStyleW500(
                                    fontSize: 14,
                                    fontColor: AppColors.darkGrey),
                              ).paddingOnly(
                                  left: 8, right: 8, top: 2, bottom: 11),
                            )
                          ],
                        ),
                      ),
                    );
                  },
                ).paddingOnly(left: 12, right: 12, top: 20, bottom: 20);
        });
  }
}
