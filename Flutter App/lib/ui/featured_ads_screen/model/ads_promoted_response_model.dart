// To parse this JSON data, do
//
//     final adsPromotedResponseModel = adsPromotedResponseModelFromJson(jsonString);

import 'dart:convert';

AdsPromotedResponseModel adsPromotedResponseModelFromJson(String str) => AdsPromotedResponseModel.fromJson(json.decode(str));

String adsPromotedResponseModelToJson(AdsPromotedResponseModel data) => json.encode(data.toJson());

class AdsPromotedResponseModel {
  bool? status;
  String? message;

  AdsPromotedResponseModel({
    this.status,
    this.message,
  });

  factory AdsPromotedResponseModel.fromJson(Map<String, dynamic> json) => AdsPromotedResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
