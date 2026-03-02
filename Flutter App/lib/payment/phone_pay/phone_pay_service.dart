import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';

class PhonePeService {
  final String merchantId = "YOUR_MERCHANT_ID";
  final String saltKey = "YOUR_SALT_KEY";
  final String saltIndex = "1";
  final bool isProduction = false;

  String get baseUrl => isProduction ? "https://api.phonepe.com" : "https://api-preprod.phonepe.com";

  Future<void> initiatePayment({
    required String transactionId,
    required String amount,
    required String mobileNumber,
  }) async {
    final payload = {
      "merchantId": merchantId,
      "merchantTransactionId": transactionId,
      "merchantUserId": "USER123",
      "amount": int.parse(amount) * 100,
      "redirectUrl": "yourapp://phonepe-response",
      "callbackUrl": "https://your-backend.com/payment/callback",
      "mobileNumber": mobileNumber,
      "paymentInstrument": {"type": "PAY_PAGE"}
    };

    final payloadBase64 = base64Encode(utf8.encode(jsonEncode(payload)));

    final checksum = generateChecksum(payloadBase64);

    final headers = {
      "Content-Type": "application/json",
      "X-VERIFY": checksum,
    };

    final response = await http.post(
      Uri.parse("$baseUrl/apis/hermes/pg/v1/pay"),
      headers: headers,
      body: jsonEncode({"request": payloadBase64}),
    );

    final res = jsonDecode(response.body);
    if (res["success"] == true) {
      final paymentUrl = res["data"]["instrumentResponse"]["redirectInfo"]["url"];
      if (await canLaunch(paymentUrl)) {
        await launch(paymentUrl);
      }
    } else {
      throw Exception("PhonePe payment failed: ${res["message"]}");
    }
  }

  String generateChecksum(String payload) {
    return "CHECKSUM###$saltIndex";
  }
}
