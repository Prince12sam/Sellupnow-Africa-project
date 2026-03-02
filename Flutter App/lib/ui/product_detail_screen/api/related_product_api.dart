import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class RelatedProductApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<AllAdsResponseModel?> callApi({required String categoryId, required String userId}) async {
    Utils.showLog("Related product List Api Calling...");
    startPagination += 1;

    Utils.showLog("categoryId::::::::::::::${categoryId}");

    final Map<String, dynamic> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
      ApiParams.categoryId: categoryId,
      ApiParams.userId: userId,
    };

    String query = Uri(queryParameters: queryParameters).query;

    Utils.showLog("Related product List Api quary => $query");
    Utils.showLog("Related product List Api quary => $queryParameters");

    final uri = Uri.parse(Api.relatedProductApi + (query.isNotEmpty ? query : ''));

    Utils.showLog("Related product List Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
    };

    log("Related product List Api headers  $headers");
    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Related product List Api Response => $jsonResponse");
        Utils.showLog("Related product List Api Response.status code => ${response.statusCode}");

        return AllAdsResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Related product List Api StateCode Error");
      }
    } catch (error) {
      Utils.showLog("Related product List Api Error => $error");
    }
    return null;
  }
}
