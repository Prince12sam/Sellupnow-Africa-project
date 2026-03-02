import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/withdraw_screen/api/withdraw_api.dart';
import 'package:listify/ui/withdraw_screen/model/withdraw_response_model.dart';
import 'package:listify/utils/utils.dart';

class WithdrawScreenController extends GetxController {
  bool isLoading = false;
  bool isSubmitting = false;
  List<WithdrawItem> items = [];

  // Form controllers
  final formKey = GlobalKey<FormState>();
  final amountController = TextEditingController();
  final contactController = TextEditingController();
  final nameController = TextEditingController();
  final reasonController = TextEditingController();
  String selectedMethod = 'Mobile Money';

  final List<String> withdrawMethods = [
    'Mobile Money',
    'Bank Transfer',
    'PayPal',
  ];

  @override
  void onInit() {
    super.onInit();
    WithdrawApi.startPagination = 0;
    fetchList();
  }

  @override
  void onClose() {
    amountController.dispose();
    contactController.dispose();
    nameController.dispose();
    reasonController.dispose();
    super.onClose();
  }

  Future<void> fetchList() async {
    isLoading = true;
    update();
    final result = await WithdrawApi.fetchList();
    if (result != null) {
      items.clear();
      items.addAll(result.data ?? []);
    }
    isLoading = false;
    update();
  }

  Future<void> onRefresh() async {
    WithdrawApi.startPagination = 0;
    items.clear();
    await fetchList();
  }

  Future<void> submitRequest() async {
    if (!formKey.currentState!.validate()) return;

    isSubmitting = true;
    update();

    final success = await WithdrawApi.submitRequest(
      amount: double.tryParse(amountController.text.trim()) ?? 0,
      contactNumber: contactController.text.trim(),
      name: nameController.text.trim(),
      withdrawMethod: selectedMethod,
      reason: reasonController.text.trim(),
    );

    isSubmitting = false;
    update();

    if (success) {
      amountController.clear();
      contactController.clear();
      nameController.clear();
      reasonController.clear();
      Get.snackbar(
        'Request Submitted',
        'Your withdrawal request has been submitted.',
        backgroundColor: Colors.green,
        colorText: Colors.white,
      );
      WithdrawApi.startPagination = 0;
      items.clear();
      await fetchList();
    } else {
      Get.snackbar(
        'Error',
        'Failed to submit request. Please try again.',
        backgroundColor: Colors.red,
        colorText: Colors.white,
      );
    }
  }

  String formatDate(String? iso) {
    if (iso == null || iso.isEmpty) return '';
    try {
      return DateFormat('dd MMM yyyy').format(DateTime.parse(iso).toLocal());
    } catch (_) {
      return iso;
    }
  }

  Color statusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'approved':
        return Colors.green;
      case 'rejected':
        return Colors.red;
      default:
        return Colors.orange;
    }
  }
}
