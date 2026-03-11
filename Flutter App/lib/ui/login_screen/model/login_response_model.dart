// To parse this JSON data, do
//
//     final loginApiResponseModel = loginApiResponseModelFromJson(jsonString);

import 'dart:convert';

LoginApiResponseModel loginApiResponseModelFromJson(String str) => LoginApiResponseModel.fromJson(json.decode(str));

String loginApiResponseModelToJson(LoginApiResponseModel data) => json.encode(data.toJson());

class LoginApiResponseModel {
  bool? status;
  String? message;
  bool? signUp;
  String? firebaseCustomToken;
  User? user;

  LoginApiResponseModel({
    this.status,
    this.message,
    this.signUp,
    this.firebaseCustomToken,
    this.user,
  });

  factory LoginApiResponseModel.fromJson(Map<String, dynamic> json) => LoginApiResponseModel(
        status: json["status"],
        message: json["message"],
        signUp: json["signUp"],
        firebaseCustomToken: json["firebaseCustomToken"],
        user: json["user"] == null ? null : User.fromJson(json["user"]),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "signUp": signUp,
        "firebaseCustomToken": firebaseCustomToken,
        "user": user?.toJson(),
      };
}

class User {
  String? id;
  int? loginType;
  String? name;
  String? profileImage;
  String? fcmToken;
  String? firebaseUid;
  String? email;
  String? phoneNumber;
  String? country;
  String? phoneCode;
  String? address;
  String? authProvider;
  String? authIdentity;
  bool? isVerified;
  bool? isBlocked;
  bool? isOnline;
  int? verificationStatus;
  String? verificationId;
  String? verificationSubmittedAt;
  String? verificationDeclineReason;

  User({
    this.id,
    this.loginType,
    this.name,
    this.profileImage,
    this.fcmToken,
    this.firebaseUid,
    this.email,
    this.phoneNumber,
    this.country,
    this.phoneCode,
    this.address,
    this.authProvider,
    this.authIdentity,
    this.isVerified,
    this.isBlocked,
    this.isOnline,
    this.verificationStatus,
    this.verificationId,
    this.verificationSubmittedAt,
    this.verificationDeclineReason,
  });

  factory User.fromJson(Map<String, dynamic> json) => User(
        id: json["_id"],
        loginType: json["loginType"],
        name: json["name"],
        profileImage: json["profileImage"],
        fcmToken: json["fcmToken"],
        firebaseUid: json["firebaseUid"],
        email: json["email"],
        phoneNumber: json["phoneNumber"] ?? json["phone"],
        country: json["country"],
        phoneCode: json["phone_code"],
        address: json["address"],
        authProvider: json["authProvider"],
        authIdentity: json["authIdentity"],
        isVerified: json["isVerified"] ?? json["account_verified"],
        isBlocked: json["isBlocked"],
        isOnline: json["isOnline"],
        verificationStatus: json["verificationStatus"],
        verificationId: json["verificationId"],
        verificationSubmittedAt: json["verificationSubmittedAt"],
        verificationDeclineReason: json["verificationDeclineReason"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "loginType": loginType,
        "name": name,
        "profileImage": profileImage,
        "fcmToken": fcmToken,
        "firebaseUid": firebaseUid,
        "email": email,
        "phoneNumber": phoneNumber,
        "country": country,
        "phone_code": phoneCode,
        "address": address,
        "authProvider": authProvider,
        "authIdentity": authIdentity,
        "isVerified": isVerified,
        "isBlocked": isBlocked,
        "isOnline": isOnline,
        "verificationStatus": verificationStatus,
        "verificationId": verificationId,
        "verificationSubmittedAt": verificationSubmittedAt,
        "verificationDeclineReason": verificationDeclineReason,
      };
}
