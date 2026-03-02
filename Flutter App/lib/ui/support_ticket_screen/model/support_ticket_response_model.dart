class SupportTicketListResponseModel {
  bool? status;
  List<SupportTicketItem>? data;

  SupportTicketListResponseModel({this.status, this.data});

  factory SupportTicketListResponseModel.fromJson(Map<String, dynamic> json) =>
      SupportTicketListResponseModel(
        status: json["status"],
        data: json["data"] == null
            ? []
            : List<SupportTicketItem>.from(
                json["data"].map((x) => SupportTicketItem.fromJson(x))),
      );
}

class SupportTicketItem {
  int? id;
  String? ticketNumber;
  String? subject;
  String? issueType;
  String? status;
  String? email;
  String? createdAt;

  SupportTicketItem({
    this.id,
    this.ticketNumber,
    this.subject,
    this.issueType,
    this.status,
    this.email,
    this.createdAt,
  });

  factory SupportTicketItem.fromJson(Map<String, dynamic> json) =>
      SupportTicketItem(
        id: json["id"],
        ticketNumber: json["ticket_number"]?.toString(),
        subject: json["subject"]?.toString(),
        issueType: json["issue_type"]?.toString(),
        status: json["status"]?.toString(),
        email: json["email"]?.toString(),
        createdAt: json["created_at"]?.toString(),
      );
}

// ── Detail model ─────────────────────────────────────────────────────────────

class SupportTicketDetailModel {
  bool? status;
  SupportTicketDetail? data;

  SupportTicketDetailModel({this.status, this.data});

  factory SupportTicketDetailModel.fromJson(Map<String, dynamic> json) =>
      SupportTicketDetailModel(
        status: json["status"],
        data: json["data"] == null
            ? null
            : SupportTicketDetail.fromJson(json["data"]),
      );
}

class SupportTicketDetail {
  int? id;
  String? ticketNumber;
  String? subject;
  String? message;
  String? issueType;
  String? status;
  String? email;
  String? phone;
  String? createdAt;
  List<TicketMessage>? messages;

  SupportTicketDetail({
    this.id,
    this.ticketNumber,
    this.subject,
    this.message,
    this.issueType,
    this.status,
    this.email,
    this.phone,
    this.createdAt,
    this.messages,
  });

  factory SupportTicketDetail.fromJson(Map<String, dynamic> json) =>
      SupportTicketDetail(
        id: json["id"],
        ticketNumber: json["ticket_number"]?.toString(),
        subject: json["subject"]?.toString(),
        message: json["message"]?.toString(),
        issueType: json["issue_type"]?.toString(),
        status: json["status"]?.toString(),
        email: json["email"]?.toString(),
        phone: json["phone"]?.toString(),
        createdAt: json["created_at"]?.toString(),
        messages: json["messages"] == null
            ? []
            : List<TicketMessage>.from(
                json["messages"].map((x) => TicketMessage.fromJson(x))),
      );
}

class TicketMessage {
  int? id;
  String? message;
  int? senderId;
  String? senderName;
  String? senderRole;
  String? createdAt;

  TicketMessage({
    this.id,
    this.message,
    this.senderId,
    this.senderName,
    this.senderRole,
    this.createdAt,
  });

  factory TicketMessage.fromJson(Map<String, dynamic> json) => TicketMessage(
        id: json["id"],
        message: json["message"]?.toString(),
        senderId: json["sender_id"],
        senderName: json["sender"]?["name"]?.toString(),
        senderRole: json["sender"]?["role"]?.toString(),
        createdAt: json["created_at"]?.toString(),
      );
}
