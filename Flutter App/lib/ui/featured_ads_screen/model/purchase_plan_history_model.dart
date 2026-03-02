// To parse this JSON data, do
//
//     final purchasePlanHistoryResponseModel = purchasePlanHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

PurchasePlanHistoryResponseModel purchasePlanHistoryResponseModelFromJson(String str) => PurchasePlanHistoryResponseModel.fromJson(json.decode(str));

String purchasePlanHistoryResponseModelToJson(PurchasePlanHistoryResponseModel data) => json.encode(data.toJson());

class PurchasePlanHistoryResponseModel {
  bool? status;
  String? message;

  PurchasePlanHistoryResponseModel({
    this.status,
    this.message,
  });

  factory PurchasePlanHistoryResponseModel.fromJson(Map<String, dynamic> json) => PurchasePlanHistoryResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
