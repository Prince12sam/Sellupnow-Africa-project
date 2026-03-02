// To parse this JSON data, do
//
//     final countryResponseModel = countryResponseModelFromJson(jsonString);

import 'dart:convert';

CountryResponseModel countryResponseModelFromJson(String str) => CountryResponseModel.fromJson(json.decode(str));

String countryResponseModelToJson(CountryResponseModel data) => json.encode(data.toJson());

class CountryResponseModel {
  bool? status;
  String? message;
  List<Datum>? data;

  CountryResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory CountryResponseModel.fromJson(Map<String, dynamic> json) => CountryResponseModel(
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
  String? phoneCode;
  String? currency;
  String? currencyName;
  String? currencySymbol;
  String? tld;
  String? native;
  String? region;
  String? subregion;
  String? nationality;
  double? latitude;
  double? longitude;
  String? emoji;
  String? emojiU;
  DateTime? createdAt;
  DateTime? updatedAt;

  Datum({
    this.id,
    this.name,
    this.phoneCode,
    this.currency,
    this.currencyName,
    this.currencySymbol,
    this.tld,
    this.native,
    this.region,
    this.subregion,
    this.nationality,
    this.latitude,
    this.longitude,
    this.emoji,
    this.emojiU,
    this.createdAt,
    this.updatedAt,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        name: json["name"],
        phoneCode: json["phone_code"],
        currency: json["currency"],
        currencyName: json["currencyName"],
        currencySymbol: json["currencySymbol"],
        tld: json["tld"],
        native: json["native"],
        region: json["region"],
        subregion: json["subregion"],
        nationality: json["nationality"],
        latitude: json["latitude"]?.toDouble(),
        longitude: json["longitude"]?.toDouble(),
        emoji: json["emoji"],
        emojiU: json["emojiU"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "phone_code": phoneCode,
        "currency": currency,
        "currencyName": currencyName,
        "currencySymbol": currencySymbol,
        "tld": tld,
        "native": native,
        "region": region,
        "subregion": subregion,
        "nationality": nationality,
        "latitude": latitude,
        "longitude": longitude,
        "emoji": emoji,
        "emojiU": emojiU,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
