import 'dart:convert';
import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/location_screen/api/get_country_api.dart';
import 'package:listify/ui/location_screen/model/country_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';
import 'package:map_location_picker/map_location_picker.dart';

// class LocationScreenController extends GetxController {
//   bool isLoading = false;
//   bool isPaginationLoading = false;
//   CountryResponseModel? countryResponseModel;
//   List<Datum> allCountryList = [];
//   ScrollController scrollController = ScrollController();
//
//   @override
//   void onInit() {
//     init();
//     super.onInit();
//   }
//
//   init() async {
//     Utils.showLog("Enter location screen Controller");
//     scrollController.addListener(onAllCountryPagination);
//
//     GetCountryApi.startPagination = 0;
//     await getAllCountry();
//   }
//
//   /// get all country api
//   getAllCountry() async {
//     isLoading = true;
//     update([Constant.idGetCountry]);
//     countryResponseModel = await GetCountryApi.callApi();
//     allCountryList.clear();
//     allCountryList.addAll(countryResponseModel?.data ?? []);
//
//     Utils.showLog("subscriptionPlan list data $allCountryList");
//
//     isLoading = false;
//     update([Constant.idGetCountry]);
//   }
//
//   /// refresh
//   onRefresh() async {
//     GetCountryApi.startPagination = 0;
//     allCountryList.clear();
//
//     getAllCountry();
//   }
//
//   /// pagination
//   Future<void> onAllCountryPagination() async {
//     if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
//       isPaginationLoading = true;
//       update([Constant.idPagination]);
//
//       countryResponseModel = await GetCountryApi.callApi();
//       allCountryList.clear();
//       allCountryList.addAll(countryResponseModel?.data ?? []);
//
//       Utils.showLog("allCountryList pagination ::::: $allCountryList");
//
//       isPaginationLoading = false;
//       update([Constant.idPagination]);
//     }
//   }
// }

class LocationScreenController extends GetxController {
  bool isLoading = false;
  bool isPaginationLoading = false;

  CountryResponseModel? countryResponseModel;
  List<Datum> allCountryList = [];
  List<Datum> filteredCountryList = []; // <-- filtered list

  ScrollController scrollController = ScrollController();
  TextEditingController searchController = TextEditingController();
  bool isSearchPerformed = false; // 👈 Add this
  Map<String, dynamic> arguments = Get.arguments ?? {};
  List<Map<String, dynamic>> attributeDataList = [];
  // bool filterScreen = false;
  bool homeLocation = false;
  bool isEdit = false;

  bool search = false;
  bool popular = false;
  bool mostLike = false;
  bool subcategory = false;
  // NearByListingScreenController nearByListingScreenController = Get.put(NearByListingScreenController());

  String? mainImage;
  List<String>? selectedImages;
  late GoogleMapController mapController;
  // AllAds? adsData;

  @override
  void onInit() {
    init();
    super.onInit();
    // Utils.showLog("currentAddress location location screen :::::::::::${nearByListingScreenController.currentAddress}");
    // Utils.showLog("finalAddress location location screen :::::::::::${nearByListingScreenController.finalAddress}");
  }

  init() async {
    Utils.showLog("Enter location screen Controller");

    log("arguments api:::::::::::::::::::::$arguments");

    scrollController.addListener(onAllCountryPagination);

    GetCountryApi.startPagination = 0;
    await getAllCountry();
    // adsData = arguments['ad'];
    isEdit = arguments['editApi'] ?? false;
    // filterScreen = arguments['filterScreen'] ?? false;
    homeLocation = arguments['homeLocation'] ?? false;
    search = arguments['search'] ?? false;
    popular = arguments['popular'] ?? false;
    mostLike = arguments['mostLike'] ?? false;
    subcategory = arguments['subcategory'] ?? false;

    // Utils.showLog("filterScreen)))))))))))))))))))$filterScreen");
    Utils.showLog("homeLocation)))))))))))))))))))$homeLocation");
    Utils.showLog("arguments:::::::::::$arguments");
    Utils.showLog("arguments location screen:::::::::::$arguments");
    Utils.showLog("editApi  ::::::::::::::::$isEdit");
    Utils.showLog("search  ::::::::::::::::$search");
    Utils.showLog("popular  ::::::::::::::::$popular");
    Utils.showLog("mostLike  ::::::::::::::::$mostLike");
    Utils.showLog("subcategory==============::$subcategory");

    // // ✅ Properly decode received attribute strings
    // final attrStrings = arguments['attributes'];
    // if (attrStrings is List) {
    //   attributeDataList = attrStrings.map<Map<String, dynamic>>((e) => jsonDecode(e)).toList();
    //
    //   for (var attr in attributeDataList) {
    //     Utils.showLog("Attr → ${attr['name']} = ${attr['value']} (Image: ${attr['image']})");
    //   }
    // }

    final attrStrings = arguments['attributes'];
    if (attrStrings is List) {
      attributeDataList = attrStrings.map<Map<String, dynamic>>((e) {
        if (e is String) {
          // Decode only if it's a JSON string
          return jsonDecode(e) as Map<String, dynamic>;
        } else if (e is Map<String, dynamic>) {
          // Already a Map
          return e;
        } else {
          return {};
        }
      }).toList();

      for (var attr in attributeDataList) {
        Utils.showLog("Attr → ${attr['name']} = ${attr['value']} (Image: ${attr['image']})");
      }
    }

    Utils.showLog('attributeDataList       :::::::::::::   $attributeDataList');
    final mainImage = arguments['mainImage'];
    final selectedImages = arguments['selectedImages'];
    final title = arguments['title'];
    final subtitle = arguments['subtitle'];
    final price = arguments['price'];
    final description = arguments['description'];
    final categoryId = arguments['categoryId'];

    Utils.showLog('Received Product: $title | $subtitle | $price | $description  |  $categoryId  |  $mainImage  |  $selectedImages');
    Utils.showLog('attributeDataList location screen :::::: $attributeDataList');

    Utils.showLog("Main Image:: $mainImage ||| selected image :: $selectedImages");
    // nearByListingScreenController.getCurrentLocation();
  }

  /// get all country api
  getAllCountry() async {
    isLoading = true;
    update([Constant.idGetCountry]);
    countryResponseModel = await GetCountryApi.callApi();

    allCountryList.clear();
    allCountryList.addAll(countryResponseModel?.data ?? []);
    filteredCountryList = List.from(allCountryList); // Initialize with full list

    isLoading = false;
    update([Constant.idGetCountry]);
  }

  /// search country by name
  void onSearchCountry(String query) {
    isSearchPerformed = query.isNotEmpty; // 👈 Track if search was done

    if (query.isEmpty) {
      filteredCountryList = List.from(allCountryList);
    } else {
      filteredCountryList = allCountryList.where((country) => (country.name ?? '').toLowerCase().contains(query.toLowerCase())).toList();
    }

    update([Constant.idGetCountry]);
  }

  /// refresh
  onRefresh() async {
    GetCountryApi.startPagination = 0;
    allCountryList.clear();
    await getAllCountry();
  }

  /// pagination
  Future<void> onAllCountryPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      isPaginationLoading = true;
      update([Constant.idPagination]);

      countryResponseModel = await GetCountryApi.callApi();
      allCountryList.addAll(countryResponseModel?.data ?? []);
      filteredCountryList = List.from(allCountryList);

      isPaginationLoading = false;
      update([Constant.idGetCountry, Constant.idPagination]);
    }
  }
}
