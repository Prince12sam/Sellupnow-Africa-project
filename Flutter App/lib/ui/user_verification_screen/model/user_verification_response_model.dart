// To parse this JSON data, do
//
//     final userVerificationResponseModel = userVerificationResponseModelFromJson(jsonString);

import 'dart:convert';

UserVerificationResponseModel userVerificationResponseModelFromJson(String str) => UserVerificationResponseModel.fromJson(json.decode(str));

String userVerificationResponseModelToJson(UserVerificationResponseModel data) => json.encode(data.toJson());

class UserVerificationResponseModel {
  bool status;
  String message;
  Data data;

  UserVerificationResponseModel({
    required this.status,
    required this.message,
    required this.data,
  });

  factory UserVerificationResponseModel.fromJson(Map<String, dynamic> json) => UserVerificationResponseModel(
        status: json["status"],
        message: json["message"],
        data: Data.fromJson(json["data"]),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data.toJson(),
      };
}

class Data {
  String uniqueId;
  String user;
  String idProof;
  String idProofFrontUrl;
  String idProofBackUrl;
  String reason;
  int status;
  DateTime submittedAt;
  String id;
  DateTime createdAt;
  DateTime updatedAt;

  Data({
    required this.uniqueId,
    required this.user,
    required this.idProof,
    required this.idProofFrontUrl,
    required this.idProofBackUrl,
    required this.reason,
    required this.status,
    required this.submittedAt,
    required this.id,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        uniqueId: json["uniqueId"],
        user: json["user"],
        idProof: json["idProof"],
        idProofFrontUrl: json["idProofFrontUrl"],
        idProofBackUrl: json["idProofBackUrl"],
        reason: json["reason"],
        status: json["status"],
        submittedAt: DateTime.parse(json["submittedAt"]),
        id: json["_id"],
        createdAt: DateTime.parse(json["createdAt"]),
        updatedAt: DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "uniqueId": uniqueId,
        "user": user,
        "idProof": idProof,
        "idProofFrontUrl": idProofFrontUrl,
        "idProofBackUrl": idProofBackUrl,
        "reason": reason,
        "status": status,
        "submittedAt": submittedAt.toIso8601String(),
        "_id": id,
        "createdAt": createdAt.toIso8601String(),
        "updatedAt": updatedAt.toIso8601String(),
      };
}
