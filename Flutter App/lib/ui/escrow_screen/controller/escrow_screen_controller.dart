import 'dart:developer';
import 'package:get/get.dart';
import 'package:listify/ui/escrow_screen/api/escrow_api.dart';
import 'package:listify/ui/escrow_screen/model/escrow_response_model.dart';

class EscrowScreenController extends GetxController {
  bool isLoading = false;
  bool isDetailLoading = false;
  String activeTab = 'buyer';
  List<EscrowOrder> orders = [];
  int total = 0;
  int currentPage = 1;
  EscrowOrder? detailOrder;

  static const String idOrders = 'escrow_orders';
  static const String idDetail = 'escrow_detail';

  @override
  void onInit() {
    fetchOrders();
    super.onInit();
  }

  void switchTab(String tab) {
    if (activeTab == tab) return;
    activeTab = tab;
    orders = [];
    total = 0;
    currentPage = 1;
    fetchOrders();
  }

  Future<void> fetchOrders() async {
    isLoading = true;
    update([idOrders]);
    final res = await EscrowApi.callOrdersApi(tab: activeTab, page: currentPage);
    if (res != null && res.status) {
      orders = res.data;
      total = res.total;
    }
    isLoading = false;
    update([idOrders]);
  }

  Future<void> fetchDetail(int id) async {
    isDetailLoading = true;
    detailOrder = null;
    update([idDetail]);
    final res = await EscrowApi.callDetailApi(id: id);
    if (res != null && res.status) {
      detailOrder = res.data;
    }
    isDetailLoading = false;
    update([idDetail]);
  }
}
