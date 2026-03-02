import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/ui/withdraw_screen/model/withdraw_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class WithdrawApi {
  static int startPagination = 0;
  static const int limitPagination = 20;

  static Future<WithdrawListResponseModel?> fetchList() async {
    final token = await FirebaseAccessToken.onGet();
    startPagination += 1;

    final query = Uri(queryParameters: {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    }).query;
    final uri = Uri.parse(Api.withdrawList + (query.isNotEmpty ? query : ''));

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid:
          '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
      ApiParams.contentType: "application/json",
    };

    try {
      final response = await http.get(uri, headers: headers);
      Utils.showLog("Withdraw List => ${response.statusCode}");
      if (response.statusCode == 200) {
        return WithdrawListResponseModel.fromJson(json.decode(response.body));
      }
    } catch (e) {
      Utils.showLog("Withdraw List Error => $e");
    }
    return null;
  }

  static Future<bool> submitRequest({
    required double amount,
    required String contactNumber,
    required String name,
    required String withdrawMethod,
    String? reason,
  }) async {
    final token = await FirebaseAccessToken.onGet();
    final uri = Uri.parse(Api.submitWithdraw);

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid:
          '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
      ApiParams.contentType: "application/json",
    };

    final body = json.encode({
      'amount': amount,
      'contact_number': contactNumber,
      'name': name,
      'withdraw_method': withdrawMethod,
      if (reason != null && reason.isNotEmpty) 'reason': reason,
    });

    try {
      final response = await http.post(uri, headers: headers, body: body);
      Utils.showLog("Submit Withdraw => ${response.statusCode} ${response.body}");
      if (response.statusCode == 200) {
        final decoded = json.decode(response.body);
        return decoded['status'] == true;
      }
    } catch (e) {
      Utils.showLog("Submit Withdraw Error => $e");
    }
    return false;
  }
}
