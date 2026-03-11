import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class GetUserProfileApi {
  static Future<GetUserProfileResponseModel?> callApi({required String loginUserId}) async {
    Utils.showLog("Get Login User Profile Api Calling...");
    Utils.showLog("Get Login User Profile loginUserId => $loginUserId");
    if (loginUserId.trim().isEmpty) {
      Utils.showLog("Get Login User Profile SKIPPED - empty auth UID");
      return null;
    }

    final token = await FirebaseAccessToken.onGet();

    if (token == null || token.isEmpty) {
      Utils.showLog("Get Login User Profile SKIPPED - no Firebase token available");
      return null;
    }

    final uri = Uri.parse(Api.getLoginUserProfile);

    Utils.showLog("Get Login User Profile uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: loginUserId,
    };

    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Get Login User Profile Response => $jsonResponse");
        Utils.showLog("Get Login User Profile Response.status code => ${response.statusCode}");

        return GetUserProfileResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Get Login User Profile StatusCode Error: ${response.statusCode}");
        Utils.showLog("Get Login User Profile Error Body: ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Get Login User Profile Api Error => $error");
    }
    return null;
  }
}
