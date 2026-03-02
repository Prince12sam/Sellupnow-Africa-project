import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/fashion_blog_screen/model/blog_by_id_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class BlogByIdApi {
  static Future<BlogByIdResponse?> callApi({required String blogId}) async {
    Utils.showLog("Blog By Id Api  Calling...");

    final uri = Uri.parse("${Api.blogById}?${ApiParams.blogId}=$blogId");
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Blog By Id Api  uri :: $uri");
    Utils.showLog("Blog By Id Api  headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Blog By Id Api  STATUS CODE :: ${response.statusCode} \n Blog By Id Api  RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return BlogByIdResponse.fromJson(jsonResponse);
      } else {
        throw Exception('Blog By Id Api  Status code is not 200');
      }
    } catch (e) {
      log("Blog By Id Api  Error :: $e");
    }
    return null;
  }
}
