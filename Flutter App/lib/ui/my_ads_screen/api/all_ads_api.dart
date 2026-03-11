import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class AllAdsApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<AllAdsResponseModel?> callApi(String? adtype) async {
    Utils.showLog("All Ads Api Calling...");
    startPagination += 1;

    final token = await FirebaseAccessToken.onGet();

    if (token == null || token.isEmpty) {
      Utils.showLog("All Ads Api skipped: Firebase token unavailable");
      return AllAdsResponseModel(
        status: false,
        message: 'Your session expired. Please login again.',
        data: const [],
      );
    }

    // Build query parameters — backend uses 'page'/'per_page'; 'limit' accepted
    // as alias but explicit names are safest.
    final Map<String, dynamic> queryParameters = {
      'page':     startPagination.toString(),
      'per_page': limitPagination.toString(),
    };

    // Add type parameter if adtype is not null or empty (for tab filtering)
    if (adtype != null && adtype.isNotEmpty) {
      queryParameters['type'] = adtype;
    }

    String query = Uri(queryParameters: queryParameters).query;

    // Build the complete URI
    final uri = Uri.parse(Api.allAddList + (query.isNotEmpty ? query : ''));

    Utils.showLog("All Ads Api uri => $uri");
    Utils.showLog("All Ads Api adtype => $adtype");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: Database.authUid,
    };

    log("All Ads Api headers  $headers");
    try {
      final response = await http.get(uri, headers: headers);

      final jsonResponse = json.decode(response.body);

      if (response.statusCode == 200) {

        Utils.showLog("All Ads Api Response => $jsonResponse");
        Utils.showLog("All Ads Api Response.status code => ${response.statusCode}");

        return AllAdsResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("All Ads Api StateCode Error: ${response.statusCode}");
        Utils.showLog("All Ads Api Error Response: ${response.body}");

        if (jsonResponse is Map<String, dynamic>) {
          return AllAdsResponseModel.fromJson({
            'status': jsonResponse['status'] ?? false,
            'message': jsonResponse['message'] ?? 'Failed to load My Ads.',
            'data': jsonResponse['data'] ?? const [],
          });
        }
      }
    } catch (error) {
      Utils.showLog("All Ads Api Error => $error");
    }
    return null;
  }
}
