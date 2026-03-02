import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/subscription%20_plan_screen/model/subscription_plan_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class SubscriptionPlanApi {
  static Future<SubscriptionPlanResponseModel?> callApi() async {
    Utils.showLog("Subscription Plan Api Calling...");

    final uri = Uri.parse(Api.subscriptionPlanApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Subscription Plan Api uri :: $uri");
    Utils.showLog("Subscription Plan Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Subscription Plan Api STATUS CODE :: ${response.statusCode} \n Subscription Plan Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return SubscriptionPlanResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Subscription Plan Api Status code is not 200');
      }
    } catch (e) {
      log("Subscription Plan Api Error :: $e");
    }
    return null;
  }
}
