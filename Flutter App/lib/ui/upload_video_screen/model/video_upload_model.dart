// To parse this JSON data, do
//
//     final videoUploadModel = videoUploadModelFromJson(jsonString);

import 'dart:convert';

VideoUploadModel videoUploadModelFromJson(String str) => VideoUploadModel.fromJson(json.decode(str));

String videoUploadModelToJson(VideoUploadModel data) => json.encode(data.toJson());

class VideoUploadModel {
  bool? status;
  String? message;
  VideoData? data;

  VideoUploadModel({
    this.status,
    this.message,
    this.data,
  });

  factory VideoUploadModel.fromJson(Map<String, dynamic> json) => VideoUploadModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] != null ? VideoData.fromJson(json["data"]) : null,
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data?.toJson(),
      };
}

class VideoData {
  String? uploader;
  String? ad;
  String? videoUrl;
  String? thumbnailUrl;
  String? caption;
  int? shares;
  String? id;
  DateTime? createdAt;
  DateTime? updatedAt;

  VideoData({
    this.uploader,
    this.ad,
    this.videoUrl,
    this.thumbnailUrl,
    this.caption,
    this.shares,
    this.id,
    this.createdAt,
    this.updatedAt,
  });

  factory VideoData.fromJson(Map<String, dynamic> json) => VideoData(
        uploader: json["uploader"],
        ad: json["ad"],
        videoUrl: json["videoUrl"],
        thumbnailUrl: json["thumbnailUrl"],
        caption: json["caption"],
        shares: json["shares"],
        id: json["_id"],
        createdAt: json["createdAt"] != null ? DateTime.parse(json["createdAt"]) : null,
        updatedAt: json["updatedAt"] != null ? DateTime.parse(json["updatedAt"]) : null,
      );

  Map<String, dynamic> toJson() => {
        "uploader": uploader,
        "ad": ad,
        "videoUrl": videoUrl,
        "thumbnailUrl": thumbnailUrl,
        "caption": caption,
        "shares": shares,
        "_id": id,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
