// To parse this JSON data, do
//
//     final recordVideoViewResponseModel = recordVideoViewResponseModelFromJson(jsonString);

import 'dart:convert';

RecordVideoViewResponseModel recordVideoViewResponseModelFromJson(String str) => RecordVideoViewResponseModel.fromJson(json.decode(str));

String recordVideoViewResponseModelToJson(RecordVideoViewResponseModel data) => json.encode(data.toJson());

class RecordVideoViewResponseModel {
  bool? status;
  String? message;
  View? view;

  RecordVideoViewResponseModel({
    this.status,
    this.message,
    this.view,
  });

  factory RecordVideoViewResponseModel.fromJson(Map<String, dynamic> json) => RecordVideoViewResponseModel(
        status: json["status"],
        message: json["message"],
        view: json["view"] == null ? null : View.fromJson(json["view"]),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "view": view?.toJson(),
      };
}

class View {
  String? video;
  String? user;
  String? id;
  DateTime? viewedAt;
  DateTime? createdAt;
  DateTime? updatedAt;

  View({
    this.video,
    this.user,
    this.id,
    this.viewedAt,
    this.createdAt,
    this.updatedAt,
  });

  factory View.fromJson(Map<String, dynamic> json) => View(
        video: json["video"],
        user: json["user"],
        id: json["_id"],
        viewedAt: json["viewedAt"] == null ? null : DateTime.parse(json["viewedAt"]),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "video": video,
        "user": user,
        "_id": id,
        "viewedAt": viewedAt?.toIso8601String(),
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
