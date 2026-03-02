// To parse this JSON data, do
//
//     final stateResponseModel = stateResponseModelFromJson(jsonString);

import 'dart:convert';

StateResponseModel stateResponseModelFromJson(String str) => StateResponseModel.fromJson(json.decode(str));

String stateResponseModelToJson(StateResponseModel data) => json.encode(data.toJson());

class StateResponseModel {
  bool? status;
  String? message;
  int? total;
  List<Datum>? data;

  StateResponseModel({
    this.status,
    this.message,
    this.total,
    this.data,
  });

  factory StateResponseModel.fromJson(Map<String, dynamic> json) => StateResponseModel(
        status: json["status"],
        message: json["message"],
        total: json["total"],
        data: json["data"] == null ? [] : List<Datum>.from(json["data"]!.map((x) => Datum.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "total": total,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Datum {
  String? id;
  CountryId? countryId;
  String? name;
  String? stateCode;
  double? latitude;
  double? longitude;
  DateTime? createdAt;
  DateTime? updatedAt;
  String? type;

  Datum({
    this.id,
    this.countryId,
    this.name,
    this.stateCode,
    this.latitude,
    this.longitude,
    this.createdAt,
    this.updatedAt,
    this.type,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        countryId: json["country_id"] == null ? null : CountryId.fromJson(json["country_id"]),
        name: json["name"],
        stateCode: json["state_code"],
        latitude: json["latitude"]?.toDouble(),
        longitude: json["longitude"]?.toDouble(),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
        type: json["type"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "country_id": countryId?.toJson(),
        "name": name,
        "state_code": stateCode,
        "latitude": latitude,
        "longitude": longitude,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
        "type": type,
      };
}

class CountryId {
  String? id;
  String? name;

  CountryId({
    this.id,
    this.name,
  });

  factory CountryId.fromJson(Map<String, dynamic> json) => CountryId(
        id: json["_id"],
        name: json["name"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
      };
}
