// import 'package:flutter/cupertino.dart';
// import 'package:get/get.dart';
// import 'package:listify/utils/database.dart';
// import 'package:listify/utils/utils.dart';
// import 'package:url_launcher/url_launcher.dart';
//
// class ContactUsScreenController extends GetxController {
//   @override
//   void onInit() {
//     email.text =
//         Database.settingApiResponseModel!.data!.supportEmail.toString();
//     Utils.showLog("email ..............${email.text}");
//     Utils.showLog("email ..............${Database.settingApiResponseModel!.data!.supportEmail.toString()}");
//
//     super.onInit();
//   }
//
//   int selectedIndex = -1;
//
//   void selectNumber(int index) {
//     selectedIndex = index;
//     update(); // Notify listeners
//   }
//
//   Future<void> openDialer(String rawNumber) async {
//     // નંબરમાંથી space/dash કાઢી દેતાં; + રહેવા દો
//     final cleaned = rawNumber.replaceAll(RegExp(r'[\s\-]'), '');
//
//     Utils.showLog("Cleaned::::::$cleaned");
//
//     if (cleaned.isEmpty) {
//       // તમારું error UI/Toast બતાવો
//       throw 'Empty phone number';
//     }
//
//     final uri = Uri(
//       scheme: 'tel',
//       path: cleaned, // e.g. +919876543210
//     );
//
//     // external app (Dialer) જ ખોલો
//     if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
//       throw 'Could not open dialer';
//     }
//   }
//
//   TextEditingController subject = TextEditingController();
//   TextEditingController description = TextEditingController();
//   TextEditingController email = TextEditingController();
// }
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';
import 'package:url_launcher/url_launcher.dart';

class ContactUsScreenController extends GetxController {
  @override
  void onInit() {
    email.text = Database.settingApiResponseModel!.data!.supportEmail.toString();
    Utils.showLog("email ..............${email.text}");
    super.onInit();
  }

  int selectedIndex = -1;

  void selectNumber(int index) {
    selectedIndex = index;
    update();
  }

  Future<void> openDialer(String rawNumber) async {
    final cleaned = rawNumber.replaceAll(RegExp(r'[\s\-]'), '');
    Utils.showLog("Cleaned::::::$cleaned");
    if (cleaned.isEmpty) throw 'Empty phone number';

    final uri = Uri(scheme: 'tel', path: cleaned);
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
      throw 'Could not open dialer';
    }
  }

  /// NEW: Open email compose with prefilled fields
  Future<void> openEmail({
    required String to,
    String? subject,
    String? body,
    List<String>? cc,
    List<String>? bcc,
  }) async {
    final trimmedTo = to.trim();
    if (trimmedTo.isEmpty) throw 'Empty email address';

    // mailto URI with queryParameters auto-encodes values
    final uri = Uri(
      scheme: 'mailto',
      path: trimmedTo,
      queryParameters: {
        if (subject != null) 'subject': subject,
        if (body != null) 'body': body,
        if (cc != null && cc.isNotEmpty) 'cc': cc.join(','),
        if (bcc != null && bcc.isNotEmpty) 'bcc': bcc.join(','),
      },
    );

    Utils.showLog('mailto:::: $uri');

    if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
      throw 'Could not open email app';
    }
  }

  TextEditingController subject = TextEditingController();
  TextEditingController description = TextEditingController();
  TextEditingController email = TextEditingController();
}
