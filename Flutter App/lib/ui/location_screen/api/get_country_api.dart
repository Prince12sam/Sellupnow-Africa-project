import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/location_screen/model/country_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class GetCountryApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<CountryResponseModel?> callApi() async {
    Utils.showLog("Get All country Api Calling...");
    startPagination += 1;

    final Map<String, dynamic> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.countryApi + (query.isNotEmpty ? query : ''));

    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Get All country Api uri :: $uri");
    Utils.showLog("Get All country Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Get All country Api STATUS CODE :: ${response.statusCode} \n Get All country Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return CountryResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Get All country Api Status code is not 200');
      }
    } catch (e) {
      log("Get All country Api Error :: $e");
    }
    return null;
  }
}
