import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/wallet_screen/api/wallet_api.dart';
import 'package:listify/ui/wallet_screen/model/wallet_response_model.dart';
import 'package:listify/utils/utils.dart';

class WalletScreenController extends GetxController {
  bool isLoading = false;
  double balance = 0.0;
  List<WalletTransaction> transactions = [];

  @override
  void onInit() {
    super.onInit();
    WalletApi.startPagination = 0;
    fetchWallet();
  }

  Future<void> fetchWallet() async {
    isLoading = true;
    update();

    final result = await WalletApi.callBalanceApi();
    if (result != null) {
      balance = result.balance ?? 0.0;
      transactions.clear();
      transactions.addAll(result.data ?? []);
    }

    Utils.showLog("Wallet loaded — balance: $balance, txns: ${transactions.length}");
    isLoading = false;
    update();
  }

  Future<void> onRefresh() async {
    WalletApi.startPagination = 0;
    transactions.clear();
    await fetchWallet();
  }

  String formatDate(String? iso) {
    if (iso == null || iso.isEmpty) return '';
    try {
      final local = DateTime.parse(iso).toLocal();
      return DateFormat('dd MMM yyyy, hh:mm a').format(local);
    } catch (_) {
      return iso;
    }
  }

  String formatBalance(double val) => 'GH₵ ${val.toStringAsFixed(2)}';
}
