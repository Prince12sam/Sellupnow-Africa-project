import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/ui/support_ticket_screen/model/support_ticket_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class SupportTicketApi {
  static int startPagination = 0;
  static const int limitPagination = 20;

  static Future<Map<String, String>> _headers() async {
    final token = await FirebaseAccessToken.onGet();
    return {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: "Bearer $token",
      ApiParams.authUid:
          '${Database.getUserProfileResponseModel?.user?.firebaseUid}',
      ApiParams.contentType: "application/json",
    };
  }

  static Future<SupportTicketListResponseModel?> fetchList() async {
    startPagination += 1;
    final query = Uri(queryParameters: {
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    }).query;
    final uri =
        Uri.parse(Api.supportTickets + (query.isNotEmpty ? query : ''));

    try {
      final response = await http.get(uri, headers: await _headers());
      Utils.showLog("Support Tickets => ${response.statusCode}");
      if (response.statusCode == 200) {
        return SupportTicketListResponseModel.fromJson(
            json.decode(response.body));
      }
    } catch (e) {
      Utils.showLog("Support Tickets Error => $e");
    }
    return null;
  }

  static Future<bool> createTicket({
    required String subject,
    required String message,
    String? issueType,
    String? email,
    String? phone,
    String? orderNumber,
  }) async {
    final uri = Uri.parse(Api.createSupportTicket);
    final body = json.encode({
      'subject': subject,
      'message': message,
      if (issueType != null && issueType.isNotEmpty) 'issue_type': issueType,
      if (email != null && email.isNotEmpty) 'email': email,
      if (phone != null && phone.isNotEmpty) 'phone': phone,
      if (orderNumber != null && orderNumber.isNotEmpty)
        'order_number': orderNumber,
    });

    try {
      final response =
          await http.post(uri, headers: await _headers(), body: body);
      Utils.showLog("Create Ticket => ${response.statusCode} ${response.body}");
      if (response.statusCode == 200) {
        return json.decode(response.body)['status'] == true;
      }
    } catch (e) {
      Utils.showLog("Create Ticket Error => $e");
    }
    return false;
  }

  static Future<SupportTicketDetailModel?> fetchDetail(int id) async {
    final uri = Uri.parse('${Api.supportTicketDetail}$id');

    try {
      final response = await http.get(uri, headers: await _headers());
      Utils.showLog("Ticket Detail => ${response.statusCode}");
      if (response.statusCode == 200) {
        return SupportTicketDetailModel.fromJson(json.decode(response.body));
      }
    } catch (e) {
      Utils.showLog("Ticket Detail Error => $e");
    }
    return null;
  }

  static Future<bool> sendReply(int id, String message) async {
    final uri = Uri.parse('${Api.replyTicket}$id');
    final body = json.encode({'message': message});

    try {
      final response =
          await http.post(uri, headers: await _headers(), body: body);
      Utils.showLog("Reply Ticket => ${response.statusCode} ${response.body}");
      if (response.statusCode == 200) {
        return json.decode(response.body)['status'] == true;
      }
    } catch (e) {
      Utils.showLog("Reply Ticket Error => $e");
    }
    return false;
  }
}
