// // To parse this JSON data, do
// //
// //     final popularProductResponseModel = popularProductResponseModelFromJson(jsonString);
//
// import 'dart:convert';
//
// PopularProductResponseModel popularProductResponseModelFromJson(String str) => PopularProductResponseModel.fromJson(json.decode(str));
//
// String popularProductResponseModelToJson(PopularProductResponseModel data) => json.encode(data.toJson());
//
// class PopularProductResponseModel {
//   bool? status;
//   String? message;
//   List<dynamic>? data;
//
//   PopularProductResponseModel({
//     this.status,
//     this.message,
//     this.data,
//   });
//
//   factory PopularProductResponseModel.fromJson(Map<String, dynamic> json) => PopularProductResponseModel(
//         status: json["status"],
//         message: json["message"],
//         data: json["data"] == null ? [] : List<dynamic>.from(json["data"]!.map((x) => x)),
//       );
//
//   Map<String, dynamic> toJson() => {
//         "status": status,
//         "message": message,
//         "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x)),
//       };
// }

class PopularProductResponseModel {
  bool? status;
  String? message;
  List<PopularProduct>? data;

  PopularProductResponseModel({this.status, this.message, this.data});

  PopularProductResponseModel.fromJson(Map<String, dynamic> json) {
    status = json['status'];
    message = json['message'];
    if (json['data'] != null) {
      data = <PopularProduct>[];
      json['data'].forEach((v) {
        data!.add(PopularProduct.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['status'] = status;
    data['message'] = message;
    if (this.data != null) {
      data['data'] = this.data!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class PopularProduct {
  String? sId;
  Seller? seller;
  String? purchasedPackage;
  Category? category;
  List<Attributes>? attributes;
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
  String? auctionStartDate;
  String? auctionEndDate;
  String? scheduledPublishDate;
  bool? isReservePriceEnabled;
  int? reservePriceAmount;
  bool? isActive;
  String? createdAt;
  int? likesCount;
  int? viewsCount;
  bool? isLike;
  bool? isPlacedBid;
  bool? isViewed;
  List<Category>? categoryHierarchy;

  PopularProduct(
      {this.sId,
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
      this.categoryHierarchy});

  PopularProduct.fromJson(Map<String, dynamic> json) {
    sId = json['_id'];
    seller = json['seller'] != null ? Seller.fromJson(json['seller']) : null;
    purchasedPackage = json['purchasedPackage'];
    category = json['category'] != null ? Category.fromJson(json['category']) : null;
    if (json['attributes'] != null) {
      attributes = <Attributes>[];
      json['attributes'].forEach((v) {
        attributes!.add(Attributes.fromJson(v));
      });
    }
    status = json['status'];
    title = json['title'];
    subTitle = json['subTitle'];
    description = json['description'];
    contactNumber = json['contactNumber'] != null ? int.tryParse(json['contactNumber'].toString()) : null;
    availableUnits = json['availableUnits'];
    primaryImage = json['primaryImage'];
    galleryImages = json['galleryImages'] != null ? List<String>.from(json['galleryImages']) : [];
    location = json['location'] != null ? Location.fromJson(json['location']) : null;
    saleType = json['saleType'];
    isOfferAllowed = json['isOfferAllowed'];
    minimumOffer = json['minimumOffer'];
    price = json['price'];
    isAuctionEnabled = json['isAuctionEnabled'];
    auctionStartingPrice = json['auctionStartingPrice'];
    auctionDurationDays = json['auctionDurationDays'];
    auctionStartDate = json['auctionStartDate'];
    auctionEndDate = json['auctionEndDate'];
    scheduledPublishDate = json['scheduledPublishDate'];
    isReservePriceEnabled = json['isReservePriceEnabled'];
    reservePriceAmount = json['reservePriceAmount'];
    isActive = json['isActive'];
    createdAt = json['createdAt'];
    likesCount = json['likesCount'];
    viewsCount = json['viewsCount'];
    isLike = json['isLike'];
    isPlacedBid = json['isPlacedBid'];
    isViewed = json['isViewed'];
    if (json['categoryHierarchy'] != null) {
      categoryHierarchy = <Category>[];
      json['categoryHierarchy'].forEach((v) {
        categoryHierarchy!.add(Category.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['_id'] = sId;
    if (seller != null) {
      data['seller'] = seller!.toJson();
    }
    data['purchasedPackage'] = purchasedPackage;
    if (category != null) {
      data['category'] = category!.toJson();
    }
    if (attributes != null) {
      data['attributes'] = attributes!.map((v) => v.toJson()).toList();
    }
    data['status'] = status;
    data['title'] = title;
    data['subTitle'] = subTitle;
    data['description'] = description;
    data['contactNumber'] = contactNumber;
    data['availableUnits'] = availableUnits;
    data['primaryImage'] = primaryImage;
    data['galleryImages'] = galleryImages;
    if (location != null) {
      data['location'] = location!.toJson();
    }
    data['saleType'] = saleType;
    data['isOfferAllowed'] = isOfferAllowed;
    data['minimumOffer'] = minimumOffer;
    data['price'] = price;
    data['isAuctionEnabled'] = isAuctionEnabled;
    data['auctionStartingPrice'] = auctionStartingPrice;
    data['auctionDurationDays'] = auctionDurationDays;
    data['auctionStartDate'] = auctionStartDate;
    data['auctionEndDate'] = auctionEndDate;
    data['scheduledPublishDate'] = scheduledPublishDate;
    data['isReservePriceEnabled'] = isReservePriceEnabled;
    data['reservePriceAmount'] = reservePriceAmount;
    data['isActive'] = isActive;
    data['createdAt'] = createdAt;
    data['likesCount'] = likesCount;
    data['viewsCount'] = viewsCount;
    data['isLike'] = isLike;
    data['isPlacedBid'] = isPlacedBid;
    data['isViewed'] = isViewed;
    if (categoryHierarchy != null) {
      data['categoryHierarchy'] = categoryHierarchy!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class Seller {
  String? sId;
  String? name;
  String? profileImage;
  String? phoneNumber;
  String? email;
  bool? isVerified;
  int? averageRating;
  int? totalRating;
  String? registeredAt;
  String? createdAt;

  Seller(
      {this.sId,
      this.name,
      this.profileImage,
      this.phoneNumber,
      this.email,
      this.isVerified,
      this.averageRating,
      this.totalRating,
      this.registeredAt,
      this.createdAt});

  Seller.fromJson(Map<String, dynamic> json) {
    sId = json['_id'];
    name = json['name'];
    profileImage = json['profileImage'];
    phoneNumber = json['phoneNumber'];
    email = json['email'];
    isVerified = json['isVerified'];
    averageRating = json['averageRating'];
    totalRating = json['totalRating'];
    registeredAt = json['registeredAt'];
    createdAt = json['createdAt'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['_id'] = sId;
    data['name'] = name;
    data['profileImage'] = profileImage;
    data['phoneNumber'] = phoneNumber;
    data['email'] = email;
    data['isVerified'] = isVerified;
    data['averageRating'] = averageRating;
    data['totalRating'] = totalRating;
    data['registeredAt'] = registeredAt;
    data['createdAt'] = createdAt;
    return data;
  }
}

class Category {
  String? sId;
  String? name;
  String? image;

  Category({this.sId, this.name, this.image});

  Category.fromJson(Map<String, dynamic> json) {
    sId = json['_id'];
    name = json['name'];
    image = json['image'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['_id'] = sId;
    data['name'] = name;
    data['image'] = image;
    return data;
  }
}

class Attributes {
  String? name;
  String? value;
  String? image;
  int? fieldType;
  List<String>? values;
  int? minLength;
  int? maxLength;
  bool? isRequired;
  bool? isActive;

  Attributes({this.name, this.value, this.image, this.fieldType, this.values, this.minLength, this.maxLength, this.isRequired, this.isActive});

  Attributes.fromJson(Map<String, dynamic> json) {
    name = json['name'];
    values = json['values'] != null ? List<String>.from(json['values']) : [];
    image = json['image'];
    fieldType = json['fieldType'];
    values = json['values'].cast<String>();
    minLength = json['minLength'];
    maxLength = json['maxLength'];
    isRequired = json['isRequired'];
    isActive = json['isActive'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['name'] = name;
    data['value'] = value;
    data['image'] = image;
    data['fieldType'] = fieldType;
    data['values'] = values;
    data['minLength'] = minLength;
    data['maxLength'] = maxLength;
    data['isRequired'] = isRequired;
    data['isActive'] = isActive;
    return data;
  }
}

class Location {
  String? country;
  String? state;
  String? city;
  double? latitude;
  double? longitude;
  String? fullAddress;

  Location({this.country, this.state, this.city, this.latitude, this.longitude, this.fullAddress});

  Location.fromJson(Map<String, dynamic> json) {
    country = json['country'];
    state = json['state'];
    city = json['city'];
    latitude = json['latitude'];
    longitude = json['longitude'];
    fullAddress = json['fullAddress'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['country'] = country;
    data['state'] = state;
    data['city'] = city;
    data['latitude'] = latitude;
    data['longitude'] = longitude;
    data['fullAddress'] = fullAddress;
    return data;
  }
}
