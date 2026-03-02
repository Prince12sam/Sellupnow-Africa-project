import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/confirm_location/widget/confirm_location_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class ConfirmLocationScreen extends StatelessWidget {
  const ConfirmLocationScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBody: true,
      bottomNavigationBar: RePostButton(),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: ConfirmLocationAppBar(
          title: EnumLocale.txtConfirmLocation.name.tr,
        ),
      ),
      backgroundColor: AppColors.white,
     
      body: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ConfirmLocationWidget(),

          Expanded(




              child: MapView())

          // Expanded(
          //   child: SingleChildScrollView(
          //     child: MapLocationShowView(),
          //   ),
          // ),
          // 6.height,
        ],
      ),
    );
  }
}
