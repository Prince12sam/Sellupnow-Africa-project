
import 'dart:convert';
import 'dart:developer';
import 'package:cool_dropdown/controllers/dropdown_controller.dart';
import 'package:cool_dropdown/models/cool_dropdown_item.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/product_detail_screen/api/edit_product_detail_api.dart';
import 'package:listify/ui/product_detail_screen/model/product_detail_response_model.dart';
import 'package:listify/ui/product_pricing_screen/api/add_listing_api.dart';
import 'package:listify/ui/product_pricing_screen/model/add_listing_api_response_model.dart';
import 'package:listify/ui/product_pricing_screen/model/update_product_detail_response_model.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/custom/dialog/ad_listing_complete.dart';
import 'package:listify/utils/utils.dart' show Utils;

class ProductPricingScreenController extends GetxController {
  bool buyNowSwitch = true;
  bool auctionSwitch = false;

  String? selectedState;
  String? selectedCountry;
  String? selectedCity;

  bool isScheduleEnabled = false;
  bool isReservePrice = false;
  bool isOfferAllowed = false;
  DateTime? scheduledDate;

  // ⬇️ Duration values as INTs
  final List<int> durationOptions = [1, 3, 5,  7, 10, 30];
  final List<String> percentage = ['10%', '20%', '30%', '40%', '50%', '60%'];

  // Dropdown items/types
  List<CoolDropdownItem<int>> durationDropdownItems = [];
  List<CoolDropdownItem<String>> percentageDropdownItems = [];

  // Selected values
  int? selectedDurationDays;
  String? selectedPercentage;
  bool isEdit = false;

  // Controllers
  final durationDropDownController = DropdownController<int>();
  final percentageDropDownController = DropdownController<String>();
  final finalPriceController = TextEditingController();
  final itemPriceController = TextEditingController();
  TextEditingController quantity = TextEditingController();
  final TextEditingController auctionStartController = TextEditingController();
  final TextEditingController reservePriceController = TextEditingController();

  List<String> filledAttributeList = [];

  Map<String, dynamic> arguments = Get.arguments;

  String? buyNowPrice;
  String? finalPrice;
  String? bidDuration;
  String? auctionStartingBid;
  String? reservePrice;
  String? price;
  double? latitude;
  double? longitude;
  bool? selectCityScreen = false;
  CreateAdListingResponseModel? createAdListingResponseModel;
  UpdateProductDetailResponseModel? updateProductDetailResponseModel;
  Product? adsData;
  String? adId;
  List<String>? selectedImages;
  List<int>? removedGalleryIndexes;

  // Attributes payload
  List<Map<String, dynamic>>? attributesJsonStrings;

  Map<String, dynamic> originalAdData = {};

  @override
  void onInit() {
    init();
    quantity.addListener(() {
      update([Constant.switchUpdate]); // 🔄 triggers button color update instantly
    });

    super.onInit();
    Utils.showLog("price data argument::::::::::::$arguments");
    Utils.showLog("adsData?.isAuctionEnabled::::::::::::${adsData?.isAuctionEnabled}");
    Utils.showLog("adsData?.saleType::::::::::::${adsData?.saleType}");
    Utils.showLog("adsData?.reservePriceAmount::::::::::::${adsData?.reservePriceAmount}");
    Utils.showLog("adsData?.auctionStartingPrice::::::::::::${adsData?.auctionStartingPrice}");
    Utils.showLog("adsData?.auctionDurationDays::::::::::::${adsData?.auctionDurationDays}");
    removedGalleryIndexes = arguments['removedGalleryIndexes'];
    Utils.showLog("removedGalleryIndexes argument::::::::::::$removedGalleryIndexes");
  }

  ///6 last
  init() {
    attributesJsonStrings = (arguments['attributes'] as List<dynamic>).map<Map<String, dynamic>>((item) {
      if (item is String) {
        return (jsonDecode(item) as Map).cast<String, dynamic>();
      } else if (item is Map) {
        return item.cast<String, dynamic>();
      } else {
        return <String, dynamic>{};
      }
    }).toList();

    isEdit               = arguments['editApi'] ?? false;
    selectedImages       = (arguments['selectedImages'] as List?)?.cast<String>();
    adsData              = arguments['ad'];
    adId                 = arguments['adId'];
    removedGalleryIndexes= arguments['removedGalleryIndexes']?.cast<int>();

    selectedState   = arguments['selectedState'];
    selectedCountry = arguments['selectedCountry'];
    selectedCity    = arguments['selectedCity'];
    latitude        = (arguments['latitude']  as num?)?.toDouble();
    longitude       = (arguments['longitude'] as num?)?.toDouble();
    price           = (arguments['price'] ?? '').toString();
    selectCityScreen = arguments['selectCityScreen'] == true;
    itemPriceController.text = arguments['price'];


    Utils.showLog("Parsed attributesJsonStrings :: $attributesJsonStrings");
    isEdit = arguments['editApi'] ?? false;
    Utils.showLog("editApi  ::::::::::::::::$isEdit");
    selectedImages = (arguments['selectedImages'] as List?)?.cast<String>();
    log("selectedImages::::::::::::::::::::${selectedImages}");
    log("selectedImages::::::::::::::::::::${arguments['selectedImages']}");
    adsData = arguments['ad'];
    quantity.text = adsData?.availableUnits.toString() ?? "";
    Utils.showLog("quantity  ::::::::::::::::$quantity");
    Utils.showLog("quantity  ::::::::::::::::${adsData?.availableUnits.toString()}");
    Utils.showLog("isAuctionEnabled  ::::::::::::::::${adsData?.isAuctionEnabled}");
    Utils.showLog("auctionStartingPrice  ::::::::::::::::${adsData?.auctionStartingPrice}");
    Utils.showLog("reservePriceAmount  ::::::::::::::::${adsData?.reservePriceAmount}");
    Utils.showLog("removeGalleryIndexes  ::::::::::::::::${arguments['removeGalleryIndexes']}");
    selectedState = Get.arguments['selectedState'];
    selectedCountry = Get.arguments['selectedCountry'];
    selectedCity = Get.arguments['selectedCity'];
    longitude = Get.arguments['longitude'];
    adId = arguments['adId'];
    latitude = Get.arguments['latitude'];
    price = arguments['price'];
    selectCityScreen = arguments['selectCityScreen'];


    Utils.showLog("selectCityScreen boolean value: $selectCityScreen");
    Utils.showLog("selectedDurationDays: $selectedDurationDays");


    if(isEdit){


    if(adsData?.saleType==1) {
      buyNowSwitch=true;
      auctionSwitch=false;
      quantity.text = adsData?.availableUnits?.toString() ?? "";
      itemPriceController.text = price ?? '';
      buyNowPrice = itemPriceController.text;

      itemPriceController.text =adsData!.price.toString();

      if (adsData?.scheduledPublishDate != null) {
        isScheduleEnabled = true;
        scheduledDate = DateTime.parse(adsData!.scheduledPublishDate.toString()).toLocal();
      }

    }else{

      buyNowSwitch=false;
      auctionSwitch=true;
      auctionStartController.text = adsData?.auctionStartingPrice.toString()??"";

      selectedDurationDays = adsData?.auctionDurationDays;

      Utils.showLog("time>>>>>>>>>>>>>>>>>>>>>>>>>${adsData?.auctionDurationDays}");
      quantity.text = adsData?.availableUnits?.toString() ?? "";
      isReservePrice = adsData?.isReservePriceEnabled??false;

      reservePriceController.text = adsData?.reservePriceAmount?.toString()??"";


      if (adsData?.scheduledPublishDate != null) {
        isScheduleEnabled = true;
        scheduledDate = DateTime.parse(adsData!.scheduledPublishDate.toString()).toLocal();
      }
    }
  }


    /// edit feild send mate add start
    originalAdData = {
      'title'                 : (adsData?.title ?? '').toString(),
      'subTitle'              : (adsData?.subTitle ?? '').toString(),
      'description'           : (adsData?.description ?? '').toString(),
      'price'                 : (adsData?.price?.toString() ?? ''),
      'availableUnits'        : (adsData?.availableUnits?.toString() ?? ''),
      'isOfferAllowed'        : (adsData?.isOfferAllowed == true),
      'isAuctionEnabled'      : (adsData?.isAuctionEnabled == true),
      'isReservePriceEnabled' : (adsData?.isReservePriceEnabled == true),
      'primaryImage'          : (adsData?.primaryImage ?? '').toString(),
      'galleryImages'         : jsonEncode(adsData?.galleryImages ?? <String>[]),
      'location'              : jsonEncode({
        'country'  : adsData?.location?.country ?? '',
        'state'    : adsData?.location?.state ?? '',
        'city'     : adsData?.location?.city ?? '',
        'latitude' : adsData?.location?.latitude ?? 0.0,
        'longitude': adsData?.location?.longitude ?? 0.0,
      }),
      'attributes'            : jsonEncode((adsData?.attributes ?? [])
          .map((a) => {'name': a.name, 'value': a.value, 'image': a.image}).toList()),

      // 🔽 auction baselines
      'reservePriceAmount'    : (adsData?.reservePriceAmount?.toString() ?? ''),
      'auctionStartingPrice'  : (adsData?.auctionStartingPrice?.toString() ?? ''),
      'auctionDurationDays'   : (adsData?.auctionDurationDays?.toString() ?? ''),
      'scheduledPublishDate'  : (adsData?.scheduledPublishDate?.toString() ?? ''),

      // 🔹 NEW: saleType baseline (string)
      'saleType'              : (adsData?.saleType?.toString() ?? ''),
    };

    ///end............


    resetPricingFields();

    itemPriceController.text = price.toString();
    buyNowPrice = price.toString();

    selectedPercentage = percentage.first;
    calculateFinalPrice();

    final locationData = getLocationDataForApi();
    Utils.showLog("getLocationDataForApi JSON to send: $locationData");
    Utils.showLog("getLocationDataForApi JSON to send: ${jsonEncode(getLocationDataForApi())}");
  }

  static bool _deepEq(dynamic a, dynamic b) {
    if (a is Map && b is Map) {
      if (a.length != b.length) return false;
      for (final k in a.keys) {
        if (!b.containsKey(k)) return false;
        if (!_deepEq(a[k], b[k])) return false;
      }
      return true;
    }
    if (a is List && b is List) {
      if (a.length != b.length) return false;
      for (int i = 0; i < a.length; i++) {
        if (!_deepEq(a[i], b[i])) return false;
      }
      return true;
    }
    // numbers: 1 vs 1.0
    if (a is num && b is num) return (a - b).abs() < 1e-9;
    return a == b;
  }

  static Map<String, dynamic> _tryDecodeMap(String? jsonStr) {
    if (jsonStr == null || jsonStr.isEmpty) return {};
    try {
      final d = jsonDecode(jsonStr);
      return (d is Map) ? d.cast<String, dynamic>() : {};
    } catch (_) {
      return {};
    }
  }


  // -------------------- build diff (ONLY real changes) --------------------
  Map<String, dynamic> buildChangedFields() {
    final Map<String, dynamic> changed = {};

    final currentTitle       = (arguments['title'] ?? '').toString();
    final currentSubTitle    = (arguments['subtitle'] ?? '').toString();
    final currentDescription = (arguments['description'] ?? '').toString();
    final currentPrimary     = (arguments['mainImage'] ?? '').toString();

    final currentPrice       = (finalPrice ?? '').toString();
    final currentAvailUnits  = quantity.text.toString();
    final currentOffer       = isOfferAllowed;
    final currentAuction     = auctionSwitch;
    final currentReserveFlag = isReservePrice;

    // location deep compare
    final Map<String, dynamic> currentLoc = (selectCityScreen == true)
        ? getLocationDataForApi()
        : (arguments['locationData'] is Map ? (arguments['locationData'] as Map).cast<String, dynamic>() : {});
    final Map<String, dynamic> origLoc = _tryDecodeMap(originalAdData['location']?.toString());

    // attributes deep compare
    final List<Map<String, dynamic>> currentAttrs = (attributesJsonStrings ?? []);
    final List currentAttrsNorm = currentAttrs;
    final List origAttrs = (() {
      try {
        final raw = jsonDecode(originalAdData['attributes'] ?? '[]');
        if (raw is List) return raw;
      } catch (_) {}
      return [];
    })();

    // gallery compare
    final List<String> currentGallery =
        (arguments['selectedImages'] as List?)?.map((e) => e.toString()).toList() ?? [];
    final List<String> origGallery = (() {
      try {
        final raw = jsonDecode(originalAdData['galleryImages'] ?? '[]');
        if (raw is List) return raw.map((e) => e.toString()).toList();
      } catch (_) {}
      return <String>[];
    })();

    // ---------- basic scalar diffs ----------
    if (currentTitle       != originalAdData['title'])        changed['title']                 = currentTitle;
    if (currentSubTitle    != originalAdData['subTitle'])     changed['subTitle']              = currentSubTitle;
    if (currentDescription != originalAdData['description'])  changed['description']           = currentDescription;
    if (currentPrice       != originalAdData['price'])        changed['price']                 = currentPrice;
    if (currentAvailUnits  != originalAdData['availableUnits']) changed['availableUnits']      = currentAvailUnits;

    if (!_deepEq(currentLoc, origLoc))                        changed['location']              = jsonEncode(currentLoc);
    if (!_deepEq(currentAttrsNorm, origAttrs))                changed['attributes']            = currentAttrs;

    if (currentOffer   != (originalAdData['isOfferAllowed'] == true))        changed['isOfferAllowed']        = currentOffer;
    if (currentAuction != (originalAdData['isAuctionEnabled'] == true))      changed['isAuctionEnabled']      = currentAuction;
    if (currentReserveFlag != (originalAdData['isReservePriceEnabled'] == true)) changed['isReservePriceEnabled'] = currentReserveFlag;

    if (currentPrimary != originalAdData['primaryImage'])     changed['primaryImage']          = currentPrimary;

    // ---------- gallery diffs ----------
    final bool galleryChanged = !_deepEq(currentGallery, origGallery);
    final bool galleryRemoved = (removedGalleryIndexes != null && removedGalleryIndexes!.isNotEmpty);
    if (galleryChanged || galleryRemoved) {
      changed['galleryImages'] = currentGallery;
      if (galleryRemoved) {
        changed['galleryIndexes'] = removedGalleryIndexes;
      }
    }

    // ---------- minimumOffer ONLY when price changed ----------
    final oldPrice = double.tryParse((originalAdData['price'] ?? '').toString());
    final newPrice = double.tryParse(currentPrice);
    if (oldPrice != newPrice) {
      final mo = (double.tryParse(buyNowPrice ?? '0') ?? 0) - (double.tryParse(finalPrice ?? '0') ?? 0);
      changed['minimumOffer'] = _formatTrim(mo);
    }

    // ---------- AUCTION CHANGES (NEW) ----------
    // Prefer controller.text so latest UI value is captured
    final String currentReservePriceAmount = reservePriceController.text.trim().isNotEmpty
        ? reservePriceController.text.trim()
        : (reservePrice ?? '').toString();

    final String currentAuctionStart = auctionStartController.text.trim().isNotEmpty
        ? auctionStartController.text.trim()
        : (auctionStartingBid ?? '').toString();

    final String currentAuctionDuration = (selectedDurationDays ?? '').toString();

    // Add when Auction is ON and value changed
    if (currentAuction == true) {
      // auctionStartingPrice
      if (currentAuctionStart.isNotEmpty &&
          currentAuctionStart != (originalAdData['auctionStartingPrice'] ?? '')) {
        changed['auctionStartingPrice'] = currentAuctionStart;
      }

      // auctionDurationDays
      if (currentAuctionDuration.isNotEmpty &&
          currentAuctionDuration != (originalAdData['auctionDurationDays'] ?? '')) {
        changed['auctionDurationDays'] = currentAuctionDuration;
      }

      // reservePriceAmount only if reserve flag is ON
      if (currentReserveFlag == true) {
        if (currentReservePriceAmount.isNotEmpty &&
            currentReservePriceAmount != (originalAdData['reservePriceAmount'] ?? '')) {
          changed['reservePriceAmount'] = currentReservePriceAmount;
        }
      } else {
        // If user turned OFF reserve flag and there was a value earlier,
        // typically server ignores amount when flag is false. So no need to send amount.
      }
    }

    // ---------- SCHEDULED PUBLISH DATE (optional) ----------
    String currentSchedule = '';
    if (isScheduleEnabled && scheduledDate != null) {
      currentSchedule = scheduledPublishDateString; // e.g. 2025-10-11T00:00:00Z
    }

    if (currentSchedule != (originalAdData['scheduledPublishDate'] ?? '')) {
      if (currentSchedule.isNotEmpty) {
        changed['scheduledPublishDate'] = currentSchedule;
      } else {
        // if you support clearing schedule on server, you may send an explicit empty string or a flag.
        // changed['scheduledPublishDate'] = '';
      }
    }

    // 🔹 NEW: saleType diff
    final String currentSaleType = saleType; // getter returns '1' or '2'
    if (currentSaleType != (originalAdData['saleType'] ?? '')) {
      changed['saleType'] = currentSaleType;
    }

    // ... (auction changes, schedule, cleanup જેમના તેમ)
    changed.removeWhere((k, v) => v == null || (v is String && v.trim().isEmpty));
    return changed;
  }


  String _formatTrim(num v) {
    final s = v.toStringAsFixed(2);
    return s.endsWith('.00') ? s.substring(0, s.length - 3) : s;
  }


  Map<String, dynamic> getLocationDataForApi() {
    return {
      'country': selectedCountry ?? '',
      'state': selectedState ?? '',
      'city': selectedCity ?? '',
      'latitude': latitude ?? 0.0,
      'longitude': longitude ?? 0.0,
    };
  }

  void toggleBuyNowSwitch(bool value) {
    if (value) {
      buyNowSwitch = true;
      auctionSwitch = false;
    } else {
      if (!auctionSwitch) {
        auctionSwitch = true;
      }
      buyNowSwitch = false;
    }
    update([Constant.switchUpdate]);
  }

  void toggleAuctionSwitch(bool value) {
    if (value) {
      auctionSwitch = true;
      buyNowSwitch = false;
    } else {
      if (!buyNowSwitch) {
        buyNowSwitch = true;
      }
      auctionSwitch = false;
    }
    update([Constant.switchUpdate]);
  }

  void toggleSchedule(BuildContext context) async {
    isScheduleEnabled = !isScheduleEnabled;
    update([Constant.switchUpdate]);
  }

  void toggleReservePrice(BuildContext context) async {
    isReservePrice = !isReservePrice;
    update([Constant.switchUpdate]);
    Utils.showLog("isReservePrice: $isReservePrice");
  }

  // Future<void> pickDate(BuildContext context) async {
  //   final picked = await showDatePicker(
  //     context: context,
  //     initialDate: scheduledDate ?? DateTime.now().add(Duration(days: 3)), // default select after 2 days
  //     firstDate: DateTime.now().add(Duration(days: 3)), // restrict from today + 2
  //     lastDate: DateTime(2100),
  //     builder: (context, child) {
  //       return Theme(
  //         data: ThemeData.light().copyWith(
  //           colorScheme: ColorScheme.light(
  //             primary: AppColors.appRedColor,
  //             onPrimary: Colors.white,
  //             onSurface: Colors.black,
  //           ),
  //           dialogBackgroundColor: Colors.white,
  //         ),
  //         child: child!,
  //       );
  //     },
  //   );
  //
  //   if (picked != null) {
  //     scheduledDate = picked;
  //     update([Constant.switchUpdate]);
  //   }
  // }



  Future<void> pickDate(BuildContext context) async {
    // min schedule days (તમારો નિયમ)
    const int _kMinScheduleDays = 3;

    // today at midnight (microseconds differences ટાળવા)
    final now = DateTime.now();
    final minDate = DateTime(now.year, now.month, now.day)
        .add(const Duration(days: _kMinScheduleDays));

    // APIથી આવેલ scheduledDate લઇ લો (localમાં)
    final fromApi = scheduledDate?.toLocal();

    // initialDate must be >= firstDate => clamp કરો
    final initial = (fromApi == null || fromApi.isBefore(minDate))
        ? minDate
        : fromApi;

    final picked = await showDatePicker(
      context: context,
      initialDate: initial,
      firstDate: minDate,
      lastDate: DateTime(2100),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme:  ColorScheme.light(
              primary: AppColors.appRedColor,
              onPrimary: Colors.white,
              onSurface: Colors.black,
            ),
            dialogBackgroundColor: Colors.white,
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      scheduledDate = picked;
      update([Constant.switchUpdate]);
    }
  }



  String get formattedScheduleDate {
    if (scheduledDate == null) return "Pick Date";
    return "${scheduledDate!.day}/${scheduledDate!.month}/${scheduledDate!.year}";
  }

  void calculateFinalPrice() {
    if (buyNowPrice != null && selectedPercentage != null) {
      final itemPrice = double.tryParse(buyNowPrice!);
      final percent = double.tryParse(selectedPercentage!.replaceAll('%', ''));

      if (itemPrice != null && percent != null) {
        final discounted = itemPrice - (itemPrice * percent / 100);
        finalPrice = discounted.toStringAsFixed(2);
        finalPriceController.text = finalPrice!;
        update([Constant.switchUpdate]);
      }
    }
  }

  void resetPricingFields() {
    selectedPercentage = null;
    selectedDurationDays = null;
    scheduledDate = null;
    finalPrice = null;
    buyNowPrice = null;

    percentageDropDownController.close();
    durationDropDownController.close();
    finalPriceController.clear();
    update([Constant.switchUpdate]);
  }

  String get saleType {
    if (buyNowSwitch) return '1';
    if (auctionSwitch) return '2';
    return '1';
  }

  String? get formattedScheduledPublishDate {
    if (isScheduleEnabled && scheduledDate != null) {
      return scheduledDate!.toUtc().toIso8601String();
    }
    return null;
  }

  bool adListing = false;

  String get scheduledPublishDateString {
    final DateTime base = scheduledDate ?? DateTime.now();

    final DateTime pureDateUtc = DateTime.utc(base.year, base.month, base.day);

    return DateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'").format(pureDateUtc);
  }

  /// edit feild send mate add start
  String formatPrice(String? price) {
    final parsed = double.tryParse(price ?? '0') ?? 0;
    if (parsed == parsed.toInt()) {
      return parsed.toInt().toString();
    }
    return parsed.toStringAsFixed(2);
  }


  /// 🔹 Check which fields have changed
  Map<String, dynamic> getChangedFields() {
    Map<String, dynamic> changedFields = {};

    String currentLocation = selectCityScreen == true
        ? jsonEncode(getLocationDataForApi())
        : jsonEncode(arguments['locationData']);

    // Title
    if (arguments['title'] != originalAdData['title']) {
      changedFields['title'] = arguments['title'];
    }

    // SubTitle
    if (arguments['subtitle'] != originalAdData['subTitle']) {
      changedFields['subTitle'] = arguments['subtitle'];
    }

    // Description
    if (arguments['description'] != originalAdData['description']) {
      changedFields['description'] = arguments['description'];
    }

    // Price
    if (finalPrice.toString() != originalAdData['price']) {
      changedFields['price'] = finalPrice.toString();
    }

    // Minimum Offer
    String currentMinOffer = formatPrice(
        (double.tryParse(buyNowPrice ?? '0')! - double.tryParse(finalPrice ?? '0')!).toString()
    );
    changedFields['minimumOffer'] = currentMinOffer; // Always send for recalculation

    // Available Units
    if (quantity.text.toString() != originalAdData['availableUnits']) {
      changedFields['availableUnits'] = quantity.text.toString();
    }

    // Location
    if (currentLocation != originalAdData['location']) {
      changedFields['location'] = currentLocation;
    }

    // Attributes
    String currentAttributes = jsonEncode(attributesJsonStrings);
    if (currentAttributes != originalAdData['attributes']) {
      changedFields['attributes'] = attributesJsonStrings;
    }

    // isOfferAllowed
    if (isOfferAllowed != originalAdData['isOfferAllowed']) {
      changedFields['isOfferAllowed'] = isOfferAllowed;
    }

    // isReservePriceEnabled
    if (isReservePrice != originalAdData['isReservePriceEnabled']) {
      changedFields['isReservePriceEnabled'] = isReservePrice;
    }

    // isAuctionEnabled
    if (auctionSwitch != originalAdData['isAuctionEnabled']) {
      changedFields['isAuctionEnabled'] = auctionSwitch;
    }

    // Primary Image (check if changed)
    if (arguments['mainImage'] != originalAdData['primaryImage']) {
      changedFields['primaryImage'] = arguments['mainImage'];
    }

    // Gallery Images (check if changed)
    String currentGalleryImages = jsonEncode(arguments['selectedImages']);
    if (currentGalleryImages != originalAdData['galleryImages'] ||
        (removedGalleryIndexes != null && removedGalleryIndexes!.isNotEmpty)) {
      changedFields['galleryImages'] = arguments['selectedImages'];
      if (removedGalleryIndexes != null && removedGalleryIndexes!.isNotEmpty) {
        changedFields['galleryIndexes'] = removedGalleryIndexes;
      }
    }

    return changedFields;
  }

  ///end............

  /// add listing api
  Future<void> submitListing() async {
    if (isScheduleEnabled == true) {
      if (scheduledDate == null) {
        Utils.showToast(Get.context!, "Please enter Date");
        return; // ⛔ stop here if date not selected
      }
    }

    if (buyNowSwitch == true) {
      if (quantity.text.toString().isEmpty || quantity.text.toString() == '0') {
        Utils.showToast(Get.context!, "Please enter product quantity");
        return;
      }
    } else {
      if (auctionStartingBid == null || auctionStartingBid.toString().isEmpty || auctionStartingBid == '0') {
        Utils.showToast(Get.context!, "Please enter start bid");
        return;
      }

      if (isReservePrice == true) {
        if (reservePrice == null || reservePrice.toString().isEmpty || reservePrice == '0') {
          Utils.showToast(Get.context!, "Please enter reservePrice");
          return;
        }
      }
    }
    Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...

    String formatPrice(String? price) {
      final parsed = double.tryParse(price ?? '0') ?? 0;
      if (parsed == parsed.toInt()) {
        return parsed.toInt().toString();
      }
      return parsed.toStringAsFixed(2);
    }



    Utils.showLog("Final Attributes JSON to send: ${jsonEncode(attributesJsonStrings)}");
    Utils.showLog("firebaseUid:::::::::: ${Database.getUserProfileResponseModel?.user?.firebaseUid}");
    Utils.showLog("selectedImages::::::::::: ${arguments['selectedImages']}");
    Utils.showLog("mainImage::::::::::: ${arguments['mainImage']}");
    Utils.showLog("title::::::::::: ${arguments['title']}");
    Utils.showLog("category Id::::::: ${arguments['categoryId']}");
    Utils.showLog("subtitle:::::: ${arguments['subtitle']}");
    Utils.showLog("description:::::::::::: ${arguments['description']}");
    Utils.showLog("selectCityScreen::::::::: $selectCityScreen");
    Utils.showLog("getLocationDataForApi:::::::: ${jsonEncode(getLocationDataForApi())}");
    Utils.showLog("locationData:::::::::::: ${jsonEncode(arguments['locationData'])}");
    Utils.showLog("attributesJsonStrings: $attributesJsonStrings");
    Utils.showLog(" DateTime.now().toUtc().toIso8601String(): ${DateTime.now().toUtc().toIso8601String()}");
    Utils.showLog("reservePriceAmount============ ${reservePrice.toString()}");
    Utils.showLog("auctionStartingPrice============ ${auctionStartingBid.toString()}");
    Utils.showLog("auctionEndDate (days) ========= $selectedDurationDays");
    Utils.showLog("availableUnits================= ${quantity.text.toString()}");
    Utils.showLog("scheduledPublishDate================= $scheduledPublishDateString");
    Utils.showLog("saleType================= $saleType");
    Utils.showLog("isReservePrice================= $isReservePrice");
    Utils.showLog("auctionSwitch================= $auctionSwitch");
    Utils.showLog("auctionDurationDays================= ${(selectedDurationDays ?? 0).toString()}");
    Utils.showLog(
        "minimumOffer================= ${formatPrice((double.tryParse(buyNowPrice ?? '0')! - double.tryParse(finalPrice ?? '0')!).toString())}");

    adListing = true;
    update([Constant.switchUpdate]);

    createAdListingResponseModel = await AddListingApi.callApi(
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      primaryImagePath: arguments['mainImage'],
      // galleryImagePaths: arguments['selectedImages'],
      galleryImagePaths: (arguments['selectedImages'] as List?)?.map((e) => e.toString()).toList() ?? [],
      categoryId: arguments['categoryId'],
      productName: arguments['title'],
      subTitle: arguments['subtitle'],
      description: arguments['description'],
      contactNumber: '1234567890',
      location: selectCityScreen == true ? jsonEncode(getLocationDataForApi()) : jsonEncode(arguments['locationData']),
      price: finalPrice.toString(),
      minimumOffer: formatPrice((double.tryParse(buyNowPrice ?? '0')! - double.tryParse(finalPrice ?? '0')!).toString()),
      // scheduledPublishDate: DateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'").format(DateTime.now().toUtc()),
      scheduledPublishDate: scheduledPublishDateString,
      availableUnits: quantity.text.toString(),
      saleType: saleType,
      isOfferAllowed: true,
      isReservePriceEnabled: isReservePrice,
      isAuctionEnabled: auctionSwitch,
      attributes: attributesJsonStrings ?? [],
      // Auction fields
      reservePriceAmount: reservePrice.toString(),
      auctionStartingPrice: auctionStartingBid.toString(),
      auctionDurationDays: (selectedDurationDays ?? 0).toString(),
    );

    if (createAdListingResponseModel?.status == true) {
      adListing = false;
      update([Constant.switchUpdate]);

      Get.dialog(
        barrierDismissible: false,
        barrierColor: AppColors.black.withValues(alpha: (0.8)),
        Dialog(
          insetPadding: EdgeInsets.symmetric(horizontal: 44),
          backgroundColor: AppColors.transparent,
          shadowColor: Colors.transparent,
          surfaceTintColor: Colors.transparent,
          elevation: 0,
          child: AdListingCompleteDialog(),
        ),
      );
    } else if (createAdListingResponseModel?.status == false) {
      if (Get.isDialogOpen ?? false) {
        Get.back(); // Stop loading
      }
      adListing = false;
      update([Constant.switchUpdate]);
      Utils.showToast(Get.context!, createAdListingResponseModel?.message ?? "");
    } else {
      adListing = false;
      update([Constant.switchUpdate]);
      Get.back(); // Stop Loading...

      Utils.showToast(Get.context!, createAdListingResponseModel?.message ?? "");
    }
  }

  /// edit ad listing api
  bool editListing = false;
  // Future<void> editApiCall() async {
  //   String formatPrice(String? price) {
  //     final parsed = double.tryParse(price ?? '0') ?? 0;
  //     if (parsed == parsed.toInt()) {
  //       return parsed.toInt().toString();
  //     }
  //     return parsed.toStringAsFixed(2);
  //   }
  //
  //   Utils.showLog("Final Attributes JSON to send: ${jsonEncode(attributesJsonStrings)}");
  //   Utils.showLog("Final Attributes JSON to send: ${Database.getUserProfileResponseModel?.user?.firebaseUid}");
  //   Utils.showLog("selectedImages send: ${arguments['selectedImages']}");
  //   Utils.showLog("Final Attributes JSON to send: ${arguments['mainImage']}");
  //   Utils.showLog("Final Attributes JSON to send: ${arguments['title']}");
  //   Utils.showLog("Final Attributes JSON to send: ${arguments['categoryId']}");
  //   Utils.showLog("Final Attributes JSON to send: ${arguments['subtitle']}");
  //   Utils.showLog("Final Attributes JSON to send: ${arguments['description']}");
  //   Utils.showLog("removedGalleryIndexes send: ${removedGalleryIndexes}");
  //   Utils.showLog("removeGalleryIndexes: ${arguments['removeGalleryIndexes']}");
  //   Utils.showLog("Final Attributes JSON to send: $selectCityScreen");
  //   Utils.showLog("Final Attributes JSON to send: ${jsonEncode(getLocationDataForApi())}");
  //   Utils.showLog("Final Attributes JSON to send: ${jsonEncode(arguments['locationData'])}");
  //   Utils.showLog("Final Attributes JSON to send: $attributesJsonStrings");
  //   Utils.showLog(" DateTime.now().toUtc().toIso8601String(): ${DateTime.now().toUtc().toIso8601String()}");
  //   Utils.showLog("minimumOffer::::::::::::::::::: ${formatPrice(
  //     (double.tryParse(buyNowPrice ?? '0')! - double.tryParse(finalPrice ?? '0')!).toString(),
  //   )}");
  //
  //   editListing = true;
  //   update([Constant.switchUpdate]);
  //
  //   updateProductDetailResponseModel = await UpdateListingApi.callApi(
  //     uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
  //     primaryImagePath: arguments['mainImage'] ?? "",
  //     // galleryImagePaths: arguments['selectedImages'] ?? [],
  //     galleryImagePaths: (arguments['selectedImages'] as List?)?.map((e) => e.toString()).toList() ?? [],
  //     title: arguments['title'],
  //     subTitle: arguments['subtitle'],
  //     description: arguments['description'],
  //     galleryIndexes:  (removedGalleryIndexes as List?)?.map((e) => e as int).toList() ?? [],
  //     contactNumber: '9876543210',
  //     location: selectCityScreen == true ? jsonEncode(getLocationDataForApi()) : jsonEncode(arguments['locationData']),
  //     price: finalPrice.toString(),
  //     minimumOffer: formatPrice((double.tryParse(buyNowPrice ?? '0')! - double.tryParse(finalPrice ?? '0')!).toString()),
  //     availableUnits: quantity.text.toString(),
  //     isOfferAllowed: true,
  //     isReservePriceEnabled: isReservePrice,
  //     isAuctionEnabled: auctionSwitch,
  //     attributes: attributesJsonStrings ?? [],
  //     adId: adId.toString(),
  //     reservePriceAmount: reservePrice.toString(),
  //     auctionStartingPrice: auctionStartingBid.toString(),
  //     auctionDurationDays: (selectedDurationDays ?? 0),
  //     scheduledPublishDate: scheduledPublishDateString,
  //   );
  //
  //   if (updateProductDetailResponseModel?.status == true) {
  //     editListing = false;
  //     update([Constant.switchUpdate]);
  //
  //     Get.offAllNamed(AppRoutes.bottomBar);
  //     Utils.showToast(Get.context!, updateProductDetailResponseModel?.message ?? "");
  //   } else {
  //     editListing = false;
  //     update([Constant.switchUpdate]);
  //     Utils.showToast(Get.context!, updateProductDetailResponseModel?.message ?? "");
  //   }
  // }


  ///edit feild api start (6 last)

  // Future<void> editApiCall() async {
  //   final changedFields = getChangedFields();
  //
  //   Utils.showLog("🔥 Changed Fields => ${jsonEncode(changedFields)}");
  //
  //   if (changedFields.isEmpty &&
  //       (removedGalleryIndexes == null || removedGalleryIndexes!.isEmpty)) {
  //     Utils.showToast(Get.context!, "No changes detected");
  //     return;
  //   }
  //
  //   editListing = true;
  //   update([Constant.switchUpdate]);
  //
  //   updateProductDetailResponseModel = await UpdateListingApi.callApi(
  //     uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
  //     adId: adId.toString(),
  //     changedFields: changedFields,
  //     removedGalleryIndexes: removedGalleryIndexes ?? [],
  //   );
  //
  //   editListing = false;
  //   update([Constant.switchUpdate]);
  //
  //   final success = updateProductDetailResponseModel?.status == true;
  //   Utils.showToast(Get.context!, updateProductDetailResponseModel?.message ?? (success ? "Updated" : "Failed"));
  //
  //   if (success) {
  //     Get.offAllNamed(AppRoutes.bottomBar);
  //   }
  // }
  ///end...........
///7
  Future<void> editApiCall() async {
    final changedFields = buildChangedFields();

    Utils.showLog("🔥 Changed Fields => ${jsonEncode(changedFields)}");

    if (changedFields.isEmpty &&
        (removedGalleryIndexes == null || removedGalleryIndexes!.isEmpty)) {
      Utils.showToast(Get.context!, "No changes detected");
      return;
    }

    editListing = true;
    update([Constant.switchUpdate]);

    Utils.showLog("changedFields.........................${changedFields}");

    final res = await UpdateListingApi.callApi(
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      adId: adId.toString(),
      changedFields: changedFields,
      removedGalleryIndexes: removedGalleryIndexes ?? [],
    );

    editListing = false;
    update([Constant.switchUpdate]);

    final success = res?.status == true;
    Utils.showToast(Get.context!, res?.message ?? (success ? "Updated" : "Failed"));

    if (success) {
      Get.offAllNamed(AppRoutes.bottomBar);
    }
  }

}
