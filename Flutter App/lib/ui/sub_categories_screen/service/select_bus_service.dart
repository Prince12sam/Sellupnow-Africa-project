// // make it a controller (not service)
// import 'package:get/get_state_manager/src/simple/get_controllers.dart';
// import 'package:listify/ui/add_product_screen/api/category_attributes_api.dart';
// import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/utils.dart';
//
// class SelectionBus extends GetxController {
//   String? selectedCategoryId;
//   String? selectedCategoryTitle;
//
//   void setSelection({required String id, required String title}) {
//     selectedCategoryId = id;
//     selectedCategoryTitle = title;
//     update(['selection']); // <-- GetBuilder id match
//   }
//
//   void clearSelection() {
//     selectedCategoryId = null;
//     selectedCategoryTitle = null;
//     update(['selection']);
//   }
//
//   bool isLoading = false;
//   CategoryAttributesResponseModel? categoryAttributesResponseModel;
//   List<Attribute> attributeDataList = [];
//
//   Future<void> getCategoryAttribute() async {
//     isLoading = true;
//     update([Constant.idAllAds]);
//
//     Utils.showLog("last category id user for attribute api ::: $selectedCategoryId");
//     categoryAttributesResponseModel = await CategoryAttributesApi.callApi(categoryId: selectedCategoryId);
//
//     if (categoryAttributesResponseModel?.status == true) {
//       attributeDataList = categoryAttributesResponseModel?.data ?? [];
//     }
//
//     Utils.showLog("fetch category vise attribute data : ${attributeDataList.length}");
//
//     isLoading = false;
//     update([Constant.idAllAds]);
//   }
// }
import 'package:get/get.dart';
import 'package:listify/utils/utils.dart';

class SelectionBus extends GetxService {
  final selectedCategoryId = RxnString();
  final selectedCategoryTitle = RxnString();
  final selectedCategoryImage = RxnString(); // 👈 NEW

  void setSelection({required String id, required String title, String? image}) {
    selectedCategoryId.value = id;
    selectedCategoryTitle.value = title;
    selectedCategoryImage.value = image; // 👈 NEW

    Utils.showLog("image>>>>>>>>>>> :::::::::::::$selectedCategoryTitle");
    Utils.showLog("image :::::::::::::$selectedCategoryId");
    Utils.showLog("image :::::::::::::$selectedCategoryImage");
  }

  void clearSelection() {
    selectedCategoryId.value = null;
    selectedCategoryTitle.value = null;
    selectedCategoryImage.value = null; // 👈 NEW
  }
}
