import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class PaypalPackageApi {
  static Future<Map<String, dynamic>?> createOrder({
    required String token,
    required String uid,
    required String packageId,
    required String packageType,
  }) async {
    final uri = Uri.parse(Api.paypalCreateOrder);
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

    Utils.showLog("PayPal create order url: $uri");

    try {
      final response = await http.post(uri, headers: headers, body: body);
      if (response.statusCode == 200) {
        return jsonDecode(response.body) as Map<String, dynamic>;
      }
      Utils.showLog("PayPal create order failed: ${response.statusCode}");
    } catch (e) {
      Utils.showLog("PayPal create order error: $e");
    }
    return null;
  }

  static Future<Map<String, dynamic>?> captureOrder({
    required String token,
    required String uid,
    required String orderId,
  }) async {
    final uri = Uri.parse(Api.paypalCaptureOrder);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: ApiParams.tokenStartPoint + token,
      ApiParams.authUid: uid,
      ApiParams.contentType: 'application/json',
    };
    final body = jsonEncode({
      ApiParams.orderId: orderId,
    });

    Utils.showLog("PayPal capture order url: $uri");

    try {
      final response = await http.post(uri, headers: headers, body: body);
      if (response.statusCode == 200) {
        return jsonDecode(response.body) as Map<String, dynamic>;
      }
      Utils.showLog("PayPal capture order failed: ${response.statusCode}");
    } catch (e) {
      Utils.showLog("PayPal capture order error: $e");
    }
    return null;
  }
}
