// To parse this JSON data, do
//
//     final blogResponseModel = blogResponseModelFromJson(jsonString);

import 'dart:convert';

BlogResponseModel blogResponseModelFromJson(String str) => BlogResponseModel.fromJson(json.decode(str));

String blogResponseModelToJson(BlogResponseModel data) => json.encode(data.toJson());

class BlogResponseModel {
  bool? status;
  String? message;
  List<Blog>? data;

  BlogResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory BlogResponseModel.fromJson(Map<String, dynamic> json) => BlogResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<Blog>.from(json["data"]!.map((x) => Blog.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Blog {
  String? id;
  String? title;
  String? slug;
  String? image;
  List<String>? tags;
  String? description;
  bool? trending;
  DateTime? createdAt;
  DateTime? updatedAt;

  Blog({
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

  factory Blog.fromJson(Map<String, dynamic> json) => Blog(
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
