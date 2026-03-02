import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/notification_screen/controller/notification_screen_controller.dart';
import 'package:listify/ui/notification_screen/shimmer/notification_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class NotificationScreenAppBar extends StatelessWidget {
  final String? title;
  const NotificationScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(kToolbarHeight),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
        // onTap: () {},
      ),
    );
  }
}

class NotificationListView extends StatelessWidget {
  const NotificationListView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<NotificationScreenController>(builder: (controller) {
      return RefreshIndicator(
        onRefresh: () async => controller.onRefresh(),
        color: AppColors.appRedColor,
        child: controller.isLoading
            ? NotificationShimmer().paddingOnly(left: 14, right: 14)
            : controller.notificationList.isEmpty
                ? SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: SizedBox(
                        height: MediaQuery.of(context).size.height * 0.8,
                        child: Center(
                            child: NoDataFound(image: AppAsset.notNotification, imageHeight: 110, text: EnumLocale.txtNotNotification.name.tr))),
                  )
                : ListView.builder(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: EdgeInsets.zero,
                    shrinkWrap: true,
                    itemCount: controller.notificationList.length,
                    itemBuilder: (context, index) {
                      final notification = controller.notificationList[index];

                      return NotificationItemView(
                        bgColor: AppColors.lightRedColor,
                        txtColor: AppColors.appRedColor,
                        title: notification.title,
                        date: controller.formatAsMonthDayYear(notification.createdAt),
                        image: notification.image,
                        subTitle: notification.message,
                      );
                    },
                  ).paddingOnly(bottom: 0).paddingOnly(right: 14, left: 14),
      );
    });
  }
}

class NotificationItemView extends StatelessWidget {
  final String? title;
  final String? subTitle;
  final String? date;
  final String? image;
  final Color? bgColor;
  final Color? txtColor;
  const NotificationItemView({super.key, this.title, this.subTitle, this.date, this.image, this.bgColor, this.txtColor});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(color: AppColors.black.withValues(alpha: 0.06), blurRadius: 14, offset: Offset(0, 0), spreadRadius: 0),
          ],
          color: AppColors.notificationBgColor,
          border: Border.all(
            color: AppColors.notificationBorderColor,
          ),
          borderRadius: BorderRadius.circular(16)),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Container(
            height: 70,
            width: 70,

            // padding: EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: bgColor!,
              borderRadius: BorderRadius.circular(14),
            ),
            child: Center(
              child: image==""?Image.asset(AppAsset.notificationGernal,height: 40,):CustomImageView(
                image: image!,
              ),
            ),
          ).paddingOnly(left: 8, top: 8, right: 12, bottom: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title!,
                  style: AppFontStyle.fontStyleW800(fontSize: 14, fontColor: txtColor!),
                ),
                SizedBox(
                  // width: Get.width * 0.72,
                  child: Text(
                    subTitle!,
                    style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.popularProductText),
                  ),
                ).paddingOnly(bottom: 6),
                Text(
                  date!,
                  style: AppFontStyle.fontStyleW700(fontSize: 9, fontColor: AppColors.black),
                ).paddingOnly(bottom: 7)
              ],
            ).paddingOnly(top: 9, right: 10),
          )
        ],
      ),
    ).paddingOnly(top: 14);
  }
}
