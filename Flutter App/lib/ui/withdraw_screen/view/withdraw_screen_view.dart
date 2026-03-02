import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/withdraw_screen/controller/withdraw_screen_controller.dart';
import 'package:listify/ui/withdraw_screen/widget/withdraw_screen_widget.dart';
import 'package:listify/utils/app_color.dart';

class WithdrawScreenView extends StatelessWidget {
  const WithdrawScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        backgroundColor: AppColors.appRedColor,
        foregroundColor: AppColors.white,
        title: const Text('Withdraw Funds'),
        centerTitle: true,
      ),
      body: GetBuilder<WithdrawScreenController>(
        builder: (c) => WithdrawBody(),
      ),
    );
  }
}
