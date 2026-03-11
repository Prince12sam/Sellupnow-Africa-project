import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/chat_detail_screen/model/chat_history_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class ChatHistoryApi {
  static int startPagination = 1;
  static int limitPagination = 20;

  static Future<ChatOldHistoryResponseModel?> callApi({
    required String adId,
    required String receiverId,
  }) async {
    Utils.showLog("📩 Chat Old History Api Calling... page => $startPagination");

    final token = await FirebaseAccessToken.onGet();

    final Map<String, dynamic> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
      ApiParams.receiverId: receiverId,
      ApiParams.adId: adId,
    };

    final uri = Uri.parse(Api.chatHistory)
        .replace(queryParameters: queryParameters);

    Utils.showLog("🔗 Chat Old History Api URI => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: Database.authUid,
    };

    try {
      final response = await http.get(uri, headers: headers);

      Utils.showLog("📦 Chat History Raw Response => ${response.body}");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        // ✅ Log parsed JSON clearly
        Utils.showLog("✅ Chat History Parsed Response => ${jsonEncode(jsonResponse)}");

        return ChatOldHistoryResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("❌ Chat History Api Error ${response.statusCode}: ${response.body}");
      }
    } catch (error, stackTrace) {
      Utils.showLog("❌ Chat History Api Exception => $error");
      log("📛 StackTrace => $stackTrace");
    }

    return null;
  }
}
