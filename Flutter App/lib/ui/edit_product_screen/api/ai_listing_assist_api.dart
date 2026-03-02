import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class AiListingAssistApi {
  static Future<Map<String, dynamic>?> callApi({
    required String uid,
    required String title,
    required String subtitle,
    required String description,
  }) async {
    Utils.showLog("AI Listing Assist API Calling...");

    final token = await FirebaseAccessToken.onGet();

    final uri = Uri.parse(Api.aiListingAssistApi);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: uid,
      ApiParams.contentType: 'application/json',
    };

    final body = jsonEncode({
      'title': title,
      'subtitle': subtitle,
      'description': description,
    });

    Utils.showLog("AI Listing Assist uri :: $uri");
    Utils.showLog("AI Listing Assist headers :: $headers");

    try {
      final response = await http.post(uri, headers: headers, body: body);
      log('AI Listing Assist STATUS CODE :: ${response.statusCode}');
      log('AI Listing Assist RESPONSE :: ${response.body}');

      final jsonResponse = json.decode(response.body);
      if (response.statusCode == 200) {
        return jsonResponse as Map<String, dynamic>;
      }

      return {
        'status': false,
        'message': jsonResponse is Map && jsonResponse['message'] != null
            ? jsonResponse['message']
            : 'Request failed',
      };
    } catch (e) {
      log("AI Listing Assist Error :: $e");
      return {
        'status': false,
        'message': 'Request failed',
      };
    }
  }
}
