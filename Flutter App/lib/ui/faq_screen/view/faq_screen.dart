import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/faq_screen/widget/faq_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class FaqScreen extends StatelessWidget {
  const FaqScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: FaqScreenAppBar(
          title: EnumLocale.txtFAQs.name.tr,
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            FaqScreenWidget(),
          ],
        ),
      ),
    );
  }
}
