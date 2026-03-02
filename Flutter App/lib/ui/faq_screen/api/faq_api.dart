import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/faq_screen/model/faq_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class FaqApi {
  static Future<FaqApiResponseModel?> callApi() async {
    Utils.showLog("FAQ Api Calling...");

    final uri = Uri.parse(Api.faqApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("FAQ Api uri :: $uri");
    Utils.showLog("FAQ Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('FAQ Api STATUS CODE :: ${response.statusCode} \n FAQ Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return FaqApiResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('FAQ Api Status code is not 200');
      }
    } catch (e) {
      log("FAQ Api Error :: $e");
    }
    return null;
  }
}
