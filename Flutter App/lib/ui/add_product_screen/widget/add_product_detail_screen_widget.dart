import 'dart:io';

import 'package:dotted_border/dotted_border.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/ui/add_product_screen/controller/add_product_screen_controller.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class AddProductDetailScreenAppBar extends StatelessWidget {
  final String? title;

  const AddProductDetailScreenAppBar({super.key, this.title});

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

class AddProductDetailScreenWidget extends StatelessWidget {
  const AddProductDetailScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<AddProductScreenController>(builder: (controller) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            EnumLocale.txtEnterProductDetail.name.tr,
            style: AppFontStyle.fontStyleW700(
                fontSize: 18, fontColor: AppColors.appRedColor),
          ).paddingOnly(top: 18, left: 12),
          Text(
            EnumLocale.txtEnterProductDetailTxt.name.tr,
            style: AppFontStyle.fontStyleW500(
                fontSize: 12, fontColor: AppColors.searchText),
          ).paddingOnly(top: 6, left: 12, right: 12, bottom: 18),
          controller.attributeDataList.isEmpty?SizedBox(

              height: Get.height*0.7,
              child: Center(child: Text("No Attribute Here!!"))): ListView.builder(
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
                  ProductDetailTile(
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
                                  initialValue:
                                      controller.textFieldValues[index],
                                  minValue: field.minLength,
                                  maxValue: field.maxLength,
                                ).paddingOnly(bottom: 28)
                              : fieldType == 3
                                  ? PickFileView(
                                      initialFileName: controller.adsData
                                          ?.attributes![index].value['name'],
                                      attributeIndex: index,
                                      baseUrl: Api.baseUrl,
                                    ).paddingOnly(bottom: 28)
                                  : fieldType == 5
                                      ? CustomDropdown(
                                          options: controller
                                                  .attributeDataList[index]
                                                  .values ??
                                              [],
                                          attributeIndex: index,
                    initialValue: (controller.adsData?.attributes?[index].value is List)
                        ? (controller.adsData?.attributes?[index].value as List).first.toString()
                        : controller.adsData?.attributes?[index].value?.toString(),

                    onChanged: (value) {
                                            controller.updateDropdownValue(
                                                index, value);
                                          },
                                        ).paddingOnly(bottom: 28)
                                      : fieldType == 6
                                          ? SelectableChipsView(
                                              initiallySelected:
                                                  controller.selectedChipValues[
                                                          index] ??
                                                      [],
                                              valueList: field.values ?? [],
                                              attributeIndex: index,
                                              onSelectionChanged:
                                                  (selectedValues) {
                                                // Save or process the selected values here (e.g., update controller)
                                                controller.selectedChipValues[
                                                    index] = selectedValues;
                                                Utils.showLog(
                                                    "Selected Items: $selectedValues");
                                              },
                                            ).paddingOnly(bottom: 28)
                                          : Offstage(),
                ],
              );
            },
          )
        ],
      );
    });
  }
}

// class AddProductDetailScreenWidget extends StatelessWidget {
//   const AddProductDetailScreenWidget({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<AddProductScreenController>(builder: (controller) {
//       return Column(
//         crossAxisAlignment: CrossAxisAlignment.start,
//         children: [
//           Text(
//             EnumLocale.txtEnterProductDetail.name.tr,
//             style: AppFontStyle.fontStyleW700(fontSize: 18, fontColor: AppColors.appRedColor),
//           ).paddingOnly(top: 18, left: 12),
//           Text(
//             EnumLocale.txtEnterProductDetailTxt.name.tr,
//             style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText),
//           ).paddingOnly(top: 6, left: 12, right: 12, bottom: 18),
//
//           // ✅ Priority based: પહેલા adsData check કરો, પછી attributeDataList
//           Builder(
//             builder: (context) {
//               // જો adsData અને attributes available છે તો તેનો ઉપયોગ કરો
//               if (controller.adsData?.attributes != null && controller.adsData!.attributes!.isNotEmpty) {
//                 return _buildFromAdsData(controller);
//               }
//               // અન્યથા attributeDataList નો ઉપયોગ કરો
//               else if (controller.attributeDataList.isNotEmpty) {
//                 return _buildFromAttributeDataList(controller);
//               }
//               // કશું available નથી
//               else {
//                 return Center(
//                   child: Text(
//                     "No attributes available",
//                     style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.searchText),
//                   ),
//                 );
//               }
//             },
//           )
//         ],
//       );
//     });
//   }
//
//   // adsData માંથી UI build કરો
//   Widget _buildFromAdsData(AddProductScreenController controller) {
//     // ✅ પહેલા પૂરા adsData ને print કરો
//     Utils.showLog("=== DEBUGGING ADS DATA ===");
//     Utils.showLog("Total adsData attributes count: ${controller.adsData?.attributes?.length ?? 0}");
//
//     // ✅ બધા attributes ની details print કરો
//     if (controller.adsData?.attributes != null) {
//       for (int i = 0; i < controller.adsData!.attributes!.length; i++) {
//         final attr = controller.adsData!.attributes![i];
//         Utils.showLog("--- Attribute Index: $i ---");
//         Utils.showLog("Name: ${attr.name}");
//         Utils.showLog("Value: ${attr.value}");
//         Utils.showLog("FieldType: ${attr.fieldType} (Type: ${attr.fieldType.runtimeType})");
//         Utils.showLog("Image: ${attr.image}");
//         Utils.showLog("Values/Options: ${attr.values}");
//         Utils.showLog("IsActive: ${attr.isActive}");
//         Utils.showLog("IsRequired: ${attr.isRequired}");
//         Utils.showLog("MinLength: ${attr.minLength}");
//         Utils.showLog("MaxLength: ${attr.maxLength}");
//         Utils.showLog("Raw JSON: ${jsonEncode(attr.toJson())}");
//         Utils.showLog("--------------------");
//       }
//     }
//     Utils.showLog("=========================");
//
//     return ListView.builder(
//       shrinkWrap: true,
//       physics: NeverScrollableScrollPhysics(),
//       itemCount: controller.adsData?.attributes?.length ?? 0,
//       itemBuilder: (context, index) {
//         final adsAttribute = controller.adsData?.attributes?[index];
//
//         // ✅ હર item માટે detailed print
//         Utils.showLog("🔥 Building UI for index: $index");
//         Utils.showLog("Attribute name: ${adsAttribute?.name}");
//         Utils.showLog("Attribute value: ${adsAttribute?.value}");
//         Utils.showLog("Raw fieldType: ${adsAttribute?.fieldType}");
//         Utils.showLog("Raw fieldType type: ${adsAttribute?.fieldType.runtimeType}");
//
//         // adsData માં fieldType field હોવું જોઈએ
//         final fieldType = adsAttribute?.fieldType?.toInt();
//         Utils.showLog("Converted fieldType: $fieldType");
//
//         // ✅ fieldType આધારે કયું widget બનશે તે print કરો
//         String widgetType = "";
//         switch (fieldType) {
//           case 1:
//             widgetType = "Text Field";
//             break;
//           case 2:
//             widgetType = "Numeric Field";
//             break;
//           case 3:
//             widgetType = "File Picker";
//             break;
//           case 4:
//             widgetType = "Radio Buttons";
//             break;
//           case 5:
//             widgetType = "Dropdown";
//             break;
//           case 6:
//             widgetType = "Multi Select Chips";
//             break;
//           default:
//             widgetType = "Default Text Field";
//         }
//         Utils.showLog("Will render: $widgetType");
//         Utils.showLog("Available options: ${adsAttribute?.values}");
//         Utils.showLog("━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
//
//         return Column(
//           crossAxisAlignment: CrossAxisAlignment.start,
//           children: [
//             ProductDetailTile(
//               text: adsAttribute?.name ?? '',
//               image: adsAttribute?.image ?? '',
//             ),
//
//             // fieldType આધારે UI render કરો
//             _buildFieldByType(
//               fieldType: fieldType,
//               index: index,
//               controller: controller,
//               adsAttribute: adsAttribute,
//             ).paddingOnly(bottom: 28),
//           ],
//         );
//       },
//     );
//   }
//
//   // attributeDataList માંથી UI build કરો (original code)
//   Widget _buildFromAttributeDataList(AddProductScreenController controller) {
//     return ListView.builder(
//       shrinkWrap: true,
//       physics: NeverScrollableScrollPhysics(),
//       itemCount: controller.attributeDataList.length ?? 0,
//       itemBuilder: (context, index) {
//         final field = controller.attributeDataList[index];
//
//         // Only render if active
//         if (field.isActive != true) {
//           return SizedBox.shrink();
//         }
//
//         final fieldType = field.fieldType;
//
//         return Column(
//           crossAxisAlignment: CrossAxisAlignment.start,
//           children: [
//             ProductDetailTile(
//               text: field.name ?? '',
//               image: field.image ?? '',
//             ),
//             _buildFieldByTypeFromAttributeList(
//               fieldType: fieldType,
//               index: index,
//               controller: controller,
//               field: field,
//             ).paddingOnly(bottom: 28),
//           ],
//         );
//       },
//     );
//   }
//
//   // adsData માટે field type આધારે widget build કરો
//   Widget _buildFieldByType({
//     required int? fieldType,
//     required int index,
//     required AddProductScreenController controller,
//     required dynamic adsAttribute,
//   }) {
//     switch (fieldType) {
//       case 4: // Radio
//         return RadioTypeView(
//           itemCount: adsAttribute?.values?.length ?? 0,
//           valueList: adsAttribute?.values ?? [],
//           attributeIndex: index,
//         );
//
//       case 1: // Text Field
//         return CustomTextFieldView(
//           isNumeric: false,
//           attributeIndex: index,
//           initialValue: controller.textFieldValues[index] ?? adsAttribute?.value?.toString(),
//         );
//
//       case 2: // Numeric Field
//         return CustomTextFieldView(
//           isNumeric: true,
//           attributeIndex: index,
//           initialValue: controller.textFieldValues[index] ?? adsAttribute?.value?.toString(),
//           minValue: adsAttribute?.minLength,
//           maxValue: adsAttribute?.maxLength,
//         );
//
//       case 3: // File
//         return PickFileView(attributeIndex: index);
//
//       case 5: // Dropdown
//         return CustomDropdown(
//           options: adsAttribute?.values ?? [],
//           attributeIndex: index,
//           onChanged: (value) {
//             controller.updateDropdownValue(index, value);
//           },
//         );
//
//       case 6: // Multi Select Chips
//         return SelectableChipsView(
//           valueList: adsAttribute?.values ?? [],
//           attributeIndex: index,
//           onSelectionChanged: (selectedValues) {
//             controller.selectedChipValues[index] = selectedValues;
//             Utils.showLog("Selected Items: $selectedValues");
//           },
//         );
//
//       default:
//         return CustomTextFieldView(
//           isNumeric: false,
//           attributeIndex: index,
//           initialValue: controller.textFieldValues[index] ?? adsAttribute?.value?.toString(),
//         );
//     }
//   }
//
//   // attributeDataList માટે field type આધારે widget build કરો (original logic)
//   Widget _buildFieldByTypeFromAttributeList({
//     required int? fieldType,
//     required int index,
//     required AddProductScreenController controller,
//     required dynamic field,
//   }) {
//     switch (fieldType) {
//       case 4:
//         return RadioTypeView(
//           itemCount: field?.values?.length ?? 0,
//           valueList: field?.values ?? [],
//           attributeIndex: index,
//         );
//
//       case 1:
//         return CustomTextFieldView(
//           isNumeric: false,
//           attributeIndex: index,
//           initialValue: controller.textFieldValues[index],
//         );
//
//       case 2:
//         return CustomTextFieldView(
//           isNumeric: true,
//           attributeIndex: index,
//           initialValue: controller.textFieldValues[index],
//           minValue: field?.minLength,
//           maxValue: field?.maxLength,
//         );
//
//       case 3:
//         return PickFileView(attributeIndex: index);
//
//       case 5:
//         return CustomDropdown(
//           options: controller.attributeDataList[index].values ?? [],
//           attributeIndex: index,
//           onChanged: (value) {
//             controller.updateDropdownValue(index, value);
//           },
//         );
//
//       case 6:
//         return SelectableChipsView(
//           valueList: field?.values ?? [],
//           attributeIndex: index,
//           onSelectionChanged: (selectedValues) {
//             controller.selectedChipValues[index] = selectedValues;
//             Utils.showLog("Selected Items: $selectedValues");
//           },
//         );
//
//       default:
//         return Offstage();
//     }
//   }
// }

///detail tile view

class ProductDetailTile extends StatelessWidget {
  final String image;
  final String text;
  const ProductDetailTile({super.key, required this.image, required this.text});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.start,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Container(
          padding: EdgeInsets.all(10),
          decoration: BoxDecoration(
              color: AppColors.appRedColor,
              borderRadius: BorderRadius.circular(12)),
          child: SizedBox(
            height: 30,
            width: 30,
            child:

            CustomImageView(
              color: AppColors.white,
              image: image,
            ),
          ),
        ).paddingOnly(right: 14),
        Text(
          text,
          style: AppFontStyle.fontStyleW500(
              fontSize: 17, fontColor: AppColors.black),
        )
      ],
    ).paddingOnly(left: 16, bottom: 20);
  }
}

///radio view

class RadioTypeView extends StatelessWidget {
  final int itemCount;
  final List<String> valueList;
  final int attributeIndex;

  const RadioTypeView({
    super.key,
    required this.itemCount,
    required this.valueList,
    required this.attributeIndex,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        SizedBox(
          height: 40,
          child: GetBuilder<AddProductScreenController>(
            builder: (controller) {
              return ListView.builder(
                itemCount: itemCount,
                scrollDirection: Axis.horizontal,
                shrinkWrap: true,
                itemBuilder: (context, index) {
                  final selectedIndex =
                      controller.selectedRadioIndices[attributeIndex];
                  final isSelected = selectedIndex == index;

                  return GestureDetector(
                    onTap: () {
                      controller.radioSelection(attributeIndex, index);
                    },
                    child: Container(
                      padding: EdgeInsets.symmetric(horizontal: 22),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(
                          width: 0.8,
                          color: isSelected
                              ? AppColors.appRedColor
                              : AppColors.txtFieldBorder,
                        ),
                        color: isSelected
                            ? AppColors.appRedColor.withValues(alpha: 0.08)
                            : AppColors.white,
                      ),
                      child: Center(
                        child: Text(
                          valueList[index],
                          style: AppFontStyle.fontStyleW500(
                            fontSize: 15,
                            fontColor: isSelected
                                ? AppColors.appRedColor
                                : AppColors.black,
                          ),
                        ),
                      ),
                    ).paddingOnly(left: 16),
                  );
                },
              );
            },
          ),
        )
      ],
    );
  }
}

/// check box view

class SelectableChipsView extends StatefulWidget {
  final List<String> valueList;
  final int attributeIndex;
  final List<String> initiallySelected; // 👈 New param
  final Function(List<String> selected) onSelectionChanged;

  const SelectableChipsView({
    super.key,
    required this.valueList,
    required this.attributeIndex,
    required this.initiallySelected,
    required this.onSelectionChanged,
  });

  @override
  SelectableChipsViewState createState() => SelectableChipsViewState();
}

class SelectableChipsViewState extends State<SelectableChipsView> {
  late Set<String> selectedItems; // 👈 use late for init from initial data

  @override
  void initState() {
    super.initState();
    selectedItems = widget.initiallySelected.toSet(); // 👈 Pre-fill from API
  }

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: 12,
      runSpacing: 12,
      children: widget.valueList.map((item) {
        final isSelected = selectedItems.contains(item);
        return GestureDetector(
          onTap: () {
            setState(() {
              if (isSelected) {
                selectedItems.remove(item);
              } else {
                selectedItems.add(item);
              }
            });

            widget.onSelectionChanged(selectedItems.toList());
            Utils.showLog(
                'Selected for index ${widget.attributeIndex}: $selectedItems');
          },
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 13, vertical: 10),
            decoration: BoxDecoration(
              color: isSelected ? AppColors.lightRedColor2 : AppColors.white,
              border: Border.all(
                color: isSelected
                    ? AppColors.appRedColor
                    : AppColors.txtFieldBorder,
              ),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Image.asset(
                  isSelected ? AppAsset.redWrightIcon : AppAsset.addBlackIcon,
                  height: 16,
                  width: 16,
                ).paddingOnly(right: 7),
                Text(
                  item,
                  style: AppFontStyle.fontStyleW500(
                    fontSize: 15,
                    fontColor:
                        isSelected ? AppColors.appRedColor : AppColors.black,
                  ),
                ),
              ],
            ),
          ),
        );
      }).toList(),
    ).paddingOnly(right: 16, left: 16);
  }
}

///custom dropdown view

// class CustomDropdown extends StatefulWidget {
//   final List<String> options;
//   final int attributeIndex;
//   final Function(String selectedValue) onChanged;
//
//   const CustomDropdown({super.key, required this.options, required this.attributeIndex, required this.onChanged});
//
//   @override
//   State<CustomDropdown> createState() => _CustomDropdownState();
// }
//
// class _CustomDropdownState extends State<CustomDropdown> {
//   final GlobalKey _key = GlobalKey();
//   final LayerLink _layerLink = LayerLink();
//
//   OverlayEntry? _overlayEntry;
//   String? selectedValue;
//
//   void _toggleDropdown() {
//     if (_overlayEntry == null) {
//       _showDropdown();
//     } else {
//       _removeDropdown();
//     }
//   }
//
//   void _showDropdown() {
//     final renderBox = _key.currentContext!.findRenderObject() as RenderBox;
//     final offset = renderBox.localToGlobal(Offset.zero);
//     final size = renderBox.size;
//
//     final screenWidth = MediaQuery.of(context).size.width;
//
//     _overlayEntry = OverlayEntry(
//       builder: (context) => GestureDetector(
//         onTap: _removeDropdown, // Close when tapping outside
//         behavior: HitTestBehavior.translucent,
//         child: Stack(
//           children: [
//             Positioned(
//               top: offset.dy + size.height,
//               left: 0,
//               width: screenWidth,
//               child: CompositedTransformFollower(
//                 link: _layerLink,
//                 showWhenUnlinked: false,
//                 offset: Offset(0, size.height + 4),
//                 child: Material(
//                   shadowColor: AppColors.transparent,
//                   clipBehavior: Clip.none,
//                   // borderRadius: BorderRadius.circular(12),
//                   color: AppColors.transparent,
//                   elevation: 3,
//                   child: ListView(
//                     padding: const EdgeInsets.symmetric(vertical: 8),
//                     shrinkWrap: true,
//                     children: widget.options.map((e) {
//                       return GestureDetector(
//                         behavior: HitTestBehavior.opaque, // Ensure full row is tappable
//                         onTap: () {
//                           setState(() {
//                             selectedValue = e;
//                           });
//                           widget.onChanged(e); // Pass selected value back to controller
//
//                           _removeDropdown();
//                         },
//                         child: Container(
//                           decoration: BoxDecoration(color: AppColors.white),
//                           width: double.infinity,
//                           padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
//                           alignment: Alignment.centerLeft, // Align text to start
//                           child: Text(
//                             e,
//                             style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
//                           ),
//                         ),
//                       );
//                     }).toList(),
//                   ).paddingSymmetric(horizontal: 10),
//                 ),
//               ),
//             ),
//           ],
//         ),
//       ),
//     );
//
//     Overlay.of(context).insert(_overlayEntry!);
//   }
//
//   void _removeDropdown() {
//     _overlayEntry?.remove();
//     _overlayEntry = null;
//   }
//
//   @override
//   void dispose() {
//     _removeDropdown();
//     super.dispose();
//   }
//
//   @override
//   Widget build(BuildContext context) {
//     return CompositedTransformTarget(
//       link: _layerLink,
//       child: GestureDetector(
//         key: _key,
//         onTap: _toggleDropdown,
//         child: Container(
//           margin: const EdgeInsets.symmetric(horizontal: 16),
//           padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
//           decoration: BoxDecoration(
//             color: AppColors.white,
//             border: Border.all(color: AppColors.txtFieldBorder),
//             borderRadius: BorderRadius.circular(12),
//           ),
//           child: Row(
//             mainAxisAlignment: MainAxisAlignment.spaceBetween,
//             children: [
//               Text(
//                 selectedValue ?? "Select Option",
//                 style: AppFontStyle.fontStyleW500(
//                   fontSize: 16,
//                   fontColor: selectedValue == null ? Colors.grey : AppColors.black,
//                 ),
//                 textAlign: TextAlign.start, // Align left
//               ),
//               Icon(Icons.keyboard_arrow_down, color: AppColors.black),
//             ],
//           ),
//         ),
//       ),
//     );
//   }
// }

class CustomDropdown extends StatefulWidget {
  final List<String> options;
  final int attributeIndex;
  final String? initialValue; // ✅ Add this
  final Function(String selectedValue) onChanged;

  const CustomDropdown({
    super.key,
    required this.options,
    required this.attributeIndex,
    required this.onChanged,
    this.initialValue,
  });

  @override
  State<CustomDropdown> createState() => _CustomDropdownState();
}

class _CustomDropdownState extends State<CustomDropdown> {
  final GlobalKey _key = GlobalKey();
  final LayerLink _layerLink = LayerLink();

  OverlayEntry? _overlayEntry;
  String? selectedValue;

  @override
  void initState() {
    super.initState();
    // ✅ Set initial API value when widget loads
    selectedValue =
        widget.initialValue?.isNotEmpty == true ? widget.initialValue : null;
  }

  void _toggleDropdown() {
    if (_overlayEntry == null) {
      _showDropdown();
    } else {
      _removeDropdown();
    }
  }

  void _showDropdown() {
    final renderBox = _key.currentContext!.findRenderObject() as RenderBox;
    final offset = renderBox.localToGlobal(Offset.zero);
    final size = renderBox.size;
    final screenWidth = MediaQuery.of(context).size.width;

    _overlayEntry = OverlayEntry(
      builder: (context) => GestureDetector(
        onTap: _removeDropdown,
        behavior: HitTestBehavior.translucent,
        child: Stack(
          children: [
            Positioned(
              top: offset.dy + size.height,
              left: 0,
              width: screenWidth,
              child: CompositedTransformFollower(
                link: _layerLink,
                offset: Offset(0, size.height + 4),
                child: Material(
                  color: Colors.transparent,
                  child: ListView(
                    padding: const EdgeInsets.symmetric(vertical: 8),
                    shrinkWrap: true,
                    children: widget.options.map((e) {
                      return GestureDetector(
                        behavior: HitTestBehavior.opaque,
                        onTap: () {
                          setState(() {
                            selectedValue = e; // ✅ Update selection
                          });
                          widget.onChanged(e);
                          _removeDropdown();
                        },
                        child: Container(
                          color: Colors.white,
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 12),
                          child: Text(
                            e,
                            style: AppFontStyle.fontStyleW500(
                              fontSize: 16,
                              fontColor: AppColors.black,
                            ),
                          ),
                        ),
                      );
                    }).toList(),
                  ).paddingSymmetric(horizontal: 10),
                ),
              ),
            ),
          ],
        ),
      ),
    );

    Overlay.of(context).insert(_overlayEntry!);
  }

  void _removeDropdown() {
    _overlayEntry?.remove();
    _overlayEntry = null;
  }

  @override
  void dispose() {
    _removeDropdown();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return CompositedTransformTarget(
      link: _layerLink,
      child: GestureDetector(
        key: _key,
        onTap: _toggleDropdown,
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 16),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
          decoration: BoxDecoration(
            color: Colors.white,
            border: Border.all(color: AppColors.txtFieldBorder),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                selectedValue ?? EnumLocale.txtSelectOption.name.tr,
                style: AppFontStyle.fontStyleW500(
                  fontSize: 16,
                  fontColor:
                      selectedValue == null ? Colors.grey : AppColors.black,
                ),
              ),
              Icon(Icons.keyboard_arrow_down, color: AppColors.black),
            ],
          ),
        ),
      ),
    );
  }
}

///bottom view

class AddProductDetailBottomButton extends StatelessWidget {
  const AddProductDetailBottomButton({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<AddProductScreenController>(builder: (controller) {
      return Container(
        decoration: BoxDecoration(
          color: AppColors.white,
          boxShadow: [
            BoxShadow(
              color: AppColors.black.withValues(alpha: 0.1),
              blurRadius: 12,
              offset: Offset(0, -2),
              spreadRadius: 0,
            ),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // PrimaryAppButton(
            //   text: EnumLocale.txtNext.name.tr,
            //   height: 54,
            //   onTap: () {
            //     if(controller.attributeDataList.isEmpty){
            //       Utils.showLog("no attribute here");
            //       Utils.showToast(context,"Please compulsory select Attribute");
            //     }
            //    else{
            //       if (controller.validateRequiredFields(context)) {
            //         controller.printAllEnteredValues();
            //       }
            //     }
            //   },
            // ).paddingSymmetric(vertical: 12, horizontal: 16),

            GetBuilder<AddProductScreenController>(
              id: 'attribute_form',
              builder: (controller) {
                return PrimaryAppButton(
                  text: EnumLocale.txtNext.name.tr,
                  height: 54,
                  color: controller.isButtonEnabled
                      ? AppColors.appRedColor
                      : AppColors.grey.withValues(alpha: 0.40),
                  onTap: controller.isButtonEnabled
                      ? () {
                    if (!controller.validateRequiredFields(context)) return;
                    controller.printAllEnteredValues();
                  }
                      : () {
                    Utils.showToast(context, "Please fill all required attributes");
                  },
// disables tap
                ).paddingSymmetric(vertical: 12, horizontal: 16);
              },
            ),


          ],
        ),
      );
    });
  }
}

///textField view

class CustomTextFieldView extends StatefulWidget {
  final int attributeIndex;
  final String? initialValue;
  final bool isNumeric;
  final int? minValue;
  final int? maxValue;

  const CustomTextFieldView({
    super.key,
    required this.attributeIndex,
    this.initialValue,
    required this.isNumeric,
    this.minValue,
    this.maxValue,
  });

  @override
  State<CustomTextFieldView> createState() => _CustomTextFieldViewState();
}

class _CustomTextFieldViewState extends State<CustomTextFieldView> {
  late final TextEditingController _textController;
  String? _errorText;

  @override
  void initState() {
    super.initState();
    _textController = TextEditingController(text: widget.initialValue ?? '');

    _textController.addListener(() {
      final value = _textController.text;

      if (widget.minValue != null && value.length < widget.minValue!) {
        setState(() {
          _errorText = 'Minimum ${widget.minValue} digits required';
        });
      } else {
        setState(() {
          _errorText = null;
        });
      }
    });
  }

  @override
  void dispose() {
    _textController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<AddProductScreenController>();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          decoration: BoxDecoration(
            color: AppColors.white,
            border: Border.all(color: AppColors.txtFieldBorder),
            borderRadius: BorderRadius.circular(12),
          ),
          child: TextField(
            controller: _textController,
            onChanged: (val) {
              controller.updateTextValue(widget.attributeIndex, val);
            },
            cursorColor: AppColors.black,
            keyboardType:
                widget.isNumeric ? TextInputType.number : TextInputType.text,
            inputFormatters: [
              if (widget.isNumeric) FilteringTextInputFormatter.digitsOnly,
              if (widget.maxValue != null)
                LengthLimitingTextInputFormatter(widget.maxValue),
            ],
            // inputFormatters: widget.isNumeric ? [FilteringTextInputFormatter.digitsOnly] : null,
            decoration: const InputDecoration(
              contentPadding:
                  EdgeInsets.symmetric(horizontal: 12, vertical: 14),
              border: InputBorder.none,
            ),
          ),
        ).paddingOnly(left: 16, right: 16),
        if (_errorText != null)
          Padding(
            padding: const EdgeInsets.only(top: 4.0, left: 12.0),
            child: Text(
              _errorText!,
              style: TextStyle(color: Colors.red, fontSize: 12),
            ),
          ),
      ],
    );
  }
}

///pick file view

// class PickFileView extends StatefulWidget {
//   final int attributeIndex; // <== ADD THIS
//
//   const PickFileView({super.key, required this.attributeIndex});
//
//   @override
//   State<PickFileView> createState() => _PickFileViewState();
// }
//
// class _PickFileViewState extends State<PickFileView> {
//   final controller = Get.find<AddProductScreenController>();
//
//   PlatformFile? selectedFile;
//
//   Future<void> pickFile() async {
//     try {
//       Utils.showLog('Starting file picker...');
//
//       // Method 1: Try with images first (most reliable)
//       FilePickerResult? result = await FilePicker.platform.pickFiles(
//         type: FileType.image,
//         allowMultiple: false,
//         withData: true, // 🔑 Important to get file bytes
//       );
//
//       if (result != null && result.files.isNotEmpty) {
//         Utils.showLog('Image file picked successfully');
//         setState(() {
//           selectedFile = result!.files.first;
//           controller.selectedFiles[widget.attributeIndex] = selectedFile!; // Save to controller
//           Utils.showLog('Image file picked successfully::::::::::::: $selectedFile');
//         });
//         return;
//       }
//
//       // Method 2: Try with any file type
//       result = await FilePicker.platform.pickFiles(
//         type: FileType.any,
//         allowMultiple: false,
//       );
//
//       if (result != null && result.files.isNotEmpty) {
//         PlatformFile file = result.files.first;
//         String? extension = file.extension?.toLowerCase();
//         List<String> allowedExtensions = ['png', 'svg', 'jpg', 'jpeg', 'pdf'];
//
//         if (extension != null && allowedExtensions.contains(extension)) {
//           Utils.showLog('Valid file picked: ${file.name}');
//           setState(() {
//             selectedFile = file;
//             controller.selectedFiles[widget.attributeIndex] = file; // ✅ This is what was missing
//             Utils.showLog('Saved to controller: ${controller.selectedFiles[widget.attributeIndex]?.name}');
//           });
//         } else {
//           Utils.showLog('Invalid file extension: $extension');
//           _showError('Please select PNG, JPG, JPEG, SVG, or PDF file');
//         }
//       }
//     } catch (e) {
//       Utils.showLog('File picker error: $e');
//
//       // Try alternative method
//       _tryAlternativeFilePicker();
//     }
//   }
//
//   // Alternative method using different approach
//   Future<void> _tryAlternativeFilePicker() async {
//     try {
//       Utils.showLog('Trying alternative file picker...');
//
//       FilePickerResult? result = await FilePicker.platform.pickFiles(
//         type: FileType.image,
//         allowMultiple: false,
//         withData: true,
//       );
//
//       if (result != null && result.files.isNotEmpty) {
//         Utils.showLog('Media file picked successfully');
//         setState(() {
//           selectedFile = result.files.first;
//           controller.selectedFiles[widget.attributeIndex] = selectedFile!; // ✅ Save properly
//         });
//       } else {
//         Utils.showLog('No file selected');
//         _showError('No file was selected');
//       }
//     } catch (e) {
//       Utils.showLog('Alternative picker also failed: $e');
//       _showError('File picker service is not available on this device');
//     }
//   }
//
//   void _showError(String message) {
//     if (mounted) {
//       Utils.showToast(context, message);
//     }
//   }
//
//   void _showSuccess(String message) {
//     if (mounted) {
//       ScaffoldMessenger.of(context).showSnackBar(
//         SnackBar(
//           content: Text(message),
//           backgroundColor: Colors.green,
//           duration: Duration(seconds: 2),
//         ),
//       );
//     }
//   }
//
//   // Get file extension display
//   String _getFileExtension(String fileName) {
//     return fileName.split('.').last.toUpperCase();
//   }
//
//   // Get file size in readable format
//   String _getFileSize(int bytes) {
//     if (bytes < 1024) return '$bytes B';
//     if (bytes < 1024 * 1024) return '${(bytes / 1024).toStringAsFixed(1)} KB';
//     return '${(bytes / (1024 * 1024)).toStringAsFixed(1)} MB';
//   }
//
//   // Remove selected file
//   void removeFile() {
//     setState(() {
//       selectedFile = null;
//     });
//   }
//
//   // Get appropriate icon for file type
//   IconData _getFileIcon(String extension) {
//     switch (extension.toLowerCase()) {
//       case 'pdf':
//         return Icons.picture_as_pdf;
//       case 'png':
//       case 'jpg':
//       case 'jpeg':
//         return Icons.image;
//       case 'svg':
//         return Icons.image_outlined;
//       default:
//         return Icons.insert_drive_file;
//     }
//   }
//
//   @override
//   Widget build(BuildContext context) {
//     return GestureDetector(
//       onTap: pickFile,
//       child: DottedBorder(
//         dashPattern: [3, 3],
//         color: AppColors.dottedBorderColor,
//         borderType: BorderType.RRect,
//         radius: const Radius.circular(12),
//         child: Container(
//           height: 56,
//           width: Get.width,
//           decoration: BoxDecoration(
//             borderRadius: BorderRadius.circular(12),
//             color: AppColors.lightColor,
//           ),
//           child: Row(
//             mainAxisAlignment: MainAxisAlignment.center,
//             children: [
//               if (selectedFile == null) ...[
//                 Image.asset(
//                   AppAsset.purpleAddIcon,
//                   height: 16,
//                   width: 16,
//                 ).paddingOnly(right: 12),
//                 Text(
//                   EnumLocale.txtAddFile.name.tr,
//                   style: AppFontStyle.fontStyleW500(fontSize: 15, fontColor: AppColors.dottedBorderColor),
//                 ),
//               ] else ...[
//                 // File icon based on extension
//                 _selectedFilePreview(selectedFile!).paddingOnly(left: 8, right: 8),
//
//                 Expanded(
//                   child: Column(
//                     crossAxisAlignment: CrossAxisAlignment.start,
//                     mainAxisAlignment: MainAxisAlignment.center,
//                     children: [
//                       Text(
//                         selectedFile!.name,
//                         style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: Colors.black),
//                         overflow: TextOverflow.ellipsis,
//                       ),
//                       if (selectedFile!.size > 0) ...[
//                         Row(
//                           children: [
//                             Text(_getFileExtension(selectedFile!.name),
//                                 style: AppFontStyle.fontStyleW500(
//                                   fontSize: 10,
//                                   fontColor: AppColors.grey,
//                                 )
//
//                                 // TextStyle(
//                                 //   fontSize: 10,
//                                 //   color: Colors.grey[600],
//                                 //   fontWeight: FontWeight.w500,
//                                 // ),
//                                 ).paddingOnly(right: 8),
//                             Text(
//                               _getFileSize(selectedFile!.size),
//                               style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.grey),
//                             ),
//                           ],
//                         ),
//                       ]
//                     ],
//                   ),
//                 ),
//
//                 GestureDetector(
//                   onTap: removeFile,
//                   child: Container(
//                     padding: EdgeInsets.all(2),
//                     decoration: BoxDecoration(
//                       color: Colors.grey[300],
//                       borderRadius: BorderRadius.circular(10),
//                     ),
//                     child: Icon(
//                       Icons.close,
//                       size: 14,
//                       color: Colors.grey[700],
//                     ),
//                   ),
//                 ).paddingOnly(left: 10, right: 8),
//               ]
//             ],
//           ),
//         ),
//       ).paddingOnly(left: 16, right: 16),
//     );
//   }
//
//   Widget _selectedFilePreview(PlatformFile file) {
//     final extension = file.extension?.toLowerCase() ?? '';
//
//     if (['jpg', 'jpeg', 'png'].contains(extension)) {
//       if (file.path != null) {
//         return Image.file(
//           File(file.path!),
//           height: 40,
//           width: 40,
//           fit: BoxFit.cover,
//         );
//       } else if (file.bytes != null) {
//         return Image.memory(
//           file.bytes!,
//           height: 40,
//           width: 40,
//           fit: BoxFit.cover,
//         );
//       }
//     }
//
//     // fallback to icon if not image
//     return Icon(
//       _getFileIcon(extension),
//       size: 30,
//       color: AppColors.appRedColor,
//     );
//   }
// }

class PickFileView extends StatefulWidget {
  final int attributeIndex;
  final String? initialFileName; // From API
  final String baseUrl; // API base URL

  const PickFileView({
    super.key,
    required this.attributeIndex,
    this.initialFileName,
    required this.baseUrl,
  });

  @override
  State<PickFileView> createState() => _PickFileViewState();
}

class _PickFileViewState extends State<PickFileView> {
  final controller = Get.find<AddProductScreenController>();
  PlatformFile? selectedFile; // only for picked file
  String? apiFileUrl; // full URL from API (just for preview)

  @override
  void initState() {
    super.initState();

    if (widget.initialFileName != null && widget.initialFileName!.isNotEmpty) {
      apiFileUrl =
          "${widget.baseUrl}/${widget.initialFileName}".replaceAll("\\", "/");
    }
  }

  /// Pick file from device
  Future<void> pickFile() async {
    FilePickerResult? result =
    await FilePicker.platform.pickFiles(type: FileType.any);
    if (result != null && result.files.isNotEmpty) {
      PlatformFile file = result.files.first;
      setState(() {
        selectedFile = file;
        apiFileUrl = null; // ✅ Replace API preview when new file picked
        controller.selectedFiles[widget.attributeIndex] =
            file; // ✅ save only picked file
      });
    }
  }

  /// Local file preview
  Widget _selectedFilePreview(PlatformFile file) {
    if (file.extension != null &&
        ["jpg", "jpeg", "png", "gif", "bmp", "webp"]
            .contains(file.extension!.toLowerCase())) {
      return Icon(Icons.insert_drive_file, size: 30, color: AppColors.appRedColor);
    }
    return Icon(Icons.insert_drive_file, size: 30, color: AppColors.appRedColor);
  }

  /// API file preview
  Widget _networkFilePreview(String url) {
    return Image.network(
      url,
      height: 40,
      width: 40,
      fit: BoxFit.cover,
      errorBuilder: (context, error, stackTrace) {
        return Icon(Icons.insert_drive_file, size: 30, color: AppColors.appRedColor);
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () {
        Utils.showLog("image::::::::::::${selectedFile}");
        Utils.showLog("image::::::::::::${widget.initialFileName}");
        Utils.showLog("image::::::::::::${widget.baseUrl}");
        Utils.showLog("image::::::::::::${widget.attributeIndex}");
        Utils.showLog("image::::::::::::${apiFileUrl}");
        pickFile();
      },
      child: DottedBorder(
        dashPattern: [3, 3],
        color: AppColors.dottedBorderColor,
        borderType: BorderType.RRect,
        radius: const Radius.circular(12),
        child: Container(
          height: 56,
          width: Get.width,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            color: AppColors.lightColor,
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if (selectedFile == null && apiFileUrl == null) ...[
                Image.asset(AppAsset.purpleAddIcon, height: 16, width: 16)
                    .paddingOnly(right: 12),
                Text(EnumLocale.txtAddFile.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 15, fontColor: AppColors.dottedBorderColor)),
              ] else ...[
                if (selectedFile != null) ...[
                  _selectedFilePreview(selectedFile!)
                      .paddingOnly(left: 8, right: 8),
                  Expanded(
                      child: Text(selectedFile!.name,
                          overflow: TextOverflow.ellipsis,
                          style: AppFontStyle.fontStyleW500(
                              fontSize: 14, fontColor: Colors.black))),
                ] else if (apiFileUrl != null) ...[
                  Container(
                      height: 50,
                      width: 50,
                      child: _networkFilePreview(apiFileUrl!)
                          .paddingOnly(left: 8, right: 8)),
                  Expanded(
                      child: Text(apiFileUrl!.split("/").last,
                          overflow: TextOverflow.ellipsis,
                          style: AppFontStyle.fontStyleW500(
                              fontSize: 14, fontColor: Colors.black))),
                ],
                GestureDetector(
                  onTap: () {
                    setState(() {
                      selectedFile = null;
                      apiFileUrl = null;
                      controller.selectedFiles.remove(widget
                          .attributeIndex); // ✅ remove only picked file reference
                    });
                  },
                  child: Container(
                    padding: const EdgeInsets.all(2),
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(Icons.close, size: 14, color: Colors.grey[700]),
                  ),
                ).paddingOnly(left: 10, right: 8),
              ]
            ],
          ),
        ),
      ).paddingOnly(left: 16, right: 16),
    );
  }
}
