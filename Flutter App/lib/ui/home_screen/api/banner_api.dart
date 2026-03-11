import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/home_screen/model/banner_api_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class BannerApi {
  static Future<BannerResponseModel?> callApi({String? placement}) async {
    Utils.showLog("Banner Api Calling...");

    final uri = placement == null || placement.isEmpty
        ? Uri.parse(Api.bannerApi)
        : Uri.parse(Api.bannerApi).replace(queryParameters: {'placement': placement});
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Banner Api uri :: $uri");
    Utils.showLog("Banner Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Banner Api STATUS CODE :: ${response.statusCode} \n Banner Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return BannerResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Banner Api Status code is not 200');
      }
    } catch (e) {
      log("Banner Api Error :: $e");
    }
    return null;
  }
}
