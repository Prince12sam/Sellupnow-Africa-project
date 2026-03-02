import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/ui/login_screen/model/user_exist_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class CheckUserExistApi {
  static UserExistResponseModel? checkUserExistModel;

  static Future<UserExistResponseModel?> callApi({
    String? identity,
    String? loginType,
    String? email,
    String? mobileNumber,
    String? password,
  }) async {
    Utils.showLog("Check User Exist Api Calling...");

    final uri = Uri.parse("${Api.checkUserExit}${ApiParams.loginType}=$loginType");

    Utils.showLog("Check User Exist Api URI => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      'Content-Type': 'application/json',
    };

    final body = jsonEncode({
      ApiParams.email: email ?? "",
      ApiParams.password: password ?? "",
    });

    try {
      var request = http.Request('POST', uri);
      request.body = body;
      request.headers.addAll(headers);

      http.StreamedResponse streamedResponse = await request.send();

      if (streamedResponse.statusCode == 200) {
        final responseBody = await streamedResponse.stream.bytesToString();
        Utils.showLog("Check User Exist Api Response => $responseBody");

        final jsonResponse = json.decode(responseBody);
        checkUserExistModel = UserExistResponseModel.fromJson(jsonResponse);
        return checkUserExistModel;
      } else {
        Utils.showLog(">>>>> Check User Exist Api StatusCode Error: ${streamedResponse.statusCode} <<<<<");
      }
    } catch (error) {
      Utils.showLog("Check User Exist Api Error => $error");
    }

    return null;
  }
}
