import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/home_screen/model/category_api_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class AllCategoryApi {
  static Future<AllCategoryResponseModel?> callApi() async {
    Utils.showLog("All category Api Calling...");

    final uri = Uri.parse(Api.categoryApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("All category Api uri :: $uri");
    Utils.showLog("All category Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('All category Api STATUS CODE :: ${response.statusCode} \n All category Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return AllCategoryResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('All category Api Status code is not 200');
      }
    } catch (e) {
      log("All category Api Error :: $e");
    }
    return null;
  }
}
