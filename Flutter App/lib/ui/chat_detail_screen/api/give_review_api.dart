import 'package:http/http.dart' as http;
import 'package:listify/ui/chat_detail_screen/model/give_review_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';
import 'dart:convert';

class GiveReviewApi {
  static Future<GiveReviewResponseModel?> giveReview({
    required String revieweeId,
    required double rating,
    required String reviewText,
    required String uid,
  }) async {
    Utils.showLog("Give Review API Calling...");

    try {
      final token = await FirebaseAccessToken.onGet();

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      final url = Uri.parse(Api.giveReviewApi); // 👈 Define this in Api.dart

      Utils.showLog("Give Review uri => $url");

      var request = http.Request('POST', url);
      request.headers.addAll(headers);

      request.body = json.encode({
        "revieweeId": revieweeId,
        "rating": rating,
        "reviewText": reviewText,
      });

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Give Review Response Code => ${response.statusCode}");
      Utils.showLog("Give Review Response => $responseBody");

      if (response.statusCode == 200) {
        final model = giveReviewResponseModelFromJson(responseBody);
        Utils.showLog("Give Review Parsed Model => ${model.toJson()}");
        return model;
      } else {
        Utils.showLog("Give Review Failed => ${response.reasonPhrase}");
        return null;
      }
    } catch (e) {
      Utils.showLog("Give Review Error => $e");
      return null;
    }
  }
}
