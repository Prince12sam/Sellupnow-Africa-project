import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/blog_screen/model/trending_blog_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class TrendingBlogApi {
  static Future<TrendingBlogResponse?> callApi() async {
    Utils.showLog("Trending Blog Api  Calling...");

    final uri = Uri.parse(Api.trendingBlog);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Trending Blog Api   uri :: $uri");
    Utils.showLog("Trending Blog Api  headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Trending Blog Api  STATUS CODE :: ${response.statusCode} \n Trending Blog Api  RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return TrendingBlogResponse.fromJson(jsonResponse);
      } else {
        throw Exception('Trending Blog Api  Status code is not 200');
      }
    } catch (e) {
      log("Trending Blog Api  Error :: $e");
    }
    return null;
  }
}
