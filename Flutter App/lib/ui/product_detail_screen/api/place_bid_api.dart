import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/product_detail_screen/model/place_bid_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class PlaceBidApi {
  static Future<PlaceBidResponseModel?> callApi({
    required String adId,
    required String bidAmount,
    required List<Map<String, dynamic>> attributes,
  }) async {
    Utils.showLog("Place Bid Api Calling...");

    final token = await FirebaseAccessToken.onGet();

    final uri = Uri.parse(Api.placeBidApi);
    Utils.showLog("Place Bid Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: Database.authUid,
    };

    log("Place Bid Api headers => $headers");

    final bodyMap = {
      ApiParams.adId: adId,
      ApiParams.bidAmount: num.parse(bidAmount),
      ApiParams.attributes: attributes,
    };

    Utils.showLog("Place Bid Api body => $bodyMap");

    try {
      final response = await http.post(
        uri,
        headers: headers,
        body: json.encode(bodyMap),
      );

      Utils.showLog("Place Bid Api status code => ${response.statusCode}");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        Utils.showLog("Place Bid Api Response => $jsonResponse");

        return PlaceBidResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Place Bid Api Error: ${response.statusCode}");
        Utils.showLog("Place Bid Api Error Response: ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Place Bid Api Exception => $error");
    }
    return null;
  }
}
