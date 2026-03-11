class AllAdsResponseModel {
  AllAdsResponseModel({
    required this.status,
    required this.message,
    required this.data,
  });

  final bool? status;
  final String? message;
  final List<AllAds> data;

  factory AllAdsResponseModel.fromJson(Map<String, dynamic> json) {
    return AllAdsResponseModel(
      status: json["status"],
      message: json["message"],
      data: json["data"] == null ? [] : List<AllAds>.from(json["data"]!.map((x) => AllAds.fromJson(x))),
    );
  }

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data.map((x) => x.toJson()).toList(),
      };
}

class AllAds {
  AllAds({
    required this.id,
    required this.seller,
    required this.purchasedPackage,
    required this.category,
    required this.attributes,
    required this.status,
    required this.title,
    required this.subTitle,
    required this.description,
    required this.contactNumber,
    required this.availableUnits,
    required this.primaryImage,
    required this.galleryImages,
    required this.location,
    required this.saleType,
    required this.isOfferAllowed,
    required this.minimumOffer,
    required this.price,
    required this.isAuctionEnabled,
    required this.auctionStartingPrice,
    required this.lastBidAmount,
    required this.auctionDurationDays,
    required this.auctionStartDate,
    required this.auctionEndDate,
    required this.scheduledPublishDate,
    required this.isReservePriceEnabled,
    required this.isOfferPlaced,
    required this.reservePriceAmount,
    required this.isFake,
    required this.isActive,
    required this.createdAt,
    required this.likesCount,
    required this.viewsCount,
    required this.isLike,
    required this.isPlacedBid,
    required this.isViewed,
    required this.categoryHierarchy,
  });

  final String? id;
  final Seller? seller;
  final String? purchasedPackage;
  final Category? category;
  final List<Attribute> attributes;
  final num? status;
  final String? title;
  final String? subTitle;
  final String? description;
  final num? contactNumber;
  final num? availableUnits;
  final String? primaryImage;
  final List<String> galleryImages;
  final Location? location;
  final num? saleType;
  final bool? isOfferAllowed;
  final num? minimumOffer;
  final num? price;
  final bool? isAuctionEnabled;
  final num? auctionStartingPrice;
  final num? lastBidAmount;
  final num? auctionDurationDays;
  final dynamic auctionStartDate;
  final dynamic auctionEndDate;
  final DateTime? scheduledPublishDate;
  final bool? isReservePriceEnabled;
  final bool? isOfferPlaced;
  final num? reservePriceAmount;
  final bool? isFake;
  final bool? isActive;
  final DateTime? createdAt;
  final num? likesCount;
  final num? viewsCount;
  bool? isLike;
  final bool? isPlacedBid;
  bool? isViewed;
  final List<Category> categoryHierarchy;

  factory AllAds.fromJson(Map<String, dynamic> json) {
    return AllAds(
      id: json["_id"],
      seller: json["seller"] == null ? null : Seller.fromJson(json["seller"]),
      purchasedPackage: json["purchasedPackage"],
      category: json["category"] == null ? null : Category.fromJson(json["category"]),
      attributes: json["attributes"] == null ? [] : List<Attribute>.from(json["attributes"]!.map((x) => Attribute.fromJson(x))),
      status: json["status"],
      title: json["title"],
      subTitle: json["subTitle"],
      description: json["description"],
      contactNumber: json["contactNumber"] != null ? num.tryParse(json["contactNumber"].toString()) : null,
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
      lastBidAmount: json["lastBidAmount"],
      auctionDurationDays: json["auctionDurationDays"],
      auctionStartDate: json["auctionStartDate"],
      auctionEndDate: json["auctionEndDate"],
      scheduledPublishDate: DateTime.tryParse(json["scheduledPublishDate"] ?? ""),
      isReservePriceEnabled: json["isReservePriceEnabled"],
      isOfferPlaced: json["isOfferPlaced"],
      reservePriceAmount: json["reservePriceAmount"],
      isFake: json["isFake"],
      isActive: json["isActive"],
      createdAt: DateTime.tryParse(json["createdAt"] ?? ""),
      likesCount: json["likesCount"],
      viewsCount: json["viewsCount"],
      isLike: json["isLike"],
      isPlacedBid: json["isPlacedBid"],
      isViewed: json["isViewed"],
      categoryHierarchy: json["categoryHierarchy"] == null ? [] : List<Category>.from(json["categoryHierarchy"]!.map((x) => Category.fromJson(x))),
    );
  }

  Map<String, dynamic> toJson() => {
        "_id": id,
        "seller": seller?.toJson(),
        "purchasedPackage": purchasedPackage,
        "category": category?.toJson(),
        "attributes": attributes.map((x) => x.toJson()).toList(),
        "status": status,
        "title": title,
        "subTitle": subTitle,
        "description": description,
        "contactNumber": contactNumber,
        "availableUnits": availableUnits,
        "primaryImage": primaryImage,
        "galleryImages": galleryImages.map((x) => x).toList(),
        "location": location?.toJson(),
        "saleType": saleType,
        "isOfferAllowed": isOfferAllowed,
        "minimumOffer": minimumOffer,
        "price": price,
        "isAuctionEnabled": isAuctionEnabled,
        "auctionStartingPrice": auctionStartingPrice,
        "lastBidAmount": lastBidAmount,
        "auctionDurationDays": auctionDurationDays,
        "auctionStartDate": auctionStartDate,
        "auctionEndDate": auctionEndDate,
        "scheduledPublishDate": scheduledPublishDate?.toIso8601String(),
        "isReservePriceEnabled": isReservePriceEnabled,
        "isOfferPlaced": isOfferPlaced,
        "reservePriceAmount": reservePriceAmount,
        "isFake": isFake,
        "isActive": isActive,
        "createdAt": createdAt?.toIso8601String(),
        "likesCount": likesCount,
        "viewsCount": viewsCount,
        "isLike": isLike,
        "isPlacedBid": isPlacedBid,
        "isViewed": isViewed,
        "categoryHierarchy": categoryHierarchy.map((x) => x.toJson()).toList(),
      };
}

class Attribute {
  Attribute({
    required this.name,
    required this.value,
    required this.image,
    required this.fieldType,
    required this.values,
    required this.minLength,
    required this.maxLength,
    required this.isRequired,
    required this.isActive,
  });

  final String? name;
  final dynamic value;
  final String? image;
  final num? fieldType;
  final List<String> values;
  final num? minLength;
  final num? maxLength;
  final bool? isRequired;
  final bool? isActive;

  factory Attribute.fromJson(Map<String, dynamic> json) {
    return Attribute(
      name: json["name"],
      value: json["value"],
      image: json["image"],
      fieldType: json["fieldType"],
      values: json["values"] == null ? [] : List<String>.from(json["values"]!.map((x) => x)),
      minLength: json["minLength"],
      maxLength: json["maxLength"],
      isRequired: json["isRequired"],
      isActive: json["isActive"],
    );
  }

  Map<String, dynamic> toJson() => {
        "name": name,
        "value": value,
        "image": image,
        "fieldType": fieldType,
        "values": values.map((x) => x).toList(),
        "minLength": minLength,
        "maxLength": maxLength,
        "isRequired": isRequired,
        "isActive": isActive,
      };
}

class ValueClass {
  ValueClass({
    required this.name,
    required this.size,
    required this.extension,
  });

  final String? name;
  final num? size;
  final String? extension;

  factory ValueClass.fromJson(Map<String, dynamic> json) {
    return ValueClass(
      name: json["name"],
      size: json["size"],
      extension: json["extension"],
    );
  }

  Map<String, dynamic> toJson() => {
        "name": name,
        "size": size,
        "extension": extension,
      };
}

class Category {
  Category({
    required this.id,
    required this.name,
    required this.image,
  });

  final String? id;
  final String? name;
  final String? image;

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json["_id"],
      name: json["name"],
      image: json["image"],
    );
  }

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "image": image,
      };
}

class Location {
  Location({
    required this.country,
    required this.state,
    required this.city,
    required this.latitude,
    required this.longitude,
    required this.fullAddress,
  });

  final String? country;
  final String? state;
  final String? city;
  final num? latitude;
  final num? longitude;
  final String? fullAddress;

  factory Location.fromJson(Map<String, dynamic> json) {
    return Location(
      country: json["country"],
      state: json["state"],
      city: json["city"],
      latitude: json["latitude"],
      longitude: json["longitude"],
      fullAddress: json["fullAddress"],
    );
  }

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
  Seller( {
    required this.id,
    required this.name,
    required this.profileImage,
    required this.phoneNumber,
    required this.email,
    required this.isVerified,
    required this.averageRating,
    required this.totalRating,
    required this.registeredAt,
    required this.createdAt,
    required this.isFeaturedSeller,
  });

  final String? id;
  final String? name;
  final String? profileImage;
  final String? phoneNumber;
  final String? email;
  final bool? isVerified;
  final num? averageRating;
  final num? totalRating;
  final String? registeredAt;
  final DateTime? createdAt;
  final  bool? isFeaturedSeller;



  factory Seller.fromJson(Map<String, dynamic> json) {
    return Seller(
      id: json["_id"],
      name: json["name"],
      profileImage: json["profileImage"],
      phoneNumber: json["phoneNumber"],
      email: json["email"],
      isVerified: json["isVerified"],
      averageRating: json["averageRating"],
      totalRating: json["totalRating"],
      registeredAt: json["registeredAt"],
      createdAt: DateTime.tryParse(json["createdAt"] ?? ""),
      isFeaturedSeller: json["isFeaturedSeller"],
    );
  }

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
    "isFeaturedSeller": isFeaturedSeller,
      };
}
