import 'dart:developer';

import 'package:firebase_auth/firebase_auth.dart';
import 'package:listify/utils/utils.dart';

class FirebaseAccessToken {
  static Future<String?> onGet() async {
    try {
      User? user = FirebaseAuth.instance.currentUser;

      log("user?.email  ${user?.email}");
      log("user?.uid  ${user?.uid}");

      bool? isExpired = await user?.getIdTokenResult().then((tokenResult) {
        DateTime expiryTime = tokenResult.expirationTime!;

        Utils.showLog("Firebase Token Expire Time => $expiryTime");

        return expiryTime.isBefore(DateTime.now());
      });

      Utils.showLog("Firebase Token Is Expire => $isExpired");

      final token = isExpired == true ? await user?.getIdToken(true) : await user?.getIdToken();

      Utils.showLog("Firebase Token => $token");
      return token;
    } catch (e) {
      Utils.showLog("Firebase Access Token Failed => $e");
      return null;
    }
  }
}

class FirebaseUserID {
  static Future<String?> getUID() async {
    try {
      User? user = FirebaseAuth.instance.currentUser;

      if (user != null) {
        log("Firebase User UID => ${user.uid}");
        return user.uid;
      } else {
        Utils.showLog("No Firebase user logged in.");
        return null;
      }
    } catch (e) {
      Utils.showLog("Firebase UID fetch failed => $e");
      return null;
    }
  }
}
