import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class ChangePasswordApi {
  static Future<Map<String, dynamic>?> callApi({
    required String uid,
    required String currentPassword,
    required String newPassword,
    required String confirmPassword,
  }) async {
    Utils.showLog("Change Password Api Calling...");
    final token = await FirebaseAccessToken.onGet();

    try {
      final uri = Uri.parse(Api.changePassword);
      final headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final body = jsonEncode({
        'current_password': currentPassword,
        'password': newPassword,
        'password_confirmation': confirmPassword,
      });

      Utils.showLog("Change Password Api URL => $uri");

      final response = await http.post(uri, headers: headers, body: body);
      final jsonResult = jsonDecode(response.body);
      log("Change Password Api Response => $jsonResult");

      return {
        'status': response.statusCode == 200,
        'statusCode': response.statusCode,
        'message': jsonResult['message'] ?? '',
      };
    } catch (e) {
      Utils.showLog("Change Password Api Error => $e");
      return null;
    }
  }
}
