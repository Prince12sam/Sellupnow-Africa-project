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
  double? averageRating;
  int? totalRating;

  GetReviewResponseModel({
    this.status,
    this.message,
    this.receivedReviews,
    this.averageRating,
    this.totalRating,
  });

  factory GetReviewResponseModel.fromJson(Map<String, dynamic> json) => GetReviewResponseModel(
        status: json["status"] ?? true,
        message: json["message"],
        receivedReviews: json["receivedReviews"] != null
            ? List<ReceivedReview>.from(json["receivedReviews"]!.map((x) => ReceivedReview.fromJson(x)))
            : json["data"]?["reviews"] != null
                ? List<ReceivedReview>.from(json["data"]["reviews"]!.map((x) => ReceivedReview.fromLegacyJson(x)))
                : [],
        averageRating: (json["averageRating"] ?? json["data"]?["average_rating_percentage"]?["rating"])?.toDouble(),
        totalRating: (json["totalRating"] ?? json["data"]?["average_rating_percentage"]?["total_review"]) is num
            ? (json["totalRating"] ?? json["data"]?["average_rating_percentage"]?["total_review"]).toInt()
            : null,
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "receivedReviews": receivedReviews == null ? [] : List<dynamic>.from(receivedReviews!.map((x) => x.toJson())),
        "averageRating": averageRating,
        "totalRating": totalRating,
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

  factory ReceivedReview.fromLegacyJson(Map<String, dynamic> json) => ReceivedReview(
        id: (json["_id"] ?? json["id"])?.toString(),
        reviewer: Reviewer(
          id: '',
          name: json["customer_name"],
          profileImage: json["customer_profile"],
        ),
        rating: json["rating"]?.toDouble(),
        reviewText: json["reviewText"] ?? json["description"],
        reviewedAt: json["reviewedAt"] != null
            ? DateTime.tryParse(json["reviewedAt"])
            : DateTime.tryParse(json["created_at"] ?? ''),
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
