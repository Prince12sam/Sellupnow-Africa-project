// To parse this JSON data, do
//
//     final userFollowUnFollowResponseModel = userFollowUnFollowResponseModelFromJson(jsonString);

import 'dart:convert';

UserFollowUnFollowResponseModel userFollowUnFollowResponseModelFromJson(String str) => UserFollowUnFollowResponseModel.fromJson(json.decode(str));

String userFollowUnFollowResponseModelToJson(UserFollowUnFollowResponseModel data) => json.encode(data.toJson());

class UserFollowUnFollowResponseModel {
  bool? status;
  String? message;
  bool? isFollow;

  UserFollowUnFollowResponseModel({
    this.status,
    this.message,
    this.isFollow,
  });

  factory UserFollowUnFollowResponseModel.fromJson(Map<String, dynamic> json) => UserFollowUnFollowResponseModel(
        status: json["status"],
        message: json["message"],
        isFollow: json["isFollow"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "isFollow": isFollow,
      };
}
