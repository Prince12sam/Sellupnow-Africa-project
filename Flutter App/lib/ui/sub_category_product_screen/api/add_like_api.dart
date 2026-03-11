import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/ui/sub_category_product_screen/model/add_like_reponse_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class AddLikeApi {
  static Future<AddLikeResponseModel?> callApi({
    required String adId,
    required String uid,
  }) async {
    final token = await FirebaseAccessToken.onGet();

    Utils.showLog("Add Like Api Calling...");

    final Map<String, dynamic> queryParameters = {
      // Backend accepts listing_id/product_id; keep adId for backward compatibility.
      'listing_id': adId,
      'product_id': adId,
      ApiParams.adId: adId,
    };

    String query = Uri(queryParameters: queryParameters).query;

    final uri = Uri.parse(Api.addLikeProduct + (query.isNotEmpty ? query : ''));

    // final uri = Uri.parse(Api.addLikeProduct);

    Utils.showLog("Add Like Api Url $uri");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid: uid,
      ApiParams.contentType: 'application/json',
    };

    Utils.showLog("Add Like Api headers $headers");

    try {
      final response = await http.post(uri, headers: headers);

      if (response.statusCode == 200) {
        Utils.showLog("Add Like Api Response => ${response.body}");

        final jsonResponse = jsonDecode(response.body);

        return AddLikeResponseModel.fromJson(jsonResponse);
      } else {
        Utils.showLog("Add Like Api StatusCode Error => ${response.statusCode}");
      }
    } catch (error) {
      Utils.showLog("Add Like Api Error => $error");
    }
    return null;
  }
}
