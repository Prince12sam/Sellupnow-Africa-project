// To parse this JSON data, do
//
//     final placeBidResponseModel = placeBidResponseModelFromJson(jsonString);

import 'dart:convert';

PlaceBidResponseModel placeBidResponseModelFromJson(String str) => PlaceBidResponseModel.fromJson(json.decode(str));

String placeBidResponseModelToJson(PlaceBidResponseModel data) => json.encode(data.toJson());

class PlaceBidResponseModel {
  bool? status;
  String? message;

  PlaceBidResponseModel({
    this.status,
    this.message,
  });

  factory PlaceBidResponseModel.fromJson(Map<String, dynamic> json) => PlaceBidResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
