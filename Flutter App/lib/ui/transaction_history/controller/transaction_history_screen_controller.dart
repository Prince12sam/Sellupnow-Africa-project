import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/transaction_history/api/transaction_history_list_api.dart';
import 'package:listify/ui/transaction_history/model/transaction_history_response_model.dart';
import 'package:listify/utils/utils.dart';

class TransactionHistoryScreenController extends GetxController {
  bool isLoading = false;
  List<Datum> transactionHistoryList = [];
  TransactionHistoryResponseModel? transactionHistoryResponseModel;

  @override
  onInit() {
    super.onInit();
    TransactionHistoryListApi.startPagination = 0;
    getTransactionHistory();
  }

  /// get all purchase history
  getTransactionHistory() async {
    isLoading = true;
    update();
    transactionHistoryResponseModel = await TransactionHistoryListApi.callApi();
    transactionHistoryList.clear();
    transactionHistoryList.addAll(transactionHistoryResponseModel?.data ?? []);

    Utils.showLog("get purchase history list data $transactionHistoryList");

    isLoading = false;
    update();
  }

  String formatUtcIsoToLocal(String? iso) {
    if (iso == null || iso.isEmpty) return '';
    final local = DateTime.parse(iso).toLocal();
    return DateFormat('dd MMM yyyy, hh:mm a').format(local);
  }

  onRefresh() async {
    TransactionHistoryListApi.startPagination = 0;
    await getTransactionHistory();
  }
}
