// To parse this JSON data, do
//
//     final createAdListingResponseModel = createAdListingResponseModelFromJson(jsonString);

import 'dart:convert';

CreateAdListingResponseModel createAdListingResponseModelFromJson(String str) => CreateAdListingResponseModel.fromJson(json.decode(str));

String createAdListingResponseModelToJson(CreateAdListingResponseModel data) => json.encode(data.toJson());

class CreateAdListingResponseModel {
  bool? status;
  String? message;

  CreateAdListingResponseModel({
    this.status,
    this.message,
  });

  factory CreateAdListingResponseModel.fromJson(Map<String, dynamic> json) => CreateAdListingResponseModel(
      status: json["status"] ?? (json["data"] != null),
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
