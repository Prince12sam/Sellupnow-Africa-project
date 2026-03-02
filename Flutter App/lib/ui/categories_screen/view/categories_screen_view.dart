import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/categories_screen/widget/categories_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class CategoriesScreenView extends StatelessWidget {
  const CategoriesScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: CategoriesScreenAppBar(
          title: EnumLocale.txtCategories.name.tr,
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            CategoriesScreenWidget(),
          ],
        ),
      ),
    );
  }
}
