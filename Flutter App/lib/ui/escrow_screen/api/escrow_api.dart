import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/escrow_screen/model/escrow_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';

class EscrowApi {
  static Future<EscrowOrdersResponse?> callOrdersApi({String tab = 'buyer', int page = 1}) async {
    final token = await FirebaseAccessToken.onGet();
    final uri = Uri.parse(
        '${Api.escrowOrders}?tab=$tab&page=$page&limit=20');
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: Database.authUid,
      ApiParams.contentType: 'application/json',
    };
    try {
      final response = await http.get(uri, headers: headers);
      log('Escrow Orders API STATUS: ${response.statusCode}');
      if (response.statusCode == 200) {
        return EscrowOrdersResponse.fromJson(json.decode(response.body));
      }
    } catch (e) {
      log('Escrow Orders API Error: $e');
    }
    return null;
  }

  static Future<EscrowDetailResponse?> callDetailApi({required int id}) async {
    final token = await FirebaseAccessToken.onGet();
    final uri = Uri.parse('${Api.escrowOrderDetail}?id=$id');
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: Database.authUid,
      ApiParams.contentType: 'application/json',
    };
    try {
      final response = await http.get(uri, headers: headers);
      log('Escrow Detail API STATUS: ${response.statusCode}');
      if (response.statusCode == 200) {
        return EscrowDetailResponse.fromJson(json.decode(response.body));
      }
    } catch (e) {
      log('Escrow Detail API Error: $e');
    }
    return null;
  }

  static Future<EscrowBreakdownResponse?> callBreakdownApi({required String listingId}) async {
    final token = await FirebaseAccessToken.onGet();
    final uri = Uri.parse('${Api.escrowBreakdown}?listing_id=$listingId');
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: Database.authUid,
      ApiParams.contentType: 'application/json',
    };
    try {
      final response = await http.get(uri, headers: headers);
      log('Escrow Breakdown API STATUS: ${response.statusCode}');
      if (response.statusCode == 200) {
        return EscrowBreakdownResponse.fromJson(json.decode(response.body));
      }
    } catch (e) {
      log('Escrow Breakdown API Error: $e');
    }
    return null;
  }

  static Future<EscrowInitiateResponse?> callInitiateApi({required String listingId}) async {
    final token = await FirebaseAccessToken.onGet();
    final uri = Uri.parse(Api.escrowInitiate);
    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: Database.authUid,
      ApiParams.contentType: 'application/json',
    };
    try {
      final response = await http.post(
        uri,
        headers: headers,
        body: json.encode({'listing_id': listingId}),
      );
      log('Escrow Initiate API STATUS: ${response.statusCode}');
      if (response.statusCode == 200 || response.statusCode == 201) {
        return EscrowInitiateResponse.fromJson(json.decode(response.body));
      } else {
        final body = json.decode(response.body);
        return EscrowInitiateResponse(
          status: false,
          message: body['message'] ?? 'Failed to initiate escrow',
        );
      }
    } catch (e) {
      log('Escrow Initiate API Error: $e');
    }
    return null;
  }
}
