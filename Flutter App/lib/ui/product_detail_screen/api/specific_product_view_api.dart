import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';
import 'package:listify/ui/product_detail_screen/model/specific_product_view_response_model.dart';

class SpecificProductViewApi {
  static Future<SpecificProductViewResponseModel?> getViewsForAd({
    required String adId,
    required String uid,
  }) async {
    Utils.showLog("Get Views For Ad API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse(
        "${Api.specificProductViewApi}?adId=$adId",
      );

      var request = http.Request('GET', url);
      request.headers.addAll(headers);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Get Views For Ad Response Code => ${response.statusCode}");
      Utils.showLog("Get Views For Ad Response => $responseBody");

      if (response.statusCode == 200) {
        final specificProductViewResponse = specificProductViewResponseModelFromJson(responseBody);
        return specificProductViewResponse;
      } else {
        return SpecificProductViewResponseModel(
          status: false,
          message: response.reasonPhrase ?? "Something went wrong",
          adView: [],
        );
      }
    } catch (e) {
      Utils.showLog("Get Views For Ad Error => $e");
      return SpecificProductViewResponseModel(
        status: false,
        message: "Exception: $e",
        adView: [],
      );
    }
  }
}
