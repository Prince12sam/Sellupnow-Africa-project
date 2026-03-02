import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/select_city_screen/controller/select_city_screen_controller.dart';
import 'package:listify/ui/select_city_screen/widget/select_city_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';

class SelectCityScreen extends StatelessWidget {
  const SelectCityScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SelectCityScreenController>(builder: (controller) {
      return Scaffold(
        backgroundColor: Colors.white,
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: SelectCityScreenAppBar(
            title: controller.selectedState,
          ),
        ),
        body: GestureDetector(
          onTap: () {
            FocusScopeNode currentFocus = FocusScope.of(context);
            if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
              currentFocus.focusedChild?.unfocus();
            }
          },
          child: GetBuilder<SelectCityScreenController>(
              id: Constant.idGetCity,
              builder: (controller) {
                return RefreshIndicator(
                  color: AppColors.appRedColor,
                  onRefresh: () => controller.onRefresh(),
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    controller: controller.scrollController,
                    child: SelectCityScreenWidget(),
                  ),
                );
              }),
        ),
      );
    });
  }
}
