// To parse this JSON data, do
//
//     final giveReviewResponseModel = giveReviewResponseModelFromJson(jsonString);

import 'dart:convert';

GiveReviewResponseModel giveReviewResponseModelFromJson(String str) => GiveReviewResponseModel.fromJson(json.decode(str));

String giveReviewResponseModelToJson(GiveReviewResponseModel data) => json.encode(data.toJson());

class GiveReviewResponseModel {
  bool? status;
  String? message;

  GiveReviewResponseModel({
    this.status,
    this.message,
  });

  factory GiveReviewResponseModel.fromJson(Map<String, dynamic> json) => GiveReviewResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
