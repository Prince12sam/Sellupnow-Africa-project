import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/product_detail_screen/model/product_detail_response_model.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/utils.dart';

class ProductDetailApi {
  static Future<ProductDetailResponseModel?> callApi({
    required String adId,
    required String userId,
  }) async {
    Utils.showLog("Product Detail Api Calling...");

    final uri = Uri.parse(
      "${Api.productDetailApi}?listing_id=$adId&userId=$userId",
    );

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: "application/json",
    };

    Utils.showLog("Product Detail Api uri :: $uri");
    Utils.showLog("Product Detail Api headers :: $headers");

    try {
      final response = await http.get(uri, headers: headers);

      log("Product Detail Api STATUS CODE :: ${response.statusCode}\nRESPONSE :::::::: ${response.body} >>>>>>>>>>end response>>>>>>>");

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return ProductDetailResponseModel.fromJson(jsonResponse);
      } else {
        throw Exception("Product Detail Api Status code :: ${response.statusCode}");
      }
    } catch (e) {
      log("Product Detail Api Error :: $e");
    }
    return null;
  }
}
