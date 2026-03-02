import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/model/sub_category_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class SubCategoriesScreenController extends GetxController {
  late bool addListingScreen;
  bool isLoading = false;
  bool isPaginationLoading = false;
  String? categoryId;
  String? categoryTitle;
  SubCategoryResponseModel? subCategoryResponseModel;
  List<Datum> subCategoryList = [];
  ScrollController scrollController = ScrollController();
  List<String> categoryIdHistory = [];
  List<String> categoryTitleHistory = [];
  Map<String, dynamic> arguments = Get.arguments ?? {};
  bool search = false;
  bool popular = false;
  bool mostLike = false;
  bool subcategory = false;

  @override
  void onInit() {
    init();
    categoryIdHistory.clear();
    categoryTitleHistory.clear();

    super.onInit();
  }

  init() async {
    scrollController.addListener(onSubCategoryPagination);

    SubCategoryApi.startPagination = 0;

    final Map<String, dynamic> arguments = Get.arguments ?? {};

    // Accessing the passed argument
    addListingScreen = arguments['addListingScreen'] ?? false;
    categoryId = arguments['categoryId'] ?? '';
    categoryTitle = arguments['categoryTitle'] ?? '';

    search = arguments["search"] ?? false;
    mostLike = arguments["mostLike"] ?? false;
    popular = arguments["popular"] ?? false;
    subcategory = arguments["subcategory"] ?? false;
    Utils.showLog("search sub categories screen :::$search");
    Utils.showLog("mostLike sub categories screen :::$mostLike");
    Utils.showLog("popular sub categories screen :::$popular");
    Utils.showLog("subcategoryyyyyyyyyyyyy :::$subcategory");

    Utils.showLog("addListingScreen value: $addListingScreen");
    Utils.showLog("categoryId value: $categoryId");

    getSubCategoryApi();
  }

  /// get sub category api
  Future<bool> getSubCategoryApi() async {
    isLoading = true;
    update();

    subCategoryResponseModel = await SubCategoryApi.callApi(parentId: categoryId);
    subCategoryList.clear();
    subCategoryList.addAll(subCategoryResponseModel?.data ?? []);

    Utils.showLog("sub category list data: ${subCategoryList.map((e) => e.name).toList()}");

    isLoading = false;
    update();
    update(['appbar']); // For AppBar title update

    return subCategoryList.isEmpty;
  }

  /// pagination
  // Future<void> onSubCategoryPagination() async {
  //   if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
  //     isPaginationLoading = true;
  //     update([Constant.idPagination]);
  //
  //     subCategoryResponseModel = await SubCategoryApi.callApi(parentId: categoryId);
  //     subCategoryList.clear();
  //     subCategoryList.addAll(subCategoryResponseModel?.data ?? []);
  //
  //     Utils.showLog("category  pagination ::::: $subCategoryList");
  //
  //     isPaginationLoading = false;
  //     update([Constant.idPagination]);
  //   }
  // }


  Future<void> onSubCategoryPagination() async {
    // Only trigger if not already loading and at end
    if (!isPaginationLoading &&
        scrollController.position.pixels >= scrollController.position.maxScrollExtent - 50) {
      isPaginationLoading = true;
      update([Constant.idPagination]);

      // Call API with current pagination
      final response = await SubCategoryApi.callApi(parentId: categoryId);

      // Append new items instead of clearing
      if (response != null && response.data!.isNotEmpty) {
        subCategoryList.addAll(response.data!);
      }

      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }

}
