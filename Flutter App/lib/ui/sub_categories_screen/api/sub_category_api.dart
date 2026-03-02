import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/sub_categories_screen/model/sub_category_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class SubCategoryApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<SubCategoryResponseModel?> callApi({required String? parentId}) async {
    Utils.showLog("Sub category Api Calling...");
    startPagination += 1;

    final Map<String, dynamic> queryParameters = {
      ApiParams.parentId: parentId,
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.subCategoryApi + (query.isNotEmpty ? query : ''));

    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Sub category Api uri :: $uri");
    Utils.showLog("Sub category Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Sub category Api STATUS CODE :: ${response.statusCode} \n Sub category Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return SubCategoryResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Sub category Api Status code is not 200');
      }
    } catch (e) {
      log("Sub category Api Error :: $e");
    }
    return null;
  }
}
