// To parse this JSON data, do
//
//     final trendingBlogResponse = trendingBlogResponseFromJson(jsonString);

import 'dart:convert';

TrendingBlogResponse trendingBlogResponseFromJson(String str) => TrendingBlogResponse.fromJson(json.decode(str));

String trendingBlogResponseToJson(TrendingBlogResponse data) => json.encode(data.toJson());

class TrendingBlogResponse {
  bool? status;
  List<Datum>? data;

  TrendingBlogResponse({
    this.status,
    this.data,
  });

  factory TrendingBlogResponse.fromJson(Map<String, dynamic> json) => TrendingBlogResponse(
        status: json["status"],
        data: json["data"] == null ? [] : List<Datum>.from(json["data"]!.map((x) => Datum.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Datum {
  String? id;
  String? title;
  String? slug;
  String? image;
  List<String>? tags;
  String? description;
  bool? trending;
  DateTime? createdAt;
  DateTime? updatedAt;

  Datum({
    this.id,
    this.title,
    this.slug,
    this.image,
    this.tags,
    this.description,
    this.trending,
    this.createdAt,
    this.updatedAt,
  });

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
        id: json["_id"],
        title: json["title"],
        slug: json["slug"],
        image: json["image"],
        tags: json["tags"] == null ? [] : List<String>.from(json["tags"]!.map((x) => x)),
        description: json["description"],
        trending: json["trending"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "title": title,
        "slug": slug,
        "image": image,
        "tags": tags == null ? [] : List<dynamic>.from(tags!.map((x) => x)),
        "description": description,
        "trending": trending,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
