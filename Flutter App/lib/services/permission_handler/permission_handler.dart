import 'dart:developer';
import 'dart:io';

import 'package:flutter/cupertino.dart';
import 'package:flutter/foundation.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';
import 'package:get/get_rx/src/rx_typedefs/rx_typedefs.dart';
import 'package:listify/utils/utils.dart';
import 'package:permission_handler/permission_handler.dart';

class PermissionHandler {
  static Future<void> _permissionQueue = Future.value();

  static Future<T> _runExclusive<T>(Future<T> Function() action) async {
    final next = _permissionQueue.then((_) => action());
    _permissionQueue = next.then((_) {}, onError: (_) {});
    return next;
  }

  static Future<void> notificationPermissions() async {
    await _runExclusive(() async {
      await Permission.notification.request();
      final isDenied = await Permission.notification.isDenied;
      if (isDenied) {
        await Permission.notification.request();
      }
    });
  }

  static Future<void> microphonePermissions() async {
    await _runExclusive(() async {
      await Permission.microphone.request();
      final isDenied = await Permission.microphone.isDenied;
      if (isDenied) {
        await Permission.microphone.request();
      }
    });
  }

  static Future<void> onGetCameraPermission({
    required Callback onGranted,
    Callback? onDenied,
  }) async {
    try {
      final status = await _runExclusive(() => Permission.camera.request());

      if (status == PermissionStatus.denied) {
        Utils.showToast(Get.context!, "Please allow camera permission.");
        onDenied?.call();
      } else if (status == PermissionStatus.permanentlyDenied) {
        Utils.showToast(
            Get.context!, "Please allow camera permission in settings.");
        await openAppSettings();
        onDenied?.call();
      } else {
        log("Camera Permission Granted");
        onGranted.call();
      }
    } catch (e) {
      onDenied?.call();
      log("Camera Permission Failed => $e");
    }
  }

  static Future<void> onGetMicrophonePermission({
    required Callback onGranted,
    Callback? onDenied,
  }) async {
    try {
      final status = await _runExclusive(() => Permission.microphone.request());

      if (status == PermissionStatus.denied) {
        Utils.showToast(Get.context!, "Please allow microphone permission.");
        onDenied?.call();
      } else if (status == PermissionStatus.permanentlyDenied) {
        Utils.showToast(
            Get.context!, "Please allow microphone permission in settings.");
        await openAppSettings();
        onDenied?.call();
      } else {
        log("microphone Permission Granted");
        onGranted.call();
      }
    } catch (e) {
      onDenied?.call();
      log("microphone Permission Failed => $e");
    }
  }

  static Future<void> storagePermissions() async {
    await _runExclusive(() async {
      await Permission.storage.request();
      final isDenied = await Permission.storage.isDenied;
      if (isDenied) {
        await Permission.storage.request();
      }
    });
  }

// -------------------- LOCATION CORE --------------------------
  Future<bool> ensureLocationOnAndPermitted(
      {bool askBackground = false}) async {
    try {
      // 1) Location services enabled?
      final servicesEnabled = await Geolocator.isLocationServiceEnabled();
      if (!servicesEnabled) {
        // Don't crash; guide user instead
        if (!kIsWeb && (Platform.isAndroid || Platform.isIOS)) {
          await Geolocator.openLocationSettings();
        }
        return false;
      }

      // 2) When-in-use permission
      var whenInUse = await Permission.locationWhenInUse.status;
      if (whenInUse.isDenied) {
        whenInUse =
            await _runExclusive(() => Permission.locationWhenInUse.request());
      }

      if (whenInUse.isPermanentlyDenied) {
        // Go to settings instead of force-unwrapping anything
        await openAppSettings();
        return false;
      }

      if (!whenInUse.isGranted) {
        return false;
      }

      // 3) Optional: Background (Always). Request ONLY after when-in-use is granted.
      if (askBackground) {
        var always = await Permission.locationAlways.status;
        if (always.isDenied || always.isRestricted) {
          always =
              await _runExclusive(() => Permission.locationAlways.request());
        }
        if (always.isPermanentlyDenied) {
          await openAppSettings();
          return false;
        }
        if (!always.isGranted) {
          return false;
        }
      }

      return true;
    } catch (e, s) {
      debugPrint('ensureLocationOnAndPermitted error: $e\n$s');
      return false;
    }
  }

  /// Safe wrapper for app start. Never uses `!`, never throws on nulls.
  Future<void> requestLocationAtAppStart({bool askBackground = false}) async {
    try {
      final ok =
          await ensureLocationOnAndPermitted(askBackground: askBackground);
      debugPrint('Location permission ok: $ok');
    } catch (e, s) {
      debugPrint('requestLocationAtAppStart error: $e\n$s');
    }
  }
}
