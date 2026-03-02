// To parse this JSON data, do
//
//     final specificSellerVideoListResponseModel = specificSellerVideoListResponseModelFromJson(jsonString);

import 'dart:convert';

SpecificSellerVideoListResponseModel specificSellerVideoListResponseModelFromJson(String str) =>
    SpecificSellerVideoListResponseModel.fromJson(json.decode(str));

String specificSellerVideoListResponseModelToJson(SpecificSellerVideoListResponseModel data) => json.encode(data.toJson());

class SpecificSellerVideoListResponseModel {
  bool? status;
  String? message;
  List<Datum>? data;

  SpecificSellerVideoListResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory SpecificSellerVideoListResponseModel.fromJson(Map<String, dynamic> json) => SpecificSellerVideoListResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<Datum>.from(json["data"]!.map((x) => Datum.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Datum {
  String? id;
  String? videoUrl;
  String? thumbnailUrl;
  String? caption;
  int? shares;
  DateTime? createdAt;
  int? totalLikes;
  int? totalViews;
  AdDetails? adDetails;

  Datum({
    this.id,
    this.videoUrl,
    this.thumbnailUrl,
    this.caption,
    this.shares,
    this.createdAt,
    this.totalLikes,
    this.totalViews,
    this.adDetails,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        videoUrl: json["videoUrl"],
        thumbnailUrl: json["thumbnailUrl"],
        caption: json["caption"],
        shares: json["shares"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        totalLikes: json["totalLikes"],
        totalViews: json["totalViews"],
        adDetails: json["adDetails"] == null ? null : AdDetails.fromJson(json["adDetails"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "videoUrl": videoUrl,
        "thumbnailUrl": thumbnailUrl,
        "caption": caption,
        "shares": shares,
        "createdAt": createdAt?.toIso8601String(),
        "totalLikes": totalLikes,
        "totalViews": totalViews,
        "adDetails": adDetails?.toJson(),
      };
}

class AdDetails {
  String? title;
  String? subTitle;

  AdDetails({
    this.title,
    this.subTitle,
  });

  factory AdDetails.fromJson(Map<String, dynamic> json) => AdDetails(
        title: json["title"],
        subTitle: json["subTitle"],
      );

  Map<String, dynamic> toJson() => {
        "title": title,
        "subTitle": subTitle,
      };
}
