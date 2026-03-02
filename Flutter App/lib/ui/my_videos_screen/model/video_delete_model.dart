// To parse this JSON data, do
//
//     final myVideoDeleteResponseModel = myVideoDeleteResponseModelFromJson(jsonString);

import 'dart:convert';

MyVideoDeleteResponseModel myVideoDeleteResponseModelFromJson(String str) => MyVideoDeleteResponseModel.fromJson(json.decode(str));

String myVideoDeleteResponseModelToJson(MyVideoDeleteResponseModel data) => json.encode(data.toJson());

class MyVideoDeleteResponseModel {
  bool? status;
  String? message;

  MyVideoDeleteResponseModel({
    this.status,
    this.message,
  });

  factory MyVideoDeleteResponseModel.fromJson(Map<String, dynamic> json) => MyVideoDeleteResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
