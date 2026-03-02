import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/about_us_screen/widget/about_us_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class AboutUsScreen extends StatelessWidget {
  const AboutUsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: AboutUsScreenAppBar(
          title: EnumLocale.txtAboutUs.name.tr,
        ),
      ),
      body: Column(
        children: [
          AboutUsScreenWidget(),
        ],
      ),
    );
  }
}
