// upload_video_screen_controller.dart

import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/ui/my_ads_screen/api/all_ads_api.dart';
import 'package:listify/ui/upload_video_screen/api/seller_ad_listing_upload_video.dart';
import 'package:listify/ui/upload_video_screen/api/upload_video_api.dart';
import 'package:listify/ui/upload_video_screen/model/seller_product_info_model.dart';
import 'package:listify/ui/upload_video_screen/model/video_upload_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';
import 'package:path_provider/path_provider.dart';
import 'package:video_player/video_player.dart';
import 'package:video_thumbnail/video_thumbnail.dart';

class UploadVideoScreenController extends GetxController {
  final ImagePicker _picker = ImagePicker();
  File? mainImage;

  File? videoFile;
  String? videoThumbPath;
  VideoPlayerController? videoController;

  TextEditingController productName = TextEditingController();
  TextEditingController productDetails = TextEditingController();
  TextEditingController productPrice = TextEditingController();
  TextEditingController productDescription = TextEditingController();

  bool isLoading = false;
  // AllAdsResponseModel? allAdsResponseModel;
  List<SellerProductInfo> allAdsList = [];
  SellerProductInfoModel? sellerProductInfoModel;
  SellerProductInfo? selectedProduct;
  VideoUploadModel? videoUploadModel;
  @override
  void onInit() {
    AllAdsApi.startPagination = 0;
    sellerProductInfo();
    super.onInit();
  }

  Future<void> pickSingleImage() async {
    try {
      final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
      if (Get.isDialogOpen == true) Get.back();

      if (image != null) {
        mainImage = File(image.path);
        update();
      }
    } catch (e) {
      Utils.showLog('Error picking image: $e');
    }
  }

  void removeImage() {
    mainImage = null;
    update();
  }

  Future<void> pickVideo() async {
    try {
      final XFile? pickedFile = await _picker.pickVideo(source: ImageSource.gallery);
      if (Get.isDialogOpen == true) Get.back();

      if (pickedFile != null) {
        videoFile = File(pickedFile.path);

        //  Generate thumbnail and store path
        final tmpDir = await getTemporaryDirectory();
        videoThumbPath = await VideoThumbnail.thumbnailFile(
          video: videoFile!.path,
          thumbnailPath: tmpDir.path,
          imageFormat: ImageFormat.JPEG,
          maxHeight: 300,
          quality: 75,
        );

        update();
        Utils.showLog("Video picked: ${pickedFile.path}");
        Utils.showLog("Thumb: $videoThumbPath");
      } else {
        Utils.showLog("No video selected");
      }
    } catch (e) {
      Utils.showLog("Error picking video: $e");
    }
  }

  void removeVideo() {
    Utils.showLog("Removing video");
    videoFile = null;
    videoThumbPath = null;
    videoController?.dispose();
    videoController = null;
    update();
  }

  /// seller product info
  Future<void> sellerProductInfo() async {
    isLoading = true;
    update([Constant.idAllAds]);

    sellerProductInfoModel = await SellerAdListingUploadVideo.callApi();
    allAdsList
      ..clear()
      ..addAll(sellerProductInfoModel?.data ?? []);

    isLoading = false;
    update([Constant.idAllAds]);
  }

  void setSelectedProduct(dynamic p) {
    selectedProduct = p;
    productName.text = p.title ?? '';
    productPrice.text = (p.id != null) ? p.id.toString() : '';
    update();
  }

  // Future<void> uploadVideo() async {
  //   Get.dialog(const LoadingWidget(), barrierDismissible: false);
  //
  //   final String? thumbPath = videoThumbPath ??
  //       await VideoThumbnail.thumbnailFile(
  //         video: videoFile!.path,
  //         thumbnailPath: (await getTemporaryDirectory()).path,
  //         imageFormat: ImageFormat.JPEG,
  //         maxHeight: 200,
  //         quality: 75,
  //       );
  //
  //   Utils.showLog('Thumb nail url $thumbPath ');
  //   if (selectedProduct?.id == null) {
  //     Utils.showToast(Get.context!, "Please select a product first.");
  //     return;
  //   }
  //
  //   if (thumbPath == null) {
  //     Utils.showToast(Get.context!, "Failed to generate thumbnail.");
  //     return;
  //   }
  //   final String adId = selectedProduct!.id!.toString();
  //   Utils.showLog('video upload adId $adId ');
  //
  //   videoUploadModel = await UploadAdVideoApi.callApi(
  //       uId: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
  //       ad: adId,
  //       caption: productDetails.text,
  //       videoPath: videoFile!.path,
  //       thumbnailPath: thumbPath,
  //       duration: "");
  //
  //   Get.close(3);
  //
  //   Utils.showToast(Get.context!, videoUploadModel?.message ?? '');
  // }

  Future<String> getVideoDuration(File videoFile) async {
    final controller = VideoPlayerController.file(videoFile);
    await controller.initialize();
    final duration = controller.value.duration;
    controller.dispose();
    return duration.inSeconds.toString();
  }

  // Future<void> uploadVideo() async {
  //   Get.dialog(const LoadingWidget(), barrierDismissible: false);
  //
  //   final String? thumbPath = videoThumbPath ??
  //       await VideoThumbnail.thumbnailFile(
  //         video: videoFile!.path,
  //         thumbnailPath: (await getTemporaryDirectory()).path,
  //         imageFormat: ImageFormat.JPEG,
  //         maxHeight: 200,
  //         quality: 75,
  //       );
  //
  //   if (selectedProduct?.id == null) {
  //     Get.close(1);
  //     Utils.showToast(Get.context!, "Please select a product first.");
  //     return;
  //   }
  //
  //   if (thumbPath == null) {
  //     Get.close(1);
  //     Utils.showToast(Get.context!, "Failed to generate thumbnail.");
  //     return;
  //   }
  //
  //   // 👇 Duration fetch karo
  //   final String videoDuration = await getVideoDuration(videoFile!);
  //
  //   final String adId = selectedProduct!.id!.toString();
  //
  //   videoUploadModel = await UploadAdVideoApi.callApi(
  //     uId: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '',
  //     ad: adId,
  //     caption: productDetails.text,
  //     videoPath: videoFile!.path,
  //     thumbnailPath: thumbPath,
  //     duration: videoDuration, // 👈 now sending duration
  //   );
  //
  //   Get.close(3);
  //
  //   Utils.showToast(Get.context!, videoUploadModel?.message ?? '');
  // }
  Future<void> uploadVideo() async {
    if (selectedProduct == null || selectedProduct?.id == null) {
      Utils.showToast(Get.context!, "Please select a product first.");
      return;
    }

    if (videoFile == null) {
      Utils.showToast(Get.context!, "Please select a video first.");
      return;
    }

    Get.dialog(const LoadingWidget(), barrierDismissible: false);

    final String? thumbPath = videoThumbPath ??
        await VideoThumbnail.thumbnailFile(
          video: videoFile!.path,
          thumbnailPath: (await getTemporaryDirectory()).path,
          imageFormat: ImageFormat.JPEG,
          maxHeight: 200,
          quality: 75,
        );

    if (thumbPath == null) {
      Get.close(1);
      Utils.showToast(Get.context!, "Failed to generate thumbnail.");
      return;
    }

    final String videoDuration = await getVideoDuration(videoFile!);

    final String adId = selectedProduct!.id!.toString();

    videoUploadModel = await UploadAdVideoApi.callApi(
      uId: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      ad: adId,
      caption: productDetails.text,
      videoPath: videoFile!.path,
      thumbnailPath: thumbPath,
      duration: videoDuration,
    );

    Get.close(3);

    Utils.showToast(Get.context!, videoUploadModel?.message ?? '');
  }

  @override
  void onClose() {
    mainImage = null;
    videoFile = null;
    videoThumbPath = null;
    videoController?.dispose();
    videoController = null;
    super.onClose();
  }
}
