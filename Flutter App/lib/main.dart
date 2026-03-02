// import 'dart:async';
// import 'dart:developer';
// import 'dart:ui'; // for PlatformDispatcher
// import 'package:firebase_core/firebase_core.dart';
// import 'package:firebase_crashlytics/firebase_crashlytics.dart';
// import 'package:firebase_messaging/firebase_messaging.dart';
// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:get_storage/get_storage.dart';
// import 'package:listify/localization/localizations_delegate.dart';
// import 'package:listify/routes/app_pages.dart';
// import 'package:listify/routes/app_routes.dart';
// import 'package:listify/services/notification_service.dart';
// import 'package:listify/services/permission_handler/permission_handler.dart';
// import 'package:listify/utils/database.dart';
// import 'package:mobile_device_identifier/mobile_device_identifier.dart';
// import 'localization/locale_constant.dart';
// import 'utils/utils.dart';
//
// void main() {
//   runZonedGuarded<Future<void>>(() async {
//     // Everything runs in the SAME zone as runApp
//     WidgetsFlutterBinding.ensureInitialized();
//
//     // (Optional) turn the zone warning into a hard error during dev
//     // BindingBase.debugZoneErrorsAreFatal = true;
//
//     await Firebase.initializeApp();
//
//     // Route framework errors to Crashlytics
//     FlutterError.onError = FirebaseCrashlytics.instance.recordFlutterFatalError;
//
//     // Route uncaught (non-Framework) async errors to Crashlytics
//     PlatformDispatcher.instance.onError = (error, stack) {
//       FirebaseCrashlytics.instance.recordError(error, stack, fatal: true);
//       return true;
//     };
//
//     await GetStorage.init();
//
//     final identity = (await MobileDeviceIdentifier().getDeviceId())!;
//     final fcmToken = await FirebaseMessaging.instance.getToken();
//
//     Utils.showLog("Device Id => $identity");
//     Utils.showLog("FCM Token => $fcmToken");
//
//     PermissionHandler.notificationPermissions();
//
//     FirebaseMessaging.onBackgroundMessage(NotificationServices.backgroundNotification);
//
//     if (fcmToken != null) {
//       await Database.init(identity, fcmToken);
//     }
//
//     runApp(const MyApp());
//   }, (error, stack) {
//     // Last-resort handler for this zone
//     FirebaseCrashlytics.instance.recordError(error, stack, fatal: true);
//   });
// }
//
// class MyApp extends StatefulWidget {
//   const MyApp({super.key});
//   @override
//   State<MyApp> createState() => _MyAppState();
// }
//
// class _MyAppState extends State<MyApp> {
//
//   @override
//   void didChangeDependencies() {
//     getLocale().then((locale) {
//       setState(() {
//         log("didChangeDependencies Preference Revoked ${locale.languageCode}");
//         log("didChangeDependencies GET LOCALE Revoked ${Get.locale?.languageCode}");
//         Get.updateLocale(locale);
//       });
//     });
//     super.didChangeDependencies();
//   }
//   @override
//   Widget build(BuildContext context) {
//     return GetMaterialApp(
//       title: 'Listify App',
//       debugShowCheckedModeBanner: false,
//       locale: const Locale("en"),
//       translations: AppLanguages(),
//       // initialRoute: Database.isLogin == true ? AppRoutes.bottomBar : AppRoutes.loginScreen,
//       initialRoute: AppRoutes.splashScreenView,
//       getPages: AppPages.list,
//       defaultTransition: Transition.fade,
//       transitionDuration: const Duration(milliseconds: 200),
//     );
//   }
// }

import 'dart:async';
import 'dart:developer';
import 'dart:ui';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/localization/localizations_delegate.dart';
import 'package:listify/routes/app_pages.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/services/notification_service.dart';
import 'package:listify/services/permission_handler/permission_handler.dart';
import 'package:listify/utils/database.dart';
import 'package:mobile_device_identifier/mobile_device_identifier.dart';

import 'localization/locale_constant.dart';
import 'ui/near_by_listing_screen/controller/map_controller.dart';
import 'utils/like_manager.dart';
import 'utils/utils.dart';

// ✅ Top-level background handler (required by FlutterFire)
Future<void> _firebaseBgHandler(RemoteMessage message) async {
  await NotificationServices.onShowBackgroundNotification(message);
}

void main() {
  runZonedGuarded<Future<void>>(() async {
    WidgetsFlutterBinding.ensureInitialized();

    await Firebase.initializeApp();

    // Crashlytics wiring
    FlutterError.onError = FirebaseCrashlytics.instance.recordFlutterFatalError;
    PlatformDispatcher.instance.onError = (error, stack) {
      FirebaseCrashlytics.instance.recordError(error, stack, fatal: true);
      return true;
    };

    await GetStorage.init();

    // (Optional) Any app‑specific stuff
    final identity = (await MobileDeviceIdentifier().getDeviceId())!;
    final fcmToken = await FirebaseMessaging.instance.getToken();
    Utils.showLog("Device Id => $identity");
    Utils.showLog("FCM Token => $fcmToken");

    // // (Optional) your own permission helper — keep if you need it
    // PermissionHandler.notificationPermissions();
    //
    // //map permission
    // await PermissionHandler().requestLocationAtAppStart(
    //   askBackground: false, // true only if you truly need background
    // );

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      // 1) Notification → wait
      await PermissionHandler.notificationPermissions();

      // (optional) થોડો ગેપ મદદગાર હોય છે
      await Future.delayed(const Duration(milliseconds: 250));

      // 2) Location → wait
      await PermissionHandler().requestLocationAtAppStart(
        askBackground: false, // true only if you truly need background
      );
    });

    // ✅ Register BG handler BEFORE any FCM usage
    FirebaseMessaging.onBackgroundMessage(_firebaseBgHandler);

    // ✅ Init notifications (plugin, channels, permissions)
    await NotificationServices.init();

    // ✅ Wire FCM listeners + initialMessage handling
    await NotificationServices.firebaseInit();

    if (fcmToken != null) {
      await Database.init(identity, fcmToken);
    }
    await Database.initSelectedLocation();
    Database.setSelectedLocationText;

    if (!Get.isRegistered<LikeManager>()) {
      Get.put(LikeManager(), permanent: true);
    }
    Get.put(MapController());
    runApp(const MyApp());
  }, (error, stack) {
    FirebaseCrashlytics.instance.recordError(error, stack, fatal: true);
  });
}

class MyApp extends StatefulWidget {
  const MyApp({super.key});
  @override
  State<MyApp> createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> {
  @override
  void didChangeDependencies() {
    getLocale().then((locale) {
      setState(() {
        log("didChangeDependencies Preference Revoked ${locale.languageCode}");
        log("didChangeDependencies GET LOCALE Revoked ${Get.locale?.languageCode}");
        Get.updateLocale(locale);
      });
    });
    super.didChangeDependencies();
  }

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: 'Sellupnow',
      debugShowCheckedModeBanner: false,
      locale: const Locale("en"),
      translations: AppLanguages(),
      navigatorObservers: [routeObserver],
      initialRoute: AppRoutes.splashScreenView, // your current choice
      getPages: AppPages.list,
      defaultTransition: Transition.fade,
      transitionDuration: const Duration(milliseconds: 200),
    );
  }
}
