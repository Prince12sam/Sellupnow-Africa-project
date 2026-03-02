// To parse this JSON data, do
//
//     final categoryAttributesResponseModel = categoryAttributesResponseModelFromJson(jsonString);

import 'dart:convert';

CategoryAttributesResponseModel categoryAttributesResponseModelFromJson(String str) => CategoryAttributesResponseModel.fromJson(json.decode(str));

String categoryAttributesResponseModelToJson(CategoryAttributesResponseModel data) => json.encode(data.toJson());

class CategoryAttributesResponseModel {
  bool? status;
  String? message;
  List<Attribute>? data;

  CategoryAttributesResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory CategoryAttributesResponseModel.fromJson(Map<String, dynamic> json) => CategoryAttributesResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<Attribute>.from(json["data"]!.map((x) => Attribute.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Attribute {
  String? id;
  String? name;
  String? image;
  int? fieldType;
  List<String>? values;
  int? minLength;
  int? maxLength;
  bool? isRequired;
  bool? isActive;
  String? categoryId;
  DateTime? createdAt;
  DateTime? updatedAt;

  Attribute({
    this.id,
    this.name,
    this.image,
    this.fieldType,
    this.values,
    this.minLength,
    this.maxLength,
    this.isRequired,
    this.isActive,
    this.categoryId,
    this.createdAt,
    this.updatedAt,
  });

  factory Attribute.fromJson(Map<String, dynamic> json) => Attribute(
        id: json["_id"],
        name: json["name"],
        image: json["image"],
        fieldType: json["fieldType"],
        values: json["values"] == null ? [] : List<String>.from(json["values"]!.map((x) => x)),
        minLength: json["minLength"],
        maxLength: json["maxLength"],
        isRequired: json["isRequired"],
        isActive: json["isActive"],
        categoryId: json["categoryId"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "image": image,
        "fieldType": fieldType,
        "values": values == null ? [] : List<dynamic>.from(values!.map((x) => x)),
        "minLength": minLength,
        "maxLength": maxLength,
        "isRequired": isRequired,
        "isActive": isActive,
        "categoryId": categoryId,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
