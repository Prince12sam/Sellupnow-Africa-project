import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/my_videos_screen/model/user_follow_unfollow_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class UserFollowUnFollowApi {
  static Future<UserFollowUnFollowResponseModel?> toggleFollowStatus({
    required String uid,
    required String toUserId,
  }) async {
    Utils.showLog("Toggle Follow/UnFollow API Calling...");

    final token = await FirebaseAccessToken.onGet();

    try {
      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
      };

      var request = http.Request(
        'POST',
        Uri.parse("${Api.userFollowUnFollowApi}?toUserId=$toUserId"),
      );

      request.headers.addAll(headers);

      log("Follow API Headers => $headers");

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      log("Follow API Response Code => ${response.statusCode}");
      log("Follow API Response => $responseBody");

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(responseBody);
        return UserFollowUnFollowResponseModel.fromJson(jsonData);
      } else {
        Utils.showLog("Follow API Error => ${response.reasonPhrase}");
      }
    } catch (e) {
      Utils.showLog("Toggle Follow/UnFollow API Exception => $e");
    }

    return null;
  }
}
