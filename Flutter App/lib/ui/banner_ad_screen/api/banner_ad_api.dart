import 'dart:convert';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:http/http.dart' as http;
import 'package:listify/ui/banner_ad_screen/model/banner_ad_response_model.dart';
import 'package:listify/utils/api.dart';

class BannerAdApi {
  static Future<BannerAdListResponse?> callListApi({int page = 1}) async {
    try {
      final user = FirebaseAuth.instance.currentUser;
      final token = await user?.getIdToken();
      final response = await http.get(
        Uri.parse('${Api.bannerAdList}?page=$page&limit=20'),
        headers: {
          'key': Api.secretKey,
          'Authorization': 'Bearer $token',
          'x-meta-auth-id': user?.uid ?? '',
          'Content-Type': 'application/json',
        },
      );
      if (response.statusCode == 200) {
        return BannerAdListResponse.fromJson(jsonDecode(response.body));
      }
    } catch (e) {
      // ignore
    }
    return null;
  }

  static Future<Map<String, dynamic>> callSubmitApi({
    required String title,
    required String requestedSlot,
    required String redirectUrl,
    required String bannerImagePath,
  }) async {
    try {
      final user = FirebaseAuth.instance.currentUser;
      final token = await user?.getIdToken();
      final request = http.MultipartRequest('POST', Uri.parse(Api.bannerAdSubmit));
      request.headers.addAll({
        'key': Api.secretKey,
        'Authorization': 'Bearer $token',
        'x-meta-auth-id': user?.uid ?? '',
      });
      request.fields.addAll({
        'title': title,
        'requested_slot': requestedSlot,
        'redirect_url': redirectUrl,
      });
      request.files.add(await http.MultipartFile.fromPath('image', bannerImagePath));

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();
      Map<String, dynamic>? decoded;
      try {
        decoded = jsonDecode(responseBody) as Map<String, dynamic>;
      } catch (_) {
        decoded = null;
      }

      if (response.statusCode == 200 || response.statusCode == 201) {
        return decoded ?? {'status': true};
      }

      final errors = decoded?['errors'];
      final message = decoded?['message']?.toString() ?? 'Request failed (${response.statusCode})';
      if (errors is Map && errors.isNotEmpty) {
        final firstError = errors.values.first;
        if (firstError is List && firstError.isNotEmpty) {
          return {'status': false, 'message': firstError.first.toString()};
        }
      }

      return {'status': false, 'message': message};
    } catch (e) {
      return {'status': false, 'message': e.toString()};
    }
  }
}
