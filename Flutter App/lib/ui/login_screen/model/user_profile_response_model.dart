// To parse this JSON data, do
//
//     final getUserProfileResponseModel = getUserProfileResponseModelFromJson(jsonString);

import 'dart:convert';

GetUserProfileResponseModel getUserProfileResponseModelFromJson(String str) => GetUserProfileResponseModel.fromJson(json.decode(str));

String getUserProfileResponseModelToJson(GetUserProfileResponseModel data) => json.encode(data.toJson());

class GetUserProfileResponseModel {
  bool? status;
  String? message;
  User? user;

  GetUserProfileResponseModel({
    this.status,
    this.message,
    this.user,
  });

  factory GetUserProfileResponseModel.fromJson(Map<String, dynamic> json) => GetUserProfileResponseModel(
        status: json["status"],
        message: json["message"],
        user: json["user"] == null ? null : User.fromJson(json["user"]),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "user": user?.toJson(),
      };
}

class User {
  String? id;
  String? profileId;
  String? name;
  String? profileImage;
  String? email;
  String? password;
  String? phoneNumber;
  String? address;
  int? loginType;
  String? authIdentity;
  String? fcmToken;
  String? firebaseUid;
  String? authProvider;
  int? averageRating;
  int? totalRating;
  bool? isLive;
  dynamic currentLiveSessionId;
  bool? isBlocked;
  bool? isOnline;
  bool? isVerified;
  bool? isNotificationsAllowed;
  bool? isContactInfoVisible;
  dynamic subscriptionPackage;
  dynamic featurePackage;
  bool? isSubscriptionExpired;
  bool? isFeaturePackageExpired;
  String? lastLoginAt;
  String? registeredAt;
  String? country;
  String? phoneCode;
  int? verificationStatus;
  String? verificationId;
  String? verificationSubmittedAt;
  String? verificationDeclineReason;
  DateTime? createdAt;
  DateTime? updatedAt;

  User({
    this.id,
    this.profileId,
    this.name,
    this.profileImage,
    this.email,
    this.password,
    this.phoneNumber,
    this.address,
    this.loginType,
    this.authIdentity,
    this.fcmToken,
    this.firebaseUid,
    this.authProvider,
    this.averageRating,
    this.totalRating,
    this.isLive,
    this.currentLiveSessionId,
    this.isBlocked,
    this.isOnline,
    this.isVerified,
    this.isNotificationsAllowed,
    this.isContactInfoVisible,
    this.subscriptionPackage,
    this.featurePackage,
    this.isSubscriptionExpired,
    this.isFeaturePackageExpired,
    this.lastLoginAt,
    this.registeredAt,
    this.country,
    this.phoneCode,
    this.verificationStatus,
    this.verificationId,
    this.verificationSubmittedAt,
    this.verificationDeclineReason,
    this.createdAt,
    this.updatedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) => User(
        id: json["_id"],
        profileId: json["profileId"],
        name: json["name"],
        profileImage: json["profileImage"],
        email: json["email"],
        password: json["password"],
        phoneNumber: json["phoneNumber"],
        address: json["address"],
        loginType: json["loginType"],
        authIdentity: json["authIdentity"],
        fcmToken: json["fcmToken"],
        firebaseUid: json["firebaseUid"],
        authProvider: json["authProvider"],
        averageRating: json["averageRating"],
        totalRating: json["totalRating"],
        isLive: json["isLive"],
        currentLiveSessionId: json["currentLiveSessionId"],
        isBlocked: json["isBlocked"],
        isOnline: json["isOnline"],
        isVerified: json["isVerified"],
        isNotificationsAllowed: json["isNotificationsAllowed"],
        isContactInfoVisible: json["isContactInfoVisible"],
        subscriptionPackage: json["subscriptionPackage"],
        featurePackage: json["featurePackage"],
        isSubscriptionExpired: json["isSubscriptionExpired"],
        isFeaturePackageExpired: json["isFeaturePackageExpired"],
        lastLoginAt: json["lastLoginAt"],
        registeredAt: json["registeredAt"],
        country: json["country"],
        phoneCode: json["phone_code"],
        verificationStatus: json["verificationStatus"],
        verificationId: json["verificationId"],
        verificationSubmittedAt: json["verificationSubmittedAt"],
        verificationDeclineReason: json["verificationDeclineReason"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "profileId": profileId,
        "name": name,
        "profileImage": profileImage,
        "email": email,
        "password": password,
        "phoneNumber": phoneNumber,
        "address": address,
        "loginType": loginType,
        "authIdentity": authIdentity,
        "fcmToken": fcmToken,
        "firebaseUid": firebaseUid,
        "authProvider": authProvider,
        "averageRating": averageRating,
        "totalRating": totalRating,
        "isLive": isLive,
        "currentLiveSessionId": currentLiveSessionId,
        "isBlocked": isBlocked,
        "isOnline": isOnline,
        "isVerified": isVerified,
        "isNotificationsAllowed": isNotificationsAllowed,
        "isContactInfoVisible": isContactInfoVisible,
        "subscriptionPackage": subscriptionPackage,
        "featurePackage": featurePackage,
        "isSubscriptionExpired": isSubscriptionExpired,
        "isFeaturePackageExpired": isFeaturePackageExpired,
        "lastLoginAt": lastLoginAt,
        "registeredAt": registeredAt,
        "country": country,
        "phone_code": phoneCode,
        "verificationStatus": verificationStatus,
        "verificationId": verificationId,
        "verificationSubmittedAt": verificationSubmittedAt,
        "verificationDeclineReason": verificationDeclineReason,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
