import 'dart:convert';
import 'dart:developer';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/add_product_screen/api/category_attributes_api.dart';
import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart' hide Attribute;
import 'package:listify/ui/product_detail_screen/model/product_detail_response_model.dart' hide Attribute;
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

// class AddProductScreenController extends GetxController {
//   int selectRadioIndex = -1;
//   bool selectedDropDown = false;
//   bool isLoading = false;
//   CategoryAttributesResponseModel? categoryAttributesResponseModel;
//   // FetchCategorySubAttrModel? fetchCategorySubAttrModel;
//
//   @override
//   void onInit() {
//     Utils.showLog('category id ::  ${Database.categoryId}');
//
//     getCategoryAttribute();
//     super.onInit();
//   }
//
//   void radioSelection(int index) {
//     if (selectRadioIndex == index) {
//       selectRadioIndex = -1;
//     } else {
//       selectRadioIndex = index;
//     }
//     update();
//   }
//
//   void toggleDropdown() {
//     selectedDropDown = !selectedDropDown;
//     update();
//   }
//
// //   Future<void> fetchCategorySubAttr() async {
// //     const String fakeResponse = '''
// // {
// //   "status": true,
// //   "message": "Categories, subcategories, and attributes retrieved successfully.",
// //   "categories": [
// //     {
// //       "_id": "65a605a666db2087a346cf90",
// //       "name": "Electronics & Gadgets"
// //     },
// //     {
// //       "_id": "65a605ba66db2087a346cf92",
// //       "name": "Footwear"
// //     },
// //     {
// //       "_id": "65a605df66db2087a346cf9c",
// //       "name": "Fashion & Apparel"
// //     }
// //   ],
// //   "subCategories": [
// //     {
// //       "_id": "65a60bba66db2087a346d0d2",
// //       "name": "Men's Clothing",
// //       "category": "65a605df66db2087a346cf9c"
// //     }
// //   ],
// //   "attributes": [
// //     {
// //       "_id": "65a60bba66db2087a346d0d2_field1",
// //       "subCategory": "65a60bba66db2087a346d0d2",
// //       "attributes": [
// //         {
// //           "name": "Material Description",
// //           "image": "https://example.com/images/material.jpg",
// //           "fieldType": 1,
// //           "values": [],
// //           "minLength": 10,
// //           "maxLength": 100,
// //           "isRequired": true,
// //           "isActive": true,
// //           "example": "100% premium cotton fabric"
// //         }
// //       ]
// //     },
// //     {
// //       "_id": "65a60bba66db2087a346d0d2_field2",
// //       "subCategory": "65a60bba66db2087a346d0d2",
// //       "attributes": [
// //         {
// //           "name": "Item Weight (grams)",
// //           "image": "https://example.com/images/weight.jpg",
// //           "fieldType": 2,
// //           "values": [],
// //           "minValue": 50,
// //           "maxValue": 1000,
// //           "isRequired": true,
// //           "isActive": true,
// //           "unit": "grams"
// //         }
// //       ]
// //     },
// //     {
// //       "_id": "65a60bba66db2087a346d0d2_field3",
// //       "subCategory": "65a60bba66db2087a346d0d2",
// //       "attributes": [
// //         {
// //           "name": "Product Images",
// //           "image": "https://example.com/images/upload.jpg",
// //           "fieldType": 3,
// //           "values": [],
// //           "maxFileSize": 5,
// //           "allowedTypes": ["jpg", "png", "webp"],
// //           "maxFiles": 4,
// //           "isRequired": true,
// //           "isActive": true
// //         }
// //       ]
// //     },
// //     {
// //       "_id": "65a60bba66db2087a346d0d2_field4",
// //       "subCategory": "65a60bba66db2087a346d0d2",
// //       "attributes": [
// //         {
// //           "name": "Primary Color",
// //           "image": "https://example.com/images/color-wheel.jpg",
// //           "fieldType": 4,
// //           "values": ["Black", "White", "Blue", "Gray", "Navy"],
// //           "isRequired": true,
// //           "isActive": true
// //         }
// //       ]
// //     },
// //     {
// //       "_id": "65a60bba66db2087a346d0d2_field5",
// //       "subCategory": "65a60bba66db2087a346d0d2",
// //       "attributes": [
// //         {
// //           "name": "Size",
// //           "image": "https://example.com/images/size-chart.jpg",
// //           "fieldType": 5,
// //           "values": ["XS", "S", "M", "L", "XL", "XXL", "3XL"],
// //           "isRequired": true,
// //           "isActive": true
// //         }
// //       ]
// //     },
// //     {
// //       "_id": "65a60bba66db2087a346d0d2_field6",
// //       "subCategory": "65a60bba66db2087a346d0d2",
// //       "attributes": [
// //         {
// //           "name": "Clothing Features",
// //           "image": "https://example.com/images/features.jpg",
// //           "fieldType": 6,
// //           "values": [
// //             "Wrinkle-resistant",
// //             "Stretchable",
// //             "Quick-dry",
// //             "Breathable",
// //             "Anti-microbial",
// //             "UV Protection"
// //           ],
// //           "isRequired": false,
// //           "isActive": true,
// //           "maxSelections": 3
// //         }
// //       ]
// //     }
// //   ]
// // }
// //   ''';
// //
// //     final Map<String, dynamic> jsonMap = jsonDecode(fakeResponse);
// //     fetchCategorySubAttrModel = FetchCategorySubAttrModel.fromJson(jsonMap);
// //     update();
// //   }
//
//   /// fetch category vise attribute
//   getCategoryAttribute() async {
//     isLoading = true;
//     update();
//
//     categoryAttributesResponseModel = await CategoryAttributesApi.callApi(categoryId: Database.categoryId);
//
//     Utils.showLog("fetch category vise attribute data : $categoryAttributesResponseModel");
//
//     isLoading = false;
//     update();
//   }
// }

extension FirstWhereOrNullExtension<E> on Iterable<E> {
  E? firstWhereOrNull(bool Function(E) test) {
    for (var element in this) {
      if (test(element)) return element;
    }
    return null;
  }
}

class AddProductScreenController extends GetxController {
  Map<int, int> selectedRadioIndices = {}; // For fieldType 4
  Map<int, String> textFieldValues = {}; // For fieldType 1
  Map<int, List<String>> selectedChipValues = {}; // For fieldType 6
  Map<int, PlatformFile> selectedFiles = {}; // For fieldType 3
  Map<int, String> selectedDropdownValues = {}; // For fieldType == 5
  Map<String, dynamic> arguments = Get.arguments ?? {};
  List<Attribute> attributeDataList = [];
  Product? adsData;
  bool selectedDropDown = false;
  bool isLoading = false;
  bool isEdit = false;
  String? image;
  Map<int, String> apiFileNames = {}; // fieldType 3 (File picker) API file names

  // CategoryAttributesResponseModel? categoryAttributesResponseModel;

  @override
  void onInit() {
    // getCategoryAttribute();
    init();
    super.onInit();
  }

  Future<void> init() async {
    Utils.showLog('category id ::  ${Database.categoryId}');

    log("arguments api:::::::::::::::::::::$arguments");
    log("arguments api:::::::::::::::::::::${arguments['mainImage']}");
    log("arguments api:::::::::::::::::::::${arguments['selectedImages']}");
    isEdit = arguments['editApi'] ?? false;
    adsData = arguments['ad'];
    final data = arguments['attributes'];
    final title = arguments['title'];
    final subtitle = arguments['subtitle'];
    final price = arguments['price'];
    final description = arguments['description'];
    final categoryId = arguments['categoryId'] ?? adsData?.category?.id;
    final locationData = arguments['locationData'] ?? adsData?.location;

    Utils.showLog("ad:::::::::::::${jsonEncode(adsData?.attributes)}");
    Utils.showLog("editApi  :::::::::::::::$isEdit");
    Utils.showLog("locationData  :::::::::::::::$locationData");


    Utils.showLog('Received Product: $title | $subtitle | $price | $description  |  $categoryId');
    attributeDataList = _extractAttributesFromArgs(data);

    // Fallback: fetch directly from API if args did not carry attributes.
    if (attributeDataList.isEmpty && (categoryId?.toString().isNotEmpty ?? false)) {
      final resp = await CategoryAttributesApi.callApi(categoryId: categoryId.toString());
      attributeDataList = resp?.data ?? [];
      Utils.showLog("Fetched attributes from API fallback: ${attributeDataList.length}");
    }

    if (attributeDataList.isEmpty) {
      Utils.showLog("Attributes are empty for categoryId=$categoryId");
    }

    // mainImage = arguments['mainImage'];
    // selectedImages = arguments['selectedImages'];
    preselectValuesFromApi();

    Utils.showLog("Main Image:: ${arguments['mainImage']} ||| selected image :: ${arguments['selectedImages']}");
    update();
  }

  List<Attribute> _extractAttributesFromArgs(dynamic raw) {
    if (raw is List<Attribute>) {
      return raw;
    }

    if (raw is List) {
      final out = <Attribute>[];
      for (final item in raw) {
        if (item is Attribute) {
          out.add(item);
          continue;
        }

        if (item is Map) {
          try {
            out.add(Attribute.fromJson(Map<String, dynamic>.from(item)));
          } catch (_) {}
        }
      }
      return out;
    }

    return [];
  }

  void preselectValuesFromApi1() {
    if (adsData?.attributes == null || adsData!.attributes!.isEmpty) return;

    for (int i = 0; i < attributeDataList.length; i++) {
      // Find the API attribute that matches by name
      final apiAttr = adsData!.attributes!.firstWhereOrNull((a) => a.name == attributeDataList[i].name);



      if (apiAttr == null) continue; // nothing to prefill

      // ✅ Radio buttons
      if (attributeDataList[i].fieldType == 4 && apiAttr.value is String) {
        final index = attributeDataList[i].values?.indexOf(apiAttr.value) ?? -1;
        if (index != -1) selectedRadioIndices[i] = index;
      }

      // ✅ Text / Numeric fields
      if ((attributeDataList[i].fieldType == 1 || attributeDataList[i].fieldType == 2) && apiAttr.value is String) {
        textFieldValues[i] = apiAttr.value;
      }

      // ✅ Dropdown
      if (attributeDataList[i].fieldType == 5 && apiAttr.value is String) {
        selectedDropdownValues[i] = apiAttr.value;
      }

      // ✅ Multi-Select Chips
      if (attributeDataList[i].fieldType == 6) {
        if (apiAttr.value is List) {
          // Directly assign if already a list
          selectedChipValues[i] = List<String>.from(apiAttr.value);
        } else if (apiAttr.value is String) {
          // Convert comma-separated string to list
          selectedChipValues[i] = apiAttr.value.split(',').map((e) => e.trim()).where((e) => e.isNotEmpty).toList();
        }
      }

      // ✅ File Picker (if you want to show preview)
      if (attributeDataList[i].fieldType == 3 && apiAttr.value is Map) {
        final fileData = apiAttr.value;
        if (fileData['name'] != null) {
          // File info store in selectedFiles
          selectedFiles[i] = PlatformFile(
            name: fileData['name'],
            size: fileData['size'] ?? 0,
            identifier: fileData['extension'],
            path: null, // null because it's from API, not local
          );

          // 🔥 File name store in a variable (map)
          apiFileNames[i] = fileData['name'];

          Utils.showLog("Saved API File Name for $i >>> ${apiFileNames[i]}");
        }
      }


    }

    update(); // refresh UI
  }

  void preselectValuesFromApi() {
    if (adsData?.attributes == null || adsData!.attributes!.isEmpty) return;

    for (int i = 0; i < attributeDataList.length; i++) {
      final apiAttr = adsData!.attributes!.firstWhereOrNull((a) => a.name == attributeDataList[i].name);
      if (apiAttr == null) continue;

      // ✅ Radio buttons
      if (attributeDataList[i].fieldType == 4 && apiAttr.value is String) {
        final index = attributeDataList[i].values?.indexOf(apiAttr.value) ?? -1;
        if (index != -1) selectedRadioIndices[i] = index;
      }

      // ✅ Text / Numeric fields
      if ((attributeDataList[i].fieldType == 1 || attributeDataList[i].fieldType == 2) && apiAttr.value is String) {
        textFieldValues[i] = apiAttr.value;
      }

      // ✅ Dropdown
      if (attributeDataList[i].fieldType == 5 && apiAttr.value is String) {
        selectedDropdownValues[i] = apiAttr.value;
      }

      // ✅ Multi-Select Chips
      if (attributeDataList[i].fieldType == 6) {
        if (apiAttr.value is List) {
          selectedChipValues[i] = List<String>.from(apiAttr.value);
        } else if (apiAttr.value is String) {
          selectedChipValues[i] =
              apiAttr.value.split(',').map((e) => e.trim()).where((e) => e.isNotEmpty).toList();
        }
      }

      // ✅ File Picker (show preview if API has file)
      if (attributeDataList[i].fieldType == 3 && apiAttr.value is Map) {
        final fileData = apiAttr.value;
        if (fileData['name'] != null) {
          selectedFiles[i] = PlatformFile(
            name: fileData['name'],
            size: fileData['size'] ?? 0,
            identifier: fileData['extension'],
            path: null,
          );
          apiFileNames[i] = fileData['name'];
        }
      }
    }

    // 🟢 This ensures button turns RED if all required fields are filled from API
    checkIfAllRequiredFieldsFilled();

    update(); // refresh UI
  }


  /// radio selection
  // void radioSelection(int attributeIndex, int selectedIndex) {
  //   selectedRadioIndices[attributeIndex] = selectedIndex;
  //   update();
  //   Utils.showLog("Selected value for field $attributeIndex is index $selectedIndex → ${attributeDataList[attributeIndex].values?[selectedIndex]}");
  // }

  void radioSelection(int attributeIndex, int selectedIndex) {
    selectedRadioIndices[attributeIndex] = selectedIndex;
    checkIfAllRequiredFieldsFilled();
    update();
  }

  /// text field values
  // void updateTextValue(int attributeIndex, String value) {
  //   textFieldValues[attributeIndex] = value;
  //   update();
  //   Utils.showLog("Text input for $attributeIndex: $value");
  // }
  void updateTextValue(int attributeIndex, String value) {
    textFieldValues[attributeIndex] = value;
    checkIfAllRequiredFieldsFilled();
    update();
  }


  void toggleDropdown() {
    selectedDropDown = !selectedDropDown;
    update();
  }

  // void updateDropdownValue(int attributeIndex, String value) {
  //   selectedDropdownValues[attributeIndex] = value;
  //   update();
  //   Utils.showLog("Dropdown value for $attributeIndex: $value");
  // }
  void updateDropdownValue(int attributeIndex, String value) {
    selectedDropdownValues[attributeIndex] = value;
    checkIfAllRequiredFieldsFilled();
    update();
  }

  void updateChipSelection(int attributeIndex, List<String> values) {
    selectedChipValues[attributeIndex] = values;
    checkIfAllRequiredFieldsFilled();
    update();
  }

  void selectFile(int attributeIndex, PlatformFile file) {
    selectedFiles[attributeIndex] = file;
    checkIfAllRequiredFieldsFilled();
    update();
  }



  void printAllEnteredValues() {
    final attributes = attributeDataList;

    for (int i = 0; i < attributes.length; i++) {
      final attr = attributes[i];

      if (attr.fieldType == 4) {
        final selectedIndex = selectedRadioIndices[i];
        final selectedValue = (selectedIndex != null && selectedIndex < (attr.values?.length ?? 0)) ? attr.values![selectedIndex] : 'Not Selected';
        Utils.showLog('Radio Field [${attr.name}]: $selectedValue');
      }

      if (attr.fieldType == 1 || attr.fieldType == 2) {
        final enteredText = textFieldValues[i] ?? 'Not Entered';
        Utils.showLog('Text Field [${attr.name}]: $enteredText');
      }

      if (attr.fieldType == 3) {
        final file = selectedFiles[i];
        if (file != null) {
          Utils.showLog('File Field [${attr.name}]: File Name = ${file.name}, Size = ${file.size}, Extension = ${file.extension}');
        } else {
          Utils.showLog('File Field [${attr.name}]: No file selected');
        }
      }

      if (attr.fieldType == 5) {
        final selected = selectedDropdownValues[i] ?? 'Not Selected';
        Utils.showLog('Dropdown Field [${attr.name}]: $selected');
      }

      if (attr.fieldType == 6) {
        final selected = selectedChipValues[i]?.join(', ') ?? 'None Selected';
        Utils.showLog('Multi Select Field [${attr.name}]: $selected');
      }
    }
  }

  List<String> getFilledAttributeListAsString() {
    List<String> result = [];

    for (int i = 0; i < attributeDataList.length; i++) {
      final attr = attributeDataList[i];
      final fieldType = attr.fieldType;
      final name = attr.name ?? 'Attribute_$i';
      final image = attr.image ?? "";

      dynamic value;

      switch (fieldType) {
        case 1:
        case 2:
          final text = textFieldValues[i];
          if (text != null && text.trim().isNotEmpty) value = text;
          break;

        case 3:
          final file = selectedFiles[i];
          if (file != null) {
            value = {
              'name': file.name,
              'size': file.size,
              'extension': file.extension,
            };
          }
          break;

        case 4:
          final index = selectedRadioIndices[i];
          if (index != null && index < (attr.values?.length ?? 0)) {
            value = attr.values![index];
          }
          break;

        case 5:
          final dropdownValue = selectedDropdownValues[i];
          if (dropdownValue != null && dropdownValue.isNotEmpty) {
            value = dropdownValue;
          }
          break;

        case 6:
          final chips = selectedChipValues[i];
          if (chips != null && chips.isNotEmpty) {
            value = chips;
          }
          break;
      }

      if (value != null) {
        final map = {
          'name': name,
          'value': value,
          'image': image,
          // 'fieldType' : fieldType
        };
        result.add(Utils.toJson(map)); // 👈 convert Map to JSON string
      }
    }

    return result;
  }

  validateRequiredFields(BuildContext context) {
    for (int i = 0; i < attributeDataList.length; i++) {
      final attr = attributeDataList[i];

      Utils.showLog("!selectedFiles.containsKey(i)${!selectedFiles.containsKey(i)}");
      Utils.showLog("!selectedFiles.containsKey(i)${attr.image}");

      if (attr.isRequired != true) continue;

      switch (attr.fieldType) {
        case 1: // Text Field
        case 2: // Numeric Field
          if ((textFieldValues[i] ?? '').trim().isEmpty) {
            Utils.showToast(context, '${attr.name} is required');
            return false;
          }
          break;

        case 3: // File Picker
          if (!selectedFiles.containsKey(i) || selectedFiles[i] == null) {
            Utils.showToast(context, '${attr.name} is required');
            return false;
          }
          break;

        case 4: // Radio
          if (!selectedRadioIndices.containsKey(i)) {
            Utils.showToast(context, '${attr.name} is required');
            return false;
          }
          break;

        case 5: // Dropdown (assuming selected value tracking to be added)
          // Implement dropdown value tracking in controller if not done yet
          // For now, assume map: Map<int, String> selectedDropdownValues
          if ((selectedDropdownValues[i] ?? '').isEmpty) {
            Utils.showToast(context, '${attr.name} is required');
            return false;
          }
          break;

        case 6: // Chip Selection
          if ((selectedChipValues[i]?.isEmpty ?? true)) {
            Utils.showToast(context, '${attr.name} is required');
            return false;
          }
          break;

        default:
          break;
      }
    }

    final List<String> attributesAsString = getFilledAttributeListAsString();
    Utils.showLog("Final filled attributes map: $attributesAsString");

    Utils.showLog('attributes passed on confirm location screen  ::: ::::: :::: ::::: ::::: :::: :::: $attributeDataList');
    // ✅ Only navigate if validation is successful
    Get.toNamed(
      AppRoutes.confirmLocationScreen,
      arguments: {
        ...arguments, // keep all original arguments (mainImage, selectedImages, etc.)
        'attributes': attributesAsString,
        'mainImage': arguments['mainImage'],
        'selectedImages': arguments['selectedImages'],
        'ad': adsData,
        'editApi': isEdit,
        'adId': adsData?.id,
      },

      //     arguments: {
      //   arguments.addAll({
      //     'attributes': '',
      //   })
      // }
    );

    return true; // All required fields are valid
  }
  bool isButtonEnabled = false;

  /// ✅ Check only required fields, ignore optional ones
  void checkIfAllRequiredFieldsFilled() {
    bool allFilled = true;

    for (int i = 0; i < attributeDataList.length; i++) {
      final attr = attributeDataList[i];
      if (attr.isRequired != true) continue; // ignore non-required

      final apiAttr = adsData?.attributes?.firstWhereOrNull((a) => a.name == attr.name);

      switch (attr.fieldType) {
        case 1: // Text
        case 2: // Number
          final value = textFieldValues[i] ?? apiAttr?.value?.toString();
          if (value == null || value.trim().isEmpty) allFilled = false;
          break;

        case 3: // File
          final hasFile = selectedFiles.containsKey(i) || (apiFileNames.containsKey(i));
          if (!hasFile) allFilled = false;
          break;

        case 4: // Radio
          final index = selectedRadioIndices[i];
          final apiValueIndex = attr.values?.indexOf(apiAttr?.value ?? '');
          if (index == null && (apiValueIndex == null || apiValueIndex == -1)) allFilled = false;
          break;

        case 5: // Dropdown
          final value = selectedDropdownValues[i] ?? apiAttr?.value?.toString();
          if (value == null || value.isEmpty) allFilled = false;
          break;

        case 6: // Chips
          final chips = selectedChipValues[i] ?? (apiAttr?.value is List ? List<String>.from(apiAttr!.value) :
          apiAttr?.value?.toString().split(',').map((e) => e.trim()).toList());
          if (chips == null || chips.isEmpty) allFilled = false;
          break;
      }
    }

    isButtonEnabled = allFilled;
    update(['attribute_form']);
  }


}
