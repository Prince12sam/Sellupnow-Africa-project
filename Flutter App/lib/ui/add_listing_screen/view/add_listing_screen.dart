import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/add_listing_screen/widget/add_listing_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class AddListingScreen extends StatelessWidget {
  const AddListingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: AddListingScreenAppBar(
          title: EnumLocale.txtAdListing.name.tr,
        ),
      ),
      body: Column(
        children: [
          SelectCategoriesView(),
        ],
      ),
    );
  }
}
