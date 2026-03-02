import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/user_verification_screen/model/id_proof_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class IdProofApi {
  static Future<IdProofResponseModel?> callApi() async {
    Utils.showLog("IdentityProof Api Calling...");

    final uri = Uri.parse(Api.idProofApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("IdentityProof Api uri :: $uri");
    Utils.showLog("IdentityProof Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('IdentityProof API STATUS CODE :: ${response.statusCode} \n IdentityProof API  RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return IdProofResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception('Status code is not 200');
      }
    } catch (e) {
      log("IdentityProof api  :: $e");
    }
    return null;
  }
}
