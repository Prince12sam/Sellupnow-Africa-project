// To parse this JSON data, do
//
//     final sellerProductInfoModel = sellerProductInfoModelFromJson(jsonString);

import 'dart:convert';

SellerProductInfoModel sellerProductInfoModelFromJson(String str) => SellerProductInfoModel.fromJson(json.decode(str));

String sellerProductInfoModelToJson(SellerProductInfoModel data) => json.encode(data.toJson());

class SellerProductInfoModel {
  bool? status;
  String? message;
  List<SellerProductInfo>? data;

  SellerProductInfoModel({
    this.status,
    this.message,
    this.data,
  });

  factory SellerProductInfoModel.fromJson(Map<String, dynamic> json) => SellerProductInfoModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<SellerProductInfo>.from(json["data"]!.map((x) => SellerProductInfo.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class SellerProductInfo {
  String? id;
  String? title;
  String? subTitle;
  String? primaryImage;
  double? price;

  SellerProductInfo({
    this.id,
    this.title,
    this.subTitle,
    this.primaryImage,
    this.price,
  });

  factory SellerProductInfo.fromJson(Map<String, dynamic> json) => SellerProductInfo(
        id: json["_id"],
        title: json["title"],
        subTitle: json["subTitle"],
        primaryImage: json["primaryImage"],
        price: json["price"]?.toDouble(),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "title": title,
        "subTitle": subTitle,
        "primaryImage": primaryImage,
        "price": price,
      };
}
