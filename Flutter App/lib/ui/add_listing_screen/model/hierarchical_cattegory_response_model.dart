import 'dart:convert';

CategoryResponseModel categoryResponseModelFromJson(String str) => CategoryResponseModel.fromJson(json.decode(str));

String categoryResponseModelToJson(CategoryResponseModel data) => json.encode(data.toJson());

class CategoryResponseModel {
  bool? success;
  String? message;
  List<Category>? data;

  CategoryResponseModel({
    this.success,
    this.message,
    this.data,
  });

  factory CategoryResponseModel.fromJson(Map<String, dynamic> json) => CategoryResponseModel(
        success: json["success"],
        message: json["message"],
        data: json["data"] == null ? [] : List<Category>.from(json["data"].map((x) => Category.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "success": success,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Category {
  String? id;
  String? name;
  String? image;
  String? parent;
  List<Category>? children;

  Category({
    this.id,
    this.name,
    this.image,
    this.parent,
    this.children,
  });

  factory Category.fromJson(Map<String, dynamic> json) => Category(
        id: json["_id"],
        name: json["name"],
        image: json["image"],
        parent: json["parent"],
        children: json["children"] == null ? [] : List<Category>.from(json["children"].map((x) => Category.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "image": image,
        "parent": parent,
        "children": children == null ? [] : List<dynamic>.from(children!.map((x) => x.toJson())),
      };
}
