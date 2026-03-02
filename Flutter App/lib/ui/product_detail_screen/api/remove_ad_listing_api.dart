import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/product_detail_screen/model/remove_ad_listing_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class RemoveAdListingApi {
  static Future callApi({
    required String adId,
    required String uid,
  }) async {
    Utils.showLog("Remove Ad Listing API Calling...");

    final uri = Uri.parse("${Api.removeProductApi}?adId=$adId");
    final token = await FirebaseAccessToken.onGet();

    final headers = {
      ApiParams.key: Api.secretKey,
      'Authorization': 'Bearer $token',
      ApiParams.authUid: uid,
    };

    Utils.showLog("Remove Ad Listing API uri :: $uri");
    Utils.showLog("Remove Ad Listing API headers :: $headers");

    try {
      // final request = http.Request('DELETE', uri);
      // request.headers.addAll(headers);

      final response = await http.delete(uri, headers: headers);

      // final response = await request.send();
      // final body = await response.stream.bytesToString();

      log('Remove Ad Listing API STATUS CODE :: ${response.statusCode} \n RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return RemoveAdListingResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("remove Ad Listing Api Error");
      }
    } catch (e) {
      log("Remove Ad Listing API Error :: $e");
      return false;
    }
  }
}
