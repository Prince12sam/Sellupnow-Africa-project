import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/featured_ads_screen/model/featured_ads_plan_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class FeaturedAdsPlanApi {
  static Future<FeaturedAdsPlanResponseModel?> callApi() async {
    Utils.showLog("Featured Ads Plan Api Calling...");

    final uri = Uri.parse(Api.featuredAdsPlanApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Featured Ads Plan Api uri :: $uri");
    Utils.showLog("Featured Ads Plan Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Featured Ads Plan Api STATUS CODE :: ${response.statusCode} \n Featured Ads Plan Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return FeaturedAdsPlanResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Featured Ads Plan Api Status code is not 200');
      }
    } catch (e) {
      log("Featured Ads Plan Api Error :: $e");
    }
    return null;
  }
}
