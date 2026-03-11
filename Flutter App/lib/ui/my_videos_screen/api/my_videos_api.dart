import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/my_videos_screen/model/my_videos_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class MyVideosApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<MyVideosResponseModel?> callApi() async {

    startPagination+=1;

    Utils.showLog("My Videos List Api Calling...");

    final Map<String, String> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
      ApiParams.userId: Database.getUserProfileResponseModel?.user?.id ?? Database.loginUserId,
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.userVideoListApi + (query.isNotEmpty ? query : ''));

    Utils.showLog("My Videos List Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
    };

    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        Utils.showLog("My Videos List Api Response => $jsonResponse");
        return MyVideosResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("My Videos List Api StateCode Error");
      }
    } catch (error) {
      Utils.showLog("My Videos List Api Error => $error");
    }
    return null;
  }
}

  



