import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';

class SellerGetAdsApi {
  static Future<AllAdsResponseModel?> getAllSellerAds({
    required String sellerId,
    required int start,
    required int limit,
    required String uid,
    required String loginUserId,
  }) async {
    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      // Backend expects snake_case: seller_id or user_id.
      var url = Uri.parse(
        '${Api.getSellerProduct}?start=$start&limit=$limit&seller_id=$sellerId&user_id=$sellerId&loginUserId=$loginUserId',
      );

      var request = http.Request('GET', url);
      request.headers.addAll(headers);

      log("📍 All Ads API URL => $url");
      log("📍 All Ads API Headers => $headers");

      http.StreamedResponse response = await request.send();

      if (response.statusCode == 200) {
        var resString = await response.stream.bytesToString();
        log("✅ All Ads API Response => $resString");

        return AllAdsResponseModel.fromJson(json.decode(resString));
      } else {
        log("❌ All Ads API Failed => ${response.reasonPhrase}");
        return null;
      }
    } catch (e) {
      log("🚨 All Ads API Exception => $e");
      return null;
    }
  }
}
