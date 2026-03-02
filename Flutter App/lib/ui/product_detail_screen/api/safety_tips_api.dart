import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/product_detail_screen/model/safety_tips_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class SafetyTipsApi {
  static Future<SafetyTipsApiResponseModel?> callApi() async {
    Utils.showLog("Safety Tips Api Calling...");

    final uri = Uri.parse(Api.safetyTipsApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Safety Tips Api uri :: $uri");
    Utils.showLog("Safety Tips Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Safety Tips Api STATUS CODE :: ${response.statusCode} \n Safety Tips Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return SafetyTipsApiResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Safety Tips Api Status code is not 200');
      }
    } catch (e) {
      log("Safety Tips Api Error :: $e");
    }
    return null;
  }
}
