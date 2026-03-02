import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/my_videos_screen/model/video_delete_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class VideoDeleteApi {
  static Future<MyVideoDeleteResponseModel?> callApi({required String? id}) async {
    final token = await FirebaseAccessToken.onGet() ?? "";

    Utils.showLog("Video Delete Api Calling...");

    // Build query parameters
    final Map<String, dynamic> queryParameters = {
      ApiParams.id: id,
    };

    String query = Uri(queryParameters: queryParameters).query;

    // Build the complete URI
    final uri = Uri.parse(Api.userVideoDeleteApi + (query.isNotEmpty ? query : ''));

    // final uri = Uri.parse(Api.userVideoDeleteApi);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
      ApiParams.contentType: "application/json",
    };
    log("Video Delete Api URL ::$uri");

    try {
      final response = await http.delete(uri, headers: headers);

      Utils.showLog("Video Delete Api Response => ${response.body}");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return MyVideoDeleteResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Video Delete Api StateCode Error");
      }
    } catch (e) {
      Utils.showLog("Video Delete Api Response => ${e.toString()}");
    }
    return null;
  }
}
