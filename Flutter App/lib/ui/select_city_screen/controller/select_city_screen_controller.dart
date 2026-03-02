import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/select_city_screen/api/city_api.dart';
import 'package:listify/ui/select_city_screen/model/city_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class SelectCityScreenController extends GetxController {
  bool isLoading = false;
  CityResponseModel? cityResponseModel;
  List<Datum> cityList = [];
  List<Datum> searchedCityList = [];
  bool isPaginationLoading = false;
  ScrollController scrollController = ScrollController();
  String? selectedState;
  String? selectedCountry;
  // bool filterScreen = false;
  bool homeLocation = false;
  bool search = false;
  bool popular = false;
  bool mostLike = false;
  bool subcategory = false;
  String searchText = "";
  Map<String, dynamic> arguments = Get.arguments ?? {};

  @override
  void onInit() {
    init();
    super.onInit();
  }

  init() async {
    Utils.showLog("Enter select city screen Controller");
    scrollController.addListener(onAllCityPagination);

    selectedState = Get.arguments['selectedState'];
    selectedCountry = Get.arguments['selectedCountry'];
    // filterScreen = arguments['filterScreen'] ?? false;
    homeLocation = arguments['homeLocation'] ?? false;
    search = arguments['search'] ?? false;
    popular = arguments['popular'] ?? false;
    mostLike = arguments['mostLike'] ?? false;
    subcategory = arguments['subcategory'] ?? false;

    Utils.showLog("State received in City Screen: $selectedState");
    // Utils.showLog("Country received in City Screen: $selectedCountry");
    Utils.showLog("argument Screen>>>>>>>>>> $arguments");
    // Utils.showLog("filterScreen?????????????????? $filterScreen");
    Utils.showLog("homeLocation?????????????????? $homeLocation");
    Utils.showLog("argument?????????????????? $arguments");
    Utils.showLog("search?????????????????? $search");
    Utils.showLog("popular?????????????????? $popular");
    Utils.showLog("mostLike?????????????????? $mostLike");
    Utils.showLog("subcategory////////////////$subcategory");

    CityApi.startPagination = 0;
    await getAllCity();
  }

  Future<void> getAllCity() async {
    isLoading = true;
    update([Constant.idGetCity]);

    cityResponseModel = await CityApi.callApi();
    cityList.clear();
    searchedCityList.clear();

    final data = cityResponseModel?.data ?? [];
    cityList.addAll(data);
    searchedCityList.addAll(data); // Initialize search list

    isLoading = false;
    update([Constant.idGetCity]);
  }

  /// search logic
  void onSearchChanged(String value) {
    searchText = value;
    if (value.isEmpty) {
      searchedCityList = List.from(cityList);
    } else {
      searchedCityList = cityList.where((city) => (city.name ?? '').toLowerCase().contains(value.toLowerCase())).toList();
    }
    update([Constant.idGetCity]);
  }

  /// pagination
  Future<void> onAllCityPagination() async {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      isPaginationLoading = true;
      update([Constant.idPagination]);

      cityResponseModel = await CityApi.callApi();
      final newData = cityResponseModel?.data ?? [];
      cityList.addAll(newData);

      // Also update searched list if applicable
      if (searchText.isEmpty) {
        searchedCityList.addAll(newData);
      } else {
        searchedCityList.addAll(
          newData.where((city) => (city.name ?? '').toLowerCase().contains(searchText.toLowerCase())),
        );
      }

      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }

  onRefresh() async {
    CityApi.startPagination = 0;
    cityList.clear();
    searchedCityList.clear();
    searchText = "";

    await getAllCity();
  }
}
