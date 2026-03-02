import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class PaystackPackageApi {
  static Future<Map<String, dynamic>?> initialize({
    required String token,
    required String uid,
    required String packageId,
    required String packageType,
  }) async {
    final uri = Uri.parse(Api.paystackInitPackage);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: ApiParams.tokenStartPoint + token,
      ApiParams.authUid: uid,
      ApiParams.contentType: 'application/json',
    };
    final body = jsonEncode({
      ApiParams.packageId: packageId,
      ApiParams.packageType: packageType,
    });

    Utils.showLog("Paystack init url: $uri");

    try {
      final response = await http.post(uri, headers: headers, body: body);
      if (response.statusCode == 200) {
        return jsonDecode(response.body) as Map<String, dynamic>;
      }
      Utils.showLog("Paystack init failed: ${response.statusCode}");
    } catch (e) {
      Utils.showLog("Paystack init error: $e");
    }
    return null;
  }

  static Future<Map<String, dynamic>?> verify({
    required String token,
    required String uid,
    required String reference,
  }) async {
    final uri = Uri.parse(Api.paystackVerifyPackage);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: ApiParams.tokenStartPoint + token,
      ApiParams.authUid: uid,
      ApiParams.contentType: 'application/json',
    };
    final body = jsonEncode({
      ApiParams.reference: reference,
    });

    Utils.showLog("Paystack verify url: $uri");

    try {
      final response = await http.post(uri, headers: headers, body: body);
      if (response.statusCode == 200) {
        return jsonDecode(response.body) as Map<String, dynamic>;
      }
      Utils.showLog("Paystack verify failed: ${response.statusCode}");
    } catch (e) {
      Utils.showLog("Paystack verify error: $e");
    }
    return null;
  }
}
