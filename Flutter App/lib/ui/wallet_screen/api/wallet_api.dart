import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/wallet_screen/model/wallet_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class WalletApi {
  static int startPagination = 0;
  static const int limitPagination = 20;

  static Future<WalletResponseModel?> callBalanceApi() async {
    final token = await FirebaseAccessToken.onGet();
    Utils.showLog("Wallet Balance Api Calling...");

    final uri = Uri.parse(Api.walletBalance);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid:
          '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
      ApiParams.contentType: "application/json",
    };

    try {
      final response = await http.get(uri, headers: headers);
      Utils.showLog("Wallet Balance Api Response Code => ${response.statusCode}");
      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        Utils.showLog("Wallet Balance Api Response => $jsonResponse");
        return WalletResponseModel.fromJson(jsonResponse);
      }
    } catch (error) {
      Utils.showLog("Wallet Balance Api Error => $error");
    }
    return null;
  }

  static Future<WalletResponseModel?> callTransactionsApi() async {
    final token = await FirebaseAccessToken.onGet();
    Utils.showLog("Wallet Transactions Api Calling...");

    startPagination += 1;
    final queryParameters = {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    };
    final query = Uri(queryParameters: queryParameters).query;
    final uri = Uri.parse(Api.walletTransactions + (query.isNotEmpty ? query : ''));

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid:
          '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
      ApiParams.contentType: "application/json",
    };

    try {
      final response = await http.get(uri, headers: headers);
      Utils.showLog("Wallet Transactions Api => ${response.statusCode}");
      if (response.statusCode == 200) {
        return WalletResponseModel.fromJson(json.decode(response.body));
      }
    } catch (error) {
      Utils.showLog("Wallet Transactions Api Error => $error");
    }
    return null;
  }
}
