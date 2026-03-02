// To parse this JSON data, do
//
//     final blogByIdResponse = blogByIdResponseFromJson(jsonString);

import 'dart:convert';

BlogByIdResponse blogByIdResponseFromJson(String str) => BlogByIdResponse.fromJson(json.decode(str));

String blogByIdResponseToJson(BlogByIdResponse data) => json.encode(data.toJson());

class BlogByIdResponse {
  bool? status;
  Data? data;

  BlogByIdResponse({
    this.status,
    this.data,
  });

  factory BlogByIdResponse.fromJson(Map<String, dynamic> json) => BlogByIdResponse(
        status: json["status"],
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "data": data?.toJson(),
      };
}

class Data {
  String? id;
  String? title;
  String? slug;
  String? image;
  List<String>? tags;
  String? description;
  bool? trending;
  DateTime? createdAt;
  DateTime? updatedAt;

  Data({
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

  factory Data.fromJson(Map<String, dynamic> json) => Data(
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
