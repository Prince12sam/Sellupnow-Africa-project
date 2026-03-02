// To parse this JSON data, do
//
//     final clearNotificationsResponseModel = clearNotificationsResponseModelFromJson(jsonString);

import 'dart:convert';

ClearNotificationsResponseModel clearNotificationsResponseModelFromJson(String str) => ClearNotificationsResponseModel.fromJson(json.decode(str));

String clearNotificationsResponseModelToJson(ClearNotificationsResponseModel data) => json.encode(data.toJson());

class ClearNotificationsResponseModel {
  bool? status;
  String? message;

  ClearNotificationsResponseModel({
    this.status,
    this.message,
  });

  factory ClearNotificationsResponseModel.fromJson(Map<String, dynamic> json) => ClearNotificationsResponseModel(
    status: json["status"],
    message: json["message"],
  );

  Map<String, dynamic> toJson() => {
    "status": status,
    "message": message,
  };
}
