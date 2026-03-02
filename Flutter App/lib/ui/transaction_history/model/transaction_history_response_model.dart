// To parse this JSON data, do
//
//     final transactionHistoryResponseModel = transactionHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

TransactionHistoryResponseModel transactionHistoryResponseModelFromJson(String str) => TransactionHistoryResponseModel.fromJson(json.decode(str));

String transactionHistoryResponseModelToJson(TransactionHistoryResponseModel data) => json.encode(data.toJson());

class TransactionHistoryResponseModel {
  bool? status;
  String? message;
  List<Datum>? data;

  TransactionHistoryResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory TransactionHistoryResponseModel.fromJson(Map<String, dynamic> json) => TransactionHistoryResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<Datum>.from(json["data"]!.map((x) => Datum.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Datum {
  String? id;
  String? packageType;
  num? amount;
  String? paymentGateway;
  String? transactionId;
  String? currency;
  DateTime? paidAt;

  Datum({
    this.id,
    this.packageType,
    this.amount,
    this.paymentGateway,
    this.transactionId,
    this.currency,
    this.paidAt,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        packageType: json["packageType"],
        amount: json["amount"],
        paymentGateway: json["paymentGateway"],
        transactionId: json["transactionId"],
        currency: json["currency"],
        paidAt: json["paidAt"] == null ? null : DateTime.parse(json["paidAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "packageType": packageType,
        "amount": amount,
        "paymentGateway": paymentGateway,
        "transactionId": transactionId,
        "currency": currency,
        "paidAt": paidAt?.toIso8601String(),
      };
}
