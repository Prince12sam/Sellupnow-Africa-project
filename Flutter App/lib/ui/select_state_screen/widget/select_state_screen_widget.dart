import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/select_state_screen/controller/select_state_screen_controller.dart';
import 'package:listify/ui/select_state_screen/shimmer/select_state_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

class SelectStateScreenAppBar extends StatelessWidget {
  final String? title;
  const SelectStateScreenAppBar({super.key, this.title});

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

class SelectStateScreenWidget extends StatelessWidget {
  const SelectStateScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SelectStateScreenController>(
        id: Constant.idGetState,
        builder: (controller) {
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
                          controller: controller.searchController,
                          onChanged: (value) {
                            controller.onSearchState(value);
                          },
                          decoration: InputDecoration(
                            hintText: EnumLocale.txtSearchState.name.tr,
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
                EnumLocale.txtChooseState.name.tr,
                style: AppFontStyle.fontStyleW700(fontSize: 18, fontColor: AppColors.appRedColor),
              ).paddingOnly(bottom: 6),
              Text(
                EnumLocale.txtChooseStateTxt.name.tr,
                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.popularProductText),
              ).paddingOnly(bottom: 16),
              controller.isLoading
                  ? SelectStateShimmer()
                  : controller.filteredStateList.isNotEmpty
                      ? ListView.builder(
                          shrinkWrap: true,
                          physics: NeverScrollableScrollPhysics(),
                          itemCount: controller.filteredStateList.length,
                          itemBuilder: (context, index) {
                            final state = controller.filteredStateList[index];
                            return GestureDetector(
                              onTap: () {
                                /*if (controller.filterScreen == true) {
                                  controller.arguments = {
                                    'selectedState': state.name,
                                    // 'filterScreen': controller.filterScreen,
                                    'selectedCountry': controller.selectedCountry,
                                  };
                                }

                                else*/ if(controller.homeLocation == true){
                                  controller.arguments = {
                                    'selectedState': state.name,
                                    'homeLocation': controller.homeLocation,
                                    'selectedCountry': controller.selectedCountry,
                                  };
                                }else if(controller.search == true){
                                  controller.arguments = {
                                    'selectedState': state.name,
                                    'search': controller.search,
                                    'selectedCountry': controller.selectedCountry,
                                  };
                                }else if(controller.popular == true){
                                  controller.arguments = {
                                    'selectedState': state.name,
                                    'popular': controller.popular,
                                    'selectedCountry': controller.selectedCountry,
                                  };
                                }else if(controller.mostLike == true){
                                  controller.arguments = {
                                    'selectedState': state.name,
                                    'mostLike': controller.mostLike,
                                    'selectedCountry': controller.selectedCountry,
                                  };
                                }else if(controller.subcategory == true){
                                  controller.arguments = {
                                    'selectedState': state.name,
                                    'subcategory': controller.subcategory,
                                    'selectedCountry': controller.selectedCountry,
                                  };
                                }
                                else {
                                  controller.arguments.addAll({
                                    'selectedState': state.name,
                                  });
                                }

                                Get.toNamed(
                                  AppRoutes.selectCityScreen,
                                  arguments: controller.arguments,
                                );
                              },
                              child: Container(
                                width: Get.width,
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(16),
                                  color: AppColors.categoriesBgColor.withValues(alpha: 0.5),
                                  border: Border.all(color: AppColors.borderColor.withValues(alpha: 0.4)),
                                ),
                                child: Row(
                                  children: [
                                    Text(
                                      state.name ?? '',
                                      style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
                                    ).paddingOnly(left: 20, bottom: 18, top: 18),
                                    Spacer(),
                                    Container(
                                      padding: EdgeInsets.all(4),
                                      decoration: BoxDecoration(color: AppColors.white, shape: BoxShape.circle),
                                      child: RotatedBox(
                                        quarterTurns: 2,
                                        child: Image.asset(
                                          AppAsset.backArrowIcon,
                                          height: 22,
                                          width: 22,
                                        ),
                                      ),
                                    ).paddingOnly(right: 16),
                                  ],
                                ),
                              ).paddingOnly(bottom: 18),
                            );
                          },
                        )
                      : controller.isSearchPerformed
                          ? Padding(
                              padding: const EdgeInsets.all(20),
                              child: Center(
                                child: Text(
                                  "No state found.",
                                  style: AppFontStyle.fontStyleW500(
                                    fontSize: 16,
                                    fontColor: AppColors.grey,
                                  ),
                                ),
                              ),
                            )
                          : SizedBox(), // If search not done yet
            ],
          ).paddingAll(14);
        });
  }
}
