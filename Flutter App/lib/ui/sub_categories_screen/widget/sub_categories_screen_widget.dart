import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/select_state_screen/shimmer/select_state_shimmer.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/controller/sub_categories_screen_controller.dart';
import 'package:listify/ui/sub_categories_screen/service/select_bus_service.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class SubCategoriesScreenAppBar extends StatelessWidget {
  const SubCategoriesScreenAppBar({super.key});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: const Size.fromHeight(120),
      child: GetBuilder<SubCategoriesScreenController>(
        id: 'appbar',
        builder: (controller) {
          return CustomAppBar(
            title: capitalizeWords(controller.categoryTitle??""),
            showLeadingIcon: true,
            onTap: () async {
              while (controller.categoryIdHistory.isNotEmpty && controller.categoryTitleHistory.isNotEmpty) {
                controller.categoryId = controller.categoryIdHistory.removeLast();
                controller.categoryTitle = controller.categoryTitleHistory.removeLast();

                SubCategoryApi.startPagination = 0;
                final isEmpty = await controller.getSubCategoryApi();

                if (!isEmpty) {
                  controller.update(['appbar']);
                  return;
                }
                // else continue loop until we find non-empty category
              }

              // If no valid previous category with children found, just go back
              Get.back();
            },
          );
        },
      ),
    );
  }
}

class SubCategoriesScreenWidget extends StatelessWidget {
  const SubCategoriesScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SubCategoriesScreenController>(builder: (controller) {
      return Column(
        children: [
          controller.isLoading
              ? SelectStateShimmer()
              : Column(
                children: [
                  ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: controller.subCategoryList.length,
                      itemBuilder: (context, index) {
                        return GestureDetector(
                          onTap: () async {
                            final selectedId = controller.subCategoryList[index].id ?? '';
                            final selectedTitle = controller.subCategoryList[index].name ?? '';

                            final selectedImage = controller.subCategoryList[index].image ?? '';


                            controller.categoryIdHistory.add(controller.categoryId ?? '');
                            controller.categoryTitleHistory.add(controller.categoryTitle ?? '');

                            // Set new values
                            controller.categoryId = selectedId;
                            controller.categoryTitle = selectedTitle;

                            SubCategoryApi.startPagination = 0;
                            final isEmpty = await controller.getSubCategoryApi(); // returns true if empty

                            // If no children, then navigate to EditProductView
                            if (isEmpty) {
                              if (controller.addListingScreen == true) {
                                Get.toNamed(AppRoutes.editProductView, arguments: {
                                  'categoryId': selectedId,
                                  'categoryTitle': selectedTitle,
                                })?.then((value) {
                                  final subCategoryScreenController = Get.find<SubCategoriesScreenController>();
                                  controller.categoryId = controller.categoryIdHistory.removeLast();
                                  controller.categoryTitle = controller.categoryTitleHistory.removeLast();

                                  SubCategoryApi.startPagination = 0;
                                  subCategoryScreenController.getSubCategoryApi();
                                });
                              } else {
                                if (controller.search == true || controller.popular == true || controller.mostLike == true) {
                                  // Get.close(2);
                                  // Utils.showLog("select id:::::::::::::${selectedId}");

                                  // Get.find<SelectionBus>().setSelection(
                                  //   id: selectedId,
                                  //   title: selectedTitle,
                                  //   image: selectedImage, // 👈 pass image
                                  // );

                                  Get.find<SelectionBus>().setSelection(id: selectedId, title: selectedTitle, image: selectedImage);
                                  Get.close(2); // 2 screen back
                                } else {
                                  Get.toNamed(AppRoutes.subCategoryProductScreen, arguments: {
                                    'categoryId': selectedId,
                                    'categoryTitle': selectedTitle,
                                    'subcategory': controller.subcategory,
                                  })?.then((value) {
                                    final subCategoryScreenController = Get.find<SubCategoriesScreenController>();
                                    controller.categoryId = controller.categoryIdHistory.removeLast();
                                    controller.categoryTitle = controller.categoryTitleHistory.removeLast();

                                    SubCategoryApi.startPagination = 0;
                                    subCategoryScreenController.getSubCategoryApi();
                                  });
                                }
                              }
                            } else {
                              Utils.showLog('addListingScreen is false');
                              controller.update(['appbar']);
                            }
                          },

                          // onTap: () async {
                          //   final selectedId = controller.subCategoryList[index].id ?? '';
                          //   final selectedTitle = controller.subCategoryList[index].name ?? '';
                          //
                          //   /// Save the current categoryId in history
                          //   controller.categoryIdHistory.add(controller.categoryId ?? '');
                          //
                          //   SubCategoryApi.startPagination = 0;
                          //   controller.categoryId = selectedId;
                          //   controller.categoryTitle = selectedTitle;
                          //
                          //   /// Fetch subcategories for selected ID
                          //   final isEmpty = await controller.getSubCategoryApi();
                          //
                          //   /// Navigate to subcategory screen if data is not empty
                          //   Get.toNamed(
                          //     AppRoutes.subCategoriesScreen,
                          //     arguments: {
                          //       "categoryId": controller.categoryId,
                          //       "title": controller.categoryTitle,
                          //       "addListingScreen": controller.addListingScreen,
                          //     },
                          //   );
                          // },
                          child: Container(

                            width: Get.width,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(16),
                              color: AppColors.categoriesBgColor.withValues(alpha: 0.5),
                              border: Border.all(color: AppColors.borderColor.withValues(alpha: 0.4)),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  height: 60,
                                  width: 60,
                                  padding: const EdgeInsets.all(6),
                                  decoration: BoxDecoration(
                                    color: AppColors.white,
                                    borderRadius: BorderRadius.circular(14),
                                  ),
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(14),
                                    child: CustomImageView(
                                      image: controller.subCategoryList[index].image ?? '',
                                      fit: BoxFit.fill,
                                    ),
                                  ),
                                  // child: Image.asset(
                                  //   AppAsset.electronicItem,
                                  //   // fit: BoxFit.fill,
                                  // ),
                                ).paddingAll(5),
                                Text(
                                  capitalizeWords(controller.subCategoryList[index].name??"") ,
                                  style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
                                ).paddingOnly(left: 10),
                                Spacer(),
                                Container(
                                  padding: EdgeInsets.all(4),
                                  decoration: BoxDecoration(color: AppColors.white, shape: BoxShape.circle),
                                  child: RotatedBox(
                                    quarterTurns: 2,
                                    child: Image.asset(
                                      AppAsset.backArrowIcon,
                                      height: 22,
                                      width: 22,
                                    ),
                                  ),
                                ).paddingOnly(right: 16)
                              ],
                            ),
                          ).paddingOnly(bottom: 14),
                        );
                      },
                    ),

                  GetBuilder<SubCategoriesScreenController>(
                    id: Constant.idPagination,
                    builder: (controller) => Visibility(
                      visible: controller.isPaginationLoading,
                      child: CircularProgressIndicator(color: AppColors.appRedColor),
                    ),
                  ),

                ],
              ),
        ],
      ).paddingOnly(left: 16, right: 16, top: 16);
    });
  }
}
