// To parse this JSON data, do
//
//     final settingApiResponseModel = settingApiResponseModelFromJson(jsonString);

import 'dart:convert';

SettingApiResponseModel settingApiResponseModelFromJson(String str) => SettingApiResponseModel.fromJson(json.decode(str));

String settingApiResponseModelToJson(SettingApiResponseModel data) => json.encode(data.toJson());

class SettingApiResponseModel {
  bool? status;
  String? message;
  Data? data;

  SettingApiResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory SettingApiResponseModel.fromJson(Map<String, dynamic> json) => SettingApiResponseModel(
        status: json["status"],
        message: json["message"],
        data: json["data"] == null ? null : Data.fromJson(json["data"]),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data?.toJson(),
      };
}

class Data {
  Currency? currency;
  bool? enableGooglePlay;
  bool? enableStripe;
  String? stripePublicKey;
  bool? enableRazorpay;
  String? razorpayKeyId;
  String? flutterwaveKeyId;
  bool? enableFlutterwave;
  bool? enablePaystack;
  String? paystackPublicKey;
  bool? enablePaypal;
  String? paypalClientId;
  String? aboutPageUrl;
  String? privacyPolicyUrl;
  String? termsAndConditionsUrl;
  String? agoraAppId;
  String? supportPhone;
  String? supportEmail;
  int? maxVideoDurationSec;
  String? id;
  DateTime? createdAt;
  DateTime? updatedAt;

  Data({
    this.currency,
    this.enableGooglePlay,
    this.enableStripe,
    this.stripePublicKey,
    this.enableRazorpay,
    this.razorpayKeyId,
    this.flutterwaveKeyId,
    this.enableFlutterwave,
    this.enablePaystack,
    this.paystackPublicKey,
    this.enablePaypal,
    this.paypalClientId,
    this.aboutPageUrl,
    this.privacyPolicyUrl,
    this.termsAndConditionsUrl,
    this.agoraAppId,
    this.supportPhone,
    this.supportEmail,
    this.maxVideoDurationSec,
    this.id,
    this.createdAt,
    this.updatedAt,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        currency: json["currency"] == null ? null : Currency.fromJson(json["currency"]),
        enableGooglePlay: json["enableGooglePlay"],
        enableStripe: json["enableStripe"],
        stripePublicKey: json["stripePublicKey"],
        enableRazorpay: json["enableRazorpay"],
        razorpayKeyId: json["razorpayKeyId"],
        flutterwaveKeyId: json["flutterwaveKeyId"],
        enableFlutterwave: json["enableFlutterwave"],
        enablePaystack: json["enablePaystack"],
        paystackPublicKey: json["paystackPublicKey"],
        enablePaypal: json["enablePaypal"],
        paypalClientId: json["paypalClientId"],
        aboutPageUrl: json["aboutPageUrl"],
        privacyPolicyUrl: json["privacyPolicyUrl"],
        termsAndConditionsUrl: json["termsAndConditionsUrl"],
        agoraAppId: json["agoraAppId"],
        supportPhone: json["supportPhone"],
        supportEmail: json["supportEmail"],
        maxVideoDurationSec: json["maxVideoDurationSec"],
        id: json["_id"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "currency": currency?.toJson(),
        "enableGooglePlay": enableGooglePlay,
        "enableStripe": enableStripe,
        "stripePublicKey": stripePublicKey,
        "enableRazorpay": enableRazorpay,
        "razorpayKeyId": razorpayKeyId,
        "flutterwaveKeyId": flutterwaveKeyId,
        "enableFlutterwave": enableFlutterwave,
        "enablePaystack": enablePaystack,
        "paystackPublicKey": paystackPublicKey,
        "enablePaypal": enablePaypal,
        "paypalClientId": paypalClientId,
        "aboutPageUrl": aboutPageUrl,
        "privacyPolicyUrl": privacyPolicyUrl,
        "termsAndConditionsUrl": termsAndConditionsUrl,
        "agoraAppId": agoraAppId,
        "supportPhone": supportPhone,
        "supportEmail": supportEmail,
        "maxVideoDurationSec": maxVideoDurationSec,
        "_id": id,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}

class Currency {
  String? name;
  String? symbol;
  String? countryCode;
  String? currencyCode;
  bool? isDefault;

  Currency({
    this.name,
    this.symbol,
    this.countryCode,
    this.currencyCode,
    this.isDefault,
  });

  factory Currency.fromJson(Map<String, dynamic> json) => Currency(
        name: json["name"],
        symbol: json["symbol"],
        countryCode: json["countryCode"],
        currencyCode: json["currencyCode"],
        isDefault: json["isDefault"],
      );

  Map<String, dynamic> toJson() => {
        "name": name,
        "symbol": symbol,
        "countryCode": countryCode,
        "currencyCode": currencyCode,
        "isDefault": isDefault,
      };
}

