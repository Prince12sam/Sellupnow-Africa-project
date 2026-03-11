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
    String? productId,
    String? orderId,
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

      final normalizedProductId = (productId ?? '').trim();
      final normalizedOrderId = (orderId ?? '').trim();

      final payload = <String, dynamic>{
        // Legacy mobile keys.
        "revieweeId": revieweeId,
        "reviewText": reviewText,
        // Current backend keys.
        ApiParams.description: reviewText,
        "rating": rating,
      };

      if (normalizedProductId.isNotEmpty) {
        payload["product_id"] = normalizedProductId;
        payload["productId"] = normalizedProductId;
        payload[ApiParams.adId] = normalizedProductId;
      }

      if (normalizedOrderId.isNotEmpty) {
        payload["order_id"] = normalizedOrderId;
        payload[ApiParams.orderId] = normalizedOrderId;
      }

      var request = http.Request('POST', url);
      request.headers.addAll(headers);

      request.body = json.encode(payload);

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      Utils.showLog("Give Review Response Code => ${response.statusCode}");
      Utils.showLog("Give Review Response => $responseBody");

      if (response.statusCode >= 200 && response.statusCode < 300) {
        final model = giveReviewResponseModelFromJson(responseBody);
        Utils.showLog("Give Review Parsed Model => ${model.toJson()}");
        return model;
      } else {
        String message = response.reasonPhrase ?? 'Failed to submit review';

        try {
          final decoded = json.decode(responseBody);
          if (decoded is Map<String, dynamic>) {
            final dynamic errors = decoded['errors'];
            if (errors is Map<String, dynamic> && errors.isNotEmpty) {
              final firstError = errors.values.first;
              if (firstError is List && firstError.isNotEmpty) {
                message = firstError.first.toString();
              }
            } else if (decoded['message'] != null) {
              message = decoded['message'].toString();
            }
          }
        } catch (_) {}

        Utils.showLog("Give Review Failed => $message");
        return GiveReviewResponseModel(status: false, message: message);
      }
    } catch (e) {
      Utils.showLog("Give Review Error => $e");
      return GiveReviewResponseModel(
        status: false,
        message: 'Unable to submit review right now.',
      );
    }
  }
}
