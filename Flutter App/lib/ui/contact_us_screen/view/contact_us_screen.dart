import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/contact_us_screen/widget/contact_us_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class ContactUsScreen extends StatelessWidget {
  const ContactUsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: ContactUsScreenAppBar(
          title: EnumLocale.txtContactUs.name.tr,
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            ContactUsScreenWidget(),
          ],
        ),
      ),
    );
  }
}
