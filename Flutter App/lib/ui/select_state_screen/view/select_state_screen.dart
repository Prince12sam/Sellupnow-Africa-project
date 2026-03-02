import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/select_state_screen/controller/select_state_screen_controller.dart';
import 'package:listify/ui/select_state_screen/widget/select_state_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';

class SelectStateScreen extends StatelessWidget {
  const SelectStateScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SelectStateScreenController>(builder: (controller) {
      return Scaffold(
        backgroundColor: Colors.white,
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: SelectStateScreenAppBar(
            title: controller.selectedCountry,
          ),
        ),
        body: GestureDetector(
          onTap: () {
            FocusScopeNode currentFocus = FocusScope.of(context);
            if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
              currentFocus.focusedChild?.unfocus();
            }
          },
          child: GetBuilder<SelectStateScreenController>(
              id: Constant.idGetState,
              builder: (controller) {
                return RefreshIndicator(
                  color: AppColors.appRedColor,
                  onRefresh: () => controller.onRefresh(),
                  child: SingleChildScrollView(
                    child: SingleChildScrollView(
                      child: SelectStateScreenWidget(),
                    ),
                  ),
                );
              }),
        ),
      );
    });
  }
}
