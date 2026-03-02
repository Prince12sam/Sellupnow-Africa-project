// To parse this JSON data, do
//
//     final specificProductLikeResponseModel = specificProductLikeResponseModelFromJson(jsonString);

import 'dart:convert';

SpecificProductLikeResponseModel specificProductLikeResponseModelFromJson(String str) => SpecificProductLikeResponseModel.fromJson(json.decode(str));

String specificProductLikeResponseModelToJson(SpecificProductLikeResponseModel data) => json.encode(data.toJson());

class SpecificProductLikeResponseModel {
  String? message;
  int? total;
  List<Like>? likes;

  SpecificProductLikeResponseModel({
    this.message,
    this.total,
    this.likes,
  });

  factory SpecificProductLikeResponseModel.fromJson(Map<String, dynamic> json) => SpecificProductLikeResponseModel(
        message: json["message"],
        total: json["total"],
        likes: json["likes"] == null ? [] : List<Like>.from(json["likes"]!.map((x) => Like.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "message": message,
        "total": total,
        "likes": likes == null ? [] : List<dynamic>.from(likes!.map((x) => x.toJson())),
      };
}

class Like {
  String? id;
  String? ad;
  User? user;
  DateTime? likedAt;
  DateTime? createdAt;
  DateTime? updatedAt;

  Like({
    this.id,
    this.ad,
    this.user,
    this.likedAt,
    this.createdAt,
    this.updatedAt,
  });

  factory Like.fromJson(Map<String, dynamic> json) => Like(
        id: json["_id"],
        ad: json["ad"],
        user: json["user"] == null ? null : User.fromJson(json["user"]),
        likedAt: json["likedAt"] == null ? null : DateTime.parse(json["likedAt"]),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "ad": ad,
        "user": user?.toJson(),
        "likedAt": likedAt?.toIso8601String(),
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}

class User {
  String? id;
  String? profileId;
  String? name;
  String? profileImage;

  User({
    this.id,
    this.profileId,
    this.name,
    this.profileImage,
  });

  factory User.fromJson(Map<String, dynamic> json) => User(
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
