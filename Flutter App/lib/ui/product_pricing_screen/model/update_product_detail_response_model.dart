// To parse this JSON data, do
//
//     final updateProductDetailResponseModel = updateProductDetailResponseModelFromJson(jsonString);

import 'dart:convert';

UpdateProductDetailResponseModel updateProductDetailResponseModelFromJson(String str) => UpdateProductDetailResponseModel.fromJson(json.decode(str));

String updateProductDetailResponseModelToJson(UpdateProductDetailResponseModel data) => json.encode(data.toJson());

class UpdateProductDetailResponseModel {
  bool? status;
  String? message;
  Data? data;

  UpdateProductDetailResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory UpdateProductDetailResponseModel.fromJson(Map<String, dynamic> json) => UpdateProductDetailResponseModel(
    status: json["status"],
    message: json["message"],
    data: json["data"] == null ? null : Data.fromJson(json["data"]),
  );

  Map<String, dynamic> toJson() => {
    "status": status,
    "message": message,
    "data": data?.toJson(),
  };
}

class Data {
  Location? location;
  GeoLocation? geoLocation;
  String? id;
  String? seller;
  String? category;
  List<String>? categoryHierarchy;
  List<Attribute>? attributes;
  int? status;
  String? rejectionNote;
  DateTime? reviewAt;
  String? title;
  String? subTitle;
  String? description;
  int? contactNumber;
  int? availableUnits;
  String? primaryImage;
  List<String>? galleryImages;
  int? saleType;
  bool? isOfferAllowed;
  double? minimumOffer;
  double? price;
  dynamic currentAuctionSession;
  bool? isAuctionEnabled;
  int? auctionStartingPrice;
  int? auctionDurationDays;
  dynamic auctionStartDate;
  dynamic auctionEndDate;
  DateTime? scheduledPublishDate;
  bool? isReservePriceEnabled;
  int? reservePriceAmount;
  bool? isActive;
  String? adminEditNotes;
  bool? isPromoted;
  dynamic promotedUntil;
  DateTime? createdAt;
  DateTime? updatedAt;

  Data({
    this.location,
    this.geoLocation,
    this.id,
    this.seller,
    this.category,
    this.categoryHierarchy,
    this.attributes,
    this.status,
    this.rejectionNote,
    this.reviewAt,
    this.title,
    this.subTitle,
    this.description,
    this.contactNumber,
    this.availableUnits,
    this.primaryImage,
    this.galleryImages,
    this.saleType,
    this.isOfferAllowed,
    this.minimumOffer,
    this.price,
    this.currentAuctionSession,
    this.isAuctionEnabled,
    this.auctionStartingPrice,
    this.auctionDurationDays,
    this.auctionStartDate,
    this.auctionEndDate,
    this.scheduledPublishDate,
    this.isReservePriceEnabled,
    this.reservePriceAmount,
    this.isActive,
    this.adminEditNotes,
    this.isPromoted,
    this.promotedUntil,
    this.createdAt,
    this.updatedAt,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
    location: json["location"] == null ? null : Location.fromJson(json["location"]),
    geoLocation: json["geoLocation"] == null ? null : GeoLocation.fromJson(json["geoLocation"]),
    id: json["_id"],
    seller: json["seller"],
    category: json["category"],
    categoryHierarchy: json["categoryHierarchy"] == null ? [] : List<String>.from(json["categoryHierarchy"]!.map((x) => x)),
    attributes: json["attributes"] == null ? [] : List<Attribute>.from(json["attributes"]!.map((x) => Attribute.fromJson(x))),
    status: json["status"],
    rejectionNote: json["rejectionNote"],
    reviewAt: json["reviewAt"] == null ? null : DateTime.parse(json["reviewAt"]),
    title: json["title"],
    subTitle: json["subTitle"],
    description: json["description"],
    contactNumber: json["contactNumber"] != null ? int.tryParse(json["contactNumber"].toString()) : null,
    availableUnits: json["availableUnits"],
    primaryImage: json["primaryImage"],
    galleryImages: json["galleryImages"] == null ? [] : List<String>.from(json["galleryImages"]!.map((x) => x)),
    saleType: json["saleType"],
    isOfferAllowed: json["isOfferAllowed"],
    minimumOffer: json["minimumOffer"]?.toDouble(),
    price: json["price"]?.toDouble(),
    currentAuctionSession: json["currentAuctionSession"],
    isAuctionEnabled: json["isAuctionEnabled"],
    auctionStartingPrice: json["auctionStartingPrice"],
    auctionDurationDays: json["auctionDurationDays"],
    auctionStartDate: json["auctionStartDate"],
    auctionEndDate: json["auctionEndDate"],
    scheduledPublishDate: json["scheduledPublishDate"] == null ? null : DateTime.parse(json["scheduledPublishDate"]),
    isReservePriceEnabled: json["isReservePriceEnabled"],
    reservePriceAmount: json["reservePriceAmount"],
    isActive: json["isActive"],
    adminEditNotes: json["adminEditNotes"],
    isPromoted: json["isPromoted"],
    promotedUntil: json["promotedUntil"],
    createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
    updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
  );

  Map<String, dynamic> toJson() => {
    "location": location?.toJson(),
    "geoLocation": geoLocation?.toJson(),
    "_id": id,
    "seller": seller,
    "category": category,
    "categoryHierarchy": categoryHierarchy == null ? [] : List<dynamic>.from(categoryHierarchy!.map((x) => x)),
    "attributes": attributes == null ? [] : List<dynamic>.from(attributes!.map((x) => x.toJson())),
    "status": status,
    "rejectionNote": rejectionNote,
    "reviewAt": reviewAt?.toIso8601String(),
    "title": title,
    "subTitle": subTitle,
    "description": description,
    "contactNumber": contactNumber,
    "availableUnits": availableUnits,
    "primaryImage": primaryImage,
    "galleryImages": galleryImages == null ? [] : List<dynamic>.from(galleryImages!.map((x) => x)),
    "saleType": saleType,
    "isOfferAllowed": isOfferAllowed,
    "minimumOffer": minimumOffer,
    "price": price,
    "currentAuctionSession": currentAuctionSession,
    "isAuctionEnabled": isAuctionEnabled,
    "auctionStartingPrice": auctionStartingPrice,
    "auctionDurationDays": auctionDurationDays,
    "auctionStartDate": auctionStartDate,
    "auctionEndDate": auctionEndDate,
    "scheduledPublishDate": scheduledPublishDate?.toIso8601String(),
    "isReservePriceEnabled": isReservePriceEnabled,
    "reservePriceAmount": reservePriceAmount,
    "isActive": isActive,
    "adminEditNotes": adminEditNotes,
    "isPromoted": isPromoted,
    "promotedUntil": promotedUntil,
    "createdAt": createdAt?.toIso8601String(),
    "updatedAt": updatedAt?.toIso8601String(),
  };
}

class Attribute {
  String? name;
  String? value;
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

class GeoLocation {
  String? type;
  List<double>? coordinates;

  GeoLocation({
    this.type,
    this.coordinates,
  });

  factory GeoLocation.fromJson(Map<String, dynamic> json) => GeoLocation(
    type: json["type"],
    coordinates: json["coordinates"] == null ? [] : List<double>.from(json["coordinates"]!.map((x) => x?.toDouble())),
  );

  Map<String, dynamic> toJson() => {
    "type": type,
    "coordinates": coordinates == null ? [] : List<dynamic>.from(coordinates!.map((x) => x)),
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
