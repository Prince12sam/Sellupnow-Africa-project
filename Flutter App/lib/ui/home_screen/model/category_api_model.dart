import 'dart:convert';

AllCategoryResponseModel allCategoryResponseModelFromJson(String str) => AllCategoryResponseModel.fromJson(json.decode(str));

String allCategoryResponseModelToJson(AllCategoryResponseModel data) => json.encode(data.toJson());

class AllCategoryResponseModel {
  bool? status;
  String? message;
  List<AllCategory>? data;

  AllCategoryResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory AllCategoryResponseModel.fromJson(Map<String, dynamic> json) => AllCategoryResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? [] : List<AllCategory>.from(json["data"]!.map((x) => AllCategory.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class AllCategory {
  String? id;
  String? name;
  String? image;

  AllCategory({
    this.id,
    this.name,
    this.image,
  });

  factory AllCategory.fromJson(Map<String, dynamic> json) => AllCategory(
        id: json["_id"],
        name: json["name"],
        image: json["image"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "image": image,
      };
}
