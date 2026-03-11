import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/my_videos_screen/model/record_video_view_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class RecordVideoViewApi {
  static Future<RecordVideoViewResponseModel?> callApi({
    required String videoId,
  }) async {
    Utils.showLog("📺 Record Video View Api Calling...");

    final token = await FirebaseAccessToken.onGet();

    // ✅ Full URL with query param
    final uri = Uri.parse("${Api.recordVideoView}?videoId=$videoId");

    Utils.showLog("Record Video View Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: Database.authUid,
    };

    log("Record Video View Api headers => $headers");

    try {
      // ✅ POST with no body
      final response = await http.post(
        uri,
        headers: headers,
      );

      Utils.showLog("Record Video View Api status code => ${response.statusCode}");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        Utils.showLog("Record Video View Api Response => $jsonResponse");

        // ✅ Parse into your model
        return RecordVideoViewResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Record Video View Api Error: ${response.statusCode}");
        Utils.showLog("Record Video View Api Error Response: ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Record Video View Api Exception => $error");
    }
    return null;
  }
}
