// To parse this JSON data, do
//
//     final adReportResponseModel = adReportResponseModelFromJson(jsonString);

import 'dart:convert';

AdReportResponseModel adReportResponseModelFromJson(String str) => AdReportResponseModel.fromJson(json.decode(str));

String adReportResponseModelToJson(AdReportResponseModel data) => json.encode(data.toJson());

class AdReportResponseModel {
  bool? status;
  String? message;

  AdReportResponseModel({
    this.status,
    this.message,
  });

  factory AdReportResponseModel.fromJson(Map<String, dynamic> json) => AdReportResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
