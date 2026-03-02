// import 'package:file_picker/file_picker.dart';
// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:listify/ui/add_product_screen/api/category_attributes_api.dart';
// import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
// import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart' hide Attribute;
// import 'package:listify/ui/sub_categories_screen/service/select_bus_service.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/utils.dart';
//
// import 'gloable_controller.dart';
//
// class ProductFilterScreenController extends GetxController {
//   String? categoryId;
//   String? categoryTitle; // 👈 NEW
//   String? categoryImage; // 👈 NEW
//   /// price
//   final TextEditingController minPriceController = TextEditingController();
//   final TextEditingController maxPriceController = TextEditingController();
//
//   /// attribute schema (keep fields; we clear only selections)
//   List<Attribute> attributeDataList = [];
//
//   /// selections (cleared on Reset)
//   final Map<int, int> selectedRadioIndices = {}; // fieldType 4
//   final Map<int, String> textFieldValues = {}; // fieldType 1/2
//   final Map<int, List<String>> selectedChipValues = {}; // fieldType 6/7
//   final Map<int, PlatformFile> selectedFiles = {}; // fieldType 3
//   final Map<int, String> selectedDropdownValues = {}; // fieldType 5
//   Map<String, dynamic> arguments = Get.arguments ?? {};
//   List<AllAds>? adsData;
//   bool search = false;
//   String? title;
//
//   /// posted since
//   String? postedSince = "all_time";
//   Worker? busWorker;
//   bool isLoading = false;
//   CategoryAttributesResponseModel? categoryAttributesResponseModel;
//
//   @override
//   void onInit() {
//     super.onInit();
//     if (Get.isRegistered<SelectionBus>()) {
//       final bus = Get.find<SelectionBus>();
//       categoryTitle = bus.selectedCategoryTitle.value; // 👈 use .value
//       categoryImage = bus.selectedCategoryImage.value; // 👈 use .value
//     }
//     // 🔁 LISTEN to SelectionBus changes (Obx નહિ; controller-level worker)
//     if (Get.isRegistered<SelectionBus>()) {
//       final bus = Get.find<SelectionBus>();
//       busWorker = ever<String?>(
//         bus.selectedCategoryId,
//         (id) {
//           // sync all 3
//           categoryId = id;
//           categoryTitle = bus.selectedCategoryTitle.value;
//           categoryImage = bus.selectedCategoryImage.value;
//
//           // header re-build
//           update([Constant.idCategoryHeader]); // 👈 NEW ID for GetBuilder
//
//           if (id == null || id.isEmpty) {
//             attributeDataList.clear();
//             update([Constant.idAllAds]);
//             return;
//           }
//           getCategoryAttribute();
//         },
//       );
//     }
//     init();
//   }
//
//   Future<void> init() async {
//     final args = Get.arguments as Map<String, dynamic>? ?? {};
//     categoryId = args['categoryId'];
//     adsData = args['ad'];
//     search = args['search'] ?? false;
//     title = "${Get.find<SelectionBus>().selectedCategoryTitle}" ?? "Select Category";
//     Utils.showLog("search filter screen :::$search");
//     Utils.showLog("category  :::$categoryId");
//
//     await getCategoryAttribute();
//   }
//
//   /// attributes schema
//   // Future<void> getCategoryAttribute() async {
//   //   isLoading = true;
//   //   update([Constant.idAllAds]);
//   //
//   //   Utils.showLog("last category id user for attribute api ::: $categoryId");
//   //   categoryAttributesResponseModel = await CategoryAttributesApi.callApi(categoryId: categoryId ?? Get.find<SelectionBus>().selectedCategoryId);
//   //
//   //   if (categoryAttributesResponseModel?.status == true) {
//   //     attributeDataList = categoryAttributesResponseModel?.data ?? [];
//   //   }
//   //
//   //   Utils.showLog("fetch category vise attribute data : ${attributeDataList.length}");
//   //
//   //   isLoading = false;
//   //   update([Constant.idAllAds]);
//   // }
//   Future<void> getCategoryAttribute() async {
//     final id = categoryId ?? Get.find<SelectionBus>().selectedCategoryId.value;
//     if (id == null || id.isEmpty) {
//       Utils.showLog("getCategoryAttribute(): categoryId is null/empty, skipping");
//       return;
//     }
//
//     isLoading = true;
//     update([Constant.idAllAds]);
//
//     try {
//       Utils.showLog("Attribute API → categoryId: $id");
//       categoryAttributesResponseModel = await CategoryAttributesApi.callApi(categoryId: id);
//
//       attributeDataList = [];
//       if (categoryAttributesResponseModel?.status == true) {
//         attributeDataList = categoryAttributesResponseModel?.data ?? [];
//       }
//       Utils.showLog("Attributes fetched: ${attributeDataList.length}");
//     } catch (e) {
//       Utils.showLog("getCategoryAttribute() error: $e");
//     } finally {
//       isLoading = false;
//       update([Constant.idAllAds]); // 🔁 UI refresh
//     }
//   }
//
//   /// radio selection
//   void radioSelection(int attributeIndex, int selectedIndex) {
//     selectedRadioIndices[attributeIndex] = selectedIndex;
//     update();
//     Utils.showLog("Selected value for field $attributeIndex is index $selectedIndex → ${attributeDataList[attributeIndex].values?[selectedIndex]}");
//   }
//
//   /// text field values
//   void updateTextValue(int attributeIndex, String value) {
//     textFieldValues[attributeIndex] = value;
//     update();
//     Utils.showLog("Text input for $attributeIndex: $value");
//   }
//
//   void updateDropdownValue(int attributeIndex, String value) {
//     selectedDropdownValues[attributeIndex] = value;
//     update([Constant.idAllAds]);
//     Utils.showLog("Dropdown value for $attributeIndex: $value");
//   }
//
//   void setPostedSince(String value) {
//     postedSince = value;
//     update([Constant.idFilterUpdate]);
//   }
//
//   /// filter API (unchanged, just compiles selected values)
//   List<AllAds> categoryWiseProductList = [];
//
//   Future<void> categoryWiseFilterProductApi() async {
//     final List<Map<String, dynamic>> selectedAttributes = [];
//     for (int i = 0; i < attributeDataList.length; i++) {
//       final attr = attributeDataList[i];
//       String? value;
//
//       switch (attr.fieldType) {
//         case 1:
//         case 2:
//           value = textFieldValues[i];
//           break;
//         case 3:
//           value = selectedFiles[i]?.name;
//           break;
//         case 4:
//           final sel = selectedRadioIndices[i];
//           if (sel != null && (attr.values?.length ?? 0) > sel) {
//             value = attr.values![sel];
//           }
//           break;
//         case 5:
//           value = selectedDropdownValues[i];
//           break;
//         case 6:
//         case 7:
//           if (selectedChipValues[i] != null) {
//             value = selectedChipValues[i]!.join(", ");
//           }
//           break;
//         default:
//           break;
//       }
//
//       if (value != null && value.isNotEmpty) {
//         selectedAttributes.add({"name": attr.name ?? "Unknown", "value": value});
//       }
//     }
//
//     final payload = FilterPayload(
//       // categoryId: categoryId == null ? Get.find<SelectionBus>().selectedCategoryId : categoryId,
//       categoryId: categoryId ?? Get.find<SelectionBus>().selectedCategoryId.value,
//
//       country: GlobalController.locationData['selectedCountry'],
//       state: GlobalController.locationData['selectedState'],
//       city: GlobalController.locationData['selectedCity'],
//       minPrice: minPriceController.text.trim(),
//       maxPrice: maxPriceController.text.trim(),
//       latitude: GlobalController.locationData['latitude']?.toString(),
//       longitude: GlobalController.locationData['longitude']?.toString(),
//       postedSince: postedSince,
//       attributes: selectedAttributes,
//     );
//
//     Get.back(result: payload.toMap());
//     Future.microtask(() => Get.find<SelectionBus>().clearSelection());
//   }
//
//   Future<void> popularProductFilterApi() async {
//     Utils.showLog("search api call");
//     final List<Map<String, dynamic>> selectedAttributes = [];
//     for (int i = 0; i < attributeDataList.length; i++) {
//       final attr = attributeDataList[i];
//       String? value;
//
//       switch (attr.fieldType) {
//         case 1:
//         case 2:
//           value = textFieldValues[i];
//           break;
//         case 3:
//           value = selectedFiles[i]?.name;
//           break;
//         case 4:
//           final sel = selectedRadioIndices[i];
//           if (sel != null && (attr.values?.length ?? 0) > sel) {
//             value = attr.values![sel];
//           }
//           break;
//         case 5:
//           value = selectedDropdownValues[i];
//           break;
//         case 6:
//         case 7:
//           if (selectedChipValues[i] != null) {
//             value = selectedChipValues[i]!.join(", ");
//           }
//           break;
//         default:
//           break;
//       }
//
//       if (value != null && value.isNotEmpty) {
//         selectedAttributes.add({"name": attr.name ?? "Unknown", "value": value});
//       }
//     }
//
//     final payload = SearchFilterPayload(
//       // categoryId: categoryId == null ? Get.find<SelectionBus>().selectedCategoryId : categoryId,
//       categoryId: Get.find<SelectionBus>().selectedCategoryId.value,
//
//       country: GlobalController.locationData['selectedCountry'],
//       state: GlobalController.locationData['selectedState'],
//       city: GlobalController.locationData['selectedCity'],
//       minPrice: minPriceController.text.trim(),
//       maxPrice: maxPriceController.text.trim(),
//       latitude: GlobalController.locationData['latitude']?.toString(),
//       longitude: GlobalController.locationData['longitude']?.toString(),
//       postedSince: postedSince,
//       attributes: selectedAttributes,
//     );
//
//     Get.back(result: payload.toMap());
//     Future.microtask(() => Get.find<SelectionBus>().clearSelection());
//   }
//
//   @override
//   void onClose() {
//     GlobalController.locationData['selectedCity'] = null;
//     GlobalController.locationData['selectedCountry'] = null;
//     GlobalController.locationData['selectedState'] = null;
//     // Get.find<SelectionBus>().selectedCategoryId == null;
//     if (Get.isRegistered<SelectionBus>()) {
//       Get.find<SelectionBus>().clearSelection();
//     }
//     busWorker?.dispose();
//     busWorker = null;
//     super.onClose();
//   }
// }
//
// class FilterPayload {
//   final String? categoryId;
//   final String? country;
//   final String? state;
//   final String? city;
//   final String? minPrice;
//   final String? maxPrice;
//   final String? latitude;
//   final String? longitude;
//   final String? postedSince;
//   final List<Map<String, dynamic>> attributes;
//
//   const FilterPayload({
//     this.categoryId,
//     this.country,
//     this.state,
//     this.city,
//     this.minPrice,
//     this.maxPrice,
//     this.latitude,
//     this.longitude,
//     this.postedSince,
//     this.attributes = const [],
//   });
//
//   Map<String, dynamic> toMap() => {
//         "categoryId": categoryId,
//         "country": country,
//         "state": state,
//         "city": city,
//         "minPrice": minPrice,
//         "maxPrice": maxPrice,
//         "latitude": latitude,
//         "longitude": longitude,
//         "postedSince": postedSince,
//         "attributes": attributes,
//       };
// }
//
// class SearchFilterPayload {
//   final String? categoryId;
//   final String? country;
//   final String? state;
//   final String? city;
//   final String? minPrice;
//   final String? maxPrice;
//   final String? latitude;
//   final String? longitude;
//   final String? postedSince;
//   final List<Map<String, dynamic>> attributes;
//
//   const SearchFilterPayload({
//     this.categoryId,
//     this.country,
//     this.state,
//     this.city,
//     this.minPrice,
//     this.maxPrice,
//     this.latitude,
//     this.longitude,
//     this.postedSince,
//     this.attributes = const [],
//   });
//
//   Map<String, dynamic> toMap() => {
//         "categoryId": categoryId,
//         "country": country,
//         "state": state,
//         "city": city,
//         "minPrice": minPrice,
//         "maxPrice": maxPrice,
//         "latitude": latitude,
//         "longitude": longitude,
//         "postedSince": postedSince,
//         "attributes": attributes,
//       };
// }
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/add_product_screen/api/category_attributes_api.dart';
import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart'
    hide Attribute;
import 'package:listify/ui/sub_categories_screen/service/select_bus_service.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

import 'gloable_controller.dart';

class ProductFilterScreenController extends GetxController {
  String? categoryId;
  String? categoryTitle;
  String? categoryImage;

  /// price
  final TextEditingController minPriceController = TextEditingController();
  final TextEditingController maxPriceController = TextEditingController();

  /// attribute schema (keep fields; we clear only selections)
  List<Attribute> attributeDataList = [];

  /// selections (cleared on Reset)
  final Map<int, int> selectedRadioIndices = {}; // fieldType 4
  final Map<int, String> textFieldValues = {}; // fieldType 1/2
  final Map<int, List<String>> selectedChipValues = {}; // fieldType 6/7
  final Map<int, PlatformFile> selectedFiles = {}; // fieldType 3
  final Map<int, String> selectedDropdownValues = {}; // fieldType 5
  Map<String, dynamic> arguments = Get.arguments ?? {};
  List<AllAds>? adsData;
  bool search = false;
  bool popular = false;
  bool mostLike = false;
  bool filterScreen = false;
  bool subcategory = false;
  String? title;


  /// ----------------- NEW: Local (screen-only) location state -----------------
  String? tempCountry;
  String? tempState;
  String? tempCity;
  String? tempLatitude;
  String? tempLongitude;
  String? tempRange;
  dynamic tempNeLat;
  dynamic tempNeLng;
  dynamic tempSwLat;
  dynamic tempSwLng;

  /// posted since
  String? postedSince = "all_time";
  Worker? busWorker;
  bool isLoading = false;
  CategoryAttributesResponseModel? categoryAttributesResponseModel;

  @override
  void onInit() {
    super.onInit();

    // પહેલા initial data લો
    _loadInitialCategoryData();

    // પછી listener set કરો
    _setupBusListener();

    syncLocalLocationFromDatabase();

    // init call કરો
    init();
  }

  void _loadInitialCategoryData() {
    if (Get.isRegistered<SelectionBus>()) {
      final bus = Get.find<SelectionBus>();
      categoryId = bus.selectedCategoryId.value;
      categoryTitle = bus.selectedCategoryTitle.value;
      categoryImage = bus.selectedCategoryImage.value;

      Utils.showLog(
          "Initial category data loaded: ID=$categoryId, Title=$categoryTitle");
    }
  }

  // ----------------- NEW: helpers for local location -----------------
  void syncLocalLocationFromDatabase() {
    // Database.selectedLocation માંથી વાંચીને local temp માં કૉપી કરો
    tempCountry   = _dbStr('selectedCountry');
    tempState     = _dbStr('selectedState');
    tempCity      = _dbStr('selectedCity');
    tempLatitude  = _dbStr('latitude');
    tempLongitude = _dbStr('longitude');
    tempRange     = _dbStr('range');

    // bounds (optional / if available)
    tempNeLat = GlobalController.locationData['ne_lat'];
    tempNeLng = GlobalController.locationData['ne_lng'];
    tempSwLat = GlobalController.locationData['sw_lat'];
    tempSwLng = GlobalController.locationData['sw_lng'];

    update([Constant.idLocationUpdate]);
  }
  void clearLocalLocationOnly() {
    tempCountry = null;
    tempState   = null;
    tempCity    = null;
    tempLatitude  = null;
    tempLongitude = null;
    tempRange     = null;

    tempNeLat = null;
    tempNeLng = null;
    tempSwLat = null;
    tempSwLng = null;

    update([Constant.idLocationUpdate]);
  }
  String? _dbStr(String key) {
    final v = Database.selectedLocation[key];
    if (v == null) return null;
    final s = v.toString().trim();
    return s.isEmpty ? null : s;
  }

  void _setupBusListener() {
    if (Get.isRegistered<SelectionBus>()) {
      final bus = Get.find<SelectionBus>();
      busWorker = ever<String?>(
        bus.selectedCategoryId,
            (id) {
          Utils.showLog("Bus listener triggered: categoryId=$id");

          // keep all 3 in sync
          categoryId    = id;
          categoryTitle = bus.selectedCategoryTitle.value;
          categoryImage = bus.selectedCategoryImage.value;

          // 🔁 Rebuild header
          update([Constant.idCategoryHeader]);

          if (id == null || id.isEmpty) {
            attributeDataList.clear();
            categoryAttributesResponseModel = null;
            update([Constant.idAllAds]);
            return;
          }

          getCategoryAttribute();
        },
      );
    }
  }
  Future<void> init() async {
    final args = Get.arguments as Map<String, dynamic>? ?? {};

    // Arguments થી પણ category data લઈ શકો
    if (args.containsKey('categoryId')) {
      categoryId = args['categoryId'];
    }

    adsData = args['ad'];
    search = args['search'] ?? false;
    popular = args['popular'] ?? false;
    mostLike = args['mostLike'] ?? false;
    filterScreen = args['filterScreen'] ?? false;
    subcategory = args['subcategory'] ?? false;

    // Title set કરો
    if (categoryTitle != null && categoryTitle!.isNotEmpty) {
      title = categoryTitle;
    } else {
      title = "Select Category";
    }

    Utils.showLog("search filter screen :::$search");
    Utils.showLog("popular filter screen :::$popular");
    Utils.showLog("mostLike filter screen :::$mostLike");
    Utils.showLog("filterScreen :::$filterScreen");
    Utils.showLog("subcategory???????????? :::$subcategory");
    Utils.showLog("category  :::$categoryId");
    Utils.showLog("category title :::$categoryTitle");

    // જો category data છે તો attributes fetch કરો
    if (categoryId != null && categoryId!.isNotEmpty) {
      await getCategoryAttribute();
    }
  }

  Future<void> getCategoryAttribute() async {
    final id = categoryId ?? Get.find<SelectionBus>().selectedCategoryId.value;
    if (id == null || id.isEmpty) {
      Utils.showLog(
          "getCategoryAttribute(): categoryId is null/empty, skipping");
      return;
    }

    isLoading = true;
    update([Constant.idAllAds]);

    try {
      Utils.showLog("Attribute API → categoryId: $id");
      categoryAttributesResponseModel =
          await CategoryAttributesApi.callApi(categoryId: id);

      attributeDataList = [];
      if (categoryAttributesResponseModel?.status == true) {
        attributeDataList = categoryAttributesResponseModel?.data ?? [];
      }
      Utils.showLog("Attributes fetched: ${attributeDataList.length}");
    } catch (e) {
      Utils.showLog("getCategoryAttribute() error: $e");
    } finally {
      isLoading = false;
      update([Constant.idAllAds]);
    }
  }



  // ✅ RESET: everything + only LOCAL location clear (Database મા હાથ નહિ લગાડવો)
  void clearFilters({
    bool clearCategory = false,
    bool localLocationOnly = true, // ← default local-only
    bool alsoClearGlobalLocation = false, // keep false to protect DB
  }) {
    postedSince = "all_time";

    minPriceController.clear();
    maxPriceController.clear();

    selectedRadioIndices.clear();
    textFieldValues.clear();
    selectedChipValues.clear();
    selectedFiles.clear();
    selectedDropdownValues.clear();

    attributeDataList.clear();
    categoryAttributesResponseModel = null;

    if (clearCategory) {
      categoryId    = null;
      categoryTitle = null;
      categoryImage = null;
      title         = "Select Category";
      if (Get.isRegistered<SelectionBus>()) {
        Get.find<SelectionBus>().clearSelection();
      }
      update([Constant.idCategoryHeader]);
    }

    if (localLocationOnly) {
      clearLocalLocationOnly(); // 👈 Databaseમાં nothing changes
    } else if (alsoClearGlobalLocation) {
      // (OPTIONAL path) — જો ક્યારેય global delete કરવું હોય તો
      GlobalController.locationData['selectedCity']    = null;
      GlobalController.locationData['selectedCountry'] = null;
      GlobalController.locationData['selectedState']   = null;
      GlobalController.locationData['latitude']        = null;
      GlobalController.locationData['longitude']       = null;
      GlobalController.locationData['range']           = null;
      GlobalController.locationData['ne_lat']          = null;
      GlobalController.locationData['ne_lng']          = null;
      GlobalController.locationData['sw_lat']          = null;
      GlobalController.locationData['sw_lng']          = null;

      // ❗️Database.selectedLocation ને NOT touching per requirement
      clearLocalLocationOnly();
    }

    update([
      Constant.idFilterUpdate,
      Constant.idAllAds,
      Constant.idPagination,
      Constant.idLocationUpdate, // location pill refresh
    ]);

    Utils.showLog('Filters cleared. Category cleared: $clearCategory | localLocationOnly: $localLocationOnly');
  }

  // ----------------- Payload builders (use LOCAL → fallback DB) -----------------
  String? _locOrDb(String? local, String key) => local ?? _dbStr(key);
  /// radio selection
  void radioSelection(int attributeIndex, int selectedIndex) {
    selectedRadioIndices[attributeIndex] = selectedIndex;
    update();
    Utils.showLog(
        "Selected value for field $attributeIndex is index $selectedIndex → ${attributeDataList[attributeIndex].values?[selectedIndex]}");
  }

  /// text field values
  void updateTextValue(int attributeIndex, String value) {
    textFieldValues[attributeIndex] = value;
    update();
    Utils.showLog("Text input for $attributeIndex: $value");
  }

  void updateDropdownValue(int attributeIndex, String value) {
    selectedDropdownValues[attributeIndex] = value;
    update([Constant.idAllAds]);
    Utils.showLog("Dropdown value for $attributeIndex: $value");
  }

  void setPostedSince(String value) {
    postedSince = value;
    update([Constant.idFilterUpdate]);
  }

  /// filter API (unchanged, just compiles selected values)
  List<AllAds> categoryWiseProductList = [];

  Future<void> categoryWiseFilterProductApi() async {
    final List<Map<String, dynamic>> selectedAttributes = [];
    for (int i = 0; i < attributeDataList.length; i++) {
      final attr = attributeDataList[i];
      String? value;

      switch (attr.fieldType) {
        case 1:
        case 2:
          value = textFieldValues[i];
          break;
        case 3:
          value = selectedFiles[i]?.name;
          break;
        case 4:
          final sel = selectedRadioIndices[i];
          if (sel != null && (attr.values?.length ?? 0) > sel) {
            value = attr.values![sel];
          }
          break;
        case 5:
          value = selectedDropdownValues[i];
          break;
        case 6:
        case 7:
          if (selectedChipValues[i] != null) {
            value = selectedChipValues[i]!.join(", ");
          }
          break;
        default:
          break;
      }

      if (value != null && value.isNotEmpty) {
        selectedAttributes
            .add({"name": attr.name ?? "Unknown", "value": value});
      }
    }

    // final payload = FilterPayload(
    //   categoryId: categoryId ?? Get.find<SelectionBus>().selectedCategoryId.value,
    //   country: Database.selectedLocation['selectedCountry'].toString(),
    //   state: Database.selectedLocation['selectedState'].toString(),
    //   city: Database.selectedLocation['selectedCity'].toString(),
    //   minPrice: minPriceController.text.trim(),
    //   maxPrice: maxPriceController.text.trim(),
    //   latitude: Database.selectedLocation['latitude']?.toString(),
    //   longitude: Database.selectedLocation['longitude']?.toString(),
    //   postedSince: postedSince,
    //   range: Database.selectedLocation['range'].toString(),
    //   attributes: selectedAttributes,
    //   rangeLatitude: GlobalController.locationData['ne_lat'],
    //   rangeLongitude: GlobalController.locationData['ne_lng'],
    // );

    final payload = FilterPayload(
      categoryId: categoryId ?? Get.find<SelectionBus>().selectedCategoryId.value,
      country:    _locOrDb(tempCountry,   'selectedCountry'),
      state:      _locOrDb(tempState,     'selectedState'),
      city:       _locOrDb(tempCity,      'selectedCity'),
      minPrice:   minPriceController.text.trim(),
      maxPrice:   maxPriceController.text.trim(),
      latitude:   _locOrDb(tempLatitude,  'latitude'),
      longitude:  _locOrDb(tempLongitude, 'longitude'),
      postedSince: postedSince,
      range:      _locOrDb(tempRange,     'range'),
      attributes: selectedAttributes,
      rangeLatitude: tempNeLat ?? GlobalController.locationData['ne_lat'],
      rangeLongitude: tempNeLng ?? GlobalController.locationData['ne_lng'],
    );

    Get.back(result: payload.toMap());
    // Future.microtask(() => Get.find<SelectionBus>().clearSelection());
  }

  Future<void> popularProductFilterApi() async {
    Utils.showLog("search api call");
    final List<Map<String, dynamic>> selectedAttributes = [];
    for (int i = 0; i < attributeDataList.length; i++) {
      final attr = attributeDataList[i];
      String? value;

      switch (attr.fieldType) {
        case 1:
        case 2:
          value = textFieldValues[i];
          break;
        case 3:
          value = selectedFiles[i]?.name;
          break;
        case 4:
          final sel = selectedRadioIndices[i];
          if (sel != null && (attr.values?.length ?? 0) > sel) {
            value = attr.values![sel];
          }
          break;
        case 5:
          value = selectedDropdownValues[i];
          break;
        case 6:
        case 7:
          if (selectedChipValues[i] != null) {
            value = selectedChipValues[i]!.join(", ");
          }
          break;
        default:
          break;
      }

      if (value != null && value.isNotEmpty) {
        selectedAttributes
            .add({"name": attr.name ?? "Unknown", "value": value});
      }
    }

    Utils.showLog(
        "GlobalController.locationData['selectedState'],${GlobalController.locationData['selectedState']}");
    Utils.showLog(
        "GlobalController.locationData['range'],${GlobalController.locationData['range']}");
    Utils.showLog(
        "Get.find<SelectionBus>().selectedCategoryId.value${Get.find<SelectionBus>().selectedCategoryId.value}");
    // final payload = SearchFilterPayload(
    //   categoryId: Get.find<SelectionBus>().selectedCategoryId.value,
    //   country: Database.selectedLocation['selectedCountry'].toString(),
    //   state: Database.selectedLocation['selectedState'].toString(),
    //   city: Database.selectedLocation['selectedCity'].toString(),
    //   minPrice: minPriceController.text.trim(),
    //   maxPrice: maxPriceController.text.trim(),
    //   latitude: Database.selectedLocation['latitude']?.toString(),
    //   longitude: Database.selectedLocation['longitude']?.toString(),
    //   postedSince: postedSince,
    //   range: Database.selectedLocation['range'].toString(),
    //   attributes: selectedAttributes,
    //   rangeLatitude: GlobalController.locationData['ne_lat'],
    //   rangeLongitude: GlobalController.locationData['ne_lng'],
    // );


    final payload = SearchFilterPayload(
      categoryId: Get.find<SelectionBus>().selectedCategoryId.value,
      country:    _locOrDb(tempCountry,   'selectedCountry'),
      state:      _locOrDb(tempState,     'selectedState'),
      city:       _locOrDb(tempCity,      'selectedCity'),
      minPrice:   minPriceController.text.trim(),
      maxPrice:   maxPriceController.text.trim(),
      latitude:   _locOrDb(tempLatitude,  'latitude'),
      longitude:  _locOrDb(tempLongitude, 'longitude'),
      postedSince: postedSince,
      range:      _locOrDb(tempRange,     'range'),
      attributes: selectedAttributes,
      rangeLatitude: tempNeLat ?? GlobalController.locationData['ne_lat'],
      rangeLongitude: tempNeLng ?? GlobalController.locationData['ne_lng'],
    );

    Utils.showLog(
        "GlobalController:::::::::::::::::${GlobalController.locationData['range']}");
    Utils.showLog("GlobalController.locationData['range'],${payload.toMap()}");
    Utils.showLog("Database.selectedLocation${Database.selectedLocation['range']}");

    Get.back(result: payload.toMap());
    // Future.microtask(() => Get.find<SelectionBus>().clearSelection());
  }

  @override
  void onClose() {
    GlobalController.locationData['selectedCity'] = null;
    GlobalController.locationData['selectedCountry'] = null;
    GlobalController.locationData['selectedState'] = null;
    GlobalController.locationData['range'] = null;
    GlobalController.locationData['ne_lat'] = null;
    GlobalController.locationData['ne_lng'] = null;
    GlobalController.locationData['sw_lat'] = null;
    GlobalController.locationData['sw_lng'] = null;

    if (Get.isRegistered<SelectionBus>()) {
      Get.find<SelectionBus>().clearSelection();
    }
    busWorker?.dispose();
    busWorker = null;
    super.onClose();
  }
}

class FilterPayload {
  final String? categoryId;
  final String? country;
  final String? state;
  final String? city;
  final String? minPrice;
  final String? maxPrice;
  final String? latitude;
  final String? longitude;
  final String? rangeLatitude;
  final String? rangeLongitude;
  final String? postedSince;
  final String? range;
  final List<Map<String, dynamic>> attributes;

  const FilterPayload({
    this.categoryId,
    this.country,
    this.state,
    this.city,
    this.minPrice,
    this.maxPrice,
    this.latitude,
    this.longitude,
    this.rangeLatitude,
    this.rangeLongitude,
    this.postedSince,
    this.range,
    this.attributes = const [],
  });

  Map<String, dynamic> toMap() => {
        "categoryId": categoryId,
        "country": country,
        "state": state,
        "city": city,
        "minPrice": minPrice,
        "maxPrice": maxPrice,
        "latitude": latitude,
        "longitude": longitude,
        "postedSince": postedSince,
        "range": range,
        "attributes": attributes,
      };
}

class SearchFilterPayload {
  final String? categoryId;
  final String? country;
  final String? state;
  final String? city;
  final String? minPrice;
  final String? maxPrice;
  final String? latitude;
  final String? longitude;
  final String? rangeLatitude;
  final String? rangeLongitude;
  final String? postedSince;
  final String? range;
  final List<Map<String, dynamic>> attributes;

  const SearchFilterPayload({
    this.categoryId,
    this.country,
    this.state,
    this.city,
    this.minPrice,
    this.maxPrice,
    this.latitude,
    this.longitude,
    this.rangeLatitude,
    this.rangeLongitude,
    this.postedSince,
    this.range,
    this.attributes = const [],
  });

  Map<String, dynamic> toMap() => {
        "categoryId": categoryId,
        "country": country,
        "state": state,
        "city": city,
        "minPrice": minPrice,
        "maxPrice": maxPrice,
        "latitude": latitude,
        "longitude": longitude,
        "postedSince": postedSince,
        "range": range,
        "attributes": attributes,
      };
}
