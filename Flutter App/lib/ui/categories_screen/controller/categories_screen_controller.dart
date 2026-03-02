import 'package:get/get.dart';
import 'package:listify/ui/home_screen/api/all_category_api.dart';
import 'package:listify/ui/home_screen/model/category_api_model.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/model/sub_category_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class CategoriesScreenController extends GetxController {
  bool isLoading = false;
  List<AllCategory> allCategoryList = [];
  AllCategoryResponseModel? allCategoryResponseModel;
  Map<String, dynamic> arguments = Get.arguments ?? {};
  bool search = false;
  bool mostLike = false;
  bool popular = false;
  bool subcategory = false;

  @override
  onInit() {
    init();
    getAllCategory();
    super.onInit();
  }

  init() {
    Utils.showLog("arguments :::$arguments");

    search = arguments["search"] ?? false;
    mostLike = arguments["mostLike"] ?? false;
    popular = arguments["popular"] ?? false;
    subcategory = arguments["subcategory"] ?? false;
    Utils.showLog("search categories screen :::$search");
    Utils.showLog("mostLike categories screen :::$mostLike");
    Utils.showLog("popular categories screen :::$popular");
    Utils.showLog("subcategory???????????????? :::$subcategory");
  }

  /// get all category api
  getAllCategory() async {
    isLoading = true;
    update([Constant.idAllCategory]);
    allCategoryResponseModel = await AllCategoryApi.callApi();
    allCategoryList.clear();
    allCategoryList.addAll(allCategoryResponseModel?.data ?? []);

    Utils.showLog("All category list data $allCategoryList");

    isLoading = false;
    update([Constant.idAllCategory]);
  }

  ///sub category api
  SubCategoryResponseModel? subCategoryResponseModel;
  List<Datum> subCategoryList = [];

  Future<bool> getSubCategoryApi(String categoryId) async {
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
}
