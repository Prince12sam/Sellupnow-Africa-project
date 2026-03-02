// To parse this JSON data, do
//
//     final specificProductViewResponseModel = specificProductViewResponseModelFromJson(jsonString);

import 'dart:convert';

SpecificProductViewResponseModel specificProductViewResponseModelFromJson(String str) => SpecificProductViewResponseModel.fromJson(json.decode(str));

String specificProductViewResponseModelToJson(SpecificProductViewResponseModel data) => json.encode(data.toJson());

class SpecificProductViewResponseModel {
  bool? status;
  String? message;
  List<AdView>? adView;

  SpecificProductViewResponseModel({
    this.status,
    this.message,
    this.adView,
  });

  factory SpecificProductViewResponseModel.fromJson(Map<String, dynamic> json) => SpecificProductViewResponseModel(
        status: json["status"],
        message: json["message"],
        adView: json["adView"] == null ? [] : List<AdView>.from(json["adView"]!.map((x) => AdView.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "adView": adView == null ? [] : List<dynamic>.from(adView!.map((x) => x.toJson())),
      };
}

class AdView {
  String? id;
  String? ad;
  User? user;
  DateTime? viewedAt;
  DateTime? createdAt;
  DateTime? updatedAt;

  AdView({
    this.id,
    this.ad,
    this.user,
    this.viewedAt,
    this.createdAt,
    this.updatedAt,
  });

  factory AdView.fromJson(Map<String, dynamic> json) => AdView(
        id: json["_id"],
        ad: json["ad"],
        user: json["user"] == null ? null : User.fromJson(json["user"]),
        viewedAt: json["viewedAt"] == null ? null : DateTime.parse(json["viewedAt"]),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "ad": ad,
        "user": user?.toJson(),
        "viewedAt": viewedAt?.toIso8601String(),
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
