// To parse this JSON data, do
//
//     final subCategoryResponseModel = subCategoryResponseModelFromJson(jsonString);

import 'dart:convert';

SubCategoryResponseModel subCategoryResponseModelFromJson(String str) => SubCategoryResponseModel.fromJson(json.decode(str));

String subCategoryResponseModelToJson(SubCategoryResponseModel data) => json.encode(data.toJson());

class SubCategoryResponseModel {
  bool? status;
  String? message;
  List<Datum>? data;

  SubCategoryResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory SubCategoryResponseModel.fromJson(Map<String, dynamic> json) => SubCategoryResponseModel(
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
  String? name;
  String? image;
  String? parent;

  Datum({
    this.id,
    this.name,
    this.image,
    this.parent,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        name: json["name"],
        image: json["image"],
        parent: json["parent"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "image": image,
        "parent": parent,
      };
}
