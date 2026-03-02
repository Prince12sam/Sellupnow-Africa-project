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
    final token = await FirebaseAccessToken.onGet();

    final uri = Uri.parse(Api.getLoginUserProfile);

    Utils.showLog("Get Login User Profile uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: loginUserId,
    };

    log("Get Login User Profile headers  $headers");
    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Get Login User Profile Response => $jsonResponse");
        Utils.showLog("Get Login User Profile Response.status code => ${response.statusCode}");

        return GetUserProfileResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Get Login User Profile StateCode Error");
      }
    } catch (error) {
      Utils.showLog("Get Login User Profile Api Error => $error");
    }
    return null;
  }
}
