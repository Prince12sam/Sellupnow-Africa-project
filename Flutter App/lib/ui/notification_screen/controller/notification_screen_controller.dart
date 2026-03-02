// import 'package:get/get.dart';
// import 'package:intl/intl.dart';
// import 'package:listify/ui/notification_screen/api/delete_notification_api.dart';
// import 'package:listify/ui/notification_screen/api/notification_api.dart';
// import 'package:listify/ui/notification_screen/model/notification_response_model.dart';
// import 'package:listify/utils/utils.dart';
//
// class NotificationScreenController extends GetxController {
//   bool isLoading = false;
//   List<NotificationData> notificationList = [];
//   NotificationResponseModel? notificationResponseModel;
//
//   @override
//   onInit() {
//     init();
//     super.onInit();
//   }
//
//   init() {
//     getNotificationListApi();
//   }
//
//   /// get notification list
//   getNotificationListApi() async {
//     isLoading = true;
//     update(); // notify UI
//     notificationResponseModel = await NotificationApi.callApi();
//     notificationList.clear();
//     notificationList.addAll(notificationResponseModel?.notification ?? []);
//
//     Utils.showLog("subscriptionPlan list data $notificationList");
//
//     isLoading = false;
//     update(); // notify UI
//   }
//
//   onRefresh() {
//     getNotificationListApi();
//   }
//
//   String formatAsMonthDayYear(dynamic value) {
//     if (value == null) return '';
//     DateTime dt;
//
//     if (value is DateTime) {
//       dt = value;
//     } else if (value is int) {
//       dt = DateTime.fromMillisecondsSinceEpoch(value);
//     } else {
//       dt = DateTime.parse(value.toString());
//     }
//
//     dt = dt.toLocal();
//     return DateFormat('MMMM d, y', 'en_US').format(dt);
//   }
//
//   clearNotificationApi() async {
//     final response = await ClearNotificationsApi.callApi();
//     if (response != null && response.status == true) {
//       Utils.showToast(Get.context!,
//           response.message ?? "Notifications cleared successfully!");
//     } else {
//       Utils.showToast(
//           Get.context!, response?.message ?? "Failed to clear notifications");
//     }
//   }
// }


import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/notification_screen/api/delete_notification_api.dart';
import 'package:listify/ui/notification_screen/api/notification_api.dart';
import 'package:listify/ui/notification_screen/model/notification_response_model.dart';
import 'package:listify/utils/utils.dart';

class NotificationScreenController extends GetxController {
  bool isLoading = false;
  List<NotificationData> notificationList = [];
  NotificationResponseModel? notificationResponseModel;

  @override
  void onInit() {
    init();
    super.onInit();
  }

  void init() {
    getNotificationListApi();
  }

  /// 🔹 Fetch all notifications
  Future<void> getNotificationListApi() async {
    isLoading = true;
    update(); // notify UI

    notificationResponseModel = await NotificationApi.callApi();

    notificationList.clear();
    notificationList.addAll(notificationResponseModel?.notification ?? []);

    Utils.showLog("📬 Notification list data => $notificationList");

    isLoading = false;
    update(); // notify UI
  }

  /// 🔹 Pull-to-refresh action
  Future<void> onRefresh() async {
    await getNotificationListApi();
  }

  /// 🔹 Format date for display
  String formatAsMonthDayYear(dynamic value) {
    if (value == null) return '';
    DateTime dt;

    if (value is DateTime) {
      dt = value;
    } else if (value is int) {
      dt = DateTime.fromMillisecondsSinceEpoch(value);
    } else {
      dt = DateTime.parse(value.toString());
    }

    dt = dt.toLocal();
    return DateFormat('MMMM d, y', 'en_US').format(dt);
  }

  /// 🔹 Clear all notifications and then refresh list
  Future<void> clearNotificationApi() async {
    final response = await ClearNotificationsApi.callApi();

    if (response != null && response.status == true) {
      Utils.showToast(
        Get.context!,
        response.message ?? "Notifications cleared successfully!",
      );

      // ✅ Call get all notifications again after clear success
      await getNotificationListApi();
    } else {
      Utils.showToast(
        Get.context!,
        response?.message ?? "Failed to clear notifications",
      );
    }
  }
}
