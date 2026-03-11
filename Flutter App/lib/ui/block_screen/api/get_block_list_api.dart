import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/block_screen/model/get_block_list_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class BlockListApi {
  static Future<UserBlockListResponseModel?> getBlockedUsers() async {
    Utils.showLog("Get Blocked Users API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      // Build complete URI
      final uri = Uri.parse(Api.blockListApi);

      Utils.showLog("Get Blocked Users Api uri => $uri");

      // Headers
      final headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.contentType: "application/json",
        ApiParams.authToken: "Bearer $token",
        ApiParams.authUid: Database.authUid,
      };

      log("Get Blocked Users Api headers => $headers");

      // Call API
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Get Blocked Users Api Response => $jsonResponse");

        return UserBlockListResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Get Blocked Users Api Error: ${response.statusCode}");
        Utils.showLog("Error Response => ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Get Blocked Users Api Exception => $error");
    }

    return null;
  }
}
