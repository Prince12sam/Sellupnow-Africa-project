import 'package:http/http.dart' as http;
import 'package:listify/ui/chat_detail_screen/model/report_user_api.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class ReportUserApi {
  static Future<ReportUserResponseModel?> reportUser({
    required String reportedUserId,
    required String reason,
    required String uid,
  }) async {
    Utils.showLog("Report User API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse(
        "${Api.reportUserApi}?reportedUserId=$reportedUserId&reason=$reason",
      );

      var request = http.Request('POST', url);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Report User Response Code => ${response.statusCode}");
      Utils.showLog("Report User Response => $responseBody");

      if (response.statusCode == 200) {
        final model = reportUserResponseModelFromJson(responseBody);
        Utils.showLog("Report User Parsed Model => ${model.toJson()}");
        return model;
      } else {
        Utils.showLog("Report User Failed => ${response.reasonPhrase}");
        return null;
      }
    } catch (e) {
      Utils.showLog("Report User Error => $e");
      return null;
    }
  }
}
