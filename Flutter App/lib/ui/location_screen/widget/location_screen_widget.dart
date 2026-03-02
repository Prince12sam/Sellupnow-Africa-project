import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/location_screen/controller/location_screen_controller.dart';
import 'package:listify/ui/location_screen/shimmer/country_shimmer.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class LocationScreenAppBar extends StatelessWidget {
  final String? title;
  const LocationScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<LocationScreenController>(
        id: Constant.idGetCountry,
        builder: (controller) {
          return Container(
            decoration: BoxDecoration(
              borderRadius: BorderRadius.vertical(bottom: Radius.circular(18)),
              boxShadow: [
                BoxShadow(
                  color: AppColors.lightGrey.withValues(alpha: 0.36),
                  spreadRadius: 0,
                  offset: const Offset(0.0, 0.0),
                  blurRadius: 12,
                ),
              ],
              color: AppColors.white,
            ),
            child: Column(
              children: [
                Row(
                  children: [
                    GestureDetector(
                      onTap: () {
                        Get.back();
                      },
                      child: Container(
                        height: 44,
                        width: 44,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: AppColors.categoriesBgColor,
                        ),
                        child: Padding(
                          padding: const EdgeInsets.all(7),
                          child: Image.asset(
                            AppAsset.backArrowIcon,
                            // width: 26,
                            // height: 26,
                            color: AppColors.black,
                          ),
                        ),
                      ),
                    ).paddingOnly(left: 17),
                    Spacer(),
                    Text(
                      EnumLocale.txtLocation.name.tr,
                      style: AppFontStyle.fontStyleW700(
                        fontSize: 20,
                        fontColor: AppColors.black,
                      ),
                    ).paddingOnly(left: Get.width * 0.06),
                    Spacer(),
                    GestureDetector(
                      onTap: () {},
                      child: Row(
                        children: [
                          Image.asset(
                            AppAsset.resetIcon,
                            height: 16,
                            width: 16,
                          ),
                          Text(
                            EnumLocale.txtReset.name.tr,
                            style: AppFontStyle.fontStyleW500(
                                fontSize: 15, fontColor: AppColors.appRedColor),
                          ).paddingOnly(left: 3),
                        ],
                      ).paddingOnly(right: 14),
                    )
                  ],
                ),
                Container(
                  height: 48,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(14),
                    color: AppColors.lightPurple.withValues(alpha: 0.2),
                    border: Border.all(color: AppColors.borderColor),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 10),
                    child: Row(
                      children: [
                        Image.asset(AppAsset.searchIcon, height: 22, width: 22),
                        const SizedBox(width: 8),
                        Expanded(
                          child: TextField(
                            controller: controller.searchController,
                            onChanged: (value) {
                              controller.onSearchCountry(value);
                            },
                            decoration: InputDecoration(
                              hintText: EnumLocale.txtSearchCountry.name.tr,
                              hintStyle: AppFontStyle.fontStyleW400(
                                fontSize: 16,
                                fontColor: AppColors.searchText,
                              ),
                              border: InputBorder.none,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ).paddingOnly(left: 16, right: 16, top: 20, bottom: 14)
              ],
            ).paddingOnly(top: Get.height * 0.042),
          );
        });
  }
}

class LocationScreenWidget extends StatelessWidget {
  const LocationScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    // final controller = Get.put(MapController());
    // Utils.showLog('${controller.latitude}');
    return GetBuilder<LocationScreenController>(
        id: Constant.idGetCountry,
        builder: (controller) {
          return Column(
            children: [
              GestureDetector(
                onTap: () {
                  Utils.showLog("?????????????????????${controller.arguments}");

                  // controller.arguments.addAll({jkjnj});

         /*         if (controller.filterScreen == true) {
                    Get.toNamed(
                      AppRoutes.nearByListingScreen,
                      arguments: {
                        'editApi': controller.isEdit,
                        // 'filterScreen': controller.filterScreen,
                        'search': controller.search,
                        'popular': controller.popular,
                        'mostLike': controller.mostLike,
                      },
                    );
                  } else*/


                    if (controller.homeLocation == true) {
                    // controller.arguments = {
                    //   'homeLocation': controller.homeLocation,
                    // };

                    Get.toNamed(
                      AppRoutes.nearByListingScreen,
                      arguments: {
                        'editApi': controller.isEdit,
                        'homeLocation': controller.homeLocation,
                        'search': controller.search,
                        'popular': controller.popular,
                        'mostLike': controller.mostLike,
                        'subcategory': controller.subcategory,
                      },
                    );
                  } else {
                    Get.toNamed(
                      AppRoutes.nearByListingScreen,
                      arguments: {
                        ...controller.arguments,
                        'editApi': controller.isEdit,
                        // 'ad': controller.adsData,
                        // 'filterScreen': controller.filterScreen,
                        'search': controller.search,
                        'popular': controller.popular,
                        'mostLike': controller.mostLike,
                        'subcategory': controller.subcategory,
                      },
                    );
                  }
                },
                child: Container(
                  padding: EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  decoration: BoxDecoration(color: AppColors.red200),
                  child: Row(
                    children: [
                      Container(
                        padding: EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: AppColors.appRedColor,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Image.asset(
                          AppAsset.locationIcon,
                          height: 30,
                          width: 30,
                          color: AppColors.white,
                        ),
                      ),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              EnumLocale.txtSelectedLocation.name.tr,
                              style: AppFontStyle.fontStyleW700(
                                  fontSize: 16,
                                  fontColor: AppColors.appRedColor),
                            ).paddingOnly(left: 12, right: 5),
                            GetBuilder<MapController>(
                              id: Constant.location,
                              builder: (controller) {
                                return Obx(() {
                                  String displayText = "";

                                  // 1) User selected → always show persisted selection
                                  if (Database.hasSelectedLocation.value) {
                                    displayText =
                                        Database.selectedLocationText();
                                  } else {
                                    // 2) Else show GPS address
                                    if (controller.isLoading &&
                                        controller.currentAddress.isEmpty) {
                                      displayText = "Getting location...";
                                    } else {
                                      // here use addressStreet + addressName
                                      if (controller
                                              .addressStreet!.isNotEmpty ||
                                          controller.addressName!.isNotEmpty) {
                                        displayText =
                                            "${controller.addressStreet}, ${controller.addressName}";
                                      } else if (controller
                                          .currentAddress.isNotEmpty) {
                                        displayText = controller.currentAddress;
                                      } else {
                                        displayText = "Getting location...";
                                      }
                                    }
                                  }

                                  return Text(
                                    displayText.isNotEmpty
                                        ? displayText
                                        : "Location not found",
                                    style: AppFontStyle.fontStyleW500(
                                        fontSize: 13,
                                        fontColor: AppColors.black),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  );
                                });
                              },
                            ).paddingOnly(left: 12, right: 5),
                          ],
                        ),

                        // GetBuilder<MapController>(
                        //     id: Constant.location,
                        //     builder: (controller) {
                        //       return Column(
                        //         crossAxisAlignment: CrossAxisAlignment.start,
                        //         children: [
                        //           Text(
                        //             EnumLocale.txtSelectedLocation.name.tr,
                        //             style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.appRedColor),
                        //           ),
                        //           controller.isLoading
                        //               ? Text("Geting Location")
                        //               : GetBuilder<MapController>(
                        //                   id: Constant.location,
                        //                   builder: (controller) {
                        //                     return
                        //                         /* controller.isLoading
                        //                     ? Shimmer.fromColors(
                        //                         baseColor: Color(0xffEBEDF9),
                        //                         highlightColor: Color(0xffF3F5FD),
                        //                         child: Container(
                        //                           height: 20,
                        //                           width: 230,
                        //                           decoration: BoxDecoration(
                        //                             borderRadius: BorderRadius.circular(7),
                        //                             color: AppColors.white,
                        //                           ),
                        //                         ),
                        //                       )
                        //                     : */
                        //                         Text(
                        //                       controller.finalAddress == null
                        //                           ? controller.currentAddress.toString()
                        //                           : controller.finalAddress.toString(),
                        //                       style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.black),
                        //                     );
                        //
                        //                     //     Text(
                        //                     //   controller.nearByListingScreenController.currentAddress.toString(),
                        //                     //   style: AppFontStyle.fontStyleW500(fontSize: 13, fontColor: AppColors.black),
                        //                     // );
                        //                   })
                        //         ],
                        //       ).paddingOnly(left: 12, right: 5);
                        //     }),
                      ),
                      GetBuilder<MapController>(
                          id: Constant.location,
                          builder: (controller) {
                            return Container(
                              padding: EdgeInsets.all(4),
                              decoration: BoxDecoration(
                                  color: AppColors.appRedColor,
                                  shape: BoxShape.circle),
                              child: RotatedBox(
                                quarterTurns: 2,
                                child: Image.asset(
                                  AppAsset.backArrowIcon,
                                  height: 18,
                                  width: 18,
                                  color: AppColors.white,
                                ),
                              ),
                            ).paddingOnly(right: 6);
                          })
                    ],
                  ),
                ).paddingOnly(top: 16),
              ),
              18.height,
              Column(
                children: [
                  controller.isLoading
                      ? CountryShimmer()
                      : controller.filteredCountryList.isNotEmpty
                          ? ListView.builder(
                              shrinkWrap: true,
                              padding: EdgeInsets.zero,
                              physics: NeverScrollableScrollPhysics(),
                              itemCount: controller.filteredCountryList.length,
                              itemBuilder: (context, index) {
                                final country =
                                    controller.filteredCountryList[index];
                                return GestureDetector(
                                  // onTap: () {
                                  //   controller.arguments.addAll({
                                  //     'selectedCountry': country.name,
                                  //     'filterScreen': true,
                                  //   });
                                  //   Utils.showLog("Selected Country: ${country.name}");
                                  //   Utils.showLog("arguments::::::::: ${controller.arguments}");
                                  //
                                  //   Get.toNamed(
                                  //     AppRoutes.selectStateScreen,
                                  //     arguments: controller.arguments,
                                  //   );
                                  // },

                                  onTap: () {
                                    Utils.showLog(
                                        "arguments=================== ${controller.arguments}");
                                    Utils.showLog(
                                        "country.name=================== ${country.name}");
                                    // Utils.showLog(
                                    //     " arguments['filterScreen']=================== ${controller.filterScreen}");
                                    Utils.showLog(
                                        " arguments['homeLocation']=================== ${controller.homeLocation}");

                                   /* if (controller.filterScreen == true) {
                                      controller.arguments = {
                                        'selectedCountry': country.name,
                                        // 'filterScreen': controller.filterScreen,
                                      };
                                    } else */


                                      if (controller.homeLocation ==
                                        true) {
                                      controller.arguments = {
                                        'selectedCountry': country.name,
                                        'homeLocation': controller.homeLocation,
                                      };
                                    }

                                    else if (controller.search ==
                                        true) {
                                      
                                      Utils.showLog("controller.search......${controller.search}");
                                      controller.arguments = {
                                        'selectedCountry': country.name,
                                        'search': controller.search,
                                      };
                                    }
                                    else if (controller.popular ==
                                        true) {
                                      controller.arguments = {
                                        'selectedCountry': country.name,
                                        'popular': controller.popular,
                                      };
                                    } else if (controller.mostLike ==
                                        true) {
                                      controller.arguments = {
                                        'selectedCountry': country.name,
                                        'mostLike': controller.mostLike,
                                      };
                                    }else if (controller.subcategory ==
                                        true) {
                                      controller.arguments = {
                                        'selectedCountry': country.name,
                                        'subcategory': controller.subcategory,
                                      };
                                    }

                                    else {
                                      controller.arguments.addAll({
                                        'selectedCountry': country.name,

                                      });
                                    }

                                    Utils.showLog("controller.arguments${controller.arguments}");
                                    Get.toNamed(
                                      AppRoutes.selectStateScreen,
                                      arguments: controller.arguments,
                                    );
                                  },

                                  // onTap: () {
                                  //   controller.arguments['selectedCountry'] = country.name;
                                  //
                                  //   // Ensure filterScreen is always a boolean
                                  //   if (controller.filterScreen is bool) {
                                  //     controller.arguments['filterScreen'] = controller.filterScreen;
                                  //   } else {
                                  //     // Convert string to bool if needed
                                  //     controller.arguments['filterScreen'] = controller.filterScreen.toString().toLowerCase() == 'true';
                                  //   }
                                  //
                                  //   Utils.showLog("Selected Country: ${country.name}");
                                  //   Utils.showLog("arguments::::::::: ${controller.arguments}");
                                  //   Utils.showLog("controller.filterScreen::::::::: ${controller.arguments['filterScreen']}");
                                  //
                                  //   Get.toNamed(
                                  //     AppRoutes.selectStateScreen,
                                  //     arguments: controller.arguments,
                                  //   );
                                  // },

                                  child: Container(
                                    width: Get.width,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(16),
                                      color: AppColors.categoriesBgColor
                                          .withValues(alpha: 0.5),
                                      border: Border.all(
                                          color: AppColors.borderColor
                                              .withValues(alpha: 0.4)),
                                    ),
                                    child: Row(
                                      children: [
                                        Container(
                                          height: 60,
                                          width: 60,
                                          padding: const EdgeInsets.all(9),
                                          decoration: BoxDecoration(
                                            color: AppColors.white,
                                            borderRadius:
                                                BorderRadius.circular(14),
                                          ),
                                          child: Center(
                                            child: Text(
                                              country.emoji ?? '🌐',
                                              style:
                                                  const TextStyle(fontSize: 28),
                                            ),
                                          ),
                                        ).paddingAll(5),
                                        Text(
                                          country.name ?? '',
                                          style: AppFontStyle.fontStyleW500(
                                            fontSize: 16,
                                            fontColor: AppColors.black,
                                          ),
                                        ).paddingOnly(left: 10),
                                        Spacer(),
                                        Container(
                                          padding: EdgeInsets.all(4),
                                          decoration: BoxDecoration(
                                              color: AppColors.white,
                                              shape: BoxShape.circle),
                                          child: RotatedBox(
                                            quarterTurns: 2,
                                            child: Image.asset(
                                              AppAsset.backArrowIcon,
                                              height: 22,
                                              width: 22,
                                            ),
                                          ),
                                        ).paddingOnly(right: 16)
                                      ],
                                    ),
                                  ).paddingOnly(bottom: 14),
                                );
                              },
                            ).paddingOnly(left: 16, right: 16)
                          : controller.isSearchPerformed
                              ? Padding(
                                  padding: const EdgeInsets.all(20),
                                  child: Center(
                                    child: Text(
                                      "No country found.",
                                      style: AppFontStyle.fontStyleW500(
                                        fontSize: 16,
                                        fontColor: AppColors.grey,
                                      ),
                                    ),
                                  ),
                                )
                              : SizedBox(), // 👈 Show nothing if no search yet

                  GetBuilder<LocationScreenController>(
                    id: Constant.idPagination,
                    builder: (controller) => Visibility(
                      visible: controller.isPaginationLoading,
                      child: CircularProgressIndicator(
                          color: AppColors.appRedColor),
                    ),
                  ),
                ],
              ),
            ],
          );
        });
  }
}
