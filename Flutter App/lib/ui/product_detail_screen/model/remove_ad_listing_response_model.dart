// To parse this JSON data, do
//
//     final removeAdListingResponseModel = removeAdListingResponseModelFromJson(jsonString);

import 'dart:convert';

RemoveAdListingResponseModel removeAdListingResponseModelFromJson(String str) => RemoveAdListingResponseModel.fromJson(json.decode(str));

String removeAdListingResponseModelToJson(RemoveAdListingResponseModel data) => json.encode(data.toJson());

class RemoveAdListingResponseModel {
  bool status;
  String message;

  RemoveAdListingResponseModel({
    required this.status,
    required this.message,
  });

  factory RemoveAdListingResponseModel.fromJson(Map<String, dynamic> json) => RemoveAdListingResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
