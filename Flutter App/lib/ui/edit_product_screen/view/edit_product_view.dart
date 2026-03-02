import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/edit_product_screen/controller/edit_product_detail_controller.dart';
import 'package:listify/ui/edit_product_screen/widget/edit_product_widget.dart';
import 'package:listify/utils/app_color.dart';

class EditProductView extends StatelessWidget {
  const EditProductView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<EditProductDetailController>(builder: (controller) {
      return Scaffold(
        bottomNavigationBar: EditProductBottomBar(),
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: EditProductAppBar(
            title: controller.categoryTitle,
          ),
        ),
        backgroundColor: AppColors.adScreenBgColor,
        body: GestureDetector(
          onTap: () {
            FocusScopeNode currentFocus = FocusScope.of(context);
            if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
              currentFocus.focusedChild?.unfocus();
            }
          },
          child: SingleChildScrollView(
            child: Column(
              children: [
                EditProductTopView(),
                EditProductDetailView(),
              ],
            ),
          ),
        ),
      );
    });
  }
}
