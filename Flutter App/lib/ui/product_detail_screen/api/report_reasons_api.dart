import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/product_detail_screen/model/report_reasons_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';

class ReportReasonsApi {
  static Future<ReportReasonsModel?> callApi() async {
    Utils.showLog("Report Reasons Api Calling...");

    final uri = Uri.parse(Api.reportReasonApi);
    final headers = {ApiParams.key: Api.secretKey};

    Utils.showLog("Report Reasons Api uri :: $uri");
    Utils.showLog("Report Reasons Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log('Report Reasons Api STATUS CODE :: ${response.statusCode} \n Report Reasons Api RESPONSE :: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return ReportReasonsModel.fromJson(jsonResponse);
      } else {
        throw Exception('Report Reasons Api Status code is not 200');
      }
    } catch (e) {
      log("Report Reasons Api Error :: $e");
    }
    return null;
  }
}
