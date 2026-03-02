import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class ProductGridView extends StatelessWidget {
  final String productName;
  final String newPrice;
  final String sellerName;
  final String sellerImage;
  final String sellerLocation;
  final String productImage;
  final VoidCallback onLikeTap;
  final bool isLiked;
  final bool topSeller;
  final bool isVerify;

  const ProductGridView({
    super.key,
    required this.productName,
    required this.newPrice,
    required this.sellerName,
    required this.sellerImage,
    required this.sellerLocation,
    required this.onLikeTap,
    required this.productImage,
    required this.isLiked,
    required this.topSeller,
    required this.isVerify,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(color: AppColors.borderColor),
        color: AppColors.white,
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // -- Top image + like --
          Stack(
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                child: SizedBox(
                  height: 142,
                  width: double.infinity, // <- no Get.width
                  child: CustomImageView(image: productImage),
                ),
              ).paddingAll(1),
              Positioned(
                top: 6,
                right: 6,
                child: GestureDetector(
                  onTap: onLikeTap,
                  child: Container(
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppColors.heartBg,
                      border: Border.all(color: AppColors.borderColor),
                    ),
                    child: Image.asset(
                      isLiked ? AppAsset.heartFillIcon : AppAsset.heartIcon,
                      height: 20,
                      width: 20,
                    ).paddingAll(6),
                  ),
                ),
              ),
            ],
          ),

          // -- Content fills remaining height --
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  productName,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: AppFontStyle.fontStyleW700(
                    fontSize: 15,
                    fontColor: AppColors.black,
                  ),
                ).paddingOnly(top: 6, bottom: 4,left: 6),

                Row(
                  children: [
                    Text(
                      newPrice,
                      style: AppFontStyle.fontStyleW900(
                        fontSize: 16,
                        fontColor: AppColors.redColor,
                      ),
                    ),
                  ],
                ).paddingOnly(left: 6),

                const Spacer(), // push seller block to bottom

                // -- Seller info at bottom --
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 8),
                  decoration: BoxDecoration(
                    color: AppColors.textFieldColor,
                    borderRadius: const BorderRadius.vertical(bottom: Radius.circular(16)),
                  ),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      Container(
                        height: 35,
                        width: 35,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: AppColors.borderColor),
                        ),
                        child: ClipOval(child: CustomImageView(image: sellerImage)),
                      ),
                      const SizedBox(width: 8),
                      Expanded( // <- take remaining width safely
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    sellerName,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: AppFontStyle.fontStyleW500(
                                      fontSize: 12.5,
                                      fontColor: AppColors.black,
                                    ),
                                  ),
                                ),
                                if (topSeller)
                                  Container(
                                    padding: const EdgeInsets.symmetric(vertical: 3, horizontal: 5),
                                    decoration: BoxDecoration(
                                      color: AppColors.blue,
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Text(
                                      EnumLocale.txtTopSeller.name.tr,
                                      style: AppFontStyle.fontStyleW500(
                                        fontSize: 8,
                                        fontColor: AppColors.white,
                                      ),
                                    ),
                                  ),

                                isVerify?Image.asset(AppAsset.verificationRightIcon,height: 15,width: 15,):SizedBox(),

                              ],
                            ),
                            Row(
                              children: [
                                Image.asset(
                                  AppAsset.locationIcon,
                                  height: 12,
                                  width: 12,
                                  color: AppColors.popularProductText,
                                ),
                                const SizedBox(width: 5),
                                Expanded(
                                  child: Text(
                                    sellerLocation,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: AppFontStyle.fontStyleW500(
                                      fontSize: 10,
                                      fontColor: AppColors.popularProductText,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}


class SelectProductGridView extends StatelessWidget {
  final String? productName;
  final String? newPrice;
  final String? sellerName;
  final String? sellerImage;
  final String? sellerLocation;
  final String? productImage;
  final VoidCallback? onSelectTap;
  final bool isSelect;

  const SelectProductGridView(
      {super.key,
      this.productName,
      this.newPrice,
      this.sellerName,
      this.sellerImage,
      this.sellerLocation,
      this.onSelectTap,
      this.productImage,
      required this.isSelect});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(color: AppColors.borderColor),
        color: AppColors.white,
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Stack(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
                child: SizedBox(
                    height: 142,
                    width: Get.width,
                    child: CustomImageView(image: productImage ?? "")),
              ).paddingAll(1),
              Positioned(
                top: 6,
                right: 6,
                child: GestureDetector(
                  onTap: onSelectTap,
                  child: isSelect
                      ? Container(
                          height: 20,
                          width: 20,
                          decoration: BoxDecoration(
                              color: AppColors.appRedColor,
                              shape: BoxShape.circle),
                          child: Image.asset(
                            AppAsset.whiteRightIcon,
                            height: 15,
                            width: 15,
                          ),
                        )
                      : Container(
                          height: 20,
                          width: 20,
                          decoration: BoxDecoration(
                              border: Border.all(color: AppColors.white),
                              shape: BoxShape.circle),
                        ),

                  // Container(
                  //   decoration: BoxDecoration(
                  //     shape: BoxShape.circle,
                  //     color: AppColors.heartBg,
                  //   ),
                  //   child: Image.asset(
                  //     isSelect ? AppAsset.heartFillIcon : AppAsset.heartIcon,
                  //     height: 20,
                  //     width: 20,
                  //   ).paddingAll(6),
                  // ),
                ),
              )
            ],
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(productName ?? "",
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: AppFontStyle.fontStyleW700(
                          fontSize: 14, fontColor: AppColors.black))
                  .paddingOnly(top: 6, bottom: 6),
              Row(
                children: [
                  Text(
                    '${Database.settingApiResponseModel?.data?.currency?.symbol} $newPrice',
                    style: AppFontStyle.fontStyleW900(
                        fontSize: 16, fontColor: AppColors.redColor),
                  ),
                ],
              ).paddingOnly(bottom: 0),
            ],
          ).paddingOnly(left: 6)
        ],
      ),
    );
  }
}

class SellerProductGridView extends StatelessWidget {
  final String productName;
  final String newPrice;
  final String? oldPrice;
  final String sellerName;
  final String sellerImage;
  final String sellerLocation;
  final String productImage;
  final VoidCallback onLikeTap;
  final bool isLiked;

  const SellerProductGridView(
      {super.key,
      required this.productName,
      required this.newPrice,
      this.oldPrice,
      required this.sellerName,
      required this.sellerImage,
      required this.sellerLocation,
      required this.onLikeTap,
      required this.productImage,
      required this.isLiked});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(color: AppColors.borderColor),
        color: AppColors.white,
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Product image
          Stack(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
                child: SizedBox(
                    height: 142,
                    width: Get.width,
                    child: CustomImageView(image: productImage)),
              ).paddingAll(1),
              Positioned(
                top: 6,
                right: 6,
                child: GestureDetector(
                  onTap: onLikeTap,
                  child: Container(
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppColors.heartBg,
                    ),
                    child: Image.asset(
                      isLiked ? AppAsset.heartFillIcon : AppAsset.heartIcon,
                      height: 20,
                      width: 20,
                    ).paddingAll(6),
                  ),
                ),
              )
            ],
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(productName,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: AppFontStyle.fontStyleW700(
                          fontSize: 14, fontColor: AppColors.black))
                  .paddingOnly(top: 5),
              Text(productName,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 12,
                          fontColor: AppColors.popularProductText))
                  .paddingOnly(bottom: 0),
              Row(
                children: [
                  Text(
                    '${Database.settingApiResponseModel?.data?.currency?.symbol} $newPrice',
                    style: AppFontStyle.fontStyleW900(
                        fontSize: 16, fontColor: AppColors.redColor),
                  ),
                  SizedBox(width: 10),
                ],
              ).paddingOnly(bottom: 6),
              Row(
                children: [
                  Image.asset(
                    AppAsset.locationIcon,
                    height: 12,
                    width: 12,
                    color: AppColors.popularProductText,
                  ).paddingOnly(right: 5),
                  Text(
                    sellerLocation,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 10, fontColor: AppColors.popularProductText),
                  ),
                ],
              ).paddingOnly(left: 6, bottom: 9)
            ],
          ).paddingOnly(left: 6)
        ],
      ),
    );
  }
}
