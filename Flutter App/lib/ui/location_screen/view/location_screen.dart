import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/location_screen/controller/location_screen_controller.dart';
import 'package:listify/ui/location_screen/widget/location_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';

class LocationScreen extends StatelessWidget {
  const LocationScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      body: Column(
        children: [
          LocationScreenAppBar(),
          Expanded(
            child: GestureDetector(
              onTap: () {
                FocusScopeNode currentFocus = FocusScope.of(context);
                if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
                  currentFocus.focusedChild?.unfocus();
                }
              },
              child: GetBuilder<LocationScreenController>(
                  id: Constant.idGetCountry,
                  builder: (controller) {
                    return RefreshIndicator(
                      color: AppColors.appRedColor,
                      onRefresh: () => controller.onRefresh(),
                      child: SingleChildScrollView(
                        physics: const AlwaysScrollableScrollPhysics(),
                        controller: controller.scrollController,
                        child: Column(
                          children: [
                            LocationScreenWidget(),
                          ],
                        ),
                      ),
                    );
                  }),
            ),
          ),
        ],
      ),
    );
  }
}
