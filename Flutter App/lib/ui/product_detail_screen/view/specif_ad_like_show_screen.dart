import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/widget/product_detail_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class SpecifAdLikeShowScreen extends StatelessWidget {
  const SpecifAdLikeShowScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: SpecificAdLikeShowAppBar(
          title: EnumLocale.txtFavorites.name.tr,
        ),
      ),
      body: SpecificLikeShow(),
    );
  }
}
