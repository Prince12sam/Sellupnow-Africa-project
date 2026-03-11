class BannerAdListResponse {
  final bool status;
  final int total;
  final List<BannerAd> data;

  BannerAdListResponse({required this.status, required this.total, required this.data});

  factory BannerAdListResponse.fromJson(Map<String, dynamic> json) {
    return BannerAdListResponse(
      status: json['status'] == true || json['status'] == 1,
      total: json['total'] ?? 0,
      data: (json['data'] as List? ?? [])
          .map((e) => BannerAd.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}

class BannerAd {
  final int id;
  final String? title;
  final String? requestedSlot;
  final String? redirectUrl;
  final String? status;
  final String? createdAt;

  BannerAd({
    required this.id,
    this.title,
    this.requestedSlot,
    this.redirectUrl,
    this.status,
    this.createdAt,
  });

  factory BannerAd.fromJson(Map<String, dynamic> json) {
    return BannerAd(
      id: json['id'] ?? 0,
      title: json['title']?.toString(),
      requestedSlot: json['requested_slot']?.toString(),
      redirectUrl: json['redirect_url']?.toString(),
      status: json['status_label']?.toString() ?? _statusFromInt(json['status']),
      createdAt: json['created_at']?.toString(),
    );
  }

  static String _statusFromInt(dynamic s) {
    switch (s?.toString()) {
      case '1': return 'approved';
      case '2': return 'rejected';
      default: return 'pending';
    }
  }

  String get slotLabel => requestedSlot
          ?.replaceAll('_', ' ')
          .replaceFirstMapped(RegExp(r'^[a-z]'), (m) => m[0]!.toUpperCase()) ??
      '—';
}
