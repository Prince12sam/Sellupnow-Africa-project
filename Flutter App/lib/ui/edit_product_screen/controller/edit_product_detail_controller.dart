import 'dart:developer';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/ui/product_detail_screen/model/product_detail_response_model.dart';
import 'package:listify/ui/profile_screen_view/api/setting_api.dart';
import 'package:listify/ui/profile_screen_view/model/setting_api_response_model.dart';
import 'package:listify/ui/edit_product_screen/api/ai_listing_assist_api.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';

class EditProductDetailController extends GetxController {
  TextEditingController productTitle = TextEditingController();
  TextEditingController productSubTitle = TextEditingController();
  TextEditingController productPrice = TextEditingController();
  TextEditingController productDescription = TextEditingController();
  String? categoryId;
  String? categoryTitle;
  SettingApiResponseModel? settingApiResponseModel;
  Product? adsData;
  Map<String, dynamic> arguments = Get.arguments ?? {};
  bool isEdit = false;
  bool isAiLoading = false;

  @override
  onInit() {
    super.onInit();

    init();
    productTitle.addListener(updateButtonState);
    productSubTitle.addListener(updateButtonState);
    productPrice.addListener(updateButtonState);
    productDescription.addListener(updateButtonState);


    Utils.showLog("Database.settingApiResponseModel?.data?.currency?.symbol${Database.settingApiResponseModel?.data?.currency?.symbol}");
    Utils.showLog("Database.settingApiResponseModel?.data?.currency?.symbol${Database.currencySymbol}");
  }
  void updateButtonState() {
    update(); // ✅ rebuilds GetBuilder<EditProductDetailController>
  }


  init() async {
    // Accessing the passed argument

    log("arguments api:::::::::::::::::::::$arguments");
    adsData = arguments['ad'];
    log("arguments api:::::::::::::::::::::${adsData?.isAuctionEnabled}");

    categoryId = arguments['categoryId'] ?? adsData?.category?.id;
    categoryTitle = arguments['categoryTitle'] ?? adsData?.category?.name;

    productTitle.text = adsData?.title ?? "";
    productSubTitle.text = adsData?.subTitle ?? "";

    productPrice.text = "${adsData?.price ?? ""}";
    productDescription.text = Utils.stripHtml(adsData?.description ?? "");
    isEdit = arguments['editApi'] ?? false;

    Utils.showLog("editApi  :::::::::::::::: $isEdit");

    // Keep a stable category id for downstream attribute fetching.
    categoryId = (arguments['categoryId'] ?? adsData?.category?.id)?.toString();

    settingApiResponseModel = await SettingApi.callApi();
    Database.settingApiResponseModel = settingApiResponseModel;

    Utils.showLog("categoryId value: $categoryId");
    Utils.showLog("categoryTitle value: $categoryTitle");
  }

  editProductDetailValidation() {
    if (productTitle.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseEnterProductTitle.name.tr);
      return;
    }

    if (productSubTitle.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseEnterProductSubtitle.name.tr);
      return;
    }

    if (productPrice.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseEnterProductPrice.name.tr);
      return;
    }

    final price = double.tryParse(productPrice.text.trim());
    if (price == null || price < 0) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseEnterAValidPrice.name.tr);
      return;
    }

    if (productDescription.text.trim().isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseEnterProductDescription.name.tr);
      return;
    }

    // Get.toNamed(AppRoutes.uploadImageScreenView, arguments: {
    //   arguments.addAll({
    //     'title': productTitle.text.trim(),
    //     'subtitle': productSubTitle.text.trim(),
    //     'price': productPrice.text.trim(),
    //     'description': productDescription.text.trim(),
    //   }),
    // });

    final mergedArguments = {
      ...arguments,
      'title': productTitle.text.trim(),
      'subtitle': productSubTitle.text.trim(),
      'price': productPrice.text.trim(),
      'description': productDescription.text.trim(),
      'ad': adsData,
      'editApi': isEdit,
      'adId': adsData?.id,
    };

    Get.toNamed(
      AppRoutes.uploadImageScreenView,
      arguments: mergedArguments,
    );
  }

  Future<void> aiAssist() async {
    if (isAiLoading) return;

    final uid = Database.loginUserId;
    if (uid.trim().isEmpty) {
      Utils.showToast(Get.context!, "Please login again");
      return;
    }

    isAiLoading = true;
    update();

    try {
      final res = await AiListingAssistApi.callApi(
        uid: uid,
        title: productTitle.text.trim(),
        subtitle: productSubTitle.text.trim(),
        description: productDescription.text.trim(),
      );

      final status = res?['status'] == true;
      final message = (res?['message'] ?? '').toString();

      if (!status) {
        Utils.showToast(Get.context!, message.isNotEmpty ? message : "AI request failed");
        return;
      }

      final data = res?['data'] is Map ? (res?['data'] as Map) : <dynamic, dynamic>{};

      final outTitle = (data['title'] ?? '').toString();
      final outSubtitle = (data['subtitle'] ?? '').toString();
      final outDescription = (data['description'] ?? '').toString();

      if (outTitle.trim().isNotEmpty) {
        productTitle.text = outTitle;
      }
      if (outSubtitle.trim().isNotEmpty) {
        productSubTitle.text = outSubtitle;
      }
      if (outDescription.trim().isNotEmpty) {
        productDescription.text = outDescription;
      }

      Utils.showToast(Get.context!, message.isNotEmpty ? message : "AI suggestion applied");
    } catch (e) {
      Utils.showToast(Get.context!, "AI request failed");
    } finally {
      isAiLoading = false;
      update();
    }
  }


  @override
  void onClose() {
    productTitle.removeListener(updateButtonState);
    productSubTitle.removeListener(updateButtonState);
    productPrice.removeListener(updateButtonState);
    productDescription.removeListener(updateButtonState);
    super.onClose();
  }
}
