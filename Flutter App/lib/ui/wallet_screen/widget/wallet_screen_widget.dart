import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/wallet_screen/controller/wallet_screen_controller.dart';
import 'package:listify/ui/wallet_screen/model/wallet_response_model.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:url_launcher/url_launcher.dart';

class WalletBody extends StatelessWidget {
  const WalletBody({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<WalletScreenController>(builder: (c) {
      if (c.isLoading) {
        return const Center(child: CircularProgressIndicator());
      }
      return RefreshIndicator(
        color: AppColors.appRedColor,
        onRefresh: c.onRefresh,
        child: CustomScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(child: _BalanceCard(controller: c)),
            // Action buttons row
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Row(
                  children: [
                    Expanded(
                      child: _ActionButton(
                        icon: Icons.add_circle_outline,
                        label: 'Fund Wallet',
                        color: Colors.green,
                        onTap: () => _showTopupSheet(context, c),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _ActionButton(
                        icon: Icons.arrow_circle_up_outlined,
                        label: 'Withdraw',
                        color: AppColors.appRedColor,
                        onTap: () => Get.toNamed(AppRoutes.withdrawScreen),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                child: Text(
                  'Recent Transactions',
                  style: AppFontStyle.fontStyleW600(
                    fontSize: 16,
                    fontColor: AppColors.black,
                  ),
                ),
              ),
            ),
            c.transactions.isEmpty
                ? SliverFillRemaining(
                    child: NoDataFound(
                      image: AppAsset.noHistoryFound,
                      imageHeight: 150,
                      text: EnumLocale.txtNoDataFound.name.tr,
                    ),
                  )
                : SliverList(
                    delegate: SliverChildBuilderDelegate(
                      (ctx, i) => _TransactionTile(
                        item: c.transactions[i],
                        controller: c,
                      ),
                      childCount: c.transactions.length,
                    ),
                  ),
          ],
        ),
      );
    });
  }

  void _showTopupSheet(BuildContext context, WalletScreenController c) {
    c.amountController.clear();
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (_) => _TopupSheet(controller: c),
    );
  }
}

class _ActionButton extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;
  const _ActionButton({
    required this.icon,
    required this.label,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: color.withOpacity(0.08),
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 14),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, color: color, size: 20),
              const SizedBox(width: 8),
              Text(
                label,
                style: AppFontStyle.fontStyleW600(
                  fontSize: 14,
                  fontColor: color,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _TopupSheet extends StatelessWidget {
  final WalletScreenController controller;
  const _TopupSheet({required this.controller});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<WalletScreenController>(builder: (c) {
      return Padding(
        padding: EdgeInsets.only(
          left: 20,
          right: 20,
          top: 20,
          bottom: MediaQuery.of(context).viewInsets.bottom + 20,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.grey.shade300,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 16),
            Text(
              'Fund Wallet',
              style: AppFontStyle.fontStyleW700(
                fontSize: 20,
                fontColor: AppColors.black,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Enter amount in Ghana Cedis (GH₵)',
              style: AppFontStyle.fontStyleW400(
                fontSize: 13,
                fontColor: AppColors.grey300,
              ),
            ),
            const SizedBox(height: 20),
            // Quick amount chips
            Wrap(
              spacing: 8,
              children: [10, 50, 100, 500].map((amt) {
                return ActionChip(
                  label: Text('GH₵ $amt'),
                  backgroundColor: AppColors.appRedColor.withOpacity(0.1),
                  labelStyle: AppFontStyle.fontStyleW500(
                    fontSize: 13,
                    fontColor: AppColors.appRedColor,
                  ),
                  onPressed: () {
                    c.amountController.text = amt.toString();
                    c.update();
                  },
                );
              }).toList(),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: c.amountController,
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              maxLines: 1,
              decoration: InputDecoration(
                prefixText: 'GH₵ ',
                hintText: '0.00',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: AppColors.appRedColor, width: 2),
                ),
              ),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: c.isTopupLoading ? null : () => _handleTopup(context, c),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.appRedColor,
                  foregroundColor: AppColors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child: c.isTopupLoading
                    ? const SizedBox(
                        width: 24,
                        height: 24,
                        child: CircularProgressIndicator(
                          color: Colors.white,
                          strokeWidth: 2.5,
                        ),
                      )
                    : Text(
                        'Proceed to Payment',
                        style: AppFontStyle.fontStyleW600(
                          fontSize: 16,
                          fontColor: AppColors.white,
                        ),
                      ),
              ),
            ),
          ],
        ),
      );
    });
  }

  Future<void> _handleTopup(BuildContext context, WalletScreenController c) async {
    final amountText = c.amountController.text.trim();
    final amount = double.tryParse(amountText);
    if (amount == null || amount < 1) {
      Get.snackbar('Invalid Amount', 'Please enter a valid amount (min GH₵ 1).',
          backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    final result = await c.initTopup(amount);
    if (result == null) {
      Get.snackbar('Error', 'Could not initialize payment. Please try again.',
          backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    final authUrl = result['authorization_url'] as String?;
    final reference = result['reference'] as String?;
    if (authUrl == null || reference == null) {
      Get.snackbar('Error', 'Invalid payment response.',
          backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    // Close the bottom sheet
    Navigator.of(context).pop();

    // Open Paystack payment page in browser
    final uri = Uri.parse(authUrl);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);

      // Show a dialog for user to confirm payment completion
      await _showVerifyDialog(reference, c);
    } else {
      Get.snackbar('Error', 'Could not open payment page.',
          backgroundColor: Colors.red, colorText: Colors.white);
    }
  }

  Future<void> _showVerifyDialog(String reference, WalletScreenController c) async {
    await Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Payment Verification'),
        content: const Text(
          'After completing payment in your browser, tap "Verify Payment" to credit your wallet.',
        ),
        actions: [
          TextButton(
            onPressed: () => Get.back(),
            child: const Text('Cancel'),
          ),
          GetBuilder<WalletScreenController>(builder: (c) {
            return ElevatedButton(
              onPressed: c.isTopupLoading
                  ? null
                  : () async {
                      final success = await c.verifyTopup(reference);
                      if (success) {
                        Get.back();
                        Get.snackbar(
                          'Success',
                          'Wallet funded successfully!',
                          backgroundColor: Colors.green,
                          colorText: Colors.white,
                        );
                      } else {
                        Get.snackbar(
                          'Failed',
                          'Payment could not be verified. If you were charged, please try again or contact support.',
                          backgroundColor: Colors.red,
                          colorText: Colors.white,
                        );
                      }
                    },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.appRedColor,
              ),
              child: c.isTopupLoading
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(
                        color: Colors.white,
                        strokeWidth: 2,
                      ),
                    )
                  : const Text('Verify Payment',
                      style: TextStyle(color: Colors.white)),
            );
          }),
        ],
      ),
      barrierDismissible: false,
    );
  }
}

class _BalanceCard extends StatelessWidget {
  final WalletScreenController controller;
  const _BalanceCard({required this.controller});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [AppColors.appRedColor, const Color(0xffFF8A80)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: AppColors.appRedColor.withOpacity(0.4),
            blurRadius: 12,
            offset: const Offset(0, 6),
          )
        ],
      ),
      child: Column(
        children: [
          Text(
            'Available Balance',
            style: AppFontStyle.fontStyleW400(
              fontSize: 14,
              fontColor: AppColors.white.withOpacity(0.85),
            ),
          ),
          const SizedBox(height: 8),
          Text(
            controller.formatBalance(controller.balance),
            style: AppFontStyle.fontStyleW700(
              fontSize: 32,
              fontColor: AppColors.white,
            ),
          ),
        ],
      ),
    );
  }
}

class _TransactionTile extends StatelessWidget {
  final WalletTransaction item;
  final WalletScreenController controller;
  const _TransactionTile({required this.item, required this.controller});

  @override
  Widget build(BuildContext context) {
    final isCredit = (item.type ?? '').toLowerCase().contains('credit') ||
        (item.amount ?? 0) >= 0;
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: AppColors.black.withOpacity(0.06),
            blurRadius: 8,
            offset: const Offset(0, 2),
          )
        ],
      ),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor:
                isCredit ? Colors.green.shade50 : Colors.red.shade50,
            child: Icon(
              isCredit ? Icons.arrow_downward : Icons.arrow_upward,
              color: isCredit ? Colors.green : Colors.red,
              size: 18,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.purpose ?? item.type ?? 'Transaction',
                  style: AppFontStyle.fontStyleW500(
                    fontSize: 13,
                    fontColor: AppColors.black,
                  ),
                ),
                if (item.note != null && item.note!.isNotEmpty)
                  Text(
                    item.note!,
                    style: AppFontStyle.fontStyleW400(
                      fontSize: 12,
                      fontColor: AppColors.black.withOpacity(0.5),
                    ),
                  ),
                Text(
                  controller.formatDate(item.createdAt),
                  style: AppFontStyle.fontStyleW400(
                    fontSize: 11,
                    fontColor: AppColors.black.withOpacity(0.4),
                  ),
                ),
              ],
            ),
          ),
          Text(
            '${isCredit ? '+' : '-'} GH₵ ${(item.amount ?? 0).abs().toStringAsFixed(2)}',
            style: AppFontStyle.fontStyleW600(
              fontSize: 14,
              fontColor: isCredit ? Colors.green : Colors.red,
            ),
          ),
        ],
      ),
    );
  }
}
