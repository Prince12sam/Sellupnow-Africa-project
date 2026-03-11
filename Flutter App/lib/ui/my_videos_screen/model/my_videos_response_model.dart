// To parse this JSON data, do
//
//     final myVideosResponseModel = myVideosResponseModelFromJson(jsonString);

import 'dart:convert';

MyVideosResponseModel myVideosResponseModelFromJson(String str) =>
    MyVideosResponseModel.fromJson(json.decode(str));

String myVideosResponseModelToJson(MyVideosResponseModel data) =>
    json.encode(data.toJson());

class MyVideosResponseModel {
  bool? status;
  String? message;
  List<MyVideo>? data;

  MyVideosResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory MyVideosResponseModel.fromJson(Map<String, dynamic> json) =>
      MyVideosResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null
            ? []
            : List<MyVideo>.from(json["data"]!.map((x) => MyVideo.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null
            ? []
            : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class MyVideo {
  String? id;
  Uploader? uploader;
  String? ad;
  String? videoUrl;
  String? thumbnailUrl;
  String? adImageUrl;
  String? caption;
  int? shares;
  DateTime? createdAt;
  AdDetails? adDetails;
  int? totalLikes;
  bool? isLike;
  bool? isFollow;
  bool? isSponsored;
  bool? isActive;
  String? adType;
  String? ctaText;
  String? ctaUrl;
  DateTime? startAt;
  DateTime? endAt;
  int? priority;
  BottomAd? bottomAd;

  MyVideo({
    this.id,
    this.uploader,
    this.ad,
    this.videoUrl,
    this.thumbnailUrl,
    this.adImageUrl,
    this.caption,
    this.shares,
    this.createdAt,
    this.adDetails,
    this.totalLikes,
    this.isLike,
    this.isFollow,
    this.isSponsored,
    this.isActive,
    this.adType,
    this.ctaText,
    this.ctaUrl,
    this.startAt,
    this.endAt,
    this.priority,
    this.bottomAd,
  });

  factory MyVideo.fromJson(Map<String, dynamic> json) => MyVideo(
        id: json["_id"],
        uploader: json["uploader"] == null
            ? null
            : Uploader.fromJson(json["uploader"]),
      ad: json["ad"]?.toString(),
        videoUrl: json["videoUrl"],
        thumbnailUrl: json["thumbnailUrl"],
        adImageUrl: json["adImageUrl"],
        caption: json["caption"],
        shares: json["shares"],
        createdAt: json["createdAt"] == null
            ? null
            : DateTime.parse(json["createdAt"]),
        adDetails: json["adDetails"] == null
            ? null
            : AdDetails.fromJson(json["adDetails"]),
        totalLikes: json["totalLikes"],
        isLike: json["isLike"],
        isFollow: json["isFollow"],
        isSponsored: json["isSponsored"],
        isActive: json["isActive"],
        adType: json["adType"],
        ctaText: json["ctaText"],
        ctaUrl: json["ctaUrl"],
        startAt:
            json["startAt"] == null ? null : DateTime.parse(json["startAt"]),
        endAt: json["endAt"] == null ? null : DateTime.parse(json["endAt"]),
        priority: json["priority"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "uploader": uploader?.toJson(),
        "ad": ad,
        "videoUrl": videoUrl,
        "thumbnailUrl": thumbnailUrl,
        "adImageUrl": adImageUrl,
        "caption": caption,
        "shares": shares,
        "createdAt": createdAt?.toIso8601String(),
        "adDetails": adDetails?.toJson(),
        "totalLikes": totalLikes,
        "isLike": isLike,
        "isFollow": isFollow,
        "isSponsored": isSponsored,
        "isActive": isActive,
        "adType": adType,
        "ctaText": ctaText,
        "ctaUrl": ctaUrl,
        "startAt": startAt?.toIso8601String(),
        "endAt": endAt?.toIso8601String(),
        "priority": priority,
      };
}

class BottomAd {
  final String imageUrl;
  final String title;
  final String? subtitle;
  final String? ctaText;
  final String? ctaUrl;

  BottomAd({
    required this.imageUrl,
    required this.title,
    this.subtitle,
    this.ctaText,
    this.ctaUrl,
  });
}

class AdDetails {
  String? title;
  String? subTitle;
  String? description;
  String? primaryImage;
  Location? location;
  double? price;

  AdDetails({
    this.title,
    this.subTitle,
    this.description,
    this.primaryImage,
    this.location,
    this.price,
  });

  factory AdDetails.fromJson(Map<String, dynamic> json) => AdDetails(
        title: json["title"],
        subTitle: json["subTitle"],
        description: json["description"],
        primaryImage: json["primaryImage"],
        location: json["location"] == null
            ? null
            : Location.fromJson(json["location"]),
        price: json["price"]?.toDouble(),
      );

  Map<String, dynamic> toJson() => {
        "title": title,
        "subTitle": subTitle,
        "description": description,
        "primaryImage": primaryImage,
        "location": location?.toJson(),
        "price": price,
      };
}

class Location {
  String? country;
  String? state;
  String? city;
  double? latitude;
  double? longitude;
  String? fullAddress;

  Location({
    this.country,
    this.state,
    this.city,
    this.latitude,
    this.longitude,
    this.fullAddress,
  });

  factory Location.fromJson(Map<String, dynamic> json) => Location(
        country: json["country"],
        state: json["state"],
        city: json["city"],
        latitude: json["latitude"]?.toDouble(),
        longitude: json["longitude"]?.toDouble(),
        fullAddress: json["fullAddress"],
      );

  Map<String, dynamic> toJson() => {
        "country": country,
        "state": state,
        "city": city,
        "latitude": latitude,
        "longitude": longitude,
        "fullAddress": fullAddress,
      };
}

class Uploader {
  String? id;
  String? name;
  String? profileImage;
  String? registeredAt;

  Uploader({
    this.id,
    this.name,
    this.profileImage,
    this.registeredAt,
  });

  factory Uploader.fromJson(Map<String, dynamic> json) => Uploader(
        id: json["_id"],
        name: json["name"],
        profileImage: json["profileImage"],
        registeredAt: json["registeredAt"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "profileImage": profileImage,
        "registeredAt": registeredAt,
      };
}
