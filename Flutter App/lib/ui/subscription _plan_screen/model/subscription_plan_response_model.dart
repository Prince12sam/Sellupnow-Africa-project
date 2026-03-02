// To parse this JSON data, do
//
//     final subscriptionPlanResponseModel = subscriptionPlanResponseModelFromJson(jsonString);

import 'dart:convert';

SubscriptionPlanResponseModel subscriptionPlanResponseModelFromJson(String str) => SubscriptionPlanResponseModel.fromJson(json.decode(str));

String subscriptionPlanResponseModelToJson(SubscriptionPlanResponseModel data) => json.encode(data.toJson());

class SubscriptionPlanResponseModel {
  bool? status;
  String? message;
  List<SubscriptionPlan>? data;

  SubscriptionPlanResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory SubscriptionPlanResponseModel.fromJson(Map<String, dynamic> json) => SubscriptionPlanResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<SubscriptionPlan>.from(json["data"]!.map((x) => SubscriptionPlan.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class SubscriptionPlan {
  String? id;
  String? name;
  num? price;
  num? discount;
  num? finalPrice;
  String? image;
  String? description;
  Advertisements? days;
  Advertisements? advertisements;
  bool? isActive;
  DateTime? createdAt;
  DateTime? updatedAt;

  SubscriptionPlan({
    this.id,
    this.name,
    this.price,
    this.discount,
    this.finalPrice,
    this.image,
    this.description,
    this.days,
    this.advertisements,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory SubscriptionPlan.fromJson(Map<String, dynamic> json) => SubscriptionPlan(
        id: json["_id"],
        name: json["name"],
        price: json["price"],
        discount: json["discount"],
        finalPrice: json["finalPrice"],
        image: json["image"],
        description: json["description"],
        days: json["days"] == null ? null : Advertisements.fromJson(json["days"]),
        advertisements: json["advertisements"] == null ? null : Advertisements.fromJson(json["advertisements"]),
        isActive: json["isActive"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "price": price,
        "discount": discount,
        "finalPrice": finalPrice,
        "image": image,
        "description": description,
        "days": days?.toJson(),
        "advertisements": advertisements?.toJson(),
        "isActive": isActive,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}

class Advertisements {
  bool? isLimited;
  num? value;

  Advertisements({
    this.isLimited,
    this.value,
  });

  factory Advertisements.fromJson(Map<String, dynamic> json) => Advertisements(
        isLimited: json["isLimited"],
        value: json["value"],
      );

  Map<String, dynamic> toJson() => {
        "isLimited": isLimited,
        "value": value,
      };
}
