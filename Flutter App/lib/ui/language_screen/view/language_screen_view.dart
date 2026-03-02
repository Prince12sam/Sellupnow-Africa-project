import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/language_screen/widget/language_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class LanguageScreenView extends StatelessWidget {
  const LanguageScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: LanguageScreenAppBar(
          title: EnumLocale.txtLanguage.name.tr,
        ),
      ),
      backgroundColor: AppColors.white,
      body: LanguageView(),
    );
  }
}
