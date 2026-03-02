import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/user_verification_screen/model/user_verification_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class UserVerificationApi {
  static Future<UserVerificationResponseModel?> callApi({
    required String uid,
    required String idProofFrontPath,
    required String idProofBackPath,
    required String selfiePath,
    required String idProof,
  }) async {
    Utils.showLog("Submit User Verification API Calling...");

    final token = await FirebaseAccessToken.onGet();

    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse(Api.userVerificationApi),
      );

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        'Content-Type': 'application/json',
      };

      Utils.showLog("id proof api $headers");

      request.headers.addAll(headers);

      // Optional field if backend expects it
      request.fields['idProof'] = idProof;

      // Attach front, back and selfie images
      request.files.add(await http.MultipartFile.fromPath('idProofFront', idProofFrontPath));
      request.files.add(await http.MultipartFile.fromPath('idProofBack', idProofBackPath));
      request.files.add(await http.MultipartFile.fromPath('selfie', selfiePath));

      Utils.showLog("Submit Verification Request => ${request.fields}");

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      log("Submit Verification Response Code => ${response.statusCode}");
      log("Submit Verification Response => $responseBody");

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(responseBody);

        return UserVerificationResponseModel.fromJson(jsonData);
      } else {
        Utils.showLog("User Verification Api Error");
      }
    } catch (e) {
      Utils.showLog("Submit Verification Error => $e");
    }
    return null;
  }
}
