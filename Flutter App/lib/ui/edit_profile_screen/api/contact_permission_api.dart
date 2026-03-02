
import 'package:http/http.dart' as http;
import 'package:listify/ui/edit_profile_screen/model/notification_permission_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class ContactPermissionApi {
  static Future<NotificationPermissionResponseModel?> updateUserPermission({
    required String uid,
    required String type,
  }) async {
    try {
      Utils.showLog("Update User Permission API Calling...");

      final token = await FirebaseAccessToken.onGet() ?? "";

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse("${Api.notificationApi}?type=$type");

      var request = http.MultipartRequest('PATCH', url);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Update User Permission Response Code => ${response.statusCode}");
      Utils.showLog("Update User Permission Response => $responseBody");

      if (response.statusCode == 200) {
        return notificationPermissionResponseModelFromJson(responseBody);
      } else {
        return NotificationPermissionResponseModel(
          status: false,
          message: response.reasonPhrase ?? "Something went wrong",
        );
      }
    } catch (e) {
      Utils.showLog("Update User Permission Error => $e");
      return NotificationPermissionResponseModel(
        status: false,
        message: "Exception: $e",
      );
    }
  }
}
