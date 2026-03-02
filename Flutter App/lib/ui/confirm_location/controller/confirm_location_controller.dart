import 'dart:convert';
import 'dart:developer';

import 'package:flutter/cupertino.dart';
import 'package:geocoding/geocoding.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart'
    hide Attribute;
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/ui/product_detail_screen/model/product_detail_response_model.dart'
    hide Attribute;
import 'package:listify/services/permission_handler/permission_handler.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';
import 'package:map_location_picker/map_location_picker.dart';

class ConfirmLocationScreenController extends GetxController {
  final Map<String, dynamic> arguments = Get.arguments ?? {};

  String? mainImage;
  List<String>? selectedImages;
  List<Attribute> attributeDataList = [];
  String? title;
  String? subtitle;
  String? price;
  String? description;
  String? categoryId;
  List<Map<String, dynamic>> filledAttributeList =
      []; // store as JSON string list
  String? addressStreet;
  String? addressCity;
  String? addressState;
  String? addressCountry;
  String? addressPostalCode;
  String? addressName;
  double? latitude;
  double? longitude;
  String? finalAddress;
  String destinationAddress = '';
  bool isLoading = false;
  Set<Marker> markers = {};
  final destinationAddressFocusNode = FocusNode();
  late GoogleMapController mapController;
  Product? adsData;
  bool isEdit = false;
  String? city;
  String? state;
  String? country;
  String? fullAddress;
  String? finalFullAddress;

  @override
  void onInit() {
    final List<String> attributesJsonStrings =
        List<String>.from(arguments['attributes']);
    for (var attrJson in attributesJsonStrings) {
      final map = jsonDecode(attrJson);
      Utils.showLog(
          "Name: ${map['name']}, Value: ${map['value']}, Image: ${map['image']}");

      filledAttributeList.add(map); // ✅ store as Map, NOT JSON string
    }
    Utils.showLog(
        "arguments['attributes']::::::::::: ${arguments['attributes']}");
    adsData = arguments['ad'];
    isEdit = arguments['editApi'] ?? false;

    Utils.showLog("editApi  :::::::::::::::: $isEdit");

    mainImage = arguments['mainImage'];
    // selectedImages = arguments['selectedImages'];
    selectedImages = (arguments['selectedImages'] as List?)?.cast<String>();
    log("image::::::::::::::::::::${selectedImages}");
    title = arguments['title'];
    subtitle = arguments['subtitle'];
    price = arguments['price'];
    description = arguments['description'];
    categoryId = arguments['categoryId'];
    // adsData = arguments['ad'];

    // if (adsData != null && adsData!.location != null) {
    //   final loc = adsData!.location!;
    //
    //   // Check if at least one field has value
    //   if ((loc.city?.isNotEmpty ?? false) ||
    //       (loc.state?.isNotEmpty ?? false) ||
    //       (loc.country?.isNotEmpty ?? false) ||
    //       (loc.fullAddress?.isNotEmpty ?? false)) {
    //     city = loc.city ?? '';
    //     state = loc.state ?? '';
    //     fullAddress = loc.fullAddress ?? '';
    //     country = loc.country ?? '';
    //
    //     finalFullAddress = "$fullAddress, $city, $state, $country";
    //
    //     Utils.showLog("✅ Location data stored → $finalFullAddress");
    //     Utils.showLog("📦 Raw location JSON: ${jsonEncode(loc.toJson())}");
    //   } else {
    //     Utils.showLog("⚠️ Location object exists but all fields are empty");
    //   }
    // } else {
    //   Utils.showLog("📭 No location data in arguments");
    // }

    // Utils.showLog("location initialAddress:::::::::::::::${city}");

    // Utils.showLog("location initialAddress${jsonEncode(adsData!.location!.toJson())} ");

    Utils.showLog(
        'Received Product: $title | $subtitle | $price | $description  |  $categoryId  |  $mainImage  |  $selectedImages');

    log("arguments api:::::::::::::::::::::$arguments");

    // mainImage = arguments['mainImage'];
    // selectedImages = arguments['selectedImages'];

    Utils.showLog('filledValues :::::: $attributeDataList');
    super.onInit();
  }

  navigateLocationScreen() {
    Utils.showLog(
        'Received Product: $title | $subtitle | $price | $description  |  $categoryId  |  $mainImage  |  $selectedImages');

    Get.toNamed(AppRoutes.locationScreen, arguments: {
      'attributes': filledAttributeList,
      'mainImage': mainImage,
      'selectedImages': selectedImages,
      'title': title,
      'subtitle': subtitle,
      'price': price,
      'description': description,
      'categoryId': categoryId,
      'adId': adsData?.id,
      'editApi': isEdit,
      'ad': adsData,
    });

    // Get.toNamed(AppRoutes.locationScreen, arguments: arguments);
  }

  Map<String, dynamic> locationDataForApi() {
    return {
      'country': Get.find<MapController>().addressCountry ?? '',
      'state': Get.find<MapController>().addressState ?? '',
      'city': Get.find<MapController>().addressCity ?? '',
      'latitude': Get.find<MapController>().latitude ?? 0.0,
      'longitude': Get.find<MapController>().longitude ?? 0.0,
      'fullAddress':
          "${Get.find<MapController>().addressStreet},${Get.find<MapController>().addressName}",
      // 'fullAddress': "$addressStreet $addressName",
    };
  }

  ///

  Future<void> onHandleTapPoint(LatLng point) async {
    try {
      // Clear previous markers
      markers.clear();

      // Add red marker
      markers.add(
        Marker(
          markerId: MarkerId(point.toString()),
          position: point,
          infoWindow: const InfoWindow(title: '📍 Selected Location'),
          icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueRed),
        ),
      );

      // Animate camera
      mapController.animateCamera(
        CameraUpdate.newCameraPosition(
          CameraPosition(target: point, zoom: 18.0),
        ),
      );

      // Update lat/lng
      latitude = point.latitude;
      longitude = point.longitude;

      // Get address
      final placemarks = await placemarkFromCoordinates(latitude!, longitude!);
      if (placemarks.isNotEmpty) {
        final place = placemarks.first;
        finalAddress =
            "${place.name}, ${place.locality}, ${place.administrativeArea}, ${place.country}";
      }

      update([Constant.location]);
    } catch (e) {
      Utils.showLog("Error in onHandleTapPoint: $e");
    }
  }

  Future<Position> getUserLocationPosition() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    LocationPermission permission = await Geolocator.checkPermission();
    if (!serviceEnabled) {
      Utils.showLog("Services Enabled :: $serviceEnabled");
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
        .then((value) {
      return value;
    }).catchError((e) async {
      return await Geolocator.getLastKnownPosition().then((value) async {
        if (value != null) {
          return value;
        } else {
          throw 'Enable Location';
        }
      }).catchError((e) {
        Utils.showLog("Error in get current position : $e");
        Utils.showToast(Get.context!, e);
        return e;
      });
    });
  }

  Future<String> buildFullAddressFromLatLong(
      double latitude, double longitude) async {
    List<Placemark> placeMark =
        await placemarkFromCoordinates(latitude, longitude)
            .catchError((e) async {
      Utils.showLog("Error in Build Full Address :: $e");
      throw e;
    });

    Placemark place = placeMark[0];

    addressName = place.name;
    addressStreet = place.street;
    addressCity = place.locality;
    addressState = place.administrativeArea;
    addressPostalCode = place.postalCode;
    addressCountry = place.country;

    Utils.showLog("🧭 Address Parts:");
    Utils.showLog("📍 Name: $addressName");
    Utils.showLog("🛣️ Street: $addressStreet");
    Utils.showLog("🏙️ City: $addressCity");
    Utils.showLog("🗺️ State: $addressState");
    Utils.showLog("📮 Postal Code: $addressPostalCode");
    Utils.showLog("🌐 Country: $addressCountry");

    finalAddress =
        '$addressStreet, $addressCity, $addressState, $addressPostalCode, $addressCountry';

    Utils.showLog("final????????????????$finalAddress");

    return finalAddress!;
  }

  // Future<void> setAddress() async {
  //   try {
  //     Position position = await getUserLocationPosition().catchError((e) {
  //       Utils.showLog("Set Address in try");
  //       Utils.showToast(Get.context!, e);
  //       return e;
  //     });
  //
  //     currentAddress = await buildFullAddressFromLatLong(position.latitude, position.longitude).catchError((e) {
  //       Utils.showLog("Catch Error in save Current Address");
  //       return e;
  //     });
  //     destinationAddressController.text = currentAddress;
  //     destinationAddress = currentAddress;
  //
  //     update([Constant.location]);
  //   } catch (e) {
  //     Utils.showLog("Error in Set Address :: $e");
  //   }
  // }

  Future<void> getCurrentLocation() async {
    bool hasPermission = await handleLocationPermission();
    if (!hasPermission) return;

    isLoading = true;
    update([Constant.location]);

    try {
      final position = await getUserLocationPosition();

      latitude = position.latitude;
      longitude = position.longitude;

      // await setAddress();

      mapController.animateCamera(
        CameraUpdate.newCameraPosition(
          CameraPosition(
            target: LatLng(latitude!, longitude!),
            zoom: 18.0,
          ),
        ),
      );

      await onHandleTapPoint(LatLng(latitude!, longitude!));
    } catch (e) {
      Utils.showLog("Error in getCurrentLocation: $e");
    }

    isLoading = false;
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
}
