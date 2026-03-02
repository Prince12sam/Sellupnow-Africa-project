import 'package:flutter/material.dart';
import 'package:get/get_state_manager/src/simple/get_state.dart';
import 'package:get/get_utils/src/extensions/export.dart';
import 'package:listify/ui/notification_screen/controller/notification_screen_controller.dart';
import 'package:listify/ui/notification_screen/widget/notification_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class NotificationScreenView extends StatelessWidget {
  const NotificationScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: NotificationScreenAppBar(
          title: EnumLocale.txtNotification.name.tr,
        ),
        actions: [
          
          GetBuilder<NotificationScreenController>(
            builder: (controller) {
              return GestureDetector(
                onTap: () {
                  controller.clearNotificationApi();
                },
                child
                    : Container(
                  color: Colors.transparent,

                  child: Text(EnumLocale.txtClearAll.name.tr,style: AppFontStyle.fontStyleW600(fontSize: 14, fontColor: AppColors.black),),
                ).paddingOnly(right: 10),
              );
            }
          )
        ],
      ),
      body: NotificationListView(),
    );
  }
}
