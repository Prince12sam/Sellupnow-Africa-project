// To parse this JSON data, do
//
//     final faqApiResponseModel = faqApiResponseModelFromJson(jsonString);

import 'dart:convert';

FaqApiResponseModel faqApiResponseModelFromJson(String str) => FaqApiResponseModel.fromJson(json.decode(str));

String faqApiResponseModelToJson(FaqApiResponseModel data) => json.encode(data.toJson());

class FaqApiResponseModel {
  bool? status;
  String? message;
  List<Datum>? data;

  FaqApiResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory FaqApiResponseModel.fromJson(Map<String, dynamic> json) => FaqApiResponseModel(
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
  String? question;
  String? answer;
  DateTime? createdAt;
  DateTime? updatedAt;

  Datum({
    this.id,
    this.question,
    this.answer,
    this.createdAt,
    this.updatedAt,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        question: json["question"],
        answer: json["answer"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "question": question,
        "answer": answer,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
