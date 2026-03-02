import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/wallet_screen/controller/wallet_screen_controller.dart';
import 'package:listify/ui/wallet_screen/model/wallet_response_model.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

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
