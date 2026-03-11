// import 'dart:developer';
//
// import 'package:country_code_picker/country_code_picker.dart';
// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:get_storage/get_storage.dart';
// import 'package:google_sign_in/google_sign_in.dart';
// import 'package:listify/routes/app_routes.dart';
// import 'package:listify/socket/socket_service.dart';
// import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
// import 'package:listify/ui/profile_screen_view/model/setting_api_response_model.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/utils.dart';
//
// class Database {
//   static final localStorage = GetStorage();
//   static const String stripeUrl = "https://api.stripe.com/v1/payment_intents";
//
//   static GetUserProfileResponseModel? getUserProfileResponseModel;
//   static SettingApiResponseModel? settingApiResponseModel;
//
//   static Future<void> init(String identity, String fcmToken) async {
//     Utils.showLog("Local Database Initialize....");
//     Utils.showLog("fcmToken $fcmToken");
//     Utils.showLog("identity $identity");
//
//     onSetFcmToken(fcmToken);
//     onSetIdentity(identity);
//
//     Utils.showLog("Stored fcmToken: $fcmToken");
//     Utils.showLog("Stored identity: $identity");
//
//     Utils.showLog("Is New User => $isNewUser");
//
//     if (isNewUser == false) {
//       // fetchLoginUserProfileModel = await FetchLoginUserProfileApi.callApi(loginUserId: loginUserFirebaseId);
//     }
//   }
//
//   // >>>>> >>>>> Get Language Database <<<<< <<<<<
//
//   static String get selectedLanguage => localStorage.read("language") ?? Constant.languageEn;
//   static String get languageCountryCode => localStorage.read("languageCountryCode") ?? Constant.countryCodeEn;
//
//   // >>>>> >>>>> Get Login Database <<<<< <<<<<
//
//   static String get fcmToken => localStorage.read("fcmToken") ?? "";
//   static String get identity => localStorage.read("identity") ?? "";
//   static String get selectedCountryCode => localStorage.read("countryCode") ?? "IN";
//   static int get loginType => localStorage.read("loginType") ?? 0;
//   static String get loginUserGender => localStorage.read("loginUserGender") ?? "Male";
//   static String get loginUserName => localStorage.read("loginUserName") ?? "";
//   static String get loginUserProfilePic => localStorage.read("loginUserProfilePic") ?? "";
//   static String get loginUserEmail => localStorage.read("loginUserEmail") ?? "";
//   static String get loginUserFirebaseId => localStorage.read("loginUserFirebaseId") ?? "";
//   static String get loginUserId => localStorage.read("loginUserId") ?? "";
//   static String get loginUserPhoneNumber => localStorage.read("loginUserPhoneNumber") ?? "";
//   static String get loginUserNickName => localStorage.read("loginUserNickName") ?? "";
//
//   ///
//   static bool get userExist => localStorage.read("userExist") ?? false;
//
//   static bool get isSeenOnBoarding => localStorage.read("isSeenOnBoarding") ?? false;
//   static bool get isNewUser => localStorage.read("isNewUser") ?? true;
//   static bool get isLogin => localStorage.read("isLogin") ?? false;
//   static bool get isFillProfile => localStorage.read("isFillProfile") ?? false;
//
//   static String get categoryId => localStorage.read("categoryId") ?? "";
//   static String get currencySymbol => localStorage.read("currencySymbol") ?? "";
//   static bool get isVerify => localStorage.read("isVerify") ?? false;
//   static String get uniqueId => localStorage.read("uniqueId") ?? "";
//   static String get verifyTime => localStorage.read("verifyTime") ?? "";
//
//   /// localization
//   static int get languageIndex => localStorage.read("languageIndex") ?? 3;
//
//   static List<String> get searchData {
//     final data = localStorage.read("searchData");
//     if (data is List) {
//       return data.cast<String>();
//     }
//     return [];
//   }
//   // >>>>> >>>>> Video Call <<<<< <<<<<
//
//   // >>>>> >>>>> Set Language Database <<<<< <<<<<
//
//   static onSetSelectedLanguage(String language) async => await localStorage.write("language", language);
//   static onSetSelectedLanguageCountryCode(String languageCountryCode) async => await localStorage.write("languageCountryCode", languageCountryCode);
//
//   // >>>>> >>>>> Notification Database <<<<< <<<<<
//
//   static bool get isShowNotification => localStorage.read("isShowNotification") ?? true;
//
//   static onSetNotification(bool isShowNotification) async => localStorage.write("isShowNotification", isShowNotification);
//
//   // >>>>> >>>>> Set Login Database <<<<< <<<<<
//
//   static onSetFcmToken(String fcmToken) async => await localStorage.write("fcmToken", fcmToken);
//   static onSetIdentity(String identity) async => await localStorage.write("identity", identity);
//   static onSetSelectedCountryCode(String countryCode) async => await localStorage.write("countryCode", countryCode);
//   static onSetLoginType(int loginType) async => localStorage.write("loginType", loginType);
//   static onSetLoginUserGender(String loginUserGender) async => localStorage.write("loginUserGender", loginUserGender);
//   static onSetLoginUserName(String loginUserName) async => localStorage.write("loginUserName", loginUserName);
//   static onSetLoginUserProfilePic(String loginUserProfilePic) async => localStorage.write("loginUserProfilePic", loginUserProfilePic);
//   static onSetLoginUserEmail(String loginUserEmail) async => localStorage.write("loginUserEmail", loginUserEmail);
//   static onSetLoginUserFirebaseId(String loginUserFirebaseId) async => localStorage.write("loginUserFirebaseId", loginUserFirebaseId);
//   static onSetLoginUserId(String loginUserId) async => localStorage.write("loginUserId", loginUserId);
//   static onSetLoginUserPhoneNumber(String loginUserPhoneNumber) async => localStorage.write("loginUserPhoneNumber", loginUserPhoneNumber);
//   static onSetLoginUserNickName(String loginUserNickName) async => localStorage.write("loginUserNickName", loginUserNickName);
//
//   ///
//   static onSetUserExist(bool userExist) async => localStorage.write("userExist", userExist);
//   static onSetUserVerify(bool isVerify) async => localStorage.write("isVerify", isVerify);
//   static onSetUniqueId(String uniqueId) async => localStorage.write("uniqueId", uniqueId);
//   static onSetVerifyTime(String verifyTime) async => localStorage.write("verifyTime", verifyTime);
//
//   static onSetSeenOnboarding(bool isSeenOnBoarding) async => await localStorage.write("isSeenOnBoarding", isSeenOnBoarding);
//   static onSetIsNewUser(bool isNewUser) async => await localStorage.write("isNewUser", isNewUser);
//   static onSetIsLogin(bool isLogin) async => await localStorage.write("isLogin", isLogin);
//   static onSetFillProfile(bool isFillProfile) async => await localStorage.write("isFillProfile", isFillProfile);
//
//   static onSetLanguageIndex(int languageIndex) async => localStorage.write("languageIndex", languageIndex);
//
//   static Future<void> onSetSearchDataStore(List<String> searchData) async => await localStorage.write("searchData", searchData);
//
//   static onSetCategoryId(String categoryId) async => await localStorage.write("categoryId", categoryId);
//
//   static onSetCurrencySymbol(String currencySymbol) async => await localStorage.write("currencySymbol", currencySymbol);
//
//   static String? dialCode;
//   static String? countryCode;
//   static getDialCode() {
//     CountryCode getCountryDialCode(String countryCode) {
//       return CountryCode.fromCountryCode(countryCode);
//     }
//
//     CountryCode country = getCountryDialCode(Database.selectedCountryCode);
//     log("country.Dial code :: ${country.dialCode}");
//
//     dialCode = country.dialCode;
//     log("Dial code :: $dialCode");
//   }
//
//   // static Future<void> onLogOut() async {
//   //   final identityDevice = identity;
//   //   final fcmTokenFirebase = fcmToken;
//   //
//   //   if (loginType == 1) {
//   //     Utils.showLog("Google Logout Success");
//   //     // await GoogleSignIn().signOut();
//   //   }
//   //
//   //   localStorage.erase();
//   //
//   //   log("logout app language $selectedLanguage");
//   //
//   //   onSetFcmToken(fcmTokenFirebase);
//   //   onSetIdentity(identityDevice);
//   //   // SocketService.socketDisConnect();
//   //
//   //   Database.onSetLanguageIndex(3);
//   //   Database.onSetSelectedLanguage(Constant.languageEn);
//   //   Database.onSetSelectedLanguageCountryCode(Constant.countryCodeEn);
//   //   Database.onSetSeenOnboarding(true);
//   //   Get.offAllNamed(AppRoutes.homeScreen);
//   //
//   //   // Update the UI
//   //   Get.updateLocale(Locale(Constant.languageEn, Constant.countryCodeEn));
//   // }
//
//   static Future<void> onLogOut() async {
//     final identityDevice = identity;
//     final fcmTokenFirebase = fcmToken;
//
//     if (loginType == 1) {
//       Utils.showLog("Google Logout Success");
//       await GoogleSignIn().signOut();
//     }
//
//     localStorage.erase();
//
//     log("logout app language $selectedLanguage");
//
//     onSetFcmToken(fcmTokenFirebase);
//     onSetIdentity(identityDevice);
//     SocketService.socketDisConnect();
//
//     // Database.onSetLanguageIndex(3);
//     Database.onSetSelectedLanguage(Constant.languageEn);
//     Database.onSetSelectedLanguageCountryCode(Constant.countryCodeEn);
//     Database.onSetSeenOnboarding(true);
//     Get.offAllNamed(AppRoutes.loginScreen);
//
//     // Update the UI
//     Get.updateLocale(Locale(Constant.languageEn, Constant.countryCodeEn));
//   }
// }
//
// // {
// // "status": true,
// // "message": "A new user has registered an account.",
// // "signUp": true,
// // "user": {
// // "_id": "6889e70b6c1207720de86289",
// // "loginType": 2,
// // "name": "",
// // "profileImage": "uploads\\user\\1753868042858-8005.png",
// // "fcmToken": "fQdfS2pEQpuybNKj1UUme2:APA91bGQkMc5ivZdknnI34Otohv6zSgZA-WspCpOVFzHUpUDQMp2jdCfxsXw4rgM5Yr-mDTynTYJ0kq64No7vdTZeHfvhU8QWxeiEr9psRkonPfMd3IQLeM"
// // }
// // }
import 'dart:developer';
import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get_storage/get_storage.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/socket/socket_service.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/ui/profile_screen_view/model/setting_api_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/like_manager.dart';
import 'package:listify/utils/utils.dart';

class Database {
  static final localStorage = GetStorage();
  static const String stripeUrl = "https://api.stripe.com/v1/payment_intents";

  static GetUserProfileResponseModel? getUserProfileResponseModel;
  static SettingApiResponseModel? settingApiResponseModel;

  // ----------------------------
  // 🔁 Selected Location (reactive + persisted)
  // ----------------------------

  // ✅ small helper: anything -> double?
  static double _toDouble(dynamic v, [double fallback = MapController.kDefaultRadiusKm]) {
    if (v == null) return fallback;
    if (v is num) return v.toDouble();
    if (v is String) return double.tryParse(v.trim()) ?? fallback;
    return fallback;
  }
  static final RxMap<String, dynamic> selectedLocation = <String, dynamic>{}.obs;
  static final RxBool hasSelectedLocation = false.obs;

  static const String _kSelectedLocation = 'selectedLocation';
  static const String _kHasSelected = 'hasSelectedLocation';

  /// call once on app start (after GetStorage.init)
  static Future<void> initSelectedLocation() async {
    final saved = localStorage.read<Map<String, dynamic>>(_kSelectedLocation);
    final savedHas = localStorage.read<bool>(_kHasSelected) ?? false;
    if (savedHas && (saved != null && saved.isNotEmpty)) {
      selectedLocation.assignAll(saved);
      hasSelectedLocation.value = true;
    } else {
      selectedLocation.clear();
      hasSelectedLocation.value = false;
    }
  }

  /// normalize different arg keys → unified keys
  static Map<String, dynamic> _normalize(Map<String, dynamic> raw) {
    final map = <String, dynamic>{
      'selectedCity'   : (raw['homeSelectedCity'] ?? raw['selectedCity'] ?? '').toString().trim(),
      'selectedState'  : (raw['homeSelectedState'] ?? raw['selectedState'] ?? '').toString().trim(),
      'selectedCountry': (raw['homeSelectedCountry'] ?? raw['selectedCountry'] ?? '').toString().trim(),
      'fullAddress'    : (raw['fullAddress'] ?? '').toString().trim(),
      'latitude'       : raw['latitude'],
      'longitude'      : raw['longitude'],
      'range'          : _toDouble(raw['range']),
      'ne_lat'         : raw['ne_lat'],
      'ne_lng'         : raw['ne_lng'],
      'sw_lat'         : raw['sw_lat'],
      'sw_lng'         : raw['sw_lng'],
    };
    map.removeWhere((k, v) => v == null);
    return map;
  }

  /// ✅ public API: set + persist
  static void setSelectedLocation(Map<String, dynamic> data) {
    final normalized = _normalize(data);
    selectedLocation.assignAll(normalized);
    hasSelectedLocation.value = true;

    localStorage.write(_kSelectedLocation, Map<String, dynamic>.from(selectedLocation));
    localStorage.write(_kHasSelected, true);

    Utils.showLog("Selected location saved: $selectedLocation");
  }

  /// ✅ Use this to persist the map selection (lat/lng + address + range + bounds)
  static void setSelectedLocationFromMap({
    required double latitude,
    required double longitude,
    String? fullAddress,
    String? city,
    String? state,
    String? country,
    required double rangeKm,
    double? neLat,
    double? neLng,
    double? swLat,
    double? swLng,
  }) {
    selectedLocation.assignAll({
      'latitude'       : latitude,
      'longitude'      : longitude,
      'fullAddress'    : (fullAddress ?? '').trim(),
      'selectedCity'   : (city ?? '').trim(),
      'selectedState'  : (state ?? '').trim(),
      'selectedCountry': (country ?? '').trim(),
      'range'          : _toDouble(rangeKm),
      'ne_lat'         : neLat,
      'ne_lng'         : neLng,
      'sw_lat'         : swLat,
      'sw_lng'         : swLng,
    });
    hasSelectedLocation.value = true;
    localStorage.write(_kSelectedLocation, Map<String, dynamic>.from(selectedLocation));
    localStorage.write(_kHasSelected, true);
  }


  /// (optional old API) – keep but now also accepts lat/lng if you pass them
  static void setSelectedLocationText(
      String text,
      String? city,
      String? country,
      dynamic range,
      String? state, {
        double? latitude,
        double? longitude,
        double? neLat,
        double? neLng,
        double? swLat,
        double? swLng,
      }) {
    setSelectedLocationFromMap(
      latitude      : (latitude ?? 0), // prefer passing real lat/lng
      longitude     : (longitude ?? 0),
      fullAddress   : text,
      city          : city,
      state         : state,
      country       : country,
      rangeKm       : _toDouble(range),
      neLat         : neLat,
      neLng         : neLng,
      swLat         : swLat,
      swLng         : swLng,
    );
  }

  /// ✅ new helper → direct text save (without city/state breakdown)
  static String selectedLocationText() {
    final ga = (selectedLocation['fullAddress'] as String?)?.trim() ?? '';
    if (ga.isNotEmpty) return ga;
    final gc = (selectedLocation['selectedCity'] as String?)?.trim() ?? '';
    final gs = (selectedLocation['selectedState'] as String?)?.trim() ?? '';
    final gcn= (selectedLocation['selectedCountry'] as String?)?.trim() ?? '';
    return [gc, gs, gcn].where((e) => e.isNotEmpty).join(', ');
  }

  /// (optional) back to GPS-only
  static void clearSelectedLocation() {
    selectedLocation.clear();
    hasSelectedLocation.value = false;

    localStorage.remove(_kSelectedLocation);
    localStorage.write(_kHasSelected, false);

    Utils.showLog("Selected location cleared → Using GPS only.");
  }

  /// helper for UI text (fullAddress > city,state,country)
  // static String selectedLocationText() {
  //   final ga = (selectedLocation['fullAddress'] as String?)?.trim() ?? '';
  //   if (ga.isNotEmpty) return ga;
  //
  //   final gc = (selectedLocation['selectedCity'] as String?)?.trim() ?? (selectedLocation['city'] as String?)?.trim() ?? '';
  //   final gs = (selectedLocation['selectedState'] as String?)?.trim() ?? (selectedLocation['state'] as String?)?.trim() ?? '';
  //   final gcn = (selectedLocation['selectedCountry'] as String?)?.trim() ?? (selectedLocation['country'] as String?)?.trim() ?? '';
  //   return [gc, gs, gcn].where((e) => e.isNotEmpty).join(', ');
  // }

  // ----------------------------
  // EXISTING DATABASE CODE
  // ----------------------------

  static Future<void> init(String identity, String fcmToken) async {
    Utils.showLog("Local Database Initialize....");
    Utils.showLog("fcmToken $fcmToken");
    Utils.showLog("identity $identity");

    // 🔸 make sure we load persisted location at app start
    await initSelectedLocation();

    onSetFcmToken(fcmToken);
    onSetIdentity(identity);

    Utils.showLog("Stored fcmToken: $fcmToken");
    Utils.showLog("Stored identity: $identity");

    Utils.showLog("Is New User => $isNewUser");

    if (isNewUser == false) {
      // fetch user profile if needed
    }
  }

  // >>>>> language getters ...
  static String get selectedLanguage => localStorage.read("language") ?? Constant.languageEn;
  static String get languageCountryCode => localStorage.read("languageCountryCode") ?? Constant.countryCodeEn;

  // >>>>> login getters ...
  static String get fcmToken => localStorage.read("fcmToken") ?? "";
  static String get identity => localStorage.read("identity") ?? "";
  static String get selectedCountryCode => localStorage.read("countryCode") ?? "IN";
  static int get loginType => localStorage.read("loginType") ?? 0;
  static String get loginUserGender => localStorage.read("loginUserGender") ?? "Male";
  static String get loginUserName => localStorage.read("loginUserName") ?? "";
  static String get loginUserProfilePic => localStorage.read("loginUserProfilePic") ?? "";
  static String get loginUserEmail => localStorage.read("loginUserEmail") ?? "";
  static String get loginUserFirebaseId => localStorage.read("loginUserFirebaseId") ?? "";
  static String get loginUserId => localStorage.read("loginUserId") ?? "";
  static String get loginUserPhoneNumber => localStorage.read("loginUserPhoneNumber") ?? "";
  static String get loginUserNickName => localStorage.read("loginUserNickName") ?? "";
  static bool get loginUserVerified => localStorage.read("loginUserVerified") ?? false;
  static String get authUid {
    final profileUid = getUserProfileResponseModel?.user?.firebaseUid?.trim() ?? "";
    if (profileUid.isNotEmpty && profileUid.toLowerCase() != "null") {
      return profileUid;
    }

    final storedUid = loginUserFirebaseId.trim();
    if (storedUid.isNotEmpty && storedUid.toLowerCase() != "null") {
      return storedUid;
    }

    return "";
  }

  static bool get userExist => localStorage.read("userExist") ?? false;
  static bool get isSeenOnBoarding => localStorage.read("isSeenOnBoarding") ?? false;
  static bool get isNewUser => localStorage.read("isNewUser") ?? true;
  static bool get isLogin => localStorage.read("isLogin") ?? false;
  static bool get isFillProfile => localStorage.read("isFillProfile") ?? false;

  static String get categoryId => localStorage.read("categoryId") ?? "";
  static String get currencySymbol => localStorage.read("currencySymbol") ?? "";
  static bool get isVerify => localStorage.read("isVerify") ?? false;
  static String get uniqueId => localStorage.read("uniqueId") ?? "";
  static String get verifyTime => localStorage.read("verifyTime") ?? "";

  static bool get demoUser => localStorage.read("demoUser") ?? false;
  static bool get hasPendingVerification => isVerify == true && uniqueId.trim().isNotEmpty;

  static int get languageIndex => localStorage.read("languageIndex") ?? 3;

  static List<String> get searchData {
    final data = localStorage.read("searchData");
    if (data is List) {
      return data.cast<String>();
    }
    return [];
  }

  // >>>>> setters ...
  static onSetSelectedLanguage(String language) async => await localStorage.write("language", language);
  static onSetSelectedLanguageCountryCode(String languageCountryCode) async => await localStorage.write("languageCountryCode", languageCountryCode);

  static bool get isShowNotification => localStorage.read("isShowNotification") ?? true;
  static onSetNotification(bool isShowNotification) async => localStorage.write("isShowNotification", isShowNotification);

  static onSetFcmToken(String fcmToken) async => await localStorage.write("fcmToken", fcmToken);
  static onSetIdentity(String identity) async => await localStorage.write("identity", identity);
  static onSetSelectedCountryCode(String countryCode) async => await localStorage.write("countryCode", countryCode);
  static onSetLoginType(int loginType) async => localStorage.write("loginType", loginType);
  static onSetLoginUserGender(String loginUserGender) async => localStorage.write("loginUserGender", loginUserGender);
  static onSetLoginUserName(String loginUserName) async => localStorage.write("loginUserName", loginUserName);
  static onSetLoginUserProfilePic(String loginUserProfilePic) async => localStorage.write("loginUserProfilePic", loginUserProfilePic);
  static onSetLoginUserEmail(String loginUserEmail) async => localStorage.write("loginUserEmail", loginUserEmail);
  static onSetLoginUserFirebaseId(String loginUserFirebaseId) async => localStorage.write("loginUserFirebaseId", loginUserFirebaseId);
  static onSetLoginUserId(String loginUserId) async => localStorage.write("loginUserId", loginUserId);
  static onSetLoginUserPhoneNumber(String loginUserPhoneNumber) async => localStorage.write("loginUserPhoneNumber", loginUserPhoneNumber);
  static onSetLoginUserNickName(String loginUserNickName) async => localStorage.write("loginUserNickName", loginUserNickName);
  static onSetLoginUserVerified(bool loginUserVerified) async => localStorage.write("loginUserVerified", loginUserVerified);

  static void syncIdentityVerificationState({
    required bool isVerified,
    int? verificationStatus,
    String? verificationId,
    String? verificationSubmittedAt,
  }) {
    final normalizedVerificationId = verificationId?.trim() ?? "";
    final normalizedVerificationTime = verificationSubmittedAt?.trim() ?? "";
    final hasPendingVerification = verificationStatus == 0 && normalizedVerificationId.isNotEmpty;

    onSetLoginUserVerified(isVerified);

    if (isVerified) {
      onSetUserVerify(false);
      onSetUniqueId('');
      onSetVerifyTime('');
      return;
    }

    if (hasPendingVerification) {
      onSetUserVerify(true);
      onSetUniqueId(normalizedVerificationId);
      onSetVerifyTime(normalizedVerificationTime);
      return;
    }

    onSetUserVerify(false);
    onSetUniqueId('');
    onSetVerifyTime('');
  }

  static onSetUserExist(bool userExist) async => localStorage.write("userExist", userExist);
  static onSetUserVerify(bool isVerify) async => localStorage.write("isVerify", isVerify);
  static onSetUniqueId(String uniqueId) async => localStorage.write("uniqueId", uniqueId);
  static onSetVerifyTime(String verifyTime) async => localStorage.write("verifyTime", verifyTime);

  static onSetSeenOnboarding(bool isSeenOnBoarding) async => await localStorage.write("isSeenOnBoarding", isSeenOnBoarding);
  static onSetIsNewUser(bool isNewUser) async => await localStorage.write("isNewUser", isNewUser);
  static onSetIsLogin(bool isLogin) async => await localStorage.write("isLogin", isLogin);
  static onSetFillProfile(bool isFillProfile) async => await localStorage.write("isFillProfile", isFillProfile);

  static onSetLanguageIndex(int languageIndex) async => localStorage.write("languageIndex", languageIndex);
  static Future<void> onSetSearchDataStore(List<String> searchData) async => await localStorage.write("searchData", searchData);
  static onSetCategoryId(String categoryId) async => await localStorage.write("categoryId", categoryId);
  static onSetCurrencySymbol(String currencySymbol) async => await localStorage.write("currencySymbol", currencySymbol);

  static onSetDemoUser(bool demoUser) async => localStorage.write("demoUser", demoUser);

  static String? dialCode;
  static String? countryCode;
  static getDialCode() {
    CountryCode getCountryDialCode(String countryCode) {
      return CountryCode.fromCountryCode(countryCode);
    }

    CountryCode country = getCountryDialCode(Database.selectedCountryCode);
    log("country.Dial code :: ${country.dialCode}");

    dialCode = country.dialCode;
    log("Dial code :: $dialCode");
  }


  // static Future<void> onLogOut() async {
  //   final identityDevice = identity;
  //   final fcmTokenFirebase = fcmToken;
  //
  //   try {
  //     // 🔹 Google logout
  //     if (loginType == 1) {
  //       Utils.showLog("Google Logout Success");
  //       await GoogleSignIn().signOut();
  //     }
  //     clearSelectedLocation();
  //     // 🔹 Erase local data
  //     localStorage.erase();
  //
  //     // 🔹 Disconnect sockets before deleting controller
  //     if (Get.isRegistered<SocketService>()) {
  //       SocketService.socketDisConnect();
  //       Get.delete<SocketService>(force: true);
  //     }
  //
  //     // 🔹 Keep LikeManager alive for a few ms to let UI dispose
  //     Future.delayed(const Duration(milliseconds: 300), () {
  //       if (Get.isRegistered<LikeManager>()) {
  //         Get.delete<LikeManager>(force: true);
  //       }
  //     });
  //
  //     // 🔹 Reset persistent values
  //     onSetFcmToken(fcmTokenFirebase);
  //     onSetIdentity(identityDevice);
  //     Database.onSetSelectedLanguage(Constant.languageEn);
  //     Database.onSetSelectedLanguageCountryCode(Constant.countryCodeEn);
  //     Database.onSetSeenOnboarding(true);
  //
  //     // 🔹 Navigate to login screen first (this disposes old widgets)
  //     Get.offAllNamed(AppRoutes.loginScreen);
  //
  //     // 🔹 Update app locale
  //     Get.updateLocale(Locale(Constant.languageEn, Constant.countryCodeEn));
  //
  //     Utils.showLog("✅ Logout complete, all session data cleared");
  //   } catch (e, st) {
  //     Utils.showLog("❌ Logout failed: $e\n$st");
  //   }
  // }

  static Future<void> onLogOut() async {
    // Database.onSetDemoUser(false);
    final identityDevice = identity;
    final fcmTokenFirebase = fcmToken;

    try {
      // (1) Sign out of Firebase Auth (all login types)
      try {
        await FirebaseAuth.instance.signOut();
        Utils.showLog("Firebase Auth signOut success");
      } catch (e) {
        Utils.showLog("Firebase Auth signOut error: $e");
      }

      // (1b) Google logout
      if (loginType == 2) {
        try {
          await GoogleSignIn().signOut();
          Utils.showLog("Google Logout Success");
        } catch (_) {}
      }

      // (2) 🔒 Location (storage + memory) સંપૂર્ણ રીતે clear કરો
      //    - storage keys remove + Rx state reset
      clearSelectedLocation();

      // (3) 🔌 Disconnect sockets પહેલા
      if (Get.isRegistered<SocketService>()) {
        SocketService.socketDisConnect();
        Get.delete<SocketService>(force: true);
      }

      // (4) MapController પણ dispose કરો; નહિ તો જૂનો state ફરીથી રહી જાય
      if (Get.isRegistered<MapController>()) {
        Get.delete<MapController>(force: true);
      }

      // (5) LikeManager થોડું મોડું dispose
      Future.delayed(const Duration(milliseconds: 300), () {
        if (Get.isRegistered<LikeManager>()) {
          Get.delete<LikeManager>(force: true);
        }
      });

      // (6) 🔥 Local storage wipe — PLEASE await this!
      await localStorage.erase();

      // (7) Reset persistent values you want to keep after erase
      await onSetFcmToken(fcmTokenFirebase);
      await onSetIdentity(identityDevice);
      await onSetSelectedLanguage(Constant.languageEn);
      await onSetSelectedLanguageCountryCode(Constant.countryCodeEn);
      await onSetSeenOnboarding(true);

      // (8) Navigate
      Get.offAllNamed(AppRoutes.loginScreen);

      // (9) Update locale
      Get.updateLocale(Locale(Constant.languageEn, Constant.countryCodeEn));

      Utils.showLog("✅ Logout complete, all session data & controllers cleared");
    } catch (e, st) {
      Utils.showLog("❌ Logout failed: $e\n$st");
    }
  }


  // >>>>> >>>>> Network Image Database <<<<< <<<<<

  static String? networkImage(String image) => localStorage.read(image);

  static onSetNetworkImage(String image) async => localStorage.write(image, image);


}

