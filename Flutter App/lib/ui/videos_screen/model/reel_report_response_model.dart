// To parse this JSON data, do
//
//     final reelReportResponseModel = reelReportResponseModelFromJson(jsonString);

import 'dart:convert';

ReelReportResponseModel reelReportResponseModelFromJson(String str) => ReelReportResponseModel.fromJson(json.decode(str));

String reelReportResponseModelToJson(ReelReportResponseModel data) => json.encode(data.toJson());

class ReelReportResponseModel {
  bool? status;
  String? message;

  ReelReportResponseModel({
    this.status,
    this.message,
  });

  factory ReelReportResponseModel.fromJson(Map<String, dynamic> json) => ReelReportResponseModel(
        status: json["status"],
        message: json["message"],
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
      };
}
