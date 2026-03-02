// To parse this JSON data, do
//
//     final reportUserResponseModel = reportUserResponseModelFromJson(jsonString);

import 'dart:convert';

ReportUserResponseModel reportUserResponseModelFromJson(String str) => ReportUserResponseModel.fromJson(json.decode(str));

String reportUserResponseModelToJson(ReportUserResponseModel data) => json.encode(data.toJson());

class ReportUserResponseModel {
  bool? status;
  String? message;

  ReportUserResponseModel({
    this.status,
    this.message,
  });

  factory ReportUserResponseModel.fromJson(Map<String, dynamic> json) => ReportUserResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
