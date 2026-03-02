import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/review_screen/model/get_review_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';

class ReviewApi {
  static Future<GetReviewResponseModel?> getReviews({
    required String userId,
    required int start,
    required int limit,
    required String uid,
  }) async {
    try {
      final token = await FirebaseAccessToken.onGet();
      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
        ApiParams.contentType: 'application/json',
      };

      var url = Uri.parse('${Api.getReviewApi}?start=$start&limit=$limit&userId=$userId');

      var request = http.Request('GET', url);
      request.headers.addAll(headers);

      log("Review API URL => $url");
      log("Review API Headers => $headers");

      http.StreamedResponse response = await request.send();

      if (response.statusCode == 200) {
        var resString = await response.stream.bytesToString();
        log("Review API Response => $resString");

        // 🔹 Parse JSON into model
        return GetReviewResponseModel.fromJson(json.decode(resString));
      } else {
        log("Review API Failed => ${response.reasonPhrase}");
        return null;
      }
    } catch (e) {
      log("Review API Exception => $e");
      return null;
    }
  }
}
