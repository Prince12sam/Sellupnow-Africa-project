// To parse this JSON data, do
//
//     final addLikeResponseModel = addLikeResponseModelFromJson(jsonString);

import 'dart:convert';

AddLikeResponseModel addLikeResponseModelFromJson(String str) => AddLikeResponseModel.fromJson(json.decode(str));

String addLikeResponseModelToJson(AddLikeResponseModel data) => json.encode(data.toJson());

class AddLikeResponseModel {
  bool? status;
  String? message;
  bool? like;

  AddLikeResponseModel({
    this.status,
    this.message,
    this.like,
  });

  factory AddLikeResponseModel.fromJson(Map<String, dynamic> json) => AddLikeResponseModel(
        status: json["status"],
        message: json["message"],
        like: json["like"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "like": like,
      };
}
