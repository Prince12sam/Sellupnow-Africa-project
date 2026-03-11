import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/my_videos_screen/model/specific_seller_video_list_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class SpecificSellerVideoApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<SpecificSellerVideoListResponseModel?> callApi() async {
    final token = await FirebaseAccessToken.onGet();

    Utils.showLog("Specific seller video list Api Calling...");
    startPagination += 1;

    final Map<String, dynamic> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.specificSellerVideoListApi + (query.isNotEmpty ? query : ''));

    Utils.showLog("Specific seller video list Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: Database.authUid,
      ApiParams.contentType: "application/json",
    };

    log("Specific seller video list Api headers  $headers");
    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Specific seller video list Api Response => $jsonResponse");
        Utils.showLog("Specific seller video list Api Response.status code => ${response.statusCode}");

        return SpecificSellerVideoListResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Specific seller video list Api StateCode Error");
      }
    } catch (error) {
      Utils.showLog("Specific seller video list Api Error => $error");
    }
    return null;
  }
}
