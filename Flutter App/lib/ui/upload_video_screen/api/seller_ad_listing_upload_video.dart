import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/upload_video_screen/model/seller_product_info_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class SellerAdListingUploadVideo {
  static Future<SellerProductInfoModel?> callApi() async {
    Utils.showLog("Seller product info Api Calling...");

    final token = await FirebaseAccessToken.onGet();

    final uri = Uri.parse(Api.sellerProductInfo);

    Utils.showLog("Seller product info Api uri => $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: Database.authUid,
    };

    log("Seller product info Api headers  $headers");
    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        Utils.showLog("Seller product info Api Response => $jsonResponse");
        Utils.showLog("Seller product info Api Response.status code => ${response.statusCode}");

        return SellerProductInfoModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Seller product info Api StateCode Error: ${response.statusCode}");
        Utils.showLog("Seller product info Api Error Response: ${response.body}");
      }
    } catch (error) {
      Utils.showLog("Seller product info Api Error => $error");
    }
    return null;
  }
}
