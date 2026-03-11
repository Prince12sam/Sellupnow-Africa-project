class EscrowOrdersResponse {
  final bool status;
  final String tab;
  final int total;
  final List<EscrowOrder> data;

  EscrowOrdersResponse({
    required this.status,
    required this.tab,
    required this.total,
    required this.data,
  });

  factory EscrowOrdersResponse.fromJson(Map<String, dynamic> json) =>
      EscrowOrdersResponse(
        status: json['status'] ?? false,
        tab: json['tab'] ?? 'buyer',
        total: json['total'] ?? 0,
        data: (json['data'] as List<dynamic>? ?? [])
            .map((e) => EscrowOrder.fromJson(e as Map<String, dynamic>))
            .toList(),
      );
}

class EscrowDetailResponse {
  final bool status;
  final EscrowOrder? data;

  EscrowDetailResponse({required this.status, this.data});

  factory EscrowDetailResponse.fromJson(Map<String, dynamic> json) =>
      EscrowDetailResponse(
        status: json['status'] ?? false,
        data: json['data'] != null
            ? EscrowOrder.fromJson(json['data'] as Map<String, dynamic>)
            : null,
      );
}

class EscrowOrder {
  final int id;
  final int? listingId;
  final String? listingTitle;
  final String? listingSlug;
  final double totalAmount;
  final double listingPrice;
  final double adminFeeAmount;
  final String? currency;
  final String? status;
  final String? paymentGateway;
  final String? paymentTransactionId;
  final String? buyerName;
  final String? sellerName;
  final String? fundedAt;
  final String? releasedAt;
  final String? sellerAcceptedAt;
  final String? sellerDeliveredAt;
  final String? buyerConfirmedAt;
  final String? counterpartyName;
  final String? createdAt;

  EscrowOrder({
    required this.id,
    this.listingId,
    this.listingTitle,
    this.listingSlug,
    required this.totalAmount,
    this.listingPrice = 0,
    this.adminFeeAmount = 0,
    this.currency,
    this.status,
    this.paymentGateway,
    this.paymentTransactionId,
    this.buyerName,
    this.sellerName,
    this.fundedAt,
    this.releasedAt,
    this.sellerAcceptedAt,
    this.sellerDeliveredAt,
    this.buyerConfirmedAt,
    this.counterpartyName,
    this.createdAt,
  });

  factory EscrowOrder.fromJson(Map<String, dynamic> json) => EscrowOrder(
        id: json['id'] ?? 0,
        listingId: json['listing_id'],
        listingTitle: json['listing_title'],
        listingSlug: json['listing_slug'],
        totalAmount: _toDouble(json['total_amount']),
        listingPrice: _toDouble(json['listing_price']),
        adminFeeAmount: _toDouble(json['admin_fee_amount']),
        currency: json['currency'],
        status: json['status'],
        paymentGateway: json['payment_gateway'],
        paymentTransactionId: json['payment_transaction_id'],
        buyerName: json['buyer_name'],
        sellerName: json['seller_name'],
        fundedAt: json['funded_at'],
        releasedAt: json['released_at'],
        sellerAcceptedAt: json['seller_accepted_at'],
        sellerDeliveredAt: json['seller_delivered_at'],
        buyerConfirmedAt: json['buyer_confirmed_at'],
        counterpartyName: json['counterparty_name'],
        createdAt: json['created_at'],
      );

  static double _toDouble(dynamic v) {
    if (v == null) return 0.0;
    if (v is num) return v.toDouble();
    return double.tryParse(v.toString()) ?? 0.0;
  }

  String get statusLabel {
    final s = status ?? '';
    return s.replaceAll('_', ' ').replaceFirstMapped(
        RegExp(r'^[a-z]'), (m) => m[0]!.toUpperCase());
  }
}

class EscrowBreakdownResponse {
  final bool status;
  final double listingPrice;
  final double platformFee;
  final double total;
  final double walletBalance;
  final bool canAfford;
  final String currency;

  EscrowBreakdownResponse({
    required this.status,
    this.listingPrice = 0,
    this.platformFee = 0,
    this.total = 0,
    this.walletBalance = 0,
    this.canAfford = false,
    this.currency = 'GHS',
  });

  factory EscrowBreakdownResponse.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? {};
    return EscrowBreakdownResponse(
      status: json['status'] ?? false,
      listingPrice: _toDouble(data['listing_price']),
      platformFee: _toDouble(data['platform_fee']),
      total: _toDouble(data['total']),
      walletBalance: _toDouble(data['wallet_balance']),
      canAfford: data['can_afford'] ?? false,
      currency: data['currency'] ?? 'GHS',
    );
  }

  static double _toDouble(dynamic v) {
    if (v == null) return 0.0;
    if (v is num) return v.toDouble();
    return double.tryParse(v.toString()) ?? 0.0;
  }
}

class EscrowInitiateResponse {
  final bool status;
  final String? message;
  final int? escrowId;

  EscrowInitiateResponse({
    required this.status,
    this.message,
    this.escrowId,
  });

  factory EscrowInitiateResponse.fromJson(Map<String, dynamic> json) =>
      EscrowInitiateResponse(
        status: json['status'] ?? false,
        message: json['message'],
        escrowId: json['data']?['escrow_id'],
      );
}
