import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/featured_ads_screen/model/ads_promoted_response_model.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class AdsPromotedApi {
  static Future<AdsPromotedResponseModel?> callApi({
    required List<String> adIds,
  }) async {
    Utils.showLog("Promote Ads Api Calling...");

    // Convert list to comma separated string
    final ids = adIds.join(",");
    final token = await FirebaseAccessToken.onGet();

    final uri = Uri.parse(
      "${Api.promoteAdsApi}?adIds=$ids", // Example: /api/client/adListing/promoteAds?adIds=1,2,3
    );

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: Database.authUid,
    };

    Utils.showLog("Promote Ads Api uri :: $uri");
    Utils.showLog("Promote Ads Api headers :: $headers");

    try {
      final request = http.MultipartRequest("PATCH", uri);
      request.headers.addAll(headers);

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      log("Promote Ads Api STATUS CODE :: ${response.statusCode}\nRESPONSE :::::::: ${response.body} >>>>>>>>>>end response>>>>>>>");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return AdsPromotedResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception("Promote Ads Api Status code :: ${response.statusCode}");
      }
    } catch (e) {
      log("Promote Ads Api Error :: $e");
    }
    return null;
  }
}
