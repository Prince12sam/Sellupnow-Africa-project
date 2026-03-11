// To parse this JSON data, do
//
//     final productDetailResponseModel = productDetailResponseModelFromJson(jsonString);

import 'dart:convert';

ProductDetailResponseModel productDetailResponseModelFromJson(String str) => ProductDetailResponseModel.fromJson(json.decode(str));

String productDetailResponseModelToJson(ProductDetailResponseModel data) => json.encode(data.toJson());

class ProductDetailResponseModel {
  bool? status;
  String? message;
  Product? data;

  ProductDetailResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory ProductDetailResponseModel.fromJson(Map<String, dynamic> json) => ProductDetailResponseModel(
    status: json["status"],
    message: json["message"],
    data: json["data"] == null ? null : Product.fromJson(json["data"]),
  );

  Map<String, dynamic> toJson() => {
    "status": status,
    "message": message,
    "data": data?.toJson(),
  };
}

class Product {
  String? id;
  Seller? seller;
  Category? category;
  List<Attribute>? attributes;
  int? status;
  String? title;
  String? subTitle;
  String? description;
  int? contactNumber;
  int? availableUnits;
  String? primaryImage;
  List<String>? galleryImages;
  Location? location;
  int? saleType;
  bool? isOfferAllowed;
  double? minimumOffer;
  double? price;
  bool? isAuctionEnabled;
  int? auctionStartingPrice;
  int? auctionDurationDays;
  dynamic auctionStartDate;
  dynamic auctionEndDate;
  DateTime? scheduledPublishDate;
  bool? isReservePriceEnabled;
  int? reservePriceAmount;
  bool? isActive;
  DateTime? createdAt;
  int? likesCount;
  int? viewsCount;
  bool? isLike;
  bool? isPlacedBid;
  bool? isViewed;
  bool? isOfferPlaced;
  int? lastBidAmount;
  bool? escrowEnabled;
  List<Category>? categoryHierarchy;

  Product({
    this.id,
    this.seller,
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
    this.isActive,
    this.createdAt,
    this.likesCount,
    this.viewsCount,
    this.isLike,
    this.isPlacedBid,
    this.isViewed,
    this.isOfferPlaced,
    this.lastBidAmount,
    this.escrowEnabled,
    this.categoryHierarchy,
  });

  factory Product.fromJson(Map<String, dynamic> json) => Product(
    id: json["_id"],
    seller: json["seller"] == null ? null : Seller.fromJson(json["seller"]),
    category: json["category"] == null ? null : Category.fromJson(json["category"]),
    attributes: json["attributes"] == null ? [] : List<Attribute>.from(json["attributes"]!.map((x) => Attribute.fromJson(x))),
    status: json["status"],
    title: json["title"],
    subTitle: json["subTitle"],
    description: json["description"],
    contactNumber: json["contactNumber"] != null ? int.tryParse(json["contactNumber"].toString()) : null,
    availableUnits: json["availableUnits"] != null ? int.tryParse(json["availableUnits"].toString()) : null,
    primaryImage: json["primaryImage"],
    galleryImages: json["galleryImages"] == null ? [] : List<String>.from(json["galleryImages"]!.map((x) => x)),
    location: json["location"] == null ? null : Location.fromJson(json["location"]),
    saleType: json["saleType"],
    isOfferAllowed: json["isOfferAllowed"],
    minimumOffer: json["minimumOffer"]?.toDouble(),
    price: json["price"]?.toDouble(),
    isAuctionEnabled: json["isAuctionEnabled"],
    auctionStartingPrice: json["auctionStartingPrice"] != null ? int.tryParse(json["auctionStartingPrice"].toString()) : null,
    auctionDurationDays: json["auctionDurationDays"] != null ? int.tryParse(json["auctionDurationDays"].toString()) : null,
    auctionStartDate: json["auctionStartDate"],
    auctionEndDate: json["auctionEndDate"],
    scheduledPublishDate: json["scheduledPublishDate"] == null ? null : DateTime.parse(json["scheduledPublishDate"]),
    isReservePriceEnabled: json["isReservePriceEnabled"],
    reservePriceAmount: json["reservePriceAmount"] != null ? int.tryParse(json["reservePriceAmount"].toString()) : null,
    isActive: json["isActive"],
    createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
    likesCount: json["likesCount"] != null ? int.tryParse(json["likesCount"].toString()) : null,
    viewsCount: json["viewsCount"] != null ? int.tryParse(json["viewsCount"].toString()) : null,
    isLike: json["isLike"],
    isPlacedBid: json["isPlacedBid"],
    isViewed: json["isViewed"],
    isOfferPlaced: json["isOfferPlaced"],
    lastBidAmount: json["lastBidAmount"] != null ? int.tryParse(json["lastBidAmount"].toString()) : null,
    escrowEnabled: json["escrowEnabled"],
    categoryHierarchy: json["categoryHierarchy"] == null ? [] : List<Category>.from(json["categoryHierarchy"]!.map((x) => Category.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "_id": id,
    "seller": seller?.toJson(),
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
    "scheduledPublishDate": scheduledPublishDate?.toIso8601String(),
    "isReservePriceEnabled": isReservePriceEnabled,
    "reservePriceAmount": reservePriceAmount,
    "isActive": isActive,
    "createdAt": createdAt?.toIso8601String(),
    "likesCount": likesCount,
    "viewsCount": viewsCount,
    "isLike": isLike,
    "isPlacedBid": isPlacedBid,
    "isViewed": isViewed,
    "isOfferPlaced": isOfferPlaced,
    "lastBidAmount": lastBidAmount,
    "escrowEnabled": escrowEnabled,
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
  int? size;
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
  int? averageRating;
  int? totalRating;
  bool? isFeaturedSeller;
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
    this.isFeaturedSeller,
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
    isFeaturedSeller: json["isFeaturedSeller"],
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
    "isFeaturedSeller": isFeaturedSeller,
    "registeredAt": registeredAt,
    "createdAt": createdAt?.toIso8601String(),
  };
}
