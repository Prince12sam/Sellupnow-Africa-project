class WalletResponseModel {
  bool? status;
  double? balance;
  List<WalletTransaction>? data;

  WalletResponseModel({this.status, this.balance, this.data});

  factory WalletResponseModel.fromJson(Map<String, dynamic> json) =>
      WalletResponseModel(
        status: json["status"],
        balance: (json["balance"] as num?)?.toDouble(),
        data: json["data"] == null
            ? []
            : List<WalletTransaction>.from(
                json["data"].map((x) => WalletTransaction.fromJson(x))),
      );
}

class WalletTransaction {
  int? id;
  double? amount;
  String? type;
  String? purpose;
  String? note;
  String? transactionId;
  String? createdAt;

  WalletTransaction({
    this.id,
    this.amount,
    this.type,
    this.purpose,
    this.note,
    this.transactionId,
    this.createdAt,
  });

  factory WalletTransaction.fromJson(Map<String, dynamic> json) =>
      WalletTransaction(
        id: json["id"],
        amount: (json["amount"] as num?)?.toDouble(),
        type: json["type"]?.toString(),
        purpose: json["purpose"]?.toString(),
        note: json["note"]?.toString(),
        transactionId: json["transaction_id"]?.toString(),
        createdAt: json["created_at"]?.toString(),
      );
}
