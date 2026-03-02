// To parse this JSON data, do
//
//     final getSocialConnectionsResponseModel = getSocialConnectionsResponseModelFromJson(jsonString);

import 'dart:convert';

GetSocialConnectionsResponseModel getSocialConnectionsResponseModelFromJson(String str) =>
    GetSocialConnectionsResponseModel.fromJson(json.decode(str));

String getSocialConnectionsResponseModelToJson(GetSocialConnectionsResponseModel data) => json.encode(data.toJson());

class GetSocialConnectionsResponseModel {
  bool? status;
  String? message;
  List<Follower>? friends;
  List<Follower>? following;
  List<Follower>? followers;

  GetSocialConnectionsResponseModel({
    this.status,
    this.message,
    this.friends,
    this.following,
    this.followers,
  });

  factory GetSocialConnectionsResponseModel.fromJson(Map<String, dynamic> json) => GetSocialConnectionsResponseModel(
        status: json["status"],
        message: json["message"],
        friends: json["friends"] == null ? [] : List<Follower>.from(json["friends"]!.map((x) => Follower.fromJson(x))),
        following: json["following"] == null ? [] : List<Follower>.from(json["following"]!.map((x) => Follower.fromJson(x))),
        followers: json["followers"] == null ? [] : List<Follower>.from(json["followers"]!.map((x) => Follower.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "friends": friends == null ? [] : List<dynamic>.from(friends!.map((x) => x.toJson())),
        "following": following == null ? [] : List<dynamic>.from(following!.map((x) => x.toJson())),
        "followers": followers == null ? [] : List<dynamic>.from(followers!.map((x) => x.toJson())),
      };
}

class Follower {
  String? id;
  String? profileId;
  String? name;
  String? profileImage;
  bool? isVerified;
  bool? isOnline;
  bool? isFollow;

  Follower({
    this.id,
    this.profileId,
    this.name,
    this.profileImage,
    this.isVerified,
    this.isOnline,
    this.isFollow,
  });

  factory Follower.fromJson(Map<String, dynamic> json) => Follower(
        id: json["_id"],
        profileId: json["profileId"],
        name: json["name"],
        profileImage: json["profileImage"],
        isVerified: json["isVerified"],
        isOnline: json["isOnline"],
        isFollow: json["isFollow"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "profileId": profileId,
        "name": name,
        "profileImage": profileImage,
        "isVerified": isVerified,
        "isOnline": isOnline,
        "isFollow": isFollow,
      };
}
