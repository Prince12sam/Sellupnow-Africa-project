// To parse this JSON data, do
//
//     final videoLikeResponseModel = videoLikeResponseModelFromJson(jsonString);

import 'dart:convert';

VideoLikeResponseModel videoLikeResponseModelFromJson(String str) => VideoLikeResponseModel.fromJson(json.decode(str));

String videoLikeResponseModelToJson(VideoLikeResponseModel data) => json.encode(data.toJson());

class VideoLikeResponseModel {
  bool? status;
  String? message;
  bool? like;

  VideoLikeResponseModel({
    this.status,
    this.message,
    this.like,
  });

  factory VideoLikeResponseModel.fromJson(Map<String, dynamic> json) => VideoLikeResponseModel(
        status: json["status"],
        message: json["message"],
        like: json["like"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "like": like,
      };
}
