import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/notification_screen/model/delete_notification_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class ClearNotificationsApi {
  static Future<ClearNotificationsResponseModel?> callApi() async {
    final token = await FirebaseAccessToken.onGet() ?? "";

    Utils.showLog("🔔 Clear Notifications Api Calling...");

    final uri = Uri.parse(
      "${Api.baseUrl}api/client/notification/clearMyNotifications",
    );
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
        ApiParams.authUid: Database.authUid,
      ApiParams.contentType: "application/json",
    };

    log("🔗 Clear Notifications Api URL :: $uri");
    log("📦 Clear Notifications Api Headers :: $headers");

    try {
      final response = await http.delete(uri, headers: headers);

      Utils.showLog("✅ Clear Notifications Api Response => ${response.body}");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return ClearNotificationsResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog(
          "⚠️ Clear Notifications Api Status Code Error :: ${response.statusCode}",
        );
      }
    } catch (e) {
      Utils.showLog("❌ Clear Notifications Api Exception => ${e.toString()}");
    }
    return null;
  }
}
