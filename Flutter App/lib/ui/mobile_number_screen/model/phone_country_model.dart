class PhoneCountryModel {
  final int id;
  final String name;
  final String countryCode;
  final String dialCode;

  PhoneCountryModel({
    required this.id,
    required this.name,
    required this.countryCode,
    required this.dialCode,
  });

  factory PhoneCountryModel.fromJson(Map<String, dynamic> json) {
    return PhoneCountryModel(
      id: (json['id'] as num?)?.toInt() ?? 0,
      name: (json['name'] as String?) ?? '',
      countryCode: (json['country_code'] as String?) ?? '',
      dialCode: json['dial_code']?.toString() ?? '',
    );
  }
}
