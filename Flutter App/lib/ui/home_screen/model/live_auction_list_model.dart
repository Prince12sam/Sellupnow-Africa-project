import 'dart:convert';

LiveAuctionProductListResponseModel liveAuctionProductListResponseModelFromJson(String str) =>
    LiveAuctionProductListResponseModel.fromJson(json.decode(str));

String liveAuctionProductListResponseModelToJson(LiveAuctionProductListResponseModel data) => json.encode(data.toJson());

class LiveAuctionProductListResponseModel {
  bool? status;
  String? message;
  List<Datum>? data;

  LiveAuctionProductListResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory LiveAuctionProductListResponseModel.fromJson(Map<String, dynamic> json) => LiveAuctionProductListResponseModel(
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
  Seller? seller;
  String? purchasedPackage;
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
  int? minimumOffer;
  int? price;
  bool? isAuctionEnabled;
  int? auctionStartingPrice;
  int? auctionDurationDays;
  DateTime? auctionStartDate;
  DateTime? auctionEndDate;
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
  List<Category>? categoryHierarchy;

  Datum({
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
    this.isActive,
    this.createdAt,
    this.likesCount,
    this.viewsCount,
    this.isLike,
    this.isPlacedBid,
    this.isViewed,
    this.categoryHierarchy,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        seller: json["seller"] == null ? null : Seller.fromJson(json["seller"]),
        purchasedPackage: json["purchasedPackage"],
        category: json["category"] == null ? null : Category.fromJson(json["category"]),
        attributes: json["attributes"] == null ? [] : List<Attribute>.from(json["attributes"]!.map((x) => Attribute.fromJson(x))),
        status: json["status"],
        title: json["title"],
        subTitle: json["subTitle"],
        description: json["description"],
        contactNumber: json["contactNumber"] != null ? int.tryParse(json["contactNumber"].toString()) : null,
        availableUnits: json["availableUnits"],
        primaryImage: json["primaryImage"],
        galleryImages: json["galleryImages"] == null ? [] : List<String>.from(json["galleryImages"]!.map((x) => x)),
        location: json["location"] == null ? null : Location.fromJson(json["location"]),
        saleType: json["saleType"],
        isOfferAllowed: json["isOfferAllowed"],
        minimumOffer: json["minimumOffer"],
        price: json["price"],
        isAuctionEnabled: json["isAuctionEnabled"],
        auctionStartingPrice: json["auctionStartingPrice"],
        auctionDurationDays: json["auctionDurationDays"],
        auctionStartDate: json["auctionStartDate"] == null ? null : DateTime.parse(json["auctionStartDate"]),
        auctionEndDate: json["auctionEndDate"] == null ? null : DateTime.parse(json["auctionEndDate"]),
        scheduledPublishDate: json["scheduledPublishDate"] == null ? null : DateTime.parse(json["scheduledPublishDate"]),
        isReservePriceEnabled: json["isReservePriceEnabled"],
        reservePriceAmount: json["reservePriceAmount"],
        isActive: json["isActive"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        likesCount: json["likesCount"],
        viewsCount: json["viewsCount"],
        isLike: json["isLike"],
        isPlacedBid: json["isPlacedBid"],
        isViewed: json["isViewed"],
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
        "auctionStartDate": auctionStartDate?.toIso8601String(),
        "auctionEndDate": auctionEndDate?.toIso8601String(),
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
