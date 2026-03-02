import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';
import 'package:listify/ui/product_detail_screen/model/specific_product_like_response_model.dart';

class SpecificProductLikeApi {
  static Future<SpecificProductLikeResponseModel?> getLikesForAd({
    required String adId,
    required String uid,
  }) async {
    Utils.showLog("Get Likes For Ad API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse(
        "${Api.specificProductLikeApi}?adId=$adId",
      );

      var request = http.Request('GET', url);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Get Likes For Ad Response Code => ${response.statusCode}");
      Utils.showLog("Get Likes For Ad Response => $responseBody");

      if (response.statusCode == 200) {
        final specificProductLikeResponse = specificProductLikeResponseModelFromJson(responseBody);
        return specificProductLikeResponse;
      } else {
        return SpecificProductLikeResponseModel(
          message: response.reasonPhrase ?? "Something went wrong",
          total: 0,
          likes: [],
        );
      }
    } catch (e) {
      Utils.showLog("Get Likes For Ad Error => $e");
      return SpecificProductLikeResponseModel(
        message: "Exception: $e",
        total: 0,
        likes: [],
      );
    }
  }
}
