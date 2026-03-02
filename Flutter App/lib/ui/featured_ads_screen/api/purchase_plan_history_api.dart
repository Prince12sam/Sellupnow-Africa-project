import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/ui/featured_ads_screen/model/purchase_plan_history_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class PurchasePlanHistoryApi {
  static Future<PurchasePlanHistoryResponseModel?> callApi({
    required String paymentGateway,
    required String token,
    required String uid,
    required String packageId,
    required String packageType,
    // required String currency,
  }) async {
    Utils.showLog("Purchase Plan history Api Calling...");

    final uri = Uri.parse(Api.purchaseHistory);

    Utils.showLog("Purchase Plan history Api Url $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: ApiParams.tokenStartPoint + token,
      ApiParams.authUid: uid,
      ApiParams.contentType: 'application/json',
    };

    Utils.showLog("Purchase Plan history Api headers $headers");

    final body = jsonEncode({
      ApiParams.packageId: packageId,
      ApiParams.packageType: packageType,
      ApiParams.paymentGateway: paymentGateway,
      // ApiParams.currency: currency,
    });

    Utils.showLog("Purchase Plan history Api body $body");

    try {
      final response = await http.post(uri, headers: headers, body: body);

      if (response.statusCode == 200) {
        Utils.showLog("Purchase Plan history Api Response => ${response.body}");

        final jsonResponse = jsonDecode(response.body);

        return PurchasePlanHistoryResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Purchase Plan history Api StatusCode Error => ${response.statusCode}");
      }
    } catch (error) {
      Utils.showLog("Purchase Plan history Api Error => $error");
    }
    return null;
  }
}
