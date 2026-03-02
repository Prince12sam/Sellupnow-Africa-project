import 'dart:math' as math;
import 'package:flutter/cupertino.dart';
import 'package:geocoding/geocoding.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:listify/services/permission_handler/permission_handler.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class MapController extends GetxController {
  // --- constants ---
  static const double kDefaultRadiusKm = 20.0;

  // --- Map state
  final Set<Marker> markers = {};
  final Set<Circle> circles = {};
  GoogleMapController? mapController;

  double? latitude;
  double? longitude;

  // circle center + radius (km)
  LatLng? center;
  double radiusKm = kDefaultRadiusKm; // default 20 Km

  // bounds (always keep ready)
  Map<String, double?>? selectedBounds;

  // --- Address state
  final destinationAddressController = TextEditingController();
  final destinationAddressFocusNode = FocusNode();
  String? finalAddress;
  String destinationAddress = '';
  String currentAddress = '';
  String? addressStreet;
  String? addressCity;
  String? addressState;
  String? addressCountry;
  String? addressPostalCode;
  String? addressName;

  bool isLoading = true;

  /// Safely read last saved range from Database, else fallback to default.
  double _loadInitialRadiusKm() {
    try {
      final raw = Database.selectedLocation['range'];
      if (raw == null) return kDefaultRadiusKm;

      // accept double/int/string
      if (raw is num) return raw.toDouble();
      if (raw is String) {
        final v = double.tryParse(raw.trim());
        if (v == null) return kDefaultRadiusKm;
        return v;
      }
      return kDefaultRadiusKm;
    } catch (_) {
      return kDefaultRadiusKm;
    }
  }

  @override
  void onInit() {
    super.onInit();
    radiusKm = _loadInitialRadiusKm();

    if (Database.hasSelectedLocation.value == true &&
        Database.selectedLocation['latitude'] != null &&
        Database.selectedLocation['longitude'] != null) {
      final lat =
          double.tryParse(Database.selectedLocation['latitude'].toString()) ??
              0.0;
      final lng =
          double.tryParse(Database.selectedLocation['longitude'].toString()) ??
              0.0;

      if (lat != 0.0 && lng != 0.0) {
        center = LatLng(lat, lng);
        latitude = lat;
        longitude = lng;

        radiusKm = _loadInitialRadiusKm();
        finalAddress = Database.selectedLocationText();
        destinationAddressController.text = finalAddress ?? "";

        _rebuildMarker();
        _rebuildCircle();
        _updateBounds();
        return; // ✅ DO NOT call GPS now
      }
    }

    // no saved selection → GPS
    radiusKm = kDefaultRadiusKm;
    getCurrentLocation();
  }

  // -------- Public APIs (UI calls) --------

  Future<void> fitCircleInView({double paddingPx = 60}) async {
    if (mapController == null || center == null) return;
    _updateBounds();
    final b = selectedBounds;
    if (b == null || b['ne_lat'] == null) return;

    final ne = LatLng(b['ne_lat']!, b['ne_lng']!);
    final sw = LatLng(b['sw_lat']!, b['sw_lng']!);
    final bounds = LatLngBounds(southwest: sw, northeast: ne);

    try {
      await mapController!.animateCamera(
        CameraUpdate.newLatLngBounds(bounds, paddingPx.toDouble()),
      );
    } catch (_) {
      await Future.delayed(const Duration(milliseconds: 100));
      try {
        await mapController!.animateCamera(
          CameraUpdate.newLatLngBounds(bounds, paddingPx.toDouble()),
        );
      } catch (_) {}
    }
  }

  /// Slider/stepper થી radius set થાય ત્યારે call કર
  // void setRadiusKm(double km) {
  //   radiusKm = km;
  //   _rebuildCircle();
  //   _updateBounds();
  //   update([Constant.location]);
  //
  //   // (Optional) Persist સાથે save કરવું હોય તો અહીં call કર:
  //   // if (latitude != null && longitude != null) {
  //   //   Database.saveSelectedLocation(
  //   //     latitude: latitude!,
  //   //     longitude: longitude!,
  //   //     address: finalAddress ?? destinationAddressController.text,
  //   //     radiusKm: radiusKm,
  //   //   );
  //   // }
  // }

  void setRadiusKm(double km) {
    radiusKm = km;
    _rebuildCircle();
    _updateBounds();
    update([Constant.location]);

    fitCircleInView(paddingPx: 60);
    // અથવા: _fitCircleWithMinSpan(minSpanKm: 6, paddingPx: 60);
  }

  Future<void> resetToCurrent() async {
    try {
      final pos = await getUserLocationPosition();
      radiusKm = kDefaultRadiusKm;
      await onHandleTapPoint(LatLng(pos.latitude, pos.longitude));
    } catch (e) {
      Utils.showLog("resetToCurrent error: $e");
    }
  }

  /// મૅપ પર ટૅપ/LocateMe -> center set + marker/circle + address + bounds
  // Future<void> onHandleTapPoint(LatLng point) async {
  //   try {
  //     center = point;
  //     latitude = point.latitude;
  //     longitude = point.longitude;
  //
  //     _rebuildMarker();
  //     _rebuildCircle();
  //     _updateBounds();
  //
  //     await _animateIfReady(
  //       CameraUpdate.newCameraPosition(
  //         CameraPosition(target: point, zoom: 15.5),
  //       ),
  //     );
  //
  //     finalAddress =
  //     await buildFullAddressFromLatLong(point.latitude, point.longitude);
  //     destinationAddressController.text = finalAddress ?? "";
  //
  //     update([Constant.location]);
  //
  //     // (Optional) Persist current selection
  //     // Database.saveSelectedLocation(
  //     //   latitude: latitude!,
  //     //   longitude: longitude!,
  //     //   address: finalAddress ?? "",
  //     //   radiusKm: radiusKm,
  //     // );
  //   } catch (e) {
  //     Utils.showLog("Error in onHandleTapPoint: $e");
  //   }
  // }

  Future<void> onHandleTapPoint(LatLng point) async {
    try {
      center = point;
      latitude = point.latitude;
      longitude = point.longitude;

      _rebuildMarker();
      _rebuildCircle();
      _updateBounds();

      // ❌ fixed zoom દૂર
      // await _animateIfReady(CameraUpdate.newCameraPosition(CameraPosition(target: point, zoom: 15.5)));

      // ✅ circle bounds પ્રમાણે fit
      await fitCircleInView(paddingPx: 60);
      // અથવા: await _fitCircleWithMinSpan(minSpanKm: 6, paddingPx: 60);

      finalAddress =
          await buildFullAddressFromLatLong(point.latitude, point.longitude);
      destinationAddressController.text = finalAddress ?? "";
      update([Constant.location]);
    } catch (e) {
      Utils.showLog("Error in onHandleTapPoint: $e");
    }
  }

  /// Apply માટે API payload (center + radius + bounds + address)
  Map<String, dynamic> locationDataForApi() {
    _updateBounds(); // ensure fresh

    final b = selectedBounds!;
    return {
      'latitude': latitude,
      'longitude': longitude,
      'range': radiusKm.toDouble(),
      'ne_lat': b['ne_lat'],
      'ne_lng': b['ne_lng'],
      'sw_lat': b['sw_lat'],
      'sw_lng': b['sw_lng'],
      'address': finalAddress,
      'street': addressStreet,
      'city': addressCity,
      'state': addressState,
      'country': addressCountry,
      'postal_code': addressPostalCode,
    };
  }

  // -------- Address helpers --------

  Future<String> buildFullAddressFromLatLong(
      double latitude, double longitude) async {
    final placeMark =
        await placemarkFromCoordinates(latitude, longitude).catchError((e) {
      Utils.showLog("Error in Build Full Address :: $e");
      throw e;
    });

    final place = placeMark[0];
    addressName = place.name ?? '';
    addressStreet = place.street ?? '';
    addressCity = place.locality ?? '';
    addressState = place.administrativeArea ?? '';
    addressPostalCode = place.postalCode ?? '';
    addressCountry = place.country ?? '';

    finalAddress = [
      addressStreet,
      addressCity,
      addressState,
      addressPostalCode,
      addressCountry,
    ].where((e) => (e ?? '').isNotEmpty).join(', ');

    return finalAddress!;
  }

  Future<Position> getUserLocationPosition() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    LocationPermission permission = await Geolocator.checkPermission();

    if (!serviceEnabled) {
      Utils.showLog("Location services disabled");
    }

    if (permission == LocationPermission.denied) {
      await Geolocator.openAppSettings();
      throw 'Location Permission Denied';
    }

    if (permission == LocationPermission.deniedForever) {
      throw 'Location Permission Denied Permanently';
    }

    return await Geolocator.getCurrentPosition(
            desiredAccuracy: LocationAccuracy.high)
        .catchError((_) async {
      final last = await Geolocator.getLastKnownPosition();
      if (last != null) return last;
      throw 'Enable Location';
    });
  }

  Future<void> setAddress() async {
    try {
      final pos = await getUserLocationPosition();
      currentAddress =
          await buildFullAddressFromLatLong(pos.latitude, pos.longitude);
      destinationAddressController.text = currentAddress;
      destinationAddress = currentAddress;
      update([Constant.location]);
    } catch (e) {
      Utils.showLog("Error in Set Address :: $e");
    }
  }

  Future<void> getCurrentLocationOld() async {
    final has = await handleLocationPermission();
    if (!has) return;

    isLoading = true;
    update([Constant.location]);

    try {
      final pos = await getUserLocationPosition();
      latitude = pos.latitude;
      longitude = pos.longitude;

      await setAddress();

      final c = LatLng(latitude!, longitude!);
      await onHandleTapPoint(c);
    } catch (e) {
      Utils.showLog("Error in getCurrentLocation: $e");
    }

    isLoading = false;
    update([Constant.location]);
  }

  // Future<void> getCurrentLocation({bool forceGps = false}) async {
  //   final has = await handleLocationPermission();
  //   if (!has) return;
  //
  //   isLoading = true;
  //   update([Constant.location]);
  //
  //   try {
  //     if (!forceGps && center != null && latitude != null && longitude != null) {
  //       // just rebuild with saved values
  //       _rebuildMarker();
  //       _rebuildCircle();
  //       _updateBounds();
  //
  //       await _animateIfReady(
  //         CameraUpdate.newCameraPosition(
  //           CameraPosition(target: center!, zoom: 15.5),
  //         ),
  //       );
  //     } else {
  //       // GPS fetch
  //       final pos = await getUserLocationPosition();
  //       latitude = pos.latitude;
  //       longitude = pos.longitude;
  //
  //       // radiusKm already set to default (or restored earlier)
  //       final c = LatLng(latitude!, longitude!);
  //       await onHandleTapPoint(c);
  //     }
  //   } catch (e) {
  //     Utils.showLog("Error in getCurrentLocation: $e");
  //   }
  //
  //   isLoading = false;
  //   update([Constant.location]);
  // }

  Future<void> getCurrentLocation({bool forceGps = false}) async {
    final has = await handleLocationPermission();
    if (!has) return;

    isLoading = true;
    update([Constant.location]);

    try {
      if (!forceGps &&
          center != null &&
          latitude != null &&
          longitude != null) {
        _rebuildMarker();
        _rebuildCircle();
        _updateBounds();

        await _animateIfReady(
          CameraUpdate.newCameraPosition(
            CameraPosition(target: center!, zoom: 15.5),
          ),
        );
      } else {
        final pos = await getUserLocationPosition();
        latitude = pos.latitude;
        longitude = pos.longitude;

        final c = LatLng(latitude!, longitude!);
        await onHandleTapPoint(c);
      }
    } catch (e) {
      Utils.showLog("Error in getCurrentLocation: $e");
    }

    isLoading = false; // ✅ reset
    update([Constant.location]);
  }

  Future<bool> handleLocationPermission() async {
    final ok = await PermissionHandler().ensureLocationOnAndPermitted(
      askBackground: false,
    );
    if (!ok) {
      Utils.showToast(
          Get.context!, "Location permission denied or services disabled.");
    }
    return ok;
  }

  // -------- Internal helpers --------

  void _rebuildMarker() {
    if (center == null) return;
    markers
      ..clear()
      ..add(
        Marker(
          markerId: const MarkerId('center'),
          position: center!,
          infoWindow: const InfoWindow(title: '📍 Selected Location'),
          icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed),
        ),
      );
  }

  void _rebuildCircle() {
    if (center == null) return;
    circles
      ..clear()
      ..add(
        Circle(
          circleId: const CircleId('range'),
          center: center!,
          radius: radiusKm.toDouble() * 1000, // Km -> meters
          fillColor: const Color(0xFFEF4C4C).withOpacity(0.18),
          strokeColor: const Color(0xFFEF4C4C).withOpacity(0.6),
          strokeWidth: 1,
        ),
      );
  }

  Future<void> _animateIfReady(CameraUpdate update) async {
    if (mapController == null) return;
    try {
      await mapController!.animateCamera(update);
    } catch (_) {}
  }

  /// Compute bounds for (center, radiusKm). Never returns null fields after center set.
  Map<String, double?> _boundsForRadius() {
    if (center == null) {
      return {'ne_lat': null, 'ne_lng': null, 'sw_lat': null, 'sw_lng': null};
    }
    final lat = center!.latitude;
    final lng = center!.longitude;

    // 1° latitude ~ 110.574 km
    const kmPerDegLat = 110.574;
    // 1° longitude ~ 111.320*cos(latitude) km
    final kmPerDegLng = 111.320 * math.cos(lat * math.pi / 180.0);

    final dLat = radiusKm / kmPerDegLat;
    final dLng = radiusKm / kmPerDegLng;

    return {
      'ne_lat': lat + dLat,
      'ne_lng': lng + dLng,
      'sw_lat': lat - dLat,
      'sw_lng': lng - dLng,
    };
  }

  void _updateBounds() {
    selectedBounds = _boundsForRadius();

    if (center == null && latitude != null && longitude != null) {
      center = LatLng(latitude!, longitude!);
      selectedBounds = _boundsForRadius();
    }
  }
}






/*class MapController extends GetxController {
  static const double kDefaultRadiusKm = 20.0;

  final Set<Marker> markers = {};
  final Set<Circle> circles = {};
  GoogleMapController? mapController;

  double? latitude;
  double? longitude;

  LatLng? center;
  double radiusKm = kDefaultRadiusKm;

  Map<String, double?>? selectedBounds;

  final destinationAddressController = TextEditingController();
  final destinationAddressFocusNode = FocusNode();
  String? finalAddress;
  String destinationAddress = '';
  String currentAddress = '';
  String? addressStreet;
  String? addressCity;
  String? addressState;
  String? addressCountry;
  String? addressPostalCode;
  String? addressName;

  bool isLoading = true;

  double _loadInitialRadiusKm() {
    try {
      final raw = Database.selectedLocation['range'];
      if (raw == null) return kDefaultRadiusKm;
      if (raw is num) return raw.toDouble();
      if (raw is String) return double.tryParse(raw.trim()) ?? kDefaultRadiusKm;
      return kDefaultRadiusKm;
    } catch (_) {
      return kDefaultRadiusKm;
    }
  }

  @override
  void onInit() {
    super.onInit();
    radiusKm = _loadInitialRadiusKm();

    if (Database.hasSelectedLocation.value == true &&
        Database.selectedLocation['latitude'] != null &&
        Database.selectedLocation['longitude'] != null) {
      final lat = double.tryParse("${Database.selectedLocation['latitude']}") ?? 0.0;
      final lng = double.tryParse("${Database.selectedLocation['longitude']}") ?? 0.0;

      if (lat != 0.0 && lng != 0.0) {
        center = LatLng(lat, lng);
        latitude = lat;
        longitude = lng;

        radiusKm = _loadInitialRadiusKm();
        finalAddress = Database.selectedLocationText();
        destinationAddressController.text = finalAddress ?? "";

        _rebuildMarker();
        _rebuildCircle();
        _updateBounds();

        isLoading = false;                // ✅ IMPORTANT
        update([Constant.location]);
        return;                           // don’t call GPS now
      }
    }

    // No saved selection → GPS
    radiusKm = kDefaultRadiusKm;
    getCurrentLocation();
  }

  // ---------- Public APIs ----------

  Future<void> fitCircleInView({double paddingPx = 60}) async {
    if (mapController == null || center == null) return;
    _updateBounds();
    final b = selectedBounds;
    if (b == null || b['ne_lat'] == null) return;

    final ne = LatLng(b['ne_lat']!, b['ne_lng']!);
    final sw = LatLng(b['sw_lat']!, b['sw_lng']!);
    final bounds = LatLngBounds(southwest: sw, northeast: ne);

    try {
      await mapController!.animateCamera(
        CameraUpdate.newLatLngBounds(bounds, paddingPx.toDouble()),
      );
    } catch (_) {
      await Future.delayed(const Duration(milliseconds: 100));
      try {
        await mapController!.animateCamera(
          CameraUpdate.newLatLngBounds(bounds, paddingPx.toDouble()),
        );
      } catch (_) {}
    }
  }

  void setRadiusKm(double km) {
    radiusKm = km;
    _rebuildCircle();
    _updateBounds();
    update([Constant.location]);
    fitCircleInView(paddingPx: 60);
  }

  Future<void> resetToCurrent() async {
    try {
      final pos = await getUserLocationPosition();
      radiusKm = kDefaultRadiusKm;
      await onHandleTapPoint(LatLng(pos.latitude, pos.longitude));
    } catch (e) {
      Utils.showLog("resetToCurrent error: $e");
    }
  }

  /// 💡 Ensure location is ready. If not picked, auto-pick GPS.
  Future<bool> ensureReadyLocation() async {
    try {
      if (center != null && latitude != null && longitude != null) {
        // already ready; still ensure bounds/address
        _rebuildMarker();
        _rebuildCircle();
        _updateBounds();
        if ((finalAddress ?? '').isEmpty) {
          finalAddress = await buildFullAddressFromLatLong(latitude!, longitude!);
          destinationAddressController.text = finalAddress ?? "";
        }
        return true;
      }
      // pick GPS
      final pos = await getUserLocationPosition();
      await onHandleTapPoint(LatLng(pos.latitude, pos.longitude));
      return true;
    } catch (e) {
      Utils.showLog("ensureReadyLocation error: $e");
      return false;
    }
  }

  Future<void> onHandleTapPoint(LatLng point) async {
    try {
      center = point;
      latitude = point.latitude;
      longitude = point.longitude;

      _rebuildMarker();
      _rebuildCircle();
      _updateBounds();

      await fitCircleInView(paddingPx: 60);

      finalAddress = await buildFullAddressFromLatLong(point.latitude, point.longitude);
      destinationAddressController.text = finalAddress ?? "";
      update([Constant.location]);
    } catch (e) {
      Utils.showLog("Error in onHandleTapPoint: $e");
    }
  }

  /// Safe even if bounds not yet computed.
  Map<String, dynamic> locationDataForApi() {
    _updateBounds();
    final b = selectedBounds ?? _boundsForRadius();

    return {
      'latitude': latitude,
      'longitude': longitude,
      'range': radiusKm.toDouble(),
      'ne_lat': b['ne_lat'],
      'ne_lng': b['ne_lng'],
      'sw_lat': b['sw_lat'],
      'sw_lng': b['sw_lng'],
      'address': finalAddress,
      'street': addressStreet,
      'city': addressCity,
      'state': addressState,
      'country': addressCountry,
      'postal_code': addressPostalCode,
    };
  }

  // ---------- Address helpers ----------

  Future<String> buildFullAddressFromLatLong(double latitude, double longitude) async {
    final placeMark = await placemarkFromCoordinates(latitude, longitude).catchError((e) {
      Utils.showLog("Error in Build Full Address :: $e");
      throw e;
    });

    final place = placeMark[0];
    addressName = place.name ?? '';
    addressStreet = place.street ?? '';
    addressCity = place.locality ?? '';
    addressState = place.administrativeArea ?? '';
    addressPostalCode = place.postalCode ?? '';
    addressCountry = place.country ?? '';

    finalAddress = [
      addressStreet,
      addressCity,
      addressState,
      addressPostalCode,
      addressCountry,
    ].where((e) => (e ?? '').isNotEmpty).join(', ');

    return finalAddress!;
  }

  Future<Position> getUserLocationPosition() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    LocationPermission permission = await Geolocator.checkPermission();

    if (!serviceEnabled) {
      Utils.showLog("Location services disabled");
    }

    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        await Geolocator.openAppSettings();
        throw 'Location Permission Denied';
      }
    }

    if (permission == LocationPermission.deniedForever) {
      throw 'Location Permission Denied Permanently';
    }

    return await Geolocator.getCurrentPosition(
      desiredAccuracy: LocationAccuracy.high,
    ).catchError((_) async {
      final last = await Geolocator.getLastKnownPosition();
      if (last != null) return last;
      throw 'Enable Location';
    });
  }

  Future<void> setAddress() async {
    try {
      final pos = await getUserLocationPosition();
      currentAddress = await buildFullAddressFromLatLong(pos.latitude, pos.longitude);
      destinationAddressController.text = currentAddress;
      destinationAddress = currentAddress;
      update([Constant.location]);
    } catch (e) {
      Utils.showLog("Error in Set Address :: $e");
    }
  }

  Future<void> getCurrentLocation({bool forceGps = false}) async {
    final has = await handleLocationPermission();
    if (!has) return;

    isLoading = true;
    update([Constant.location]);

    try {
      if (!forceGps && center != null && latitude != null && longitude != null) {
        _rebuildMarker();
        _rebuildCircle();
        _updateBounds();

        await _animateIfReady(
          CameraUpdate.newCameraPosition(
            CameraPosition(target: center!, zoom: 15.5),
          ),
        );
      } else {
        final pos = await getUserLocationPosition();
        latitude = pos.latitude;
        longitude = pos.longitude;

        final c = LatLng(latitude!, longitude!);
        await onHandleTapPoint(c);
      }
    } catch (e) {
      Utils.showLog("Error in getCurrentLocation: $e");
    }

    isLoading = false;
    update([Constant.location]);
  }

  Future<bool> handleLocationPermission() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      Utils.showToast(Get.context!, "Location services are disabled. Please enable it.");
      return false;
    }

    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        Utils.showToast(Get.context!, "Location permission denied.");
        return false;
      }
    }

    if (permission == LocationPermission.deniedForever) {
      Utils.showToast(Get.context!, "Location permission permanently denied.");
      return false;
    }

    return true;
  }

  // ---------- Internal helpers ----------

  void _rebuildMarker() {
    if (center == null) return;
    markers
      ..clear()
      ..add(
        Marker(
          markerId: const MarkerId('center'),
          position: center!,
          infoWindow: const InfoWindow(title: '📍 Selected Location'),
          icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed),
        ),
      );
  }

  void _rebuildCircle() {
    if (center == null) return;
    circles
      ..clear()
      ..add(
        Circle(
          circleId: const CircleId('range'),
          center: center!,
          radius: radiusKm.toDouble() * 1000,
          fillColor: const Color(0xFFEF4C4C).withOpacity(0.18),
          strokeColor: const Color(0xFFEF4C4C).withOpacity(0.6),
          strokeWidth: 1,
        ),
      );
  }

  Future<void> _animateIfReady(CameraUpdate update) async {
    if (mapController == null) return;
    try {
      await mapController!.animateCamera(update);
    } catch (_) {}
  }

  Map<String, double?> _boundsForRadius() {
    if (center == null) {
      return {'ne_lat': null, 'ne_lng': null, 'sw_lat': null, 'sw_lng': null};
    }
    final lat = center!.latitude;
    final lng = center!.longitude;

    const kmPerDegLat = 110.574;
    final kmPerDegLng = 111.320 * math.cos(lat * math.pi / 180.0);

    final dLat = radiusKm / kmPerDegLat;
    final dLng = radiusKm / kmPerDegLng;

    return {
      'ne_lat': lat + dLat,
      'ne_lng': lng + dLng,
      'sw_lat': lat - dLat,
      'sw_lng': lng - dLng,
    };
  }

  void _updateBounds() {
    selectedBounds = _boundsForRadius();

    if (center == null && latitude != null && longitude != null) {
      center = LatLng(latitude!, longitude!);
      selectedBounds = _boundsForRadius();
    }
  }
}*/



// 68e4aa46bfe6c0322eda7ff2
// 68e4a9ecbfe6c0322eda7fa6