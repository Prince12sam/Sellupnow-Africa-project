// To parse this JSON data, do
//
//     final idProofResponseModel = idProofResponseModelFromJson(jsonString);

import 'dart:convert';

IdProofResponseModel idProofResponseModelFromJson(String str) => IdProofResponseModel.fromJson(json.decode(str));

String idProofResponseModelToJson(IdProofResponseModel data) => json.encode(data.toJson());

class IdProofResponseModel {
  bool? status;
  String? message;
  List<IdProof>? data;

  IdProofResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory IdProofResponseModel.fromJson(Map<String, dynamic> json) => IdProofResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<IdProof>.from(json["data"]!.map((x) => IdProof.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class IdProof {
  String? id;
  String? title;
  bool? isActive;
  DateTime? createdAt;
  DateTime? updatedAt;

  IdProof({
    this.id,
    this.title,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory IdProof.fromJson(Map<String, dynamic> json) => IdProof(
        id: json["_id"],
        title: json["title"],
        isActive: json["isActive"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "title": title,
        "isActive": isActive,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
