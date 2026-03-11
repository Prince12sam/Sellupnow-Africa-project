import 'dart:math' hide log;
import 'dart:developer';

import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart'; // for VoidCallback
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;

import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/splash_screen/controller/splash_screen_controller.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/global_variables.dart';
import 'package:listify/utils/utils.dart';

typedef TapCallback = void Function();

class NotificationServices {
  // --- Public singletons ---
  static final FirebaseMessaging messaging = FirebaseMessaging.instance;
  static final FlutterLocalNotificationsPlugin _fln =
      FlutterLocalNotificationsPlugin();

  // --- State ---
  static bool notificationVisit = false;
  static TapCallback? _tapCallback; // set per message for tap handling
  static final SplashScreenController splashScreenController =
      Get.find<SplashScreenController>();
  static bool _inited = false;
  static bool _wired = false;

  // --- Channel constants ---
  static const String _androidChannelId = 'sellupnow_high_importance';
  static const String _androidChannelName = 'High Importance Notifications';
  static const String _androidChannelDesc = 'General high-priority alerts';

  /// Call this in `main()` before runApp()
  static Future<void> init() async {
    if (_inited) return; // ✅ guard
    _inited = true;

    // Android init (use your app icon or custom notif icon)
    const AndroidInitializationSettings androidInit =
        AndroidInitializationSettings('@mipmap/noti_logo');

    // iOS init
    const DarwinInitializationSettings darwinInit =
        DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );

    // Initialize plugin + tap callback
    await _fln.initialize(
      const InitializationSettings(android: androidInit, iOS: darwinInit),
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        // When user taps the notification
        _tapCallback?.call();
      },
    );

    // Ask FCM permission (iOS + Android 13+ behavior)
    final settings = await messaging.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );
    log('FCM permission: $settings');

    // (Optional) Get FCM token for debugging
    String? token;
    try {
      token = await messaging.getToken().timeout(const Duration(seconds: 5));
    } catch (e) {
      log('FCM token fetch failed (non-blocking): $e');
    }
    log('FCM token: $token');

    // Ensure Android channel exists (safe to call multiple times)
    await _fln
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(const AndroidNotificationChannel(
          _androidChannelId,
          _androidChannelName,
          description: _androidChannelDesc,
          importance: Importance.max,
          playSound: true,
          enableVibration: true,
        ));
  }

  /// Foreground + "app tapped from terminated state" wiring.
  /// Call this in `main()` after `init()`.
  static Future<void> firebaseInit() async {
    if (_wired) return; // ✅ guard
    _wired = true;
    // App opened from terminated state by tapping a notif
    final initialMessage = await FirebaseMessaging.instance.getInitialMessage();
    if (initialMessage != null) {
      log('Initial message data: ${initialMessage.data}');
      _handleNotificationVisitFlag();
      // Guard your self-chat case (same as your old code)
      if (id != initialMessage.data["senderId"] ||
          initialMessage.data["senderId"] == null) {
        await handleMessage(initialMessage);
      }
      splashScreenController.update([Constant.idNotification]);
    }

    // App resumed from background via tap
    FirebaseMessaging.onMessageOpenedApp.listen((message) async {
      log('onMessageOpenedApp: ${message.data}');
      if (id != message.data["senderId"] || message.data["senderId"] == null) {
        await handleMessage(message);
      }
    });

    // Foreground messages
    FirebaseMessaging.onMessage.listen((message) async {
      log('Foreground FCM data: ${message.data}');
      log('Foreground FCM notif: ${message.notification}');

      // Set tap callback for this specific message
      _tapCallback = () async {
        try {
          await handleMessage(message);
        } catch (e) {
          log('Tap -> handleMessage failed: $e');
        } finally {
          _tapCallback = null;
        }
      };

      // Show local notification (unless web)
      if (!kIsWeb) {
        await showNotification(message);
      } else {
        log('Web platform: skip local notifications');
      }
    });
  }

  /// Background/terminated handler — register in main():
  /// `FirebaseMessaging.onBackgroundMessage(NotificationServices.onShowBackgroundNotification);`
  static Future<void> onShowBackgroundNotification(
      RemoteMessage message) async {
    // NOTE: This must be a top-level or static function.
    // The plugin is not initialized in background isolate by default,
    // so keep logic lightweight here.
    log('Background FCM: ${message.messageId} | data: ${message.data}');
  }

  static Future<void> showNotification(RemoteMessage message) async {
    if (Get.currentRoute == AppRoutes.chatDetailScreenView) {
      log("🔕 Skipping notification because user is already on ChatDetailScreen");
      return;
    }

    final data = message.data;
    String title =
        message.notification?.title ?? data['title'] ?? 'New Message';
    String rawBody = message.notification?.body ?? data['body'] ?? '';
    String formattedBody = rawBody;

    // Try to extract productName and productPrice from body or data
    String? productName = data['productName'];
    String? productPrice = data['productPrice']?.toString();
    String? productImage = data['productImage'];

    // Check if the rawBody string contains product details
    final hasProduct =
        rawBody.contains("productName:") && rawBody.contains("productPrice:");

    if ((productName == null || productPrice == null || productImage == null) &&
        hasProduct) {
      try {
        final nameMatch =
            RegExp(r'productName:\s*(.*?)(,|$)').firstMatch(rawBody);
        final priceMatch =
            RegExp(r'productPrice:\s*(.*?)(,|$)').firstMatch(rawBody);
        final imageMatch =
            RegExp(r'productImage:\s*(.*?)(,|$)').firstMatch(rawBody);

        productName = nameMatch?.group(1)?.trim();
        productPrice = priceMatch?.group(1)?.trim();
        productImage = imageMatch?.group(1)?.trim();
      } catch (e) {
        log('Regex parsing failed: $e');
      }
    }

    Uint8List? imgBytes;
    bool isProductMessage =
        productName != null && productPrice != null && productImage != null;

    if (isProductMessage) {
      formattedBody = '$productName\n₹$productPrice';

      try {
        // Fix Windows slashes in image path
        String imageUrl = productImage.replaceAll('\\', '/');
        if (!imageUrl.startsWith('http')) {
          final base = Api.baseUrl;
          imageUrl = base.endsWith('/') ? '$base$imageUrl' : '$base/$imageUrl';
        }

        final response = await http.get(Uri.parse(imageUrl));
        if (response.statusCode == 200) {
          imgBytes = response.bodyBytes;
        }
      } catch (e) {
        log('Image download error: $e');
      }
    }

    final AndroidNotificationDetails android =
        isProductMessage && imgBytes != null
            ? AndroidNotificationDetails(
                _androidChannelId,
                _androidChannelName,
                channelDescription: _androidChannelDesc,
                importance: Importance.high,
                priority: Priority.high,
                playSound: true,
                enableVibration: true,
                ticker: 'Sellupnow',
                icon: '@mipmap/noti_logo',
                largeIcon: ByteArrayAndroidBitmap(imgBytes),
                styleInformation: BigPictureStyleInformation(
                  ByteArrayAndroidBitmap(imgBytes),
                  contentTitle: productName,
                  summaryText: '₹$productPrice',
                ),
              )
            : AndroidNotificationDetails(
                _androidChannelId,
                _androidChannelName,
                channelDescription: _androidChannelDesc,
                importance: Importance.high,
                priority: Priority.high,
                playSound: true,
                enableVibration: true,
                ticker: 'Sellupnow',
                icon: '@mipmap/noti_logo',
              );

    const DarwinNotificationDetails darwin = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );

    final details = NotificationDetails(android: android, iOS: darwin);
    final notifId = Random.secure().nextInt(100000);

    await _fln.show(
      notifId,
      title,
      isProductMessage
          ? null
          : formattedBody, // For product, body shown inside bigPicture
      details,
    );
  }

  /// Your original route handling preserved (tweaked to be reusable)
  static Future<void> handleMessage(RemoteMessage message) async {
    final data = message.data;

    bool parseBool(dynamic v, {bool fallback = false}) {
      if (v is bool) return v;
      if (v is String) {
        final s = v.trim().toLowerCase();
        if (s == 'true') return true;
        if (s == 'false') return false;
      }
      return fallback;
    }

    Utils.showLog(">>>>>>>>>>>${data["senderName"]}");
    Utils.showLog(">>>>>>>>>>>${data["senderImage"]}");
    Utils.showLog(">>>>>>>>>>>${data["senderImage"]}");
    Utils.showLog(">>>>>>>>>>>${data["adId"]}");
    Utils.showLog(">>>>>>>>>>>${data["receiverId"]}");
    Utils.showLog(">>>>>>>>>>>${data["senderId"]}");
    Utils.showLog(">>>>>>>>>>>${data["chatTopicId"]}");
    Utils.showLog(">>>>>>>>>>>${data["price"]?.toString()}");
    Utils.showLog(">>>>>>>>>>>${data["title"]}");
    Utils.showLog(">>>>>>>>>>>${data["primaryImage"]}");
    Utils.showLog(">>>>>>>>>>>${parseBool(data["view"], fallback: true)}");
    Utils.showLog(">>>>>>>>>>>${parseBool(data["isOnline"], fallback: false)}");
    if (data.isEmpty) {
      // Generic notification list
      Get.toNamed(AppRoutes.notificationScreenView);
      return;
    }

    // Prevent self-routing when sender is self (your original logic)
    id = data["senderId"];

    // If it's a chat notification with metadata
    if (data["chatTopic"] != null ||
        data["senderName"] != null ||
        data["senderImage"] != null) {
      await Get.toNamed(
        AppRoutes.chatDetailScreenView,
        // arguments: [
        //   // data["senderId"],
        //   data["chatTopic"],
        //   data["senderName"],
        //   data["senderImage"],
        //   data["adId"],
        //   data["receiverId"],
        //   data["price"],
        //   data["title"],
        //   data["primaryImage"],
        // ],

        ///my detail screen
        //    arguments: {
        //      'name':  data["senderName"],
        //      'image': data["senderImage"],
        //      'profileImage': data["senderImage"],
        //      'adId': data["adId"],
        //      'receiverId':  data["receiverId"],
        //      'productPrice': data["price"],
        //      'productName': data["title"],
        //      'primaryImage': data["primaryImage"],
        //      'isViewed': data["view"],
        //    }
        arguments: {
          'name': data["senderName"],
          'image': data["senderImage"],
          'profileImage': data["senderImage"],
          'adId': data["adId"],
          'receiverId': data["senderId"],
          'senderId': data["senderId"],
          'chatTopic': data["chatTopicId"],
          'productPrice': data["price"]?.toString(),
          'productName': data["title"],
          'primaryImage': data["primaryImage"],
          'isViewed': parseBool(data["view"], fallback: true),
          'isOnline': parseBool(data["isOnline"], fallback: false),
        },
      )?.then((_) {
        id = '';
        log("Id reset after navigation: $id");
      });
      return;
    }

    // Fallback to notification list if no known payload
    // Get.toNamed(AppRoutes.notificationScreenView);
  }

  /// Old boolean flip preserved for your splash flow
  static void _handleNotificationVisitFlag() {
    log("NotificationVisit before :: $notificationVisit");
    notificationVisit = !notificationVisit;
    log("NotificationVisit after :: $notificationVisit");
  }
}
