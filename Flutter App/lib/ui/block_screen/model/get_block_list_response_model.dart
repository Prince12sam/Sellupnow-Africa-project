// To parse this JSON data, do
//
//     final userBlockListResponseModel = userBlockListResponseModelFromJson(jsonString);

import 'dart:convert';

UserBlockListResponseModel userBlockListResponseModelFromJson(String str) => UserBlockListResponseModel.fromJson(json.decode(str));

String userBlockListResponseModelToJson(UserBlockListResponseModel data) => json.encode(data.toJson());

class UserBlockListResponseModel {
  bool? status;
  String? message;
  List<BlockedUser>? blockedUsers;

  UserBlockListResponseModel({
    this.status,
    this.message,
    this.blockedUsers,
  });

  factory UserBlockListResponseModel.fromJson(Map<String, dynamic> json) => UserBlockListResponseModel(
        status: json["status"],
        message: json["message"],
        blockedUsers: json["blockedUsers"] == null ? [] : List<BlockedUser>.from(json["blockedUsers"]!.map((x) => BlockedUser.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "blockedUsers": blockedUsers == null ? [] : List<dynamic>.from(blockedUsers!.map((x) => x.toJson())),
      };
}

class BlockedUser {
  String? id;
  String? blockerId;
  BlockedId? blockedId;
  DateTime? createdAt;
  DateTime? updatedAt;

  BlockedUser({
    this.id,
    this.blockerId,
    this.blockedId,
    this.createdAt,
    this.updatedAt,
  });

  factory BlockedUser.fromJson(Map<String, dynamic> json) => BlockedUser(
        id: json["_id"],
        blockerId: json["blockerId"],
        blockedId: json["blockedId"] == null ? null : BlockedId.fromJson(json["blockedId"]),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "blockerId": blockerId,
        "blockedId": blockedId?.toJson(),
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}

class BlockedId {
  String? id;
  String? profileId;
  String? name;
  String? profileImage;

  BlockedId({
    this.id,
    this.profileId,
    this.name,
    this.profileImage,
  });

  factory BlockedId.fromJson(Map<String, dynamic> json) => BlockedId(
        id: json["_id"],
        profileId: json["profileId"],
        name: json["name"],
        profileImage: json["profileImage"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "profileId": profileId,
        "name": name,
        "profileImage": profileImage,
      };
}
