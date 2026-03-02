// To parse this JSON data, do
//
//     final adViewResponseModel = adViewResponseModelFromJson(jsonString);

import 'dart:convert';

AdViewResponseModel adViewResponseModelFromJson(String str) => AdViewResponseModel.fromJson(json.decode(str));

String adViewResponseModelToJson(AdViewResponseModel data) => json.encode(data.toJson());

class AdViewResponseModel {
  bool? status;
  String? message;
  View? view;

  AdViewResponseModel({
    this.status,
    this.message,
    this.view,
  });

  factory AdViewResponseModel.fromJson(Map<String, dynamic> json) => AdViewResponseModel(
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
  String? ad;
  String? user;
  String? id;
  DateTime? viewedAt;
  DateTime? createdAt;
  DateTime? updatedAt;

  View({
    this.ad,
    this.user,
    this.id,
    this.viewedAt,
    this.createdAt,
    this.updatedAt,
  });

  factory View.fromJson(Map<String, dynamic> json) => View(
        ad: json["ad"],
        user: json["user"],
        id: json["_id"],
        viewedAt: json["viewedAt"] == null ? null : DateTime.parse(json["viewedAt"]),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "ad": ad,
        "user": user,
        "_id": id,
        "viewedAt": viewedAt?.toIso8601String(),
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
