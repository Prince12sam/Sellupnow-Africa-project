// To parse this JSON data, do
//
//     final reportReasonsModel = reportReasonsModelFromJson(jsonString);

import 'dart:convert';

ReportReasonsModel reportReasonsModelFromJson(String str) => ReportReasonsModel.fromJson(json.decode(str));

String reportReasonsModelToJson(ReportReasonsModel data) => json.encode(data.toJson());

class ReportReasonsModel {
  bool? status;
  String? message;
  List<Datum>? data;

  ReportReasonsModel({
    this.status,
    this.message,
    this.data,
  });

  factory ReportReasonsModel.fromJson(Map<String, dynamic> json) => ReportReasonsModel(
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
  String? title;
  DateTime? createdAt;
  DateTime? updatedAt;

  Datum({
    this.id,
    this.title,
    this.createdAt,
    this.updatedAt,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        title: json["title"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "title": title,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
