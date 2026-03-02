import 'package:http/http.dart' as http;
import 'package:listify/ui/product_detail_screen/model/ad_report_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class AdReportApi {
  static Future<AdReportResponseModel?> reportAd({
    required String adId,
    required String reason,
    required String uid,
  }) async {
    Utils.showLog("Report Ad API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse(
        "${Api.adReportApi}?adId=$adId&reason=$reason",
      );
      Utils.showLog("Report Ad uri => $url");

      var request = http.Request('POST', url);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Report Ad Response Code => ${response.statusCode}");
      Utils.showLog("Report Ad Response => $responseBody");

      if (response.statusCode == 200) {
        final model = adReportResponseModelFromJson(responseBody);
        Utils.showLog("Report Ad Parsed Model => ${model.toJson()}");
        return model;
      } else {
        Utils.showLog("Report Ad Failed => ${response.reasonPhrase}");
        return null;
      }
    } catch (e) {
      Utils.showLog("Report Ad Error => $e");
      return null;
    }
  }
}
