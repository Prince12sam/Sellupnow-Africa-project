// To parse this JSON data, do
//
//     final userBlockResponseModel = userBlockResponseModelFromJson(jsonString);

import 'dart:convert';

UserBlockResponseModel userBlockResponseModelFromJson(String str) => UserBlockResponseModel.fromJson(json.decode(str));

String userBlockResponseModelToJson(UserBlockResponseModel data) => json.encode(data.toJson());

class UserBlockResponseModel {
  bool? status;
  String? message;
  String? action;

  UserBlockResponseModel({
    this.status,
    this.message,
    this.action,
  });

  factory UserBlockResponseModel.fromJson(Map<String, dynamic> json) => UserBlockResponseModel(
        status: json["status"],
        message: json["message"],
        action: json["action"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "action": action,
      };
}
