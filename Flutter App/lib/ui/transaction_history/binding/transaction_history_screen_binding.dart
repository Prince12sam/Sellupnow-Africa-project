import 'package:get/get.dart';
import 'package:listify/ui/transaction_history/controller/transaction_history_screen_controller.dart';

class TransactionHistoryScreenBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<TransactionHistoryScreenController>(() => TransactionHistoryScreenController());
  }
}
