import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/sub_category_product_screen/controller/gloable_controller.dart';
import 'package:listify/ui/sub_category_product_screen/controller/product_filter_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/widget/product_filter_screen_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ProductFilterScreen extends StatelessWidget {
  const ProductFilterScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      resizeToAvoidBottomInset: false,
      backgroundColor: AppColors.white,
      bottomNavigationBar: BottomButton(),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: ProductFilterScreenAppBar(
          title: EnumLocale.txtFilter.name.tr,
        ),
        actions: [
          GetBuilder<ProductFilterScreenController>(builder: (controller) {
            return GestureDetector(
              // onTap: () {
              //
              //   Utils.showLog('controller.postedSince  ::::  ${controller.postedSince}');
              //   controller.postedSince = '';
              //   controller.postedSince == null;
              //
              //   GlobalController.locationData['selectedCity'] = null;
              //   GlobalController.locationData['selectedCountry'] = null;
              //   GlobalController.locationData['selectedState'] = null;
              //   GlobalController.locationData['latitude'] = null;
              //   GlobalController.locationData['longitude'] = null;
              //
              //   controller.selectedRadioIndices == {};
              //   controller.textFieldValues == {};
              //   controller.selectedChipValues == {};
              //   controller.selectedFiles == {};
              //   controller.selectedFiles == {};
              //   controller.minPriceController.clear();
              //   controller.maxPriceController.clear();
              //
              //   controller.selectedRadioIndices.clear();
              //   controller.textFieldValues.clear();
              //   controller.selectedChipValues.clear();
              //   controller.selectedFiles.clear();
              //   controller.selectedFiles.clear();
              //
              //   controller.update([Constant.idFilterUpdate, Constant.idAllAds, Constant.idPagination, Constant.idLocationUpdate]);
              //   Utils.showLog('controller.postedSince  ::::  ${controller.postedSince}');
              //   //
              //   // controller.clearFilters(alsoClearLocation: true);
              // },
              onTap: () {

                controller.clearFilters(
                  clearCategory: true,
                  localLocationOnly: true,      // ← Database અડતું નથી
                  alsoClearGlobalLocation: false,
                );
              },
              child: Row(
                children: [
                  Text(
                    EnumLocale.txtReset.name.tr,
                    style: AppFontStyle.fontStyleW500(fontSize: 15, fontColor: AppColors.appRedColor),
                  ).paddingOnly(right: 3),
                  Image.asset(
                    AppAsset.resetIcon,
                    height: 18,
                    width: 18,
                  ),
                ],
              ).paddingOnly(right: 14),
            );
          })
        ],
      ),
      body: GestureDetector(
        onTap: () {
          FocusScopeNode currentFocus = FocusScope.of(context);
          if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
            currentFocus.focusedChild?.unfocus();
          }
        },
        child: SingleChildScrollView(
          child: Column(
            children: [
              FilterDetailAddView(),
              GetBuilder<ProductFilterScreenController>(
                  id: Constant.idAllAds,
                  builder: (controller) {
                    return ListView.builder(
                      shrinkWrap: true,
                      physics: NeverScrollableScrollPhysics(),
                      itemCount: controller.attributeDataList.length,
                      itemBuilder: (context, index) {
                        final field = controller.attributeDataList[index];
                        // Only render if active
                        if (field.isActive != true) {
                          return SizedBox.shrink(); // returns an empty widget
                        }
                        final fieldType = field.fieldType;

                        return Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            field.fieldType == 3
                                ? Offstage()
                                : ProductDetailTile(
                                    text: field.name ?? '',
                                    image: field.image ?? '',
                                  ),
                            fieldType == 4
                                ? RadioTypeView(
                                    itemCount: field.values?.length ?? 0,
                                    valueList: field.values ?? [],
                                    attributeIndex: index,
                                  ).paddingOnly(bottom: 28)
                                : fieldType == 1
                                    ? CustomTextFieldView(
                                        isNumeric: false,
                                        attributeIndex: index,
                                        initialValue: controller.textFieldValues[index],
                                      ).paddingOnly(bottom: 28)
                                    : fieldType == 2
                                        ? CustomTextFieldView(
                                            isNumeric: false,
                                            attributeIndex: index,
                                            initialValue: controller.textFieldValues[index],
                                            minValue: field.minLength,
                                            maxValue: field.maxLength,
                                          ).paddingOnly(bottom: 28)
                                        : fieldType == 3
                                            ? Offstage()
                                            : fieldType == 5
                                                ? CustomDropdown(
                                                    options: controller.attributeDataList[index].values ?? [],
                                                    attributeIndex: index,
                                                    initialValue: (controller.adsData != null &&
                                                            controller.adsData!.isNotEmpty &&
                                                            controller.adsData![0].attributes.length > index)
                                                        ? controller.adsData![0].attributes[index].value
                                                        : null,
                                                    onChanged: (value) {
                                                      controller.updateDropdownValue(index, value);
                                                    },
                                                  ).paddingOnly(bottom: 28)
                                                : fieldType == 6
                                                    ? SelectableChipsView(
                                                        initiallySelected: controller.selectedChipValues[index] ?? [],
                                                        valueList: field.values ?? [],
                                                        attributeIndex: index,
                                                        onSelectionChanged: (selectedValues) {
                                                          // Save or process the selected values here (e.g., update controller)
                                                          controller.selectedChipValues[index] = selectedValues;
                                                          Utils.showLog("Selected Items: $selectedValues");
                                                        },
                                                      ).paddingOnly(bottom: 28)
                                                    : fieldType == 7
                                                        ? SelectableChipsView(
                                                            initiallySelected: controller.selectedChipValues[index] ?? [],
                                                            valueList: field.values ?? [],
                                                            attributeIndex: index,
                                                            onSelectionChanged: (selectedValues) {
                                                              // Save or process the selected values here (e.g., update controller)
                                                              controller.selectedChipValues[index] = selectedValues;
                                                              Utils.showLog("Selected Items: $selectedValues");
                                                            },
                                                          ).paddingOnly(bottom: 28)
                                                        : Offstage(),
                          ],
                        );
                      },
                    );
                  }),
            ],
          ),
        ),
      ),
    );
  }
}
