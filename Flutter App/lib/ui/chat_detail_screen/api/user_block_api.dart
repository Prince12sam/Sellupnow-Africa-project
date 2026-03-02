import 'package:http/http.dart' as http;
import 'package:listify/ui/chat_detail_screen/model/user_block_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class BlockUserApi {
  static Future<UserBlockResponseModel?> toggleBlockUser({
    required String blockedId,
    required String uid,
  }) async {
    Utils.showLog("Toggle Block User API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse(
        "${Api.blockUserApi}?blockedId=$blockedId",
      );

      var request = http.Request('POST', url);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Toggle Block User Response Code => ${response.statusCode}");
      Utils.showLog("Toggle Block User Response => $responseBody");

      if (response.statusCode == 200) {
        final userBlockResponse = userBlockResponseModelFromJson(responseBody);
        return userBlockResponse;
      } else {
        return UserBlockResponseModel(
          status: false,
          message: response.reasonPhrase ?? "Something went wrong",
        );
      }
    } catch (e) {
      Utils.showLog("Toggle Block User Error => $e");
      return UserBlockResponseModel(
        status: false,
        message: "Exception: $e",
      );
    }
  }
}
