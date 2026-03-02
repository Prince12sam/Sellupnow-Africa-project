import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/wallet_screen/controller/wallet_screen_controller.dart';
import 'package:listify/ui/wallet_screen/widget/wallet_screen_widget.dart';
import 'package:listify/utils/app_color.dart';

class WalletScreenView extends StatelessWidget {
  const WalletScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        backgroundColor: AppColors.appRedColor,
        foregroundColor: AppColors.white,
        title: const Text('My Wallet'),
        centerTitle: true,
      ),
      body: GetBuilder<WalletScreenController>(
        builder: (controller) => WalletBody(),
      ),
    );
  }
}
