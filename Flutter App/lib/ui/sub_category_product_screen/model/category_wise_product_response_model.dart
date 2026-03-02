// To parse this JSON data, do
//
//     final categoryWiseProductResponseModel = categoryWiseProductResponseModelFromJson(jsonString);

import 'dart:convert';

CategoryWiseProductResponseModel categoryWiseProductResponseModelFromJson(String str) => CategoryWiseProductResponseModel.fromJson(json.decode(str));

String categoryWiseProductResponseModelToJson(CategoryWiseProductResponseModel data) => json.encode(data.toJson());

class CategoryWiseProductResponseModel {
  bool? status;
  String? message;
  List<CategoryWiseProduct>? data;

  CategoryWiseProductResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory CategoryWiseProductResponseModel.fromJson(Map<String, dynamic> json) => CategoryWiseProductResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<CategoryWiseProduct>.from(json["data"]!.map((x) => CategoryWiseProduct.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class CategoryWiseProduct {
  String? id;
  Seller? seller;
  String? purchasedPackage;
  Category? category;
  List<Attribute>? attributes;
  num? status;
  String? title;
  String? subTitle;
  String? description;
  num? contactNumber;
  num? availableUnits;
  String? primaryImage;
  List<String>? galleryImages;
  Location? location;
  num? saleType;
  bool? isOfferAllowed;
  num? minimumOffer;
  double? price;
  bool? isAuctionEnabled;
  num? auctionStartingPrice;
  num? auctionDurationDays;
  dynamic auctionStartDate;
  dynamic auctionEndDate;
  dynamic scheduledPublishDate;
  bool? isReservePriceEnabled;
  num? reservePriceAmount;
  bool? isFake;
  bool? isActive;
  DateTime? createdAt;
  num? likesCount;
  num? viewsCount;
  bool? isLike;
  List<Category>? categoryHierarchy;

  CategoryWiseProduct({
    this.id,
    this.seller,
    this.purchasedPackage,
    this.category,
    this.attributes,
    this.status,
    this.title,
    this.subTitle,
    this.description,
    this.contactNumber,
    this.availableUnits,
    this.primaryImage,
    this.galleryImages,
    this.location,
    this.saleType,
    this.isOfferAllowed,
    this.minimumOffer,
    this.price,
    this.isAuctionEnabled,
    this.auctionStartingPrice,
    this.auctionDurationDays,
    this.auctionStartDate,
    this.auctionEndDate,
    this.scheduledPublishDate,
    this.isReservePriceEnabled,
    this.reservePriceAmount,
    this.isFake,
    this.isActive,
    this.createdAt,
    this.likesCount,
    this.viewsCount,
    this.isLike,
    this.categoryHierarchy,
  });

  factory CategoryWiseProduct.fromJson(Map<String, dynamic> json) => CategoryWiseProduct(
        id: json["_id"],
        seller: json["seller"] == null ? null : Seller.fromJson(json["seller"]),
        purchasedPackage: json["purchasedPackage"],
        category: json["category"] == null ? null : Category.fromJson(json["category"]),
        attributes: json["attributes"] == null ? [] : List<Attribute>.from(json["attributes"]!.map((x) => Attribute.fromJson(x))),
        status: json["status"],
        title: json["title"],
        subTitle: json["subTitle"],
        description: json["description"],
        contactNumber: json["contactNumber"],
        availableUnits: json["availableUnits"],
        primaryImage: json["primaryImage"],
        galleryImages: json["galleryImages"] == null ? [] : List<String>.from(json["galleryImages"]!.map((x) => x)),
        location: json["location"] == null ? null : Location.fromJson(json["location"]),
        saleType: json["saleType"],
        isOfferAllowed: json["isOfferAllowed"],
        minimumOffer: json["minimumOffer"],
        price: json["price"].toDouble(),
        isAuctionEnabled: json["isAuctionEnabled"],
        auctionStartingPrice: json["auctionStartingPrice"],
        auctionDurationDays: json["auctionDurationDays"],
        auctionStartDate: json["auctionStartDate"],
        auctionEndDate: json["auctionEndDate"],
        scheduledPublishDate: json["scheduledPublishDate"],
        isReservePriceEnabled: json["isReservePriceEnabled"],
        reservePriceAmount: json["reservePriceAmount"],
        isFake: json["isFake"],
        isActive: json["isActive"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        likesCount: json["likesCount"],
        viewsCount: json["viewsCount"],
        isLike: json["isLike"],
        categoryHierarchy: json["categoryHierarchy"] == null ? [] : List<Category>.from(json["categoryHierarchy"]!.map((x) => Category.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "seller": seller?.toJson(),
        "purchasedPackage": purchasedPackage,
        "category": category?.toJson(),
        "attributes": attributes == null ? [] : List<dynamic>.from(attributes!.map((x) => x.toJson())),
        "status": status,
        "title": title,
        "subTitle": subTitle,
        "description": description,
        "contactNumber": contactNumber,
        "availableUnits": availableUnits,
        "primaryImage": primaryImage,
        "galleryImages": galleryImages == null ? [] : List<dynamic>.from(galleryImages!.map((x) => x)),
        "location": location?.toJson(),
        "saleType": saleType,
        "isOfferAllowed": isOfferAllowed,
        "minimumOffer": minimumOffer,
        "price": price,
        "isAuctionEnabled": isAuctionEnabled,
        "auctionStartingPrice": auctionStartingPrice,
        "auctionDurationDays": auctionDurationDays,
        "auctionStartDate": auctionStartDate,
        "auctionEndDate": auctionEndDate,
        "scheduledPublishDate": scheduledPublishDate,
        "isReservePriceEnabled": isReservePriceEnabled,
        "reservePriceAmount": reservePriceAmount,
        "isFake": isFake,
        "isActive": isActive,
        "createdAt": createdAt?.toIso8601String(),
        "likesCount": likesCount,
        "viewsCount": viewsCount,
        "isLike": isLike,
        "categoryHierarchy": categoryHierarchy == null ? [] : List<dynamic>.from(categoryHierarchy!.map((x) => x.toJson())),
      };
}

class Attribute {
  String? name;
  dynamic value;
  String? image;

  Attribute({
    this.name,
    this.value,
    this.image,
  });

  factory Attribute.fromJson(Map<String, dynamic> json) => Attribute(
        name: json["name"],
        value: json["value"],
        image: json["image"],
      );

  Map<String, dynamic> toJson() => {
        "name": name,
        "value": value,
        "image": image,
      };
}

class ValueClass {
  String? name;
  num? size;
  String? extension;

  ValueClass({
    this.name,
    this.size,
    this.extension,
  });

  factory ValueClass.fromJson(Map<String, dynamic> json) => ValueClass(
        name: json["name"],
        size: json["size"],
        extension: json["extension"],
      );

  Map<String, dynamic> toJson() => {
        "name": name,
        "size": size,
        "extension": extension,
      };
}

class Category {
  String? id;
  String? name;
  String? image;

  Category({
    this.id,
    this.name,
    this.image,
  });

  factory Category.fromJson(Map<String, dynamic> json) => Category(
        id: json["_id"],
        name: json["name"],
        image: json["image"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "image": image,
      };
}

class Location {
  String? country;
  String? state;
  String? city;
  double? latitude;
  double? longitude;
  String? fullAddress;

  Location({
    this.country,
    this.state,
    this.city,
    this.latitude,
    this.longitude,
    this.fullAddress,
  });

  factory Location.fromJson(Map<String, dynamic> json) => Location(
        country: json["country"],
        state: json["state"],
        city: json["city"],
        latitude: json["latitude"]?.toDouble(),
        longitude: json["longitude"]?.toDouble(),
        fullAddress: json["fullAddress"],
      );

  Map<String, dynamic> toJson() => {
        "country": country,
        "state": state,
        "city": city,
        "latitude": latitude,
        "longitude": longitude,
        "fullAddress": fullAddress,
      };
}

class Seller {
  String? id;
  String? name;
  String? profileImage;
  String? phoneNumber;
  String? email;
  bool? isVerified;
  num? averageRating;
  num? totalRating;
  String? registeredAt;
  DateTime? createdAt;

  Seller({
    this.id,
    this.name,
    this.profileImage,
    this.phoneNumber,
    this.email,
    this.isVerified,
    this.averageRating,
    this.totalRating,
    this.registeredAt,
    this.createdAt,
  });

  factory Seller.fromJson(Map<String, dynamic> json) => Seller(
        id: json["_id"],
        name: json["name"],
        profileImage: json["profileImage"],
        phoneNumber: json["phoneNumber"],
        email: json["email"],
        isVerified: json["isVerified"],
        averageRating: json["averageRating"],
        totalRating: json["totalRating"],
        registeredAt: json["registeredAt"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "profileImage": profileImage,
        "phoneNumber": phoneNumber,
        "email": email,
        "isVerified": isVerified,
        "averageRating": averageRating,
        "totalRating": totalRating,
        "registeredAt": registeredAt,
        "createdAt": createdAt?.toIso8601String(),
      };
}
