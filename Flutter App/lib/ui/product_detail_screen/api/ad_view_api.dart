import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/product_detail_screen/model/ad_view_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class AdViewsApi {
  static Future<AdViewResponseModel?> callApi({required String adId}) async {
    Utils.showLog("Ad Views Api Calling...");

    final token = await FirebaseAccessToken.onGet();

    // ✅ Full URL
    final uri = Uri.parse("${Api.adViewApi}?adId=$adId");

    Utils.showLog("Ad Views Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: "${Database.getUserProfileResponseModel?.user?.firebaseUid}",
    };

    log("Ad Views Api headers => $headers");

    try {
      // ✅ POST with no body
      final response = await http.post(
        uri,
        headers: headers,
      );

      Utils.showLog("Ad Views Api status code => ${response.statusCode}");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        Utils.showLog("Ad Views Api Response => $jsonResponse");

        // ✅ Parse into your model
        return AdViewResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Ad Views Api Error: ${response.statusCode}");
        Utils.showLog("Ad Views Api Error Response: ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Ad Views Api Exception => $error");
    }
    return null;
  }
}
