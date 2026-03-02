// To parse this JSON data, do
//
//     final notificationPermissionResponseModel = notificationPermissionResponseModelFromJson(jsonString);

import 'dart:convert';

NotificationPermissionResponseModel notificationPermissionResponseModelFromJson(String str) =>
    NotificationPermissionResponseModel.fromJson(json.decode(str));

String notificationPermissionResponseModelToJson(NotificationPermissionResponseModel data) => json.encode(data.toJson());

class NotificationPermissionResponseModel {
  bool? status;
  String? message;

  NotificationPermissionResponseModel({
    this.status,
    this.message,
  });

  factory NotificationPermissionResponseModel.fromJson(Map<String, dynamic> json) => NotificationPermissionResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
