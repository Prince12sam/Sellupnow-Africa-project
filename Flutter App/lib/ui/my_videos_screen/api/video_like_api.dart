import 'package:http/http.dart' as http;
import 'package:listify/ui/my_videos_screen/model/video_like_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class VideoLikeApi {
  static Future<VideoLikeResponseModel?> callApi({
    required String adVideoId,
  }) async {
    Utils.showLog("Video like API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
        ApiParams.contentType: 'application/json',
      };

      final Map<String, dynamic> queryParameters = {
        ApiParams.adVideoId: adVideoId,
      };

      String query = Uri(queryParameters: queryParameters).query;

      final uri = Uri.parse(Api.likeVideoApi + (query.isNotEmpty ? query : ''));

      var request = http.Request('POST', uri);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Video like Response Code => ${response.statusCode}");
      Utils.showLog("Video like Response => $responseBody");

      if (response.statusCode == 200) {
        final model = videoLikeResponseModelFromJson(responseBody);
        Utils.showLog("Video like Parsed Model => ${model.toJson()}");
        return model;
      } else {
        Utils.showLog("Video like Failed => ${response.reasonPhrase}");
        return null;
      }
    } catch (e) {
      Utils.showLog("Video like Error => $e");
      return null;
    }
  }
}
