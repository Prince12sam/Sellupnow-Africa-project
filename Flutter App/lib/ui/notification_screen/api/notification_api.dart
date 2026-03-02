import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/notification_screen/model/notification_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class NotificationApi {
  static Future<NotificationResponseModel?> callApi() async {
    final token = await FirebaseAccessToken.onGet();

    Utils.showLog("Notification Api List Calling...");

    final uri = Uri.parse(Api.notificationListApi);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      ApiParams.contentType: 'application/json',
    };

    Utils.showLog("Notification Api List uri :: $uri");
    Utils.showLog("Notification Api List headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Notification Api List STATUS CODE :: ${response.statusCode} \n Notification Api List RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return NotificationResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Notification Api List Status code is not 200');
      }
    } catch (e) {
      log("Notification Api List Error :: $e");
    }
    return null;
  }
}
