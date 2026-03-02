import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get/get_utils/get_utils.dart';
import 'package:listify/ui/transaction_history/controller/transaction_history_screen_controller.dart';
import 'package:listify/ui/transaction_history/widget/transaction_history_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class TransactionHistoryScreenView extends StatelessWidget {
  const TransactionHistoryScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: TransactionHistoryAppBar(
          title: EnumLocale.txtTransactionHistory.name.tr,
        ),
      ),
      backgroundColor: AppColors.white,
      body: GetBuilder<TransactionHistoryScreenController>(builder: (controller) {
        return TransactionView();
      }),
    );
  }
}
