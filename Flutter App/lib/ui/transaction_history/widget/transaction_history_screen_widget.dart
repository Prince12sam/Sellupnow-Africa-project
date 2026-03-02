import 'package:dotted_line/dotted_line.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/categories_screen/shimmer/all_category_shimmer.dart';
import 'package:listify/ui/transaction_history/controller/transaction_history_screen_controller.dart';
import 'package:listify/ui/transaction_history/shimmer/transaction_history_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class TransactionHistoryAppBar extends StatelessWidget {
  final String? title;
  const TransactionHistoryAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(300),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class TransactionView extends StatelessWidget {
  const TransactionView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TransactionHistoryScreenController>(builder: (controller) {
      return controller.isLoading
          ? TransactionHistoryShimmer()
          : controller.transactionHistoryList.isEmpty
              ? NoDataFound(image: AppAsset.noHistoryFound, imageHeight: 180, text: EnumLocale.txtNoDataFound.name.tr)
              : RefreshIndicator(
                  color: AppColors.appRedColor,
                  onRefresh: () => controller.onRefresh(),
                  child: ListView.builder(
                    padding: EdgeInsets.only(top: 20),
                    physics: AlwaysScrollableScrollPhysics(),
                    clipBehavior: Clip.none,
                    shrinkWrap: true,
                    itemCount: controller.transactionHistoryList.length,
                    itemBuilder: (context, index) {
                      final data = controller.transactionHistoryList[index];
                      return PaymentSuccessCard(
                        amount: data.amount.toString(),
                        date: controller.formatUtcIsoToLocal(data.paidAt?.toString()),
                        paymentMethod: data.paymentGateway ?? '',
                        isSuccess: true,
                        paymentId: data.transactionId ?? '',
                      );
                    },
                  ),
                );
    });
  }
}

class PaymentSuccessCard extends StatelessWidget {
  final String? paymentId;
  final String? amount;
  final String? date;
  final String? time;
  final String? paymentMethod;
  final bool isSuccess;

  const PaymentSuccessCard({
    super.key,
    this.paymentId,
    this.amount,
    this.date,
    this.time,
    this.isSuccess = true,
    this.paymentMethod,
  });

  @override
  Widget build(BuildContext context) {
    return Stack(
      clipBehavior: Clip.none,
      children: [
        Container(
          width: Get.width,
          height: 126,
          decoration: BoxDecoration(
            color: AppColors.tranActionBgColor,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        decoration: BoxDecoration(color: AppColors.appRedColor.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(6)),
                        child: Center(
                          child: Text(
                            paymentMethod ?? '',
                            style: AppFontStyle.fontStyleW700(fontSize: 13, fontColor: AppColors.appRedColor),
                          ).paddingSymmetric(horizontal: 10, vertical: 5),
                        ),
                      ).paddingOnly(top: 9, bottom: 6, right: 80),
                      Container(
                        height: 27,
                        width: 27,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(7),
                          color: AppColors.white,
                          border: Border.all(color: AppColors.appRedColor),
                        ),
                        child: Center(
                          child: Image.asset(
                            AppAsset.paymentChainIcon,
                            height: 18,
                            width: 18,
                          ),
                        ),
                      )
                    ],
                  ),
                  SizedBox(
                    width: 190,
                    child: Text(
                      paymentId ?? '',
                      style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.faqTxt),
                    ),
                  ).paddingOnly(bottom: 5),
                  Row(
                    children: [
                      Text(
                        EnumLocale.txtDateTime.name.tr,
                        style: AppFontStyle.fontStyleW500(fontSize: 11.5, fontColor: AppColors.grey),
                      ),
                      Text(
                        date ?? '',
                        style: AppFontStyle.fontStyleW500(fontSize: 11.5, fontColor: AppColors.faqTxt),
                      ),
                    ],
                  )
                ],
              ).paddingOnly(left: 38),
              Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  FittedBox(
                    // fit: BoxFit.cover,
                    child: Text(
                      '${Database.settingApiResponseModel?.data?.currency?.symbol} $amount',
                      style: AppFontStyle.fontStyleW800(fontSize: 16, fontColor: AppColors.appRedColor),
                    ),
                  ),
                  // Text(
                  //   "Succeed",
                  //   style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.greenColor),
                  // ),
                ],
              ).paddingOnly(right: 40, left: 16),
            ],
          ),
        ).paddingOnly(left: 16, right: 16),
        Positioned(
          top: 0,
          bottom: 0,
          child: Container(
            height: 40,
            width: 40,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
          ),
        ),
        Positioned(
          right: 0,
          top: 0,
          bottom: 0,
          child: Container(
            height: 40,
            width: 40,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
          ),
        ),
        Positioned(
          right: 115,
          top: -10,
          child: Container(
            height: 20,
            width: 20,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
          ),
        ),
        Positioned(
          right: 115,
          bottom: -10,
          child: Container(
            height: 20,
            width: 20,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
          ),
        ),
        Positioned(
          right: 126,
          top: 7,
          child: SizedBox(
              height: 110,
              width: 0,
              child: DottedLine(
                direction: Axis.vertical,
                alignment: WrapAlignment.center,
                lineLength: double.infinity,
                lineThickness: 1.4,
                dashLength: 4.0,
                dashColor: AppColors.white,
                dashRadius: 0.0,
                dashGapLength: 4.0,
                dashGapColor: Colors.transparent,
                dashGapRadius: 0.0,
              )).paddingOnly(left: 20),
        )
      ],
    ).paddingOnly(bottom: 14);
  }
}
