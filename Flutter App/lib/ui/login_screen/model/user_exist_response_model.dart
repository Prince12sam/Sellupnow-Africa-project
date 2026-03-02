// To parse this JSON data, do
//
//     final userExistResponseModel = userExistResponseModelFromJson(jsonString);

import 'dart:convert';

UserExistResponseModel userExistResponseModelFromJson(String str) => UserExistResponseModel.fromJson(json.decode(str));

String userExistResponseModelToJson(UserExistResponseModel data) => json.encode(data.toJson());

class UserExistResponseModel {
  bool? status;
  String? message;
  bool? isLogin;

  UserExistResponseModel({
    this.status,
    this.message,
    this.isLogin,
  });

  factory UserExistResponseModel.fromJson(Map<String, dynamic> json) => UserExistResponseModel(
        status: json["status"],
        message: json["message"],
        isLogin: json["isLogin"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "isLogin": isLogin,
      };
}
