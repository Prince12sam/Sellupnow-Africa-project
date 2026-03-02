// To parse this JSON data, do
//
//     final notificationResponseModel = notificationResponseModelFromJson(jsonString);

import 'dart:convert';

NotificationResponseModel notificationResponseModelFromJson(String str) => NotificationResponseModel.fromJson(json.decode(str));

String notificationResponseModelToJson(NotificationResponseModel data) => json.encode(data.toJson());

class NotificationResponseModel {
  bool? status;
  String? message;
  List<NotificationData>? notification;

  NotificationResponseModel({
    this.status,
    this.message,
    this.notification,
  });

  factory NotificationResponseModel.fromJson(Map<String, dynamic> json) => NotificationResponseModel(
        status: json["status"],
        message: json["message"],
        notification: json["notification"] == null ? [] : List<NotificationData>.from(json["notification"]!.map((x) => NotificationData.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "notification": notification == null ? [] : List<dynamic>.from(notification!.map((x) => x.toJson())),
      };
}

class NotificationData {
  String? id;
  String? sendType;
  String? user;
  Ad? ad;
  String? title;
  String? message;
  String? image;
  DateTime? date;
  DateTime? createdAt;
  DateTime? updatedAt;

  NotificationData({
    this.id,
    this.sendType,
    this.user,
    this.ad,
    this.title,
    this.message,
    this.image,
    this.date,
    this.createdAt,
    this.updatedAt,
  });

  factory NotificationData.fromJson(Map<String, dynamic> json) => NotificationData(
        id: json["_id"],
        sendType: json["sendType"],
        user: json["user"],
        ad: json["ad"] == null ? null : Ad.fromJson(json["ad"]),
        title: json["title"],
        message: json["message"],
        image: json["image"],
        date: json["date"] == null ? null : DateTime.parse(json["date"]),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "sendType": sendType,
        "user": user,
        "ad": ad?.toJson(),
        "title": title,
        "message": message,
        "image": image,
        "date": date?.toIso8601String(),
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}

class Ad {
  String? id;
  String? title;
  double? price;

  Ad({
    this.id,
    this.title,
    this.price,
  });

  factory Ad.fromJson(Map<String, dynamic> json) => Ad(
        id: json["_id"],
        title: json["title"],
        price: json["price"]?.toDouble(),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "title": title,
        "price": price,
      };
}
