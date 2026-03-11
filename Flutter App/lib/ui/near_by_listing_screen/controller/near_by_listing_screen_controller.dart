import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/utils/utils.dart';

class NearByListingScreenController extends GetxController {
  Set<Marker> markers = {};
  double? latitude;
  double? longitude;
  late GoogleMapController mapController;
  final destinationAddressController = TextEditingController();
  final destinationAddressFocusNode = FocusNode();
  String? finalAddress;
  String destinationAddress = '';
  String currentAddress = '';
  Map<String, dynamic> arguments = Get.arguments ?? {};
  String? addressStreet;
  String? addressCity;
  String? addressState;
  String? addressCountry;
  String? addressPostalCode;
  String? addressName;
  bool isLoading = true;
  bool isEdit = false;
  // bool filterScreen = false;
  bool homeLocation = false;
  bool search = false;
  bool popular = false;
  bool mostLike = false;
  bool subcategory = false;
  dynamic adsData;
  MapController controller = Get.find();

  @override
  void onInit() {
    // TODO: implement onInit
    super.onInit();
    adsData = arguments['ad'];
    // filterScreen = arguments['filterScreen'] ?? false;
    homeLocation = arguments['homeLocation'] ?? false;
    search = arguments['search'] ?? false;
    popular = arguments['popular'] ?? false;
    mostLike = arguments['mostLike'] ?? false;
    isEdit = arguments['editApi'] ?? false;
    subcategory = arguments['subcategory'] ?? false;
    Utils.showLog("controller.addressCity${controller.addressCity}");
    Utils.showLog("editApi  ::::::::::::::::$isEdit");
    Utils.showLog("search  ::::::::::::::::$search");
    Utils.showLog("popular  ::::::::::::::::$popular");
    Utils.showLog("mostLike  ::::::::::::::::$mostLike");
    Utils.showLog("subcategory0000000000000000:::::$subcategory");

    // getCurrentLocation();

    Utils.showLog("near arguments::::::::::$arguments");
    // Utils.showLog("filterScreen||||||||||||||||||||||$filterScreen");
    Utils.showLog("homeLocation||||||||||||||||||||||$homeLocation");
  }

  Map<String, dynamic> locationDataForApi() {
    return {
      'country': Get.find<MapController>().addressCountry ?? '',
      'state': Get.find<MapController>().addressState ?? '',
      'city': Get.find<MapController>().addressCity ?? '',
      'latitude': Get.find<MapController>().latitude ?? 0.0,
      'longitude': Get.find<MapController>().longitude ?? 0.0,
      'fullAddress': "${Get.find<MapController>().addressStreet},${Get.find<MapController>().addressName}",
      'ne_lat': "${Get.find<MapController>().selectedBounds!["ne_lat"]}",
      'ne_lng': "${Get.find<MapController>().selectedBounds!["ne_lng"]}",
      'sw_lat': "${Get.find<MapController>().selectedBounds!["sw_lat"]}",
      'sw_lng': "${Get.find<MapController>().selectedBounds!["sw_lng"]}",
      'range': "${Get.find<MapController>().radiusKm}",
    };
  }
}
