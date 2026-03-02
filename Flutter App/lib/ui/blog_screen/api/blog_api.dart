import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/blog_screen/model/blog_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class BlogApi {
  static Future<BlogResponseModel?> callApi() async {
    Utils.showLog("Blog Api Calling...");

    final uri = Uri.parse(Api.blog);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Blog Api uri :: $uri");
    Utils.showLog("Blog Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Blog Api STATUS CODE :: ${response.statusCode} \n Blog Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return BlogResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Blog Api Status code is not 200');
      }
    } catch (e) {
      log("Blog Api Error :: $e");
    }
    return null;
  }
}
