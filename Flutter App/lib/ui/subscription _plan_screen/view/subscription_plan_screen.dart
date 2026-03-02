import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/subscription%20_plan_screen/widget/subscription_plan_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class SubscriptionPlanScreen extends StatelessWidget {
  const SubscriptionPlanScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: SubscriptionPlanScreenAppBar(
          title: EnumLocale.txtSubscriptionPlan.name.tr,
        ),
      ),
      body: Column(
        children: [
          SubscriptionPlanScreenWidget(),
        ],
      ),
    );
  }
}
