import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/select_city_screen/model/city_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class CityApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<CityResponseModel?> callApi() async {
    Utils.showLog("City Api Calling...");
    startPagination += 1;

    final Map<String, dynamic> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.cityApi + (query.isNotEmpty ? query : ''));

    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("City Api uri :: $uri");
    Utils.showLog("City Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('City Api STATUS CODE :: ${response.statusCode} \n City Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return CityResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('City Api Status code is not 200');
      }
    } catch (e) {
      log("City Api Error :: $e");
    }
    return null;
  }
}
