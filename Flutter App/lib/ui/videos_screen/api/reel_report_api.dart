import 'package:http/http.dart' as http;
import 'package:listify/ui/videos_screen/model/reel_report_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class ReelReportApi {
  static Future<ReelReportResponseModel?> reportReel({
    required String reelId,
    required String reason,
    required String uid,
  }) async {
    Utils.showLog("Report Reel API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse(
        "${Api.reelReportApi}?adVideoId=$reelId&reason=$reason",
      );
      Utils.showLog("Report Reel uri => $url");

      var request = http.Request('POST', url);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Report Reel Response Code => ${response.statusCode}");
      Utils.showLog("Report Reel Response => $responseBody");

      if (response.statusCode == 200) {
        final model = reelReportResponseModelFromJson(responseBody);
        Utils.showLog("Report Reel Parsed Model => ${model.toJson()}");
        return model;
      } else {
        Utils.showLog("Report Reel Failed => ${response.reasonPhrase}");
        return null;
      }
    } catch (e) {
      Utils.showLog("Report Reel Error => $e");
      return null;
    }
  }
}
