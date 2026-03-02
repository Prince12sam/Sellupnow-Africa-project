import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class CategoryAttributesApi {
  static Future<CategoryAttributesResponseModel?> callApi({required String? categoryId}) async {
    Utils.showLog("category Attributes Calling...");

    final Map<String, dynamic> queryParameters = {
      ApiParams.categoryId: categoryId,
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.categoryAttribute + (query.isNotEmpty ? query : ''));

    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("category Attributes uri :: $uri");
    Utils.showLog("category Attributes headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('category Attributes STATUS CODE :: ${response.statusCode} \n category Attributes RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return CategoryAttributesResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('category Attributes Status code is not 200');
      }
    } catch (e) {
      log("category Attributes Error :: $e");
    }
    return null;
  }
}
