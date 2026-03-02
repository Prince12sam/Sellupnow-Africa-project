// To parse this JSON data, do
//
//     final cityResponseModel = cityResponseModelFromJson(jsonString);

import 'dart:convert';

CityResponseModel cityResponseModelFromJson(String str) => CityResponseModel.fromJson(json.decode(str));

String cityResponseModelToJson(CityResponseModel data) => json.encode(data.toJson());

class CityResponseModel {
  bool? status;
  String? message;
  int? total;
  List<Datum>? data;

  CityResponseModel({
    this.status,
    this.message,
    this.total,
    this.data,
  });

  factory CityResponseModel.fromJson(Map<String, dynamic> json) => CityResponseModel(
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
  StateId? stateId;
  String? name;
  double? latitude;
  double? longitude;
  DateTime? createdAt;
  DateTime? updatedAt;

  Datum({
    this.id,
    this.stateId,
    this.name,
    this.latitude,
    this.longitude,
    this.createdAt,
    this.updatedAt,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        stateId: json["state_id"] == null ? null : StateId.fromJson(json["state_id"]),
        name: json["name"],
        latitude: json["latitude"]?.toDouble(),
        longitude: json["longitude"]?.toDouble(),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "state_id": stateId?.toJson(),
        "name": name,
        "latitude": latitude,
        "longitude": longitude,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}

class StateId {
  String? id;
  String? name;

  StateId({
    this.id,
    this.name,
  });

  factory StateId.fromJson(Map<String, dynamic> json) => StateId(
        id: json["_id"],
        name: json["name"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
      };
}
