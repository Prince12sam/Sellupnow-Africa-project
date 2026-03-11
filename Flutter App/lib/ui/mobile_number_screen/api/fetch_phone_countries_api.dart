import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/mobile_number_screen/model/phone_country_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';

class FetchPhoneCountriesApi {
  static Future<List<PhoneCountryModel>?> call() async {
    final uri = Uri.parse(Api.countryApi);
    final headers = {ApiParams.key: Api.secretKey};

    log('FetchPhoneCountriesApi uri: $uri');

    try {
      final response = await http.get(uri, headers: headers);
      log('FetchPhoneCountriesApi status: ${response.statusCode}');

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body) as Map<String, dynamic>;
        final data = decoded['data'] as Map<String, dynamic>?;
        final list = (data?['countries'] as List<dynamic>?) ?? [];
        return list
            .map((e) => PhoneCountryModel.fromJson(e as Map<String, dynamic>))
            .toList();
      }
    } catch (e) {
      log('FetchPhoneCountriesApi error: $e');
    }
    return null;
  }
}
