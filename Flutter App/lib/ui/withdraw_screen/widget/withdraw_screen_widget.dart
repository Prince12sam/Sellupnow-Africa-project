import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/withdraw_screen/controller/withdraw_screen_controller.dart';
import 'package:listify/ui/withdraw_screen/model/withdraw_response_model.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class WithdrawBody extends StatelessWidget {
  const WithdrawBody({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<WithdrawScreenController>(builder: (c) {
      return RefreshIndicator(
        color: AppColors.appRedColor,
        onRefresh: c.onRefresh,
        child: ListView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16),
          children: [
            _WithdrawForm(controller: c),
            const SizedBox(height: 24),
            Text(
              'Withdrawal History',
              style: AppFontStyle.fontStyleW600(
                fontSize: 16,
                fontColor: AppColors.black,
              ),
            ),
            const SizedBox(height: 12),
            if (c.isLoading)
              const Center(child: CircularProgressIndicator())
            else if (c.items.isEmpty)
              NoDataFound(
                image: AppAsset.noHistoryFound,
                imageHeight: 140,
                text: EnumLocale.txtNoDataFound.name.tr,
              )
            else
              ...c.items.map((item) => _WithdrawCard(item: item, controller: c)),
          ],
        ),
      );
    });
  }
}

class _WithdrawForm extends StatelessWidget {
  final WithdrawScreenController controller;
  const _WithdrawForm({required this.controller});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.lightWhiteColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Form(
        key: controller.formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'New Withdrawal Request',
              style: AppFontStyle.fontStyleW600(
                fontSize: 15,
                fontColor: AppColors.black,
              ),
            ),
            const SizedBox(height: 14),
            _field(
              controller: controller.amountController,
              label: 'Amount (GH₵)',
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              validator: (v) => (v == null || v.isEmpty || double.tryParse(v) == null)
                  ? 'Enter a valid amount'
                  : null,
            ),
            const SizedBox(height: 10),
            _field(
              controller: controller.nameController,
              label: 'Account / Recipient Name',
              validator: (v) =>
                  (v == null || v.isEmpty) ? 'Name is required' : null,
            ),
            const SizedBox(height: 10),
            _field(
              controller: controller.contactController,
              label: 'Contact Number / Account',
              keyboardType: TextInputType.phone,
              validator: (v) =>
                  (v == null || v.isEmpty) ? 'Contact is required' : null,
            ),
            const SizedBox(height: 10),
            GetBuilder<WithdrawScreenController>(builder: (c) {
              return DropdownButtonFormField<String>(
                value: c.selectedMethod,
                decoration: InputDecoration(
                  labelText: 'Withdrawal Method',
                  filled: true,
                  fillColor: AppColors.white,
                  border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: BorderSide.none),
                ),
                items: c.withdrawMethods
                    .map((m) => DropdownMenuItem(value: m, child: Text(m)))
                    .toList(),
                onChanged: (val) {
                  if (val != null) {
                    c.selectedMethod = val;
                    c.update();
                  }
                },
              );
            }),
            const SizedBox(height: 10),
            _field(
              controller: controller.reasonController,
              label: 'Reason (optional)',
              maxLines: 2,
            ),
            const SizedBox(height: 16),
            GetBuilder<WithdrawScreenController>(builder: (c) {
              return SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.appRedColor,
                    foregroundColor: AppColors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10)),
                  ),
                  onPressed: c.isSubmitting ? null : c.submitRequest,
                  child: c.isSubmitting
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                              color: Colors.white, strokeWidth: 2))
                      : const Text('Submit Request',
                          style: TextStyle(fontSize: 15)),
                ),
              );
            }),
          ],
        ),
      ),
    );
  }

  Widget _field({
    required TextEditingController controller,
    required String label,
    TextInputType? keyboardType,
    String? Function(String?)? validator,
    int maxLines = 1,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      maxLines: maxLines,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: BorderSide.none,
        ),
      ),
    );
  }
}

class _WithdrawCard extends StatelessWidget {
  final WithdrawItem item;
  final WithdrawScreenController controller;
  const _WithdrawCard({required this.item, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
              color: AppColors.black.withOpacity(0.06),
              blurRadius: 8,
              offset: const Offset(0, 2))
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.withdrawMethod ?? '',
                  style: AppFontStyle.fontStyleW600(
                      fontSize: 13, fontColor: AppColors.black),
                ),
                if (item.name != null)
                  Text(item.name!,
                      style: AppFontStyle.fontStyleW400(
                          fontSize: 12,
                          fontColor: AppColors.black.withOpacity(0.6))),
                Text(
                  controller.formatDate(item.createdAt),
                  style: AppFontStyle.fontStyleW400(
                      fontSize: 11,
                      fontColor: AppColors.black.withOpacity(0.4)),
                ),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                'GH₵ ${(item.amount ?? 0).toStringAsFixed(2)}',
                style: AppFontStyle.fontStyleW700(
                    fontSize: 14, fontColor: AppColors.black),
              ),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color:
                      controller.statusColor(item.status).withOpacity(0.12),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  item.status?.toUpperCase() ?? '',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w600,
                    color: controller.statusColor(item.status),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
