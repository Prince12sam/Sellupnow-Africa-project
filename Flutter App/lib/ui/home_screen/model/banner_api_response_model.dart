// To parse this JSON data, do
//
//     final bannerResponseModel = bannerResponseModelFromJson(jsonString);

import 'dart:convert';

BannerResponseModel bannerResponseModelFromJson(String str) => BannerResponseModel.fromJson(json.decode(str));

String bannerResponseModelToJson(BannerResponseModel data) => json.encode(data.toJson());

class BannerResponseModel {
  bool? status;
  String? message;
  List<BannerList>? data;

  BannerResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory BannerResponseModel.fromJson(Map<String, dynamic> json) => BannerResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<BannerList>.from(json["data"]!.map((x) => BannerList.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class BannerList {
  String? id;
  String? image;
  String? redirectUrl;
  bool? isActive;
  DateTime? createdAt;
  DateTime? updatedAt;

  BannerList({
    this.id,
    this.image,
    this.redirectUrl,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory BannerList.fromJson(Map<String, dynamic> json) => BannerList(
        id: json["_id"],
        image: json["image"],
        redirectUrl: json["redirectUrl"],
        isActive: json["isActive"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "image": image,
        "redirectUrl": redirectUrl,
        "isActive": isActive,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
