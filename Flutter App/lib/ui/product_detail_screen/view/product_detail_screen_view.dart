import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/ui/product_detail_screen/shimmer/product_detail_shimmer.dart';
import 'package:listify/ui/product_detail_screen/widget/product_detail_screen_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class ProductDetailScreenView extends StatelessWidget {
  const ProductDetailScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductDetailScreenController>(
        id: Constant.idProductDetail,
        builder: (controller) {
          Utils.showLog('enter .....................');

          return Scaffold(
            bottomNavigationBar: controller.isDetailLoading ? ProductDetailBottomShimmer() : DetailBottomView(),
            backgroundColor: AppColors.white,
            body: Stack(
              clipBehavior: Clip.none,
              children: [
                controller.isDetailLoading
                    ? ProductDetailShimmer()
                    : SingleChildScrollView(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            DetailTopView(iconShow: controller.sellerDetail == true),
                            ProductDetailView(),
                            ProductDescriptionDetailView(),

                            controller.sellerDetail == true ? SellerDetailView() : SizedBox.shrink(),
                            ProductLocationView(productController: controller),
                            controller.relatedProduct == true ? ReportAdsRelatedProductView() : SizedBox.shrink(),
                          ],
                        ),
                      ),

                // Back Button
                Positioned(
                  left: 17,
                  top: 30,
                  child: GestureDetector(
                    onTap: () => Get.back(),
                    child: Container(
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: AppColors.categoriesBgColor,
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(7),
                        child: Image.asset(
                          height: 22,
                          width: 22,
                          AppAsset.backArrowIcon,
                          color: AppColors.black,
                        ),
                      ),
                    ),
                  ),
                ),

                // Share Icon (if iconShow is true)
                if (controller.sellerDetail == true)
                  Positioned(
                    right: 70,
                    top: 30,
                    child: GestureDetector(
                      onTap: () {
                        // Share action
                      },
                      child: Container(
                        // height: 40,
                        // width: 40,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: AppColors.categoriesBgColor,
                        ),
                        child: Padding(
                          padding: const EdgeInsets.all(7),
                          child: Image.asset(
                            height: 22,
                            width: 22,
                            AppAsset.blackShareIcon,
                            color: AppColors.black,
                          ),
                        ),
                      ),
                    ),
                  ),

                // Favorite Icon (if iconShow is true)
                if (controller.sellerDetail == true)
                  Positioned(
                    right: 17,
                    top: 30,
                    child: GetBuilder<ProductDetailScreenController>(builder: (controller) {
                      return GestureDetector(
                        onTap: () async {
                          // final res = await AddLikeApi.callApi(
                          //   adId: controller.adsData?.id ?? "",
                          //   uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
                          // );
                          //
                          // if (res != null && res.status == true) {
                          //   // Local model toggle
                          //   controller.adsData?.isLike = !(controller.adsData?.isLike ?? false);
                          //
                          //   controller.update();
                          // } else {
                          //   Utils.showLog("Add like failed");
                          // }
                          await controller.toggleCurrentAdLike();
                        },
                        child: Container(
                          // height: 42,
                          // width: 42,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: AppColors.categoriesBgColor,
                          ),
                          child: Padding(
                            padding: const EdgeInsets.all(7),
                            child: Image.asset(
                              height: 22,
                              width: 22,
                              controller.isCurrentAdLiked ? AppAsset.heartFillIcon : AppAsset.heartIcon,
                              // controller.adsData?.isLike == true ? AppAsset.favouriteFillIcon : AppAsset.favouriteIcon,
                            ),
                          ),
                        ),
                      );
                    }),
                  ),
              ],
            ),
          );
        });
  }
}
