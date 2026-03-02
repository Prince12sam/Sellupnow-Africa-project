import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ProductListViewContainer extends StatelessWidget {
  final String productName;
  final String newPrice;
  final String? oldPrice;
  final String sellerName;
  final String sellerImage;
  final String sellerLocation;
  final String productImage;
  final String description;
  final VoidCallback onLikeTap;
  final bool isLiked;
  final bool isOffer;
  final bool isVerify;

  const ProductListViewContainer(
      {super.key,
      required this.productName,
      required this.newPrice,
      this.oldPrice,
      required this.sellerName,
      required this.sellerImage,
      required this.sellerLocation,
      required this.productImage,
      required this.description,
      required this.onLikeTap,
      required this.isLiked,
      required this.isOffer,
      required this.isVerify,
      });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(15),
        border: Border.all(color: AppColors.lightGreyBorder),
      ),
      child: Row(
        children: [
          Container(
            width: Get.width * 0.36,
            height: Get.height * 0.15,
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(12),
                topLeft: Radius.circular(12),
              ),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(12),
                topLeft: Radius.circular(12),
              ),
              child: CustomImageView(image: productImage),
            ),
          ).paddingOnly(bottom: 2, left: 2, top: 2, right: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  productName,
                  overflow: TextOverflow.ellipsis,
                  style: AppFontStyle.fontStyleW700(
                      fontSize: 15, fontColor: AppColors.black),
                ).paddingOnly(bottom: 4),
                Text(
                  description,
                  overflow: TextOverflow.ellipsis,
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 13, fontColor: AppColors.grey),
                ).paddingOnly(bottom: 4),
                Row(
                  children: [
                    Text(
                      "${Database.settingApiResponseModel?.data?.currency?.symbol ?? ""} $newPrice",
                      style: AppFontStyle.fontStyleW900(
                          fontSize: 15, fontColor: AppColors.appRedColor),
                    ).paddingOnly(right: 6),
                  ],
                ).paddingOnly(bottom: 9),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Container(
                          height: 33,
                          width: 33,
                          clipBehavior: Clip.hardEdge,
                          decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(color: AppColors.borderColor)),
                          child: ClipOval(
                            child: CustomImageView(
                              image: sellerImage,
                              fit: BoxFit.cover,
                            ),
                          ),
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                SizedBox(
                                  width: Get.width * 0.19,
                                  child: Text(
                                    overflow: TextOverflow.ellipsis,
                                    maxLines: 1,
                                    sellerName,
                                    style: AppFontStyle.fontStyleW500(
                                        fontSize: 10,
                                        fontColor: AppColors.black),
                                  ),
                                ),
                                4.width,
                                isOffer == true
                                    ? Container(
                                        padding: EdgeInsets.symmetric(
                                            vertical: 3, horizontal: 5),
                                        decoration: BoxDecoration(
                                          color: AppColors.blue,
                                          borderRadius:
                                              BorderRadius.circular(10),
                                        ),
                                        child: Text(
                                          EnumLocale.txtTopSeller.name.tr,
                                          style: AppFontStyle.fontStyleW500(
                                              fontSize: 8,
                                              fontColor: AppColors.white),
                                        ),
                                      )
                                    : SizedBox(),

                                isVerify?Image.asset(AppAsset.verificationRightIcon,height: 15,width: 15,):SizedBox(),
                              ],
                            ),
                            Row(
                              children: [
                                Image.asset(
                                  AppAsset.locationIcon,
                                  height: 11,
                                  width: 11,
                                  color: AppColors.popularProductText,
                                ).paddingOnly(right: 5),
                                Text(
                                  sellerLocation,
                                  style: AppFontStyle.fontStyleW500(
                                      fontSize: 9,
                                      fontColor: AppColors.popularProductText),
                                ),
                              ],
                            )
                          ],
                        ).paddingOnly(left: 6)
                      ],
                    ),
                    GestureDetector(
                      onTap: onLikeTap,
                      child: Container(
                        decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: AppColors.heartBg,
                            border: Border.all(color: AppColors.borderColor)),
                        child: Image.asset(
                          isLiked ? AppAsset.heartFillIcon : AppAsset.heartIcon,
                          width: 20,
                          height: 20,
                        ).paddingAll(6),
                      ),
                    ).paddingOnly(bottom: 6),
                  ],
                )
              ],
            ).paddingOnly(
              right: 6,
            ),
          )
        ],
      ),
    ).paddingOnly(bottom: 10);
  }
}
