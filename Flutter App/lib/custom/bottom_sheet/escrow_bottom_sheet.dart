import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/ui/escrow_screen/api/escrow_api.dart';
import 'package:listify/ui/escrow_screen/model/escrow_response_model.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class EscrowBottomSheet extends StatefulWidget {
  final String listingId;
  final String listingTitle;
  final String listingImage;

  const EscrowBottomSheet({
    super.key,
    required this.listingId,
    required this.listingTitle,
    required this.listingImage,
  });

  @override
  State<EscrowBottomSheet> createState() => _EscrowBottomSheetState();
}

class _EscrowBottomSheetState extends State<EscrowBottomSheet> {
  bool isLoading = true;
  bool isSubmitting = false;
  EscrowBreakdownResponse? breakdown;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadBreakdown();
  }

  Future<void> _loadBreakdown() async {
    final res = await EscrowApi.callBreakdownApi(listingId: widget.listingId);
    if (mounted) {
      setState(() {
        isLoading = false;
        if (res != null && res.status) {
          breakdown = res;
        } else {
          errorMessage = 'Unable to load price breakdown';
        }
      });
    }
  }

  Future<void> _initiateEscrow() async {
    setState(() => isSubmitting = true);
    final res = await EscrowApi.callInitiateApi(listingId: widget.listingId);
    if (!mounted) return;
    setState(() => isSubmitting = false);

    if (res != null && res.status) {
      Get.back();
      Get.snackbar(
        'Escrow Created',
        res.message ?? 'Your payment is held securely until delivery is confirmed.',
        snackPosition: SnackPosition.TOP,
        backgroundColor: AppColors.green,
        colorText: AppColors.white,
        duration: const Duration(seconds: 3),
      );
    } else {
      Get.snackbar(
        'Error',
        res?.message ?? 'Failed to initiate escrow',
        snackPosition: SnackPosition.TOP,
        backgroundColor: AppColors.appRedColor,
        colorText: AppColors.white,
        duration: const Duration(seconds: 3),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.only(top: 12),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: AppColors.lightGrey,
                borderRadius: BorderRadius.circular(2),
              ),
            ),

            // Header
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
              child: Row(
                children: [
                  Icon(Icons.shield_outlined, color: AppColors.green, size: 28),
                  const SizedBox(width: 10),
                  Text(
                    'Buy with Escrow',
                    style: AppFontStyle.fontStyleW700(
                      fontSize: 18,
                      fontColor: AppColors.black,
                    ),
                  ),
                  const Spacer(),
                  InkWell(
                    onTap: () => Get.back(),
                    child: Icon(Icons.close, size: 24, color: AppColors.grey300),
                  ),
                ],
              ),
            ),
            const Divider(height: 1),

            if (isLoading)
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 60),
                child: CircularProgressIndicator(),
              )
            else if (errorMessage != null)
              Padding(
                padding: const EdgeInsets.symmetric(vertical: 40, horizontal: 16),
                child: Text(errorMessage!, style: AppFontStyle.fontStyleW400(fontSize: 14, fontColor: AppColors.appRedColor)),
              )
            else ...[
              // Product info
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 12),
                child: Row(
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: Image.network(
                        widget.listingImage,
                        width: 56,
                        height: 56,
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => Container(
                          width: 56,
                          height: 56,
                          color: AppColors.lightGrey100,
                          child: Icon(Icons.image, color: AppColors.lightGrey),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        widget.listingTitle,
                        style: AppFontStyle.fontStyleW500(
                          fontSize: 14,
                          fontColor: AppColors.black,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ),

              // Price breakdown
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppColors.lightGrey100,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  children: [
                    _buildRow('Product Price', '${breakdown!.currency} ${breakdown!.listingPrice.toStringAsFixed(2)}'),
                    const SizedBox(height: 8),
                    _buildRow('Platform Fee', '${breakdown!.currency} ${breakdown!.platformFee.toStringAsFixed(2)}'),
                    const Divider(height: 20),
                    _buildRow(
                      'Total',
                      '${breakdown!.currency} ${breakdown!.total.toStringAsFixed(2)}',
                      isBold: true,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),

              // Wallet balance
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Row(
                  children: [
                    Icon(Icons.account_balance_wallet_outlined, size: 20, color: AppColors.grey300),
                    const SizedBox(width: 8),
                    Text(
                      'Wallet Balance: ${breakdown!.currency} ${breakdown!.walletBalance.toStringAsFixed(2)}',
                      style: AppFontStyle.fontStyleW500(
                        fontSize: 13,
                        fontColor: breakdown!.canAfford ? AppColors.green : AppColors.appRedColor,
                      ),
                    ),
                  ],
                ),
              ),

              if (!breakdown!.canAfford)
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
                  child: Text(
                    'Insufficient wallet balance. Please top up your wallet first.',
                    style: AppFontStyle.fontStyleW400(fontSize: 13, fontColor: AppColors.appRedColor),
                  ),
                ),

              const SizedBox(height: 8),

              // Info note
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16),
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppColors.green.withValues(alpha: 0.08),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Icon(Icons.info_outline, size: 18, color: AppColors.green),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        'Your payment will be held securely until you confirm delivery of the product.',
                        style: AppFontStyle.fontStyleW400(fontSize: 13, fontColor: AppColors.green),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              // Pay button
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
                child: PrimaryAppButton(
                  height: 54,
                  onTap: breakdown!.canAfford && !isSubmitting ? _initiateEscrow : null,
                  color: breakdown!.canAfford ? AppColors.green : AppColors.lightGrey,
                  text: isSubmitting ? 'Processing...' : 'Pay Securely',
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildRow(String label, String value, {bool isBold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: isBold
              ? AppFontStyle.fontStyleW700(fontSize: 14, fontColor: AppColors.black)
              : AppFontStyle.fontStyleW400(fontSize: 14, fontColor: AppColors.grey300),
        ),
        Text(
          value,
          style: isBold
              ? AppFontStyle.fontStyleW700(fontSize: 14, fontColor: AppColors.black)
              : AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.black),
        ),
      ],
    );
  }
}
