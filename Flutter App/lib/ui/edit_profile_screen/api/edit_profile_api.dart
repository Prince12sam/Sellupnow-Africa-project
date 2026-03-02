import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/edit_profile_screen/model/edit_profile_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class EditProfileApi {
  static Future<EditProfileModel?> callApi({
    required String uid,
    required String phoneNumber,
    required String address,
    String? image,
    String? name,
    String? email,
    bool? notification,
    bool? isNotificationsAllowed,
    bool? isContactInfoVisible,
  }) async {
    Utils.showLog("Edit Profile Api Calling...");
    final token = await FirebaseAccessToken.onGet();

    try {
      var request = http.MultipartRequest(
        'PATCH',
        Uri.parse(Api.editProfile),
      );
      Utils.showLog("Edit Profile Api URL => ${request.url}");

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        // ApiParams.contentType: 'application/json',
        ApiParams.authUid: uid
      };
      Utils.showLog("Edit Profile Api Headers => $headers");

      request.fields.addAll({
        ApiParams.email: email ?? '',
        ApiParams.address: address,
        ApiParams.name: name ?? '',
        ApiParams.phoneNumber: phoneNumber,
        ApiParams.profileImage: image ?? '',
        "isNotificationsAllowed": isNotificationsAllowed.toString(),
        "isContactInfoVisible": isContactInfoVisible.toString(),
      });

      Utils.showLog("image::::$image");
      if (image != null) {
        request.files.add(await http.MultipartFile.fromPath('profileImage', image));
      }

      request.headers.addAll(headers);
      log("Edit Profile Api Request => ${request.fields}");

      final response = await request.send();
      log("Edit Profile Api Response => ${response.statusCode}");
      log("Edit Profile Api Status => ${response.statusCode}");

      final responseBody = await response.stream.bytesToString();
      final jsonResult = jsonDecode(responseBody);
      Utils.showLog("Edit Profile Api Response => $jsonResult");

      return EditProfileModel.fromJson(jsonResult);
    } catch (e) {
      Utils.showLog("Edit Profile Api Error => $e");
      return null;
    }
  }
}
