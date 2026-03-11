import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/escrow_screen/controller/escrow_screen_controller.dart';
import 'package:listify/ui/escrow_screen/model/escrow_response_model.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';

class EscrowDetailScreenView extends StatelessWidget {
  const EscrowDetailScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    final orderId = Get.arguments as int;
    final controller = Get.find<EscrowScreenController>();
    WidgetsBinding.instance.addPostFrameCallback((_) => controller.fetchDetail(orderId));

    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        backgroundColor: AppColors.appRedColor,
        foregroundColor: AppColors.white,
        title: const Text('Order Detail'),
        centerTitle: true,
      ),
      body: GetBuilder<EscrowScreenController>(
        id: EscrowScreenController.idDetail,
        builder: (c) {
          if (c.isDetailLoading) {
            return const Center(child: CircularProgressIndicator());
          }
          final o = c.detailOrder;
          if (o == null) {
            return const Center(child: Text('Order not found'));
          }
          final currency = Database.settingApiResponseModel?.data?.currency?.symbol ?? o.currency ?? '';
          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Listing Info
                _Section(
                  title: 'Listing',
                  child: Text(
                    o.listingTitle ?? 'Listing removed',
                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15),
                  ),
                ),
                const SizedBox(height: 14),

                // Parties
                _Section(
                  title: 'Parties',
                  child: Column(
                    children: [
                      _InfoRow(label: 'Buyer', value: o.buyerName ?? '—'),
                      const SizedBox(height: 4),
                      _InfoRow(label: 'Seller', value: o.sellerName ?? '—'),
                    ],
                  ),
                ),
                const SizedBox(height: 14),

                // Amount Breakdown
                _Section(
                  title: 'Amount',
                  child: Column(
                    children: [
                      _InfoRow(label: 'Listing Price', value: '$currency${(o.listingPrice ?? 0).toStringAsFixed(2)}'),
                      const SizedBox(height: 4),
                      _InfoRow(label: 'Platform Fee', value: '$currency${(o.adminFeeAmount ?? 0).toStringAsFixed(2)}'),
                      const Divider(height: 16),
                      _InfoRow(
                        label: 'Total',
                        value: '$currency${o.totalAmount.toStringAsFixed(2)}',
                        bold: true,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 14),

                // Status
                _Section(
                  title: 'Status',
                  child: Row(
                    children: [
                      _StatusChip(status: o.status ?? ''),
                    ],
                  ),
                ),
                const SizedBox(height: 14),

                // Timeline
                _Section(
                  title: 'Timeline',
                  child: _Timeline(order: o),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}

class _Section extends StatelessWidget {
  final String title;
  final Widget child;
  const _Section({required this.title, required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.profileItemBgColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title,
              style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: AppColors.searchText,
                  letterSpacing: 0.5)),
          const SizedBox(height: 8),
          child,
        ],
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final String label;
  final String value;
  final bool bold;
  const _InfoRow({required this.label, required this.value, this.bold = false});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label,
            style: TextStyle(fontSize: 13, color: AppColors.searchText)),
        Text(value,
            style: TextStyle(
                fontSize: 13,
                fontWeight: bold ? FontWeight.w700 : FontWeight.w500)),
      ],
    );
  }
}

class _StatusChip extends StatelessWidget {
  final String status;
  const _StatusChip({required this.status});

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

  String get _label => status
      .split('_')
      .map((w) => w.isEmpty ? '' : '${w[0].toUpperCase()}${w.substring(1)}')
      .join(' ');

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
      decoration: BoxDecoration(
        color: _color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: _color.withOpacity(0.4)),
      ),
      child: Text(_label,
          style: TextStyle(
              color: _color, fontWeight: FontWeight.w600, fontSize: 13)),
    );
  }
}

class _Timeline extends StatelessWidget {
  final EscrowOrder order;
  const _Timeline({required this.order});

  static const _months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

  String _fmt(String? dt) {
    if (dt == null) return '—';
    try {
      final d = DateTime.parse(dt);
      return '${d.day} ${_months[d.month - 1]} ${d.year}, ${d.hour.toString().padLeft(2,'0')}:${d.minute.toString().padLeft(2,'0')}';
    } catch (_) {
      return dt;
    }
  }

  @override
  Widget build(BuildContext context) {
    final steps = [
      _Step('Order Created', order.createdAt, true),
      _Step('Funds Received', order.fundedAt, order.fundedAt != null),
      _Step('Seller Confirmed', order.sellerAcceptedAt, order.sellerAcceptedAt != null),
      _Step('Delivered', order.sellerDeliveredAt, order.sellerDeliveredAt != null),
      _Step('Buyer Confirmed', order.buyerConfirmedAt, order.buyerConfirmedAt != null),
      _Step('Released', order.releasedAt, order.releasedAt != null),
    ];

    return Column(
      children: steps
          .map((s) => Padding(
                padding: const EdgeInsets.only(bottom: 10),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column(
                      children: [
                        Container(
                          width: 16,
                          height: 16,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: s.done ? AppColors.appRedColor : Colors.grey.shade300,
                          ),
                          child: s.done
                              ? const Icon(Icons.check, size: 10, color: Colors.white)
                              : null,
                        ),
                      ],
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(s.label,
                              style: TextStyle(
                                  fontWeight: FontWeight.w600,
                                  fontSize: 13,
                                  color: s.done ? Colors.black87 : Colors.grey)),
                          Text(_fmt(s.ts),
                              style: TextStyle(
                                  fontSize: 11,
                                  color: AppColors.searchText)),
                        ],
                      ),
                    ),
                  ],
                ),
              ))
          .toList(),
    );
  }
}

class _Step {
  final String label;
  final String? ts;
  final bool done;
  _Step(this.label, this.ts, this.done);
}
