import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/transaction_history/model/transaction_history_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class TransactionHistoryListApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<TransactionHistoryResponseModel?> callApi() async {
    final token = await FirebaseAccessToken.onGet();

    Utils.showLog("Transaction List Api Calling...");
    startPagination += 1;

    final Map<String, dynamic> queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.getPurchaseHistory + (query.isNotEmpty ? query : ''));

    Utils.showLog("Transaction List Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
      ApiParams.contentType: "application/json",
    };

    log("Transaction List Api headers  $headers");
    try {
      final response = await http.get(uri, headers: headers);
      Utils.showLog("Transaction List Api Response Code => ${response.statusCode}");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Transaction List Api Response => $jsonResponse");
        Utils.showLog("Transaction List Api Response.status code => ${response.statusCode}");

        return TransactionHistoryResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Transaction List Api StateCode Error");
      }
    } catch (error) {
      Utils.showLog("Transaction List Api Error => $error");
    }
    return null;
  }
}
