// To parse this JSON data, do
//
//     final chatListResponseModel = chatListResponseModelFromJson(jsonString);

import 'dart:convert';

ChatListResponseModel chatListResponseModelFromJson(String str) => ChatListResponseModel.fromJson(json.decode(str));

String chatListResponseModelToJson(ChatListResponseModel data) => json.encode(data.toJson());

class ChatListResponseModel {
  bool? status;
  String? message;
  List<ChatList>? chatList;

  ChatListResponseModel({
    this.status,
    this.message,
    this.chatList,
  });

  factory ChatListResponseModel.fromJson(Map<String, dynamic> json) => ChatListResponseModel(
        status: json["status"],
        message: json["message"],
        chatList: json["chatList"] == null ? [] : List<ChatList>.from(json["chatList"]!.map((x) => ChatList.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "chatList": chatList == null ? [] : List<dynamic>.from(chatList!.map((x) => x.toJson())),
      };
}

class ChatList {
  String? id;
  String? receiverId;
  String? adId;
  String? name;
  String? profileImage;
  bool? isOnline;
  String? chatTopic;
  int? chatType;
  String? senderId;
  int? messageType;
  String? message;
  DateTime? lastChatMessageTime;
  String? productTitle;
  String? productImage;
  double? productPrice;
  int? unreadCount;
  String? time;

  ChatList({
    this.id,
    this.receiverId,
    this.adId,
    this.name,
    this.profileImage,
    this.isOnline,
    this.chatTopic,
    this.chatType,
    this.senderId,
    this.messageType,
    this.message,
    this.lastChatMessageTime,
    this.productTitle,
    this.productImage,
    this.productPrice,
    this.unreadCount,
    this.time,
  });

  factory ChatList.fromJson(Map<String, dynamic> json) => ChatList(
        id: json["_id"],
        receiverId: json["receiverId"],
        adId: json["adId"],
        name: json["name"],
        profileImage: json["profileImage"],
        isOnline: json["isOnline"],
        chatTopic: json["chatTopic"],
        chatType: json["chatType"],
        senderId: json["senderId"],
        messageType: json["messageType"],
        message: json["message"],
        lastChatMessageTime: json["lastChatMessageTime"] == null ? null : DateTime.parse(json["lastChatMessageTime"]),
        productTitle: json["productTitle"],
        productImage: json["productImage"],
        productPrice: json["productPrice"]?.toDouble(),
        unreadCount: json["unreadCount"],
        time: json["time"],
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "receiverId": receiverId,
        "adId": adId,
        "name": name,
        "profileImage": profileImage,
        "isOnline": isOnline,
        "chatTopic": chatTopic,
        "chatType": chatType,
        "senderId": senderId,
        "messageType": messageType,
        "message": message,
        "lastChatMessageTime": lastChatMessageTime?.toIso8601String(),
        "productTitle": productTitle,
        "productImage": productImage,
        "productPrice": productPrice,
        "unreadCount": unreadCount,
        "time": time,
      };
}
