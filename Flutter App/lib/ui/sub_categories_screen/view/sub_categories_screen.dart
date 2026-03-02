import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/controller/sub_categories_screen_controller.dart';
import 'package:listify/ui/sub_categories_screen/widget/sub_categories_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';

class SubCategoriesScreen extends StatelessWidget {
  const SubCategoriesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SubCategoriesScreenController>(
        id: Constant.appbar,
        builder: (controller) {
          return PopScope(
            canPop: false,
            onPopInvokedWithResult: (didPop, result) async {
              if (didPop) return;

              final controller = Get.find<SubCategoriesScreenController>();
              while (controller.categoryIdHistory.isNotEmpty && controller.categoryTitleHistory.isNotEmpty) {
                controller.categoryId = controller.categoryIdHistory.removeLast();
                controller.categoryTitle = controller.categoryTitleHistory.removeLast();

                SubCategoryApi.startPagination = 0;
                final isEmpty = await controller.getSubCategoryApi();

                if (!isEmpty) {
                  controller.update([Constant.appbar]);
                  return;
                }
              }

              // If no parent with children found, go back
              Get.back();
            },
            child: Scaffold(
              backgroundColor: AppColors.white,
              appBar: AppBar(
                automaticallyImplyLeading: false,
                flexibleSpace: const SubCategoriesScreenAppBar(), // no need to pass title
              ),
              body: SingleChildScrollView(
                controller: controller.scrollController,
                child: Column(
                  children: [
                    SubCategoriesScreenWidget(),
                  ],
                ),
              ),
            ),
          );
        });
  }
}
