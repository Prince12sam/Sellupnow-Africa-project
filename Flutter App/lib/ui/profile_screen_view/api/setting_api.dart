import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/profile_screen_view/model/setting_api_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class SettingApi {
  static Future<SettingApiResponseModel?> callApi() async {
    Utils.showLog("Setting Api Calling...");

    final uri = Uri.parse(Api.settingApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Setting Api uri :: $uri");
    Utils.showLog("Setting Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Setting Api STATUS CODE :: ${response.statusCode} \n Setting Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return SettingApiResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Setting Api Status code is not 200');
      }
    } catch (e) {
      log("Setting Api Error :: $e");
    }
    return null;
  }
}
