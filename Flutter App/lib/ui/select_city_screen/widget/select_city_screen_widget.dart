import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/location_screen/controller/location_screen_controller.dart';
import 'package:listify/ui/select_city_screen/controller/select_city_screen_controller.dart';
import 'package:listify/ui/select_state_screen/shimmer/select_state_shimmer.dart';
import 'package:listify/ui/sub_category_product_screen/controller/gloable_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class SelectCityScreenAppBar extends StatelessWidget {
  final String? title;
  const SelectCityScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class SelectCityScreenWidget extends StatelessWidget {
  const SelectCityScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SelectCityScreenController>(
        id: Constant.idGetCity,
        builder: (controller) {
          final cityListToShow = controller.searchText.isEmpty
              ? controller.cityList
              : controller.searchedCityList;

          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
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
                          onChanged: controller.onSearchChanged,
                          decoration: InputDecoration(
                            hintText: EnumLocale.txtChooseCity.name.tr,
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
              ).paddingOnly(top: 4, bottom: 16),
              Text(
                EnumLocale.txtChooseCity.name.tr,
                style: AppFontStyle.fontStyleW700(
                    fontSize: 18, fontColor: AppColors.appRedColor),
              ).paddingOnly(bottom: 6),
              Text(
                EnumLocale.txtChooseCityTxt.name.tr,
                style: AppFontStyle.fontStyleW500(
                    fontSize: 12, fontColor: AppColors.popularProductText),
              ).paddingOnly(bottom: 16),
              controller.isLoading
                  ? SelectStateShimmer()
                  : ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: cityListToShow.length,
                      itemBuilder: (context, index) {
                        final city = cityListToShow[index];
                        return GestureDetector(
                          // onTap: () {
                          //   final controller = Get.find<LocationScreenController>();
                          //   final cityController = Get.find<SelectCityScreenController>();
                          //
                          //   final source = controller.arguments['source'];
                          //   Utils.showLog("source select city screen :::::::::::$source");
                          //
                          //   if (controller.filterScreen == true) {
                          //     cityController.arguments = {
                          //       'selectedCity': city.name,
                          //       'latitude': city.latitude,
                          //       'longitude': city.longitude,
                          //       'selectCityScreen': true,
                          //       'filterScreen': cityController.filterScreen,
                          //       'selectedState': cityController.selectedState,
                          //       'selectedCountry': cityController.selectedCountry,
                          //     };
                          //
                          //     Utils.showLog("filterScreen arguments//////////////////${cityController.arguments}");
                          //   } else {
                          //     controller.arguments.addAll({
                          //       'selectedCity': city.name,
                          //       'latitude': city.latitude,
                          //       'longitude': city.longitude,
                          //       'selectCityScreen': true,
                          //     });
                          //     Get.toNamed(
                          //       AppRoutes.productPricingScreen,
                          //       arguments: controller.arguments,
                          //     );
                          //   }
                          // },

                          onTap: () {
                            final controller =
                                Get.find<LocationScreenController>();
                            final cityController =
                                Get.find<SelectCityScreenController>();

                            final source = controller.arguments['source'];
                            Utils.showLog(
                                "source select city screen :::::::::::$source");

                           /* if (controller.filterScreen == true) {
                              final args = {
                                'selectedCity': city.name,
                                'latitude': city.latitude,
                                'longitude': city.longitude,
                                'selectCityScreen': true,
                                // 'filterScreen': cityController.filterScreen,
                                'selectedState': cityController.selectedState,
                                'selectedCountry':
                                    cityController.selectedCountry,
                              };

                              // Update global state
                              GlobalController.updateLocation(args);

                              // Navigate back
                              int count = 0;
                              Get.until((route) {
                                count++;
                                return count >= 4;
                              });
                            } else*/


                              if (controller.homeLocation == true) {
                                final args = {
                                  'homeSelectedCity': city.name,
                                  'latitude': city.latitude,
                                  'longitude': city.longitude,
                                  'selectCityScreen': true,
                                  'homeLocation': cityController.homeLocation,
                                  'homeSelectedState': cityController.selectedState,
                                  'homeSelectedCountry':
                                      cityController.selectedCountry,
                                };

                                // Update global state
                                // GlobalController.updateLocation(args);
                                Database.setSelectedLocation(args);

                                // Navigate back
                                int count = 0;
                                Get.until((route) {
                                  count++;
                                  return count >= 4;
                                });
                            } else if (controller.search == true) {
                                final args = {
                                  'homeSelectedCity': city.name,
                                  'latitude': city.latitude,
                                  'longitude': city.longitude,
                                  'selectCityScreen': true,
                                  'search': cityController.search,
                                  'homeSelectedState': cityController.selectedState,
                                  'homeSelectedCountry':
                                      cityController.selectedCountry,
                                };


                                Utils.showLog("args..........${args}");

                                // Update global state
                                // GlobalController.updateLocation(args);
                                Database.setSelectedLocation(args);



                                // Navigate back
                                int count = 0;
                                Get.until((route) {
                                  count++;
                                  return count >= 4;
                                });
                            }else if (controller.popular == true) {
                                final args = {
                                  'homeSelectedCity': city.name,
                                  'latitude': city.latitude,
                                  'longitude': city.longitude,
                                  'selectCityScreen': true,
                                  'popular': cityController.popular,
                                  'homeSelectedState': cityController.selectedState,
                                  'homeSelectedCountry':
                                      cityController.selectedCountry,
                                };

                                // Update global state
                                // GlobalController.updateLocation(args);
                                Database.setSelectedLocation(args);

                                // Navigate back
                                int count = 0;
                                Get.until((route) {
                                  count++;
                                  return count >= 4;
                                });
                            }else if (controller.mostLike == true) {
                                final args = {
                                  'homeSelectedCity': city.name,
                                  'latitude': city.latitude,
                                  'longitude': city.longitude,
                                  'selectCityScreen': true,
                                  'mostLike': cityController.mostLike,
                                  'homeSelectedState': cityController.selectedState,
                                  'homeSelectedCountry':
                                      cityController.selectedCountry,
                                };

                                // Update global state
                                // GlobalController.updateLocation(args);
                                Database.setSelectedLocation(args);

                                // Navigate back
                                int count = 0;
                                Get.until((route) {
                                  count++;
                                  return count >= 4;
                                });
                            }else if (controller.subcategory == true) {
                                final args = {
                                  'homeSelectedCity': city.name,
                                  'latitude': city.latitude,
                                  'longitude': city.longitude,
                                  'selectCityScreen': true,
                                  'subcategory': cityController.subcategory,
                                  'homeSelectedState': cityController.selectedState,
                                  'homeSelectedCountry':
                                      cityController.selectedCountry,
                                };

                                // Update global state
                                // GlobalController.updateLocation(args);
                                Database.setSelectedLocation(args);

                                // Navigate back
                                int count = 0;
                                Get.until((route) {
                                  count++;
                                  return count >= 4;
                                });
                            } else {
                              controller.arguments.addAll({
                                'selectedCity': city.name,
                                'latitude': city.latitude,
                                'longitude': city.longitude,
                                'selectCityScreen': true,
                              });

                              Get.toNamed(
                                AppRoutes.productPricingScreen,
                                arguments: controller.arguments,
                              );
                            }
                          },
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
                                Text(
                                  city.name ?? '',
                                  style: AppFontStyle.fontStyleW500(
                                      fontSize: 16, fontColor: AppColors.black),
                                ).paddingOnly(left: 20, bottom: 18, top: 18),
                                Spacer(),
                                Container(
                                  padding: EdgeInsets.all(4),
                                  decoration: BoxDecoration(
                                      color: AppColors.white,
                                      shape: BoxShape.circle),
                                  child: RotatedBox(
                                    quarterTurns: 2,
                                    child: Image.asset(AppAsset.backArrowIcon,
                                        height: 22, width: 22),
                                  ),
                                ).paddingOnly(right: 16)
                              ],
                            ),
                          ).paddingOnly(bottom: 18),
                        );
                      },
                    )
            ],
          ).paddingAll(14);
        });
  }
}
