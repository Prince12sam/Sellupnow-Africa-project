import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/message_screen/model/chat_list_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class ChatListApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<ChatListResponseModel?> callApi({required int chatType}) async {
    Utils.showLog("Chat List Api Calling...");
    startPagination += 1;

    final token = await FirebaseAccessToken.onGet();

    // Build query parameters
    final Map<String, dynamic> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
      ApiParams.chatType: chatType.toString(),
    };

    String query = Uri(queryParameters: queryParameters).query;

    // Build the complete URI
    final uri = Uri.parse(Api.chatList + (query.isNotEmpty ? query : ''));

    Utils.showLog("Chat List Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: "${Database.getUserProfileResponseModel?.user?.firebaseUid}",
    };

    log("Chat List Api headers  $headers");
    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Chat List Api Response => $jsonResponse");
        Utils.showLog("Chat List Api Response.status code => ${response.statusCode}");

        return ChatListResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Chat List Api StateCode Error: ${response.statusCode}");
        Utils.showLog("Chat List Api Error Response: ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Chat List Api Error => $error");
    }
    return null;
  }
}
