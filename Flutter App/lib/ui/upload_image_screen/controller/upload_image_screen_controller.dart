import 'dart:developer';

import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/add_product_screen/api/category_attributes_api.dart';
import 'package:listify/ui/add_product_screen/model/category_attributes_response_model.dart';
import 'package:listify/ui/product_detail_screen/model/product_detail_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';

// class UploadImageScreenController extends GetxController {
//   final ImagePicker _picker = ImagePicker();
//   String? mainImage;
//   List<String> selectedImages = <String>[].obs;
//   String? title;
//   String? subtitle;
//   String? price;
//   String? description;
//   String? categoryId;
//   Map<String, dynamic> arguments = Get.arguments ?? {};
//   Product? adsData;
//   String? apiMainImage;
//   List<String> apiGalleryImages = [];
//   bool isEdit = false;
//   List<String> get finalGalleryImages =>
//       [...apiGalleryImages, ...selectedImages].take(5).toList();
//   @override
//   void onInit() {
//     super.onInit();
//
//     init();
//   }
//
//   init() {
//     log("arguments api:::::::::::::::::::::$arguments");
//
//     if (arguments['ad'] is Product) {
//       adsData = arguments['ad'] as Product;
//     }
//
//     apiMainImage = adsData?.primaryImage?.isNotEmpty == true ? adsData!.primaryImage : null;
//     apiGalleryImages = adsData?.galleryImages ?? [];
//
//     Utils.showLog("API Main Image: $apiMainImage");
//     Utils.showLog("API Gallery Images: $apiGalleryImages");
//
//     isEdit = arguments['editApi'] ?? false;
//     title = arguments['title'];
//     subtitle = arguments['subtitle'];
//     price = arguments['price'];
//     description = arguments['description'];
//     categoryId = arguments['categoryId'] ?? adsData?.category?.id;
//
//     // mainImage = (adsData?.primaryImage?.isNotEmpty ?? false)
//     //     ? (adsData!.primaryImage!.startsWith('http') ? adsData!.primaryImage! : "${adsData!.primaryImage!}")
//     //     : null;
//     // selectedImages = (adsData?.galleryImages ?? []).map((img) => (img.startsWith('http') ? img : "$img")).toList();
//
//     Utils.showLog("mainImage::::::::::::::$mainImage");
//     Utils.showLog("selectedImages:::::::::::::$selectedImages");
//
//     Utils.showLog("primaryImage::::::::::::::${adsData?.primaryImage}");
//     Utils.showLog("galleryImages::::::::::::::${adsData?.galleryImages}");
//
//     Utils.showLog('Received Product: $title | $subtitle | $price | $description  |  $categoryId');
//     Utils.showLog('categoryId  :::::::::::::::: $categoryId');
//     Utils.showLog('editApi  :::::::::::::::: ${arguments['editApi']}');
//   }
//
//   String getFullImageUrl(String image) {
//     if (image.startsWith('http') || image.startsWith('https')) {
//       return image; // already full url
//     } else {
//       return "${Api.baseUrl}$image"; // relative hoy to prefix
//     }
//   }
//
//   Future<void> pickSingleImage() async {
//     try {
//       final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
//
//       if (Get.isDialogOpen!) {
//         Get.back();
//       }
//
//       Utils.showLog("Image picked: ${image?.path}"); // Debug
//
//       if (image != null) {
//         mainImage = image.path;
//         update();
//         Utils.showLog("Image added successfully!"); // Debug
//       } else {
//         Utils.showLog("No image selected or operation cancelled");
//       }
//     } catch (e) {
//       Utils.showLog('Error picking image: $e');
//     }
//   }
//
//   void removeImage({bool fromApi = false}) {
//     Utils.showLog("Removing image");
//     // mainImage = null;
//
//     if (fromApi) {
//       apiMainImage = null; // remove API main image
//     } else {
//       mainImage = null; // remove picked main image
//     }
//     update();
//     Utils.showLog("Image removed successfully!");
//   }
//
// /*  Future<void> pickOtherImages() async {
//     try {
//       final List<XFile> images = await _picker.pickMultiImage();
//
//       if (Get.isDialogOpen!) {
//         Get.back();
//       }
//
//       Utils.showLog("Images picked: ${images.length}"); // Debug
//
//       if (images.isNotEmpty) {
//         if ((selectedImages.length + images.length) <= 5) {
//           selectedImages.addAll(images.map((img) => img.path));
//           update();
//           Utils.showLog("Images added successfully!"); // Debug
//         }
//       } else {
//         Utils.showLog("No images selected or operation cancelled");
//       }
//     } catch (e) {
//       Utils.showLog('Error picking images: $e');
//     }
//   }
//
//   void removeOtherImage(int index, {bool fromApi = false}) {
//     Utils.showLog("Removing image at index: $index");
//
//     if (fromApi) {
//       if (index >= 0 && index < apiGalleryImages.length) {
//         apiGalleryImages.removeAt(index);
//         update();
//         Utils.showLog("Image removed successfully!");
//       }
//     } else {
//       if (index >= 0 && index < selectedImages.length) {
//         selectedImages.removeAt(index);
//         update();
//         Utils.showLog("Image removed successfully!");
//       }
//     }
//   }*/
//
// // Helper getters
//   int get _totalCount => apiGalleryImages.length + selectedImages.length;
//   int get _remainingSlots => 5 - _totalCount;
//
//
//   List<int> removedIndexes = [];
//
//
//   Future<void> pickOtherImages() async {
//     try {
//       final images = await _picker.pickMultiImage();
//
//       if ((Get.isDialogOpen ?? false)) {
//         Get.back();
//       }
//
//       Utils.showLog("Images picked: ${images.length}");
//
//       if (images.isEmpty) {
//         Utils.showLog("No images selected or operation cancelled");
//         return;
//       }
//
//       final remaining = _remainingSlots;
//       if (remaining <= 0) {
//         Utils.showLog("Max 5 images allowed (API + Local). No more can be added.");
//         return;
//       }
//
//       // Take only what fits
//       final toAdd = images.take(remaining).map((x) => x.path).toList();
//       if (toAdd.isNotEmpty) {
//         selectedImages.addAll(toAdd);
//         update();
//         Utils.showLog("Added ${toAdd.length} image(s). Total now: $_totalCount (cap 5).");
//       }
//
//       // If some were ignored due to cap, log it
//       final ignored = images.length - toAdd.length;
//       if (ignored > 0) {
//         Utils.showLog("$ignored image(s) ignored due to 5-image cap (API + Local).");
//       }
//     } catch (e) {
//       Utils.showLog('Error picking images: $e');
//     }
//   }
//
//   void removeOtherImage(int index) {
//     // merged list from API + Local
//     final merged = finalGalleryImages;
//
//     if (index < 0 || index >= merged.length) {
//       Utils.showLog("❌ Invalid index: $index");
//       return;
//     }
//
//     final imageToRemove = merged[index];
//
//     if (apiGalleryImages.contains(imageToRemove)) {
//       // remove from API gallery
//       final apiIndex = apiGalleryImages.indexOf(imageToRemove);
//       apiGalleryImages.removeAt(apiIndex);
//
//       // save the original apiIndex for backend
//       removedIndexes.add(apiIndex);
//
//       Utils.showLog(
//           "✅ Removed API image at global index $index (apiIndex: $apiIndex)");
//       Utils.showLog("📌 RemovedIndexes: $removedIndexes");
//     } else if (selectedImages.contains(imageToRemove)) {
//       // remove from local gallery
//       final localIndex = selectedImages.indexOf(imageToRemove);
//       selectedImages.removeAt(localIndex);
//
//       Utils.showLog(
//           "✅ Removed Local image at global index $index (localIndex: $localIndex)");
//     }
//
//     update();
//   }
//
//   int get totalCount => apiGalleryImages.length + selectedImages.length;
//   int get remainingSlots => 5 - totalCount;
//   /// get category wise attribute
//   bool isLoading = false;
//   CategoryAttributesResponseModel? categoryAttributesResponseModel;
//
//   getCategoryAttribute() async {
//     isLoading = true;
//     update();
//
//     Utils.showLog("last category id user for attribute api ::: $categoryId");
//
//     categoryAttributesResponseModel = await CategoryAttributesApi.callApi(categoryId: categoryId);
//
//     Utils.showLog("fetch category vise attribute data : $categoryAttributesResponseModel");
//
//     isLoading = false;
//     update();
//   }
//
//   ///pick kar ae image argument ma jay che replace thai jay che ae function
//   Future<bool> validationForImage1() async {
//     Utils.showLog("Main Image Path: $mainImage | API Main: $apiMainImage");
//     Utils.showLog("Other Selected Images: $selectedImages | API Gallery: $apiGalleryImages");
//     Utils.showLog("finalGalleryImages: $finalGalleryImages");
//
//     if ((mainImage == null || mainImage!.isEmpty) && (apiMainImage == null || apiMainImage!.isEmpty)) {
//       Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectMainCoverImage.name.tr);
//       return false;
//     }
//
//     if (selectedImages.isEmpty && apiGalleryImages.isEmpty) {
//       Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectAtLeastOneOtherImage.name.tr);
//       return false;
//     }
//
//     await getCategoryAttribute();
//     final attributes = categoryAttributesResponseModel?.data;
//
//     final Map<String, dynamic> navuArguments = {
//       ...arguments,
//       'ad': adsData,
//       'adId': adsData?.id,
//       'editApi': isEdit,
//     };
//
//     if (mainImage != null && mainImage!.isNotEmpty) {
//       navuArguments['mainImage'] = mainImage;
//       navuArguments['editApi'] = isEdit;
//     }
//
//     if (selectedImages.isNotEmpty) {
//       navuArguments['selectedImages'] = selectedImages;
//       navuArguments['editApi'] = isEdit;
//     } else {
//       navuArguments['selectedImages'] = finalGalleryImages;
//       navuArguments['editApi'] = isEdit;
//     }
//
//     if (attributes != null && attributes.isNotEmpty) {
//       navuArguments['attributes'] = attributes;
//       navuArguments['editApi'] = isEdit;
//       Get.toNamed(AppRoutes.addProductScreen, arguments: navuArguments);
//     } else {
//       navuArguments['editApi'] = isEdit;
//       Get.toNamed(AppRoutes.addProductScreen, arguments: navuArguments);
//     }
//
//     return true;
//   }
//
//   /// changes  function
//   Future<bool> validationForImage() async {
//     Utils.showLog("Main Image Path: $mainImage | API Main: $apiMainImage");
//     Utils.showLog("Other Selected Images: $selectedImages | API Gallery: $apiGalleryImages");
//     Utils.showLog("finalGalleryImages: $finalGalleryImages");
//
//     if ((mainImage == null || mainImage!.isEmpty) && (apiMainImage == null || apiMainImage!.isEmpty)) {
//       Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectMainCoverImage.name.tr);
//       return false;
//     }
//
//     if (finalGalleryImages.isEmpty) {
//       Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectAtLeastOneOtherImage.name.tr);
//       return false;
//     }
//
//     await getCategoryAttribute();
//     final attributes = categoryAttributesResponseModel?.data;
//
//     Utils.showLog("finalGalleryImages>>>>>>>>>>>>>>>>>>>>>>>>>$finalGalleryImages");
//     Utils.showLog("removedIndexes>>>>>>>>>>>>>>>>>>>>>>>>>$removedIndexes");
//
//     final Map<String, dynamic> navuArguments = {
//       ...arguments,
//       'ad': adsData,
//       'adId': adsData?.id,
//       'editApi': isEdit,
//       'mainImage': (mainImage?.isNotEmpty == true) ? mainImage : apiMainImage,
//       'selectedImages': finalGalleryImages,
//       'removeGalleryIndexes ': removedIndexes,
//     };
//
//     if (attributes != null && attributes.isNotEmpty) {
//       navuArguments['attributes'] = attributes;
//     }
//
//     Get.toNamed(AppRoutes.addProductScreen, arguments: navuArguments);
//
//     return true;
//   }
//
//   @override
//   void onClose() {
//     selectedImages = [];
//     mainImage = null;
//     super.onClose();
//   }
// }

class UploadImageScreenController extends GetxController {
  final ImagePicker _picker = ImagePicker();

  String? mainImage;
  // ❗️use a normal List, not RxList, since you call update()
  List<String> selectedImages = [];
  String? title;
  String? subtitle;
  String? price;
  String? description;
  String? categoryId;

  Map<String, dynamic> arguments = Get.arguments ?? {};
  Product? adsData;

  String? apiMainImage;
  List<String> apiGalleryImages = [];

  bool isEdit = false;

  /// Final list used in UI (ALWAYS ≤ 5)
  List<String> get finalGalleryImages =>
      [...apiGalleryImages, ...selectedImages].take(5).toList();

  /// Total and remaining under the 5-cap
  int get _totalCount => apiGalleryImages.length + selectedImages.length;
  int get _remainingSlots => (5 - _totalCount).clamp(0, 5);

  /// How many API images are currently visible in finalGalleryImages
  int get shownApiCount => apiGalleryImages.length.clamp(0, 5);

  /// Exposed (if you need outside)
  int get totalCount => _totalCount;
  int get remainingSlots => _remainingSlots;

  /// Track which combined indices were removed (as you asked)
  final List<int> removedIndexes = [];

  @override
  void onInit() {
    super.onInit();
    init();
  }

  void init() {
    log("arguments api:::::::::::::::::::::$arguments");

    if (arguments['ad'] is Product) {
      adsData = arguments['ad'] as Product;
    }

    apiMainImage = (adsData?.primaryImage?.isNotEmpty == true)
        ? adsData!.primaryImage
        : null;

    apiGalleryImages = List<String>.from(adsData?.galleryImages ?? []);

    Utils.showLog("API Main Image: $apiMainImage");
    Utils.showLog("API Gallery Images: $apiGalleryImages");

    isEdit = arguments['editApi'] ?? false;
    title = arguments['title'];
    subtitle = arguments['subtitle'];
    price = arguments['price'];
    description = arguments['description'];
    categoryId = arguments['categoryId'] ?? adsData?.category?.id;

    Utils.showLog("mainImage::::::::::::::$mainImage");
    Utils.showLog("selectedImages:::::::::::::$selectedImages");
    Utils.showLog("primaryImage::::::::::::::${adsData?.primaryImage}");
    Utils.showLog("galleryImages::::::::::::::${adsData?.galleryImages}");
    Utils.showLog('Received Product: $title | $subtitle | $price | $description  |  $categoryId');
    Utils.showLog('categoryId  :::::::::::::::: $categoryId');
    Utils.showLog('editApi  :::::::::::::::: ${arguments['editApi']}');

    // Optional: enforce the 5-cap at init (trim API if needed)
    _reconcileCapOnInit();
  }

  void _reconcileCapOnInit() {
    // de-dup API (optional)
    final seen = <String>{};
    apiGalleryImages = apiGalleryImages.where((p) => seen.add(p)).toList();

    if (apiGalleryImages.length >= 5) {
      apiGalleryImages = apiGalleryImages.take(5).toList();
      selectedImages.clear();
    } else {
      final remain = 5 - apiGalleryImages.length;
      if (selectedImages.length > remain) {
        selectedImages = selectedImages.take(remain).toList();
      }
    }
    update();
  }

  String getFullImageUrl(String image) {
    if (image.startsWith('http')) return image;
    return "${Api.baseUrl}$image";
  }

  // ---------------- Main image (single) ----------------

  Future<void> pickSingleImage() async {
    try {
      final XFile? image = await _picker.pickImage(source: ImageSource.gallery);

      if ((Get.isDialogOpen ?? false)) {
        Get.back();
      }

      Utils.showLog("Image picked: ${image?.path}");

      if (image != null) {
        mainImage = image.path;
        update();
        Utils.showLog("Image added successfully!");
      } else {
        Utils.showLog("No image selected or operation cancelled");
      }
    } catch (e) {
      Utils.showLog('Error picking image: $e');
    }
  }

  void removeImage({bool fromApi = false}) {
    Utils.showLog("Removing image (main). fromApi=$fromApi");
    if (fromApi) {
      apiMainImage = null;
    } else {
      mainImage = null;
    }
    update();
    Utils.showLog("Image removed successfully!");
  }

  // ---------------- Gallery (multi) ----------------

  Future<void> pickOtherImages() async {
    try {
      final images = await _picker.pickMultiImage();

      if ((Get.isDialogOpen ?? false)) {
        Get.back();
      }

      Utils.showLog("Images picked: ${images.length}");

      if (images.isEmpty) {
        Utils.showLog("No images selected or operation cancelled");
        return;
      }

      final remaining = _remainingSlots;
      if (remaining <= 0) {
        Utils.showLog("Max 5 images allowed (API + Local). No more can be added.");
        return;
      }

      // Take only what fits
      final toAdd = images.take(remaining).map((x) => x.path).toList();

      // Optional: avoid exact duplicates vs API/local
      final existing = {...apiGalleryImages, ...selectedImages};
      final filtered = toAdd.where((p) => !existing.contains(p)).toList();

      if (filtered.isNotEmpty) {
        selectedImages.addAll(filtered);
        update();
        Utils.showLog("Added ${filtered.length} image(s). Total now: $_totalCount (cap 5).");
      }

      final ignored = images.length - filtered.length;
      if (ignored > 0) {
        Utils.showLog("$ignored image(s) ignored (cap or duplicates).");
      }
    } catch (e) {
      Utils.showLog('Error picking images: $e');
    }
  }

  /// ✅ FIXED: remove strictly by combined index (position), not by value/indexOf
  void removeOtherImage(int index) {
    final totalShown = finalGalleryImages.length;

    if (index < 0 || index >= totalShown) {
      Utils.showLog("❌ Invalid index: $index (shown=$totalShown)");
      return;
    }

    final apiShown = shownApiCount; // how many API images are visible
    final bool isApi = index < apiShown;
    final int realIndex = isApi ? index : (index - apiShown);

    if (isApi) {
      if (realIndex >= 0 && realIndex < apiGalleryImages.length) {
        final removed = apiGalleryImages.removeAt(realIndex);
        Utils.showLog("✅ Removed API image at combined $index (apiIndex: $realIndex) -> $removed");
      } else {
        Utils.showLog("⚠️ api realIndex OOR: $realIndex (len=${apiGalleryImages.length})");
        return;
      }
    } else {
      if (realIndex >= 0 && realIndex < selectedImages.length) {
        final removed = selectedImages.removeAt(realIndex);
        Utils.showLog("✅ Removed Local image at combined $index (localIndex: $realIndex) -> $removed");
      } else {
        Utils.showLog("⚠️ local realIndex OOR: $realIndex (len=${selectedImages.length})");
        return;
      }
    }

    // Keep track of which combined index user removed (as you wanted)
    removedIndexes.add(index);
    Utils.showLog("📌 removedIndexes => $removedIndexes");
    Utils.showLog("👉 Total now: $_totalCount");

    update();
  }

  // --------------- Attributes / Navigation ----------------

  bool isLoading = false;
  CategoryAttributesResponseModel? categoryAttributesResponseModel;

  Future<void> getCategoryAttribute() async {
    isLoading = true;
    update();

    Utils.showLog("last category id user for attribute api ::: $categoryId");
    categoryAttributesResponseModel =
    await CategoryAttributesApi.callApi(categoryId: categoryId);
    Utils.showLog("fetch category vise attribute data : $categoryAttributesResponseModel");

    isLoading = false;
    update();
  }

  /// (legacy) keep if needed
  Future<bool> validationForImage1() async {
    Utils.showLog("Main Image Path: $mainImage | API Main: $apiMainImage");
    Utils.showLog("Other Selected Images: $selectedImages | API Gallery: $apiGalleryImages");
    Utils.showLog("finalGalleryImages: $finalGalleryImages");

    if ((mainImage?.isEmpty ?? true) && (apiMainImage?.isEmpty ?? true)) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectMainCoverImage.name.tr);
      return false;
    }

    if (selectedImages.isEmpty && apiGalleryImages.isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectAtLeastOneOtherImage.name.tr);
      return false;
    }

    await getCategoryAttribute();
    final attributes = categoryAttributesResponseModel?.data;

    final Map<String, dynamic> navuArguments = {
      ...arguments,
      'ad': adsData,
      'adId': adsData?.id,
      'editApi': isEdit,
    };

    if (mainImage?.isNotEmpty == true) {
      navuArguments['mainImage'] = mainImage;
      navuArguments['editApi'] = isEdit;
    }

    if (selectedImages.isNotEmpty) {
      navuArguments['selectedImages'] = selectedImages;
      navuArguments['editApi'] = isEdit;
    } else {
      navuArguments['selectedImages'] = finalGalleryImages;
      navuArguments['editApi'] = isEdit;
    }

    if (attributes != null && attributes.isNotEmpty) {
      navuArguments['attributes'] = attributes;
      navuArguments['editApi'] = isEdit;
    }

    Get.toNamed(AppRoutes.addProductScreen, arguments: navuArguments);
    return true;
  }

  /// preferred validation (always passes finalGalleryImages)
  Future<bool> validationForImage() async {
    Utils.showLog("Main Image Path: $mainImage | API Main: $apiMainImage");
    Utils.showLog("Other Selected Images: $selectedImages | API Gallery: $apiGalleryImages");
    Utils.showLog("finalGalleryImages: $finalGalleryImages");
    Utils.showLog("removedIndexes: $removedIndexes");

    if ((mainImage?.isEmpty ?? true) && (apiMainImage?.isEmpty ?? true)) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectMainCoverImage.name.tr);
      return false;
    }

    if (finalGalleryImages.isEmpty) {
      Utils.showToast(Get.context!, EnumLocale.txtPleaseSelectAtLeastOneOtherImage.name.tr);
      return false;
    }

    await getCategoryAttribute();
    final attributes = categoryAttributesResponseModel?.data;

    final Map<String, dynamic> navuArguments = {
      ...arguments,
      'ad': adsData,
      'adId': adsData?.id,
      'editApi': isEdit,
      'mainImage': (mainImage?.isNotEmpty == true) ? mainImage : apiMainImage,
      'selectedImages': finalGalleryImages,
      'removedGalleryIndexes': removedIndexes, // ✅ fixed key (no trailing space)
    };

    if (attributes != null && attributes.isNotEmpty) {
      navuArguments['attributes'] = attributes;
    }

    Get.toNamed(AppRoutes.addProductScreen, arguments: navuArguments);
    return true;
  }

  @override
  void onClose() {
    selectedImages = [];
    mainImage = null;
    super.onClose();
  }
}
