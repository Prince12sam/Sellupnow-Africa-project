import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/escrow_screen/controller/escrow_screen_controller.dart';
import 'package:listify/ui/escrow_screen/model/escrow_response_model.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';

class EscrowScreenView extends StatelessWidget {
  const EscrowScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        backgroundColor: AppColors.appRedColor,
        foregroundColor: AppColors.white,
        title: const Text('Escrow Orders'),
        centerTitle: true,
      ),
      body: GetBuilder<EscrowScreenController>(
        id: EscrowScreenController.idOrders,
        builder: (c) => Column(
          children: [
            // Tab Row
            Container(
              color: AppColors.white,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              child: Row(
                children: [
                  _TabButton(label: 'As Buyer', value: 'buyer', active: c.activeTab),
                  const SizedBox(width: 12),
                  _TabButton(label: 'As Seller', value: 'seller', active: c.activeTab),
                ],
              ),
            ),
            const Divider(height: 1),
            Expanded(
              child: c.isLoading
                  ? const Center(child: CircularProgressIndicator())
                  : c.orders.isEmpty
                      ? const _EmptyView()
                      : RefreshIndicator(
                          onRefresh: c.fetchOrders,
                          child: ListView.separated(
                            padding: const EdgeInsets.all(16),
                            itemCount: c.orders.length,
                            separatorBuilder: (_, __) => const SizedBox(height: 10),
                            itemBuilder: (_, i) => _OrderCard(order: c.orders[i]),
                          ),
                        ),
            ),
          ],
        ),
      ),
    );
  }
}

class _TabButton extends StatelessWidget {
  final String label;
  final String value;
  final String active;
  const _TabButton({required this.label, required this.value, required this.active});

  @override
  Widget build(BuildContext context) {
    final isActive = value == active;
    return Expanded(
      child: GestureDetector(
        onTap: () => Get.find<EscrowScreenController>().switchTab(value),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            color: isActive ? AppColors.appRedColor : AppColors.profileItemBgColor,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
              fontWeight: FontWeight.w600,
              color: isActive ? AppColors.white : AppColors.profileTxtColor,
            ),
          ),
        ),
      ),
    );
  }
}

class _OrderCard extends StatelessWidget {
  final EscrowOrder order;
  const _OrderCard({required this.order});

  @override
  Widget build(BuildContext context) {
    final currency = Database.settingApiResponseModel?.data?.currency?.symbol ?? order.currency ?? '';
    return GestureDetector(
      onTap: () => Get.toNamed(AppRoutes.escrowDetailScreen, arguments: order.id),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.profileItemBgColor,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              order.listingTitle ?? 'Listing removed',
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
            ),
            const SizedBox(height: 6),
            Row(
              children: [
                if (order.counterpartyName != null)
                  Expanded(
                    child: Text(
                      order.counterpartyName!,
                      style: TextStyle(fontSize: 12, color: AppColors.searchText),
                    ),
                  ),
                const SizedBox(width: 8),
                Text(
                  '$currency${order.totalAmount.toStringAsFixed(2)}',
                  style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                _StatusBadge(status: order.status ?? ''),
                Text(
                  _formatDate(order.createdAt),
                  style: TextStyle(fontSize: 11, color: AppColors.searchText),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  String _formatDate(String? dt) {
    if (dt == null) return '';
    try {
      final d = DateTime.parse(dt);
      return '${d.day} ${_months[d.month - 1]} ${d.year}';
    } catch (_) {
      return dt;
    }
  }

  static const _months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
}

class _StatusBadge extends StatelessWidget {
  final String status;
  const _StatusBadge({required this.status});

  Color get _color {
    switch (status) {
      case 'released': return Colors.green;
      case 'funded': return Colors.blue;
      case 'seller_confirmed': return Colors.indigo;
      case 'seller_delivered': return Colors.orange;
      case 'refunded':
      case 'disputed': return Colors.red;
      default: return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: _color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: _color.withOpacity(0.4)),
      ),
      child: Text(
        status.replaceAll('_', ' ').replaceFirstMapped(
            RegExp(r'^[a-z]'), (m) => m[0]!.toUpperCase()),
        style: TextStyle(fontSize: 11, color: _color, fontWeight: FontWeight.w600),
      ),
    );
  }
}

class _EmptyView extends StatelessWidget {
  const _EmptyView();

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.lock_outline, size: 60, color: Colors.grey.shade300),
          const SizedBox(height: 12),
          const Text('No escrow orders yet.',
              style: TextStyle(color: Colors.grey, fontSize: 14)),
        ],
      ),
    );
  }
}
