// To parse this JSON data, do
//
//     final mostLikeResponseModel = mostLikeResponseModelFromJson(jsonString);

import 'dart:convert';

MostLikeResponseModel mostLikeResponseModelFromJson(String str) => MostLikeResponseModel.fromJson(json.decode(str));

String mostLikeResponseModelToJson(MostLikeResponseModel data) => json.encode(data.toJson());

class MostLikeResponseModel {
  bool? status;
  String? message;
  List<MostLikeData>? data;

  MostLikeResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory MostLikeResponseModel.fromJson(Map<String, dynamic> json) => MostLikeResponseModel(
    status: json["status"],
    message: json["message"],
    data: json["data"] == null ? [] : List<MostLikeData>.from(json["data"]!.map((x) => MostLikeData.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "status": status,
    "message": message,
    "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
  };
}

class MostLikeData {
  String? id;
  Seller? seller;
  int? status;
  String? title;
  String? subTitle;
  String? description;
  int? availableUnits;
  String? primaryImage;
  List<String>? galleryImages;
  Location? location;
  int? saleType;
  double? price;
  bool? isAuctionEnabled;
  int? auctionStartingPrice;
  DateTime? auctionEndDate;
  DateTime? createdAt;
  int? likesCount;
  int? viewsCount;
  bool? isLike;

  MostLikeData({
    this.id,
    this.seller,
    this.status,
    this.title,
    this.subTitle,
    this.description,
    this.availableUnits,
    this.primaryImage,
    this.galleryImages,
    this.location,
    this.saleType,
    this.price,
    this.isAuctionEnabled,
    this.auctionStartingPrice,
    this.auctionEndDate,
    this.createdAt,
    this.likesCount,
    this.viewsCount,
    this.isLike,
  });

  factory MostLikeData.fromJson(Map<String, dynamic> json) => MostLikeData(
    id: json["_id"],
    seller: json["seller"] == null ? null : Seller.fromJson(json["seller"]),
    status: json["status"],
    title: json["title"],
    subTitle: json["subTitle"],
    description: json["description"],
    availableUnits: json["availableUnits"],
    primaryImage: json["primaryImage"],
    galleryImages: json["galleryImages"] == null ? [] : List<String>.from(json["galleryImages"]!.map((x) => x)),
    location: json["location"] == null ? null : Location.fromJson(json["location"]),
    saleType: json["saleType"],
    price: json["price"]?.toDouble(),
    isAuctionEnabled: json["isAuctionEnabled"],
    auctionStartingPrice: json["auctionStartingPrice"],
    auctionEndDate: json["auctionEndDate"] == null ? null : DateTime.parse(json["auctionEndDate"]),
    createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
    likesCount: json["likesCount"],
    viewsCount: json["viewsCount"],
    isLike: json["isLike"],
  );

  Map<String, dynamic> toJson() => {
    "_id": id,
    "seller": seller?.toJson(),
    "status": status,
    "title": title,
    "subTitle": subTitle,
    "description": description,
    "availableUnits": availableUnits,
    "primaryImage": primaryImage,
    "galleryImages": galleryImages == null ? [] : List<dynamic>.from(galleryImages!.map((x) => x)),
    "location": location?.toJson(),
    "saleType": saleType,
    "price": price,
    "isAuctionEnabled": isAuctionEnabled,
    "auctionStartingPrice": auctionStartingPrice,
    "auctionEndDate": auctionEndDate?.toIso8601String(),
    "createdAt": createdAt?.toIso8601String(),
    "likesCount": likesCount,
    "viewsCount": viewsCount,
    "isLike": isLike,
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
  bool? isVerified;
  int? averageRating;
  int? totalRating;
  bool? isFeaturedSeller;

  Seller({
    this.id,
    this.name,
    this.profileImage,
    this.isVerified,
    this.averageRating,
    this.totalRating,
    this.isFeaturedSeller,
  });

  factory Seller.fromJson(Map<String, dynamic> json) => Seller(
    id: json["_id"],
    name: json["name"],
    profileImage: json["profileImage"],
    isVerified: json["isVerified"],
    averageRating: json["averageRating"],
    totalRating: json["totalRating"],
    isFeaturedSeller: json["isFeaturedSeller"],
  );

  Map<String, dynamic> toJson() => {
    "_id": id,
    "name": name,
    "profileImage": profileImage,
    "isVerified": isVerified,
    "averageRating": averageRating,
    "totalRating": totalRating,
    "isFeaturedSeller": isFeaturedSeller,
  };
}
