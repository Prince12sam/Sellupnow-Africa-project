import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/select_state_screen/api/get_all_state_api.dart';
import 'package:listify/ui/select_state_screen/model/state_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class SelectStateScreenController extends GetxController {
  bool isLoading = false;
  bool isPaginationLoading = false;
  StateResponseModel? stateResponseModel;
  List<Datum> allStateList = [];
  ScrollController scrollController = ScrollController();
  String? selectedCountry;
  TextEditingController searchController = TextEditingController(); // For input
  List<Datum> filteredStateList = [];
  bool isSearchPerformed = false; // Track search state
  // bool filterScreen = false;
bool homeLocation = false;
bool search = false;
bool popular = false;
bool mostLike = false;
bool subcategory = false;
  Map<String, dynamic> arguments = Get.arguments ?? {};

  @override
  void onInit() {
    init();
    super.onInit();
  }

  init() async {
    Utils.showLog("Enter select state screen Controller");
    scrollController.addListener(onAllCountryPagination);
    selectedCountry = Get.arguments['selectedCountry'];
    // filterScreen = Get.arguments['filterScreen'] ?? false;
    homeLocation = Get.arguments['homeLocation'] ?? false;
    popular = Get.arguments['popular'] ?? false;
    mostLike = Get.arguments['mostLike'] ?? false;
    search = Get.arguments['search'] ?? false;
    subcategory = Get.arguments['subcategory'] ?? false;

    // Utils.showLog("filterScreen:::::::::: $filterScreen");
    Utils.showLog("homeLocation:::::::::: $homeLocation");
    Utils.showLog("search:::::::::: $search");
    Utils.showLog("search:::::::::: ${Get.arguments['search']}");
    Utils.showLog("popular:::::::::: $popular");
    Utils.showLog("mostLike:::::::::: $mostLike");
    Utils.showLog("subcategory>>>+++++++++++:::::::::: $subcategory");

    Utils.showLog("Country received in State Screen: $selectedCountry");
    Utils.showLog("arguments screen::: $arguments");

    GetAllStateApi.startPagination = 0;
    await getAllStateApi();
  }

  /// get all country api
  getAllStateApi() async {
    isLoading = true;
    update([Constant.idGetState]);
    stateResponseModel = await GetAllStateApi.callApi();
    allStateList.clear();
    allStateList.addAll(stateResponseModel?.data ?? []);
    filteredStateList = List.from(allStateList);

    Utils.showLog("subscriptionPlan list data $allStateList");

    isLoading = false;
    update([Constant.idGetState]);
  }

  /// search state
  void onSearchState(String query) {
    isSearchPerformed = query.isNotEmpty;

    if (query.isEmpty) {
      filteredStateList = List.from(allStateList);
    } else {
      filteredStateList = allStateList.where((state) => (state.name ?? '').toLowerCase().contains(query.toLowerCase())).toList();
    }

    update([Constant.idGetState]);
  }

  /// refresh
  onRefresh() async {
    GetAllStateApi.startPagination = 0;
    allStateList.clear();

    getAllStateApi();
  }

  /// pagination
  Future<void> onAllCountryPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      isPaginationLoading = true;
      update([Constant.idPagination]);

      stateResponseModel = await GetAllStateApi.callApi();
      allStateList.clear();
      allStateList.addAll(stateResponseModel?.data ?? []);

      Utils.showLog("allCountryList pagination ::::: $allStateList");

      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }
}
