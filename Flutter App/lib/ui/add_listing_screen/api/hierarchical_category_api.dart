import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/add_listing_screen/model/hierarchical_cattegory_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class HierarchicalCategoryApi {
  static Future<CategoryResponseModel?> callApi() async {
    Utils.showLog("Hierarchical category Api Calling...");

    final uri = Uri.parse(Api.hierarchicalCategoryApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Hierarchical category Api uri :: $uri");
    Utils.showLog("Hierarchical category Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Hierarchical category Api STATUS CODE :: ${response.statusCode} \n Hierarchical category Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return CategoryResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Hierarchical category Api Status code is not 200');
      }
    } catch (e) {
      log("Hierarchical category Api Error :: $e");
    }
    return null;
  }
}
