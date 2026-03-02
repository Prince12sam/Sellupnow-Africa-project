import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/widget/specific_product_view_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class SpecifAdViewShowScreen extends StatelessWidget {
  const SpecifAdViewShowScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: SpecificAdViewShowAppBar(
          title: EnumLocale.txtViews.name.tr,
        ),
      ),
      body: SpecificViewShow(),
    );
  }
}
