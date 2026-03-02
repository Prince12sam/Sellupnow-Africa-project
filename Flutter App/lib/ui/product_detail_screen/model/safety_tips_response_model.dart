// To parse this JSON data, do
//
//     final safetyTipsApiResponseModel = safetyTipsApiResponseModelFromJson(jsonString);

import 'dart:convert';

SafetyTipsApiResponseModel safetyTipsApiResponseModelFromJson(String str) => SafetyTipsApiResponseModel.fromJson(json.decode(str));

String safetyTipsApiResponseModelToJson(SafetyTipsApiResponseModel data) => json.encode(data.toJson());

class SafetyTipsApiResponseModel {
  bool? status;
  String? message;
  List<SafetyTips>? data;

  SafetyTipsApiResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory SafetyTipsApiResponseModel.fromJson(Map<String, dynamic> json) => SafetyTipsApiResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<SafetyTips>.from(json["data"]!.map((x) => SafetyTips.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class SafetyTips {
  String? id;
  String? description;
  bool? isActive;
  DateTime? createdAt;
  DateTime? updatedAt;

  SafetyTips({
    this.id,
    this.description,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory SafetyTips.fromJson(Map<String, dynamic> json) => SafetyTips(
        id: json["_id"],
        description: json["description"],
        isActive: json["isActive"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "description": description,
        "isActive": isActive,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
