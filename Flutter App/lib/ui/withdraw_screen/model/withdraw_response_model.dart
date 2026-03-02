class WithdrawListResponseModel {
  bool? status;
  List<WithdrawItem>? data;

  WithdrawListResponseModel({this.status, this.data});

  factory WithdrawListResponseModel.fromJson(Map<String, dynamic> json) =>
      WithdrawListResponseModel(
        status: json["status"],
        data: json["data"] == null
            ? []
            : List<WithdrawItem>.from(
                json["data"].map((x) => WithdrawItem.fromJson(x))),
      );
}

class WithdrawItem {
  int? id;
  double? amount;
  String? contactNumber;
  String? name;
  String? withdrawMethod;
  String? reason;
  String? status;
  String? createdAt;

  WithdrawItem({
    this.id,
    this.amount,
    this.contactNumber,
    this.name,
    this.withdrawMethod,
    this.reason,
    this.status,
    this.createdAt,
  });

  factory WithdrawItem.fromJson(Map<String, dynamic> json) => WithdrawItem(
        id: json["id"],
        amount: (json["amount"] as num?)?.toDouble(),
        contactNumber: json["contact_number"]?.toString(),
        name: json["name"]?.toString(),
        withdrawMethod: json["withdraw_method"]?.toString(),
        reason: json["reason"]?.toString(),
        status: json["status"]?.toString(),
        createdAt: json["created_at"]?.toString(),
      );
}
