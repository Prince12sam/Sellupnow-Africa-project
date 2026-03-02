// To parse this JSON data, do
//
//     final chatOldHistoryResponseModel = chatOldHistoryResponseModelFromJson(jsonString);

import 'dart:convert';

ChatOldHistoryResponseModel chatOldHistoryResponseModelFromJson(String str) => ChatOldHistoryResponseModel.fromJson(json.decode(str));

String chatOldHistoryResponseModelToJson(ChatOldHistoryResponseModel data) => json.encode(data.toJson());

class ChatOldHistoryResponseModel {
  bool? status;
  String? message;
  String? chatTopic;
  List<OldChat>? chat;

  ChatOldHistoryResponseModel({
    this.status,
    this.message,
    this.chatTopic,
    this.chat,
  });

  factory ChatOldHistoryResponseModel.fromJson(Map<String, dynamic> json) => ChatOldHistoryResponseModel(
        status: json["status"],
        message: json["message"],
        chatTopic: json["chatTopic"],
        chat: json["chat"] == null ? [] : List<OldChat>.from(json["chat"]!.map((x) => OldChat.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "chatTopic": chatTopic,
        "chat": chat == null ? [] : List<dynamic>.from(chat!.map((x) => x.toJson())),
      };
}

class OldChat {
  String? id;
  String? chatTopicId;
  String? senderId;
  int? messageType;
  String? message;
  String? image;
  String? audio;
  int? giftType;
  int? giftCount;
  bool? isRead;
  dynamic callId;
  String? callDuration;
  String? date;
  DateTime? createdAt;
  DateTime? updatedAt;

  OldChat({
    this.id,
    this.chatTopicId,
    this.senderId,
    this.messageType,
    this.message,
    this.image,
    this.audio,
    this.giftType,
    this.giftCount,
    this.isRead,
    this.callId,
    this.callDuration,
    this.date,
    this.createdAt,
    this.updatedAt,
  });

  factory OldChat.fromJson(Map<String, dynamic> json) => OldChat(
        id: json["_id"],
        chatTopicId: json["chatTopicId"],
        senderId: json["senderId"],
        messageType: json["messageType"],
        message: json["message"],
        image: json["image"],
        audio: json["audio"],
        giftType: json["giftType"],
        giftCount: json["giftCount"],
        isRead: json["isRead"],
        callId: json["callId"],
        callDuration: json["callDuration"],
        date: json["date"],
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  Map<String, dynamic> toJson() => {
        "_id": id,
        "chatTopicId": chatTopicId,
        "senderId": senderId,
        "messageType": messageType,
        "message": message,
        "image": image,
        "audio": audio,
        "giftType": giftType,
        "giftCount": giftCount,
        "isRead": isRead,
        "callId": callId,
        "callDuration": callDuration,
        "date": date,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };

  /// 🔹 Helper: parse product data from `message`
  Map<String, String> get messageData {
    Map<String, String> data = {};
    if (message != null && message!.contains(":")) {
      final parts = message!.split(",");
      for (var part in parts) {
        var kv = part.split(":");
        if (kv.length >= 2) {
          final key = kv[0].trim();
          final value = kv.sublist(1).join(":").trim();
          data[key] = value;
        }
      }
    }
    return data;
  }
}

extension OldChatHelper on OldChat {
  /// Skip if `message` string andar "messageType: 1" hoy
  bool get isInnerMessageType1 {
    if (message == null) return false;
    // return message!.contains("productMessageViewType: 1");
    return message!.contains("message: 1");
  }

  bool get isInnerMessageType2 {
    if (message == null) return false;
    return message!.contains("message: 2");
    // return message!.contains("ProductMessageType: 2");
  }
}

// extension OldChatHelper on OldChat {
//   /// true = this message is only product info
//   bool get isProductMessage {
//     if (message == null) return false;
//
//     // 1) Check if JSON type product message
//     if (message!.trim().startsWith("{") && message!.contains("productName")) {
//       return true;
//     }
//
//     // 2) Check if "productName:" string aave
//     if (message!.contains("productName:")) {
//       return true;
//     }
//
//     return false;
//   }
// }
