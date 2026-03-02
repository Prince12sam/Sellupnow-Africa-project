import 'package:get/get.dart';
import 'package:listify/ui/home_screen/api/all_category_api.dart';
import 'package:listify/ui/home_screen/model/category_api_model.dart';
import 'package:listify/ui/sub_categories_screen/api/sub_category_api.dart';
import 'package:listify/ui/sub_categories_screen/model/sub_category_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class AddListingScreenController extends GetxController {
  bool isLoading = false;
  // CategoryResponseModel? hierarchicalCategoryResponseModel;

  List<AllCategory> allCategoryList = [];
  AllCategoryResponseModel? allCategoryResponseModel;

  bool addListingScreen = true;

  @override
  onInit() {
    super.onInit();
    // getHierarchicalCategoryApi();
    getAllCategory();
  }

  /// Hierarchical category list data
  // getHierarchicalCategoryApi() async {
  //   isLoading = true;
  //   update([Constant.idAllCategory]);
  //   hierarchicalCategoryResponseModel = await HierarchicalCategoryApi.callApi();
  //   Utils.showLog("All category list data $hierarchicalCategoryResponseModel");
  //   isLoading = false;
  //   update([Constant.idAllCategory]);
  // }

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
    subCategoryResponseModel =
        await SubCategoryApi.callApi(parentId: categoryId);
    subCategoryList.clear();
    subCategoryList.addAll(subCategoryResponseModel?.data ?? []);

    Utils.showLog(
        "sub category list data: ${subCategoryList.map((e) => e.name).toList()}");

    update(['appbar']); // For AppBar title update

    return subCategoryList.isEmpty;
  }
}
