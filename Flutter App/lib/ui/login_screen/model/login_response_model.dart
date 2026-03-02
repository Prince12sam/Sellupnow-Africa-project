// To parse this JSON data, do
//
//     final loginApiResponseModel = loginApiResponseModelFromJson(jsonString);

import 'dart:convert';

LoginApiResponseModel loginApiResponseModelFromJson(String str) => LoginApiResponseModel.fromJson(json.decode(str));

String loginApiResponseModelToJson(LoginApiResponseModel data) => json.encode(data.toJson());

class LoginApiResponseModel {
  bool? status;
  String? message;
  bool? signUp;
  User? user;

  LoginApiResponseModel({
    this.status,
    this.message,
    this.signUp,
    this.user,
  });

  factory LoginApiResponseModel.fromJson(Map<String, dynamic> json) => LoginApiResponseModel(
        status: json["status"],
        message: json["message"],
        signUp: json["signUp"],
        user: json["user"] == null ? null : User.fromJson(json["user"]),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "signUp": signUp,
        "user": user?.toJson(),
      };
}

class User {
  String? id;
  int? loginType;
  String? name;
  String? profileImage;
  String? fcmToken;

  User({
    this.id,
    this.loginType,
    this.name,
    this.profileImage,
    this.fcmToken,
  });

  factory User.fromJson(Map<String, dynamic> json) => User(
        id: json["_id"],
        loginType: json["loginType"],
        name: json["name"],
        profileImage: json["profileImage"],
        fcmToken: json["fcmToken"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "loginType": loginType,
        "name": name,
        "profileImage": profileImage,
        "fcmToken": fcmToken,
      };
}
