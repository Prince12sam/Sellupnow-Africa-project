// To parse this JSON data, do
//
//     final featuredAdsPlanResponseModel = featuredAdsPlanResponseModelFromJson(jsonString);

import 'dart:convert';

FeaturedAdsPlanResponseModel featuredAdsPlanResponseModelFromJson(String str) => FeaturedAdsPlanResponseModel.fromJson(json.decode(str));

String featuredAdsPlanResponseModelToJson(FeaturedAdsPlanResponseModel data) => json.encode(data.toJson());

class FeaturedAdsPlanResponseModel {
  bool? status;
  String? message;
  List<Datum>? data;

  FeaturedAdsPlanResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory FeaturedAdsPlanResponseModel.fromJson(Map<String, dynamic> json) => FeaturedAdsPlanResponseModel(
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
  String? iosProductId;
  num? price;
  num? discount;
  num? finalPrice;
  String? image;
  String? description;
  num? days;
  num? advertisementLimit;
  bool? isActive;
  DateTime? createdAt;
  DateTime? updatedAt;

  Datum({
    this.id,
    this.name,
    this.iosProductId,
    this.price,
    this.discount,
    this.finalPrice,
    this.image,
    this.description,
    this.days,
    this.advertisementLimit,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        name: json["name"],
        iosProductId: json["iosProductId"],
        price: json["price"],
        discount: json["discount"],
        finalPrice: json["finalPrice"],
        image: json["image"],
        description: json["description"],
        days: json["days"],
        advertisementLimit: json["advertisementLimit"],
        isActive: json["isActive"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "iosProductId": iosProductId,
        "price": price,
        "discount": discount,
        "finalPrice": finalPrice,
        "image": image,
        "description": description,
        "days": days,
        "advertisementLimit": advertisementLimit,
        "isActive": isActive,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
