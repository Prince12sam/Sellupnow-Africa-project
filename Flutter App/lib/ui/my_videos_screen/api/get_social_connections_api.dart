import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/my_videos_screen/model/get_social_onnections_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class GetSocialConnectionsApi {
  static Future<GetSocialConnectionsResponseModel?> callApi() async {
    Utils.showLog("Get Social Connections Api Calling...");

    final token = await FirebaseAccessToken.onGet();

    final uri = Uri.parse(Api.getSocialConnectionsApi);

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: "${Database.getUserProfileResponseModel?.user?.firebaseUid}",
    };

    log("Get Social Connections Api headers => $headers");
    log("Get Social Connections Api uri => $uri");

    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Get Social Connections Api Response => $jsonResponse");

        return GetSocialConnectionsResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Get Social Connections Api Error => ${response.statusCode}");
        Utils.showLog("Error Body => ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Get Social Connections Api Exception => $error");
    }

    return null;
  }
}
