// To parse this JSON data, do
//
//     final getReviewResponseModel = getReviewResponseModelFromJson(jsonString);

import 'dart:convert';

GetReviewResponseModel getReviewResponseModelFromJson(String str) => GetReviewResponseModel.fromJson(json.decode(str));

String getReviewResponseModelToJson(GetReviewResponseModel data) => json.encode(data.toJson());

class GetReviewResponseModel {
  bool? status;
  String? message;
  List<ReceivedReview>? receivedReviews;

  GetReviewResponseModel({
    this.status,
    this.message,
    this.receivedReviews,
  });

  factory GetReviewResponseModel.fromJson(Map<String, dynamic> json) => GetReviewResponseModel(
        status: json["status"],
        message: json["message"],
        receivedReviews:
            json["receivedReviews"] == null ? [] : List<ReceivedReview>.from(json["receivedReviews"]!.map((x) => ReceivedReview.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "receivedReviews": receivedReviews == null ? [] : List<dynamic>.from(receivedReviews!.map((x) => x.toJson())),
      };
}

class ReceivedReview {
  String? id;
  Reviewer? reviewer;
  double? rating;
  String? reviewText;
  DateTime? reviewedAt;

  ReceivedReview({
    this.id,
    this.reviewer,
    this.rating,
    this.reviewText,
    this.reviewedAt,
  });

  factory ReceivedReview.fromJson(Map<String, dynamic> json) => ReceivedReview(
        id: json["_id"],
        reviewer: json["reviewer"] == null ? null : Reviewer.fromJson(json["reviewer"]),
        rating: json["rating"]?.toDouble(),
        reviewText: json["reviewText"],
        reviewedAt: json["reviewedAt"] == null ? null : DateTime.parse(json["reviewedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "reviewer": reviewer?.toJson(),
        "rating": rating,
        "reviewText": reviewText,
        "reviewedAt": reviewedAt?.toIso8601String(),
      };
}

class Reviewer {
  String? id;
  String? name;
  String? profileImage;

  Reviewer({
    this.id,
    this.name,
    this.profileImage,
  });

  factory Reviewer.fromJson(Map<String, dynamic> json) => Reviewer(
        id: json["_id"],
        name: json["name"],
        profileImage: json["profileImage"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "profileImage": profileImage,
      };
}
