import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/custom/title/custom_title.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/sub_categories_screen/service/select_bus_service.dart';
import 'package:listify/ui/sub_category_product_screen/controller/gloable_controller.dart';
import 'package:listify/ui/sub_category_product_screen/controller/product_filter_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/controller/sub_category_product_screen_controller.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ProductFilterScreenAppBar extends StatelessWidget {
  final String? title;
  const ProductFilterScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class FilterDetailAddView extends StatelessWidget {
  const FilterDetailAddView({super.key});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () {
        FocusScopeNode currentFocus = FocusScope.of(context);
        if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
          currentFocus.focusedChild?.unfocus();
        }
      },
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [

          Text(
            EnumLocale.txtLocation.name.tr,
            style: AppFontStyle.fontStyleW500(
              fontSize: 14,
              fontColor: AppColors.popularProductText,
            ),
          ).paddingOnly(bottom: 12, left: 5),
          // GetBuilder<ProductFilterScreenController>(
          //     id: Constant.idLocationUpdate,
          //     builder: (controller) {
          //       return GestureDetector(
          //         onTap: () {
          //           Get.toNamed(
          //             AppRoutes.locationScreen,
          //             arguments: {
          //               // 'filterScreen': controller.filterScreen,
          //               'search': controller.search,
          //               'popular': controller.popular,
          //               'mostLike': controller.mostLike,
          //               'subcategory': controller.subcategory,
          //             },
          //           );
          //         },
          //         child: Obx(() {
          //           return Container(
          //             height: 54,
          //             decoration: BoxDecoration(
          //                 color: AppColors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.txtFieldBorder)),
          //             child: Row(
          //               children: [
          //                 Image.asset(AppAsset.locationFillIcon, color: AppColors.black.withValues(alpha: 0.2), height: 20, width: 20)
          //                     .paddingOnly(left: 10),
          //                 6.width,
          //                 Text(
          //                   Database.selectedLocation['selectedCity'] == null
          //                       ? EnumLocale.txtAllCities.name.tr
          //                       : "${Database.selectedLocation['selectedCity'] ?? ""} , ${Database.selectedLocation['selectedState'] ?? ""} , ${Database.selectedLocation['selectedCountry'] ?? ""}",
          //                   // GlobalController.locationData['selectedCity'],
          //                   style: AppFontStyle.fontStyleW500(
          //                     fontSize: 15,
          //                     fontColor: AppColors.black.withValues(alpha: 0.2),
          //                   ),
          //                 ),
          //               ],
          //             ),
          //           ).paddingOnly(bottom: 32);
          //         },)
          //       );
          //     }),


        GetBuilder<ProductFilterScreenController>(
          id: Constant.idLocationUpdate,
          builder: (controller) {
            final showCity    = controller.tempCity;
            final showState   = controller.tempState;
            final showCountry = controller.tempCountry;

            final hasAny = showCity != null || showState != null || showCountry != null;

            return GestureDetector(
              onTap: () {
                Get.toNamed(
                  AppRoutes.locationScreen,
                  arguments: {
                    'search': controller.search,
                    'popular': controller.popular,
                    'mostLike': controller.mostLike,
                    'subcategory': controller.subcategory,
                  },
                )?.then((_) {
                  // Location screen પરથી પાછા આવ્યા પછી Database → Local temp re-sync
                  controller.syncLocalLocationFromDatabase();
                });
              },
              child: Container(
                height: 54,
                decoration: BoxDecoration(
                  color: AppColors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.txtFieldBorder),
                ),
                child: Row(
                  children: [
                    Image.asset(
                      AppAsset.locationFillIcon,
                      color: AppColors.black.withValues(alpha: 0.2),
                      height: 20,
                      width: 20,
                    ).paddingOnly(left: 10),
                    6.width,
                    Expanded(
                      child: Text(
                        hasAny
                            ? "${showCity ?? ""}"
                            "${(showCity != null && (showState != null || showCountry != null)) ? ' , ' : ''}"
                            "${showState ?? ""}"
                            "${(showState != null && showCountry != null) ? ' , ' : ''}"
                            "${showCountry ?? ""}"
                            : EnumLocale.txtAllCities.name.tr,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: AppFontStyle.fontStyleW500(
                          fontSize: 15,
                          fontColor: AppColors.black.withValues(alpha: 0.2),
                        ),
                      ),
                    ),
                  ],
                ),
              ).paddingOnly(bottom: 32),
            );
          },
        ),


        GetBuilder<ProductFilterScreenController>(
              // id: Constant.idCategoryHeader,
              builder: (controller) {
            return controller.categoryId == null
                ? CategorySelect(
                    onTap: () {
                      Utils.showLog(">>>>>>>>>>>>>>>>>>>>>>>${controller.search}");
                      Get.toNamed(AppRoutes.categoriesScreen, arguments: {
                        "search": controller.search,
                        "popular": controller.popular,
                        "mostLike": controller.mostLike,
                      });
                    },
                  )
                : Offstage();
          }),

          CustomTitle(
            title: EnumLocale.txtBudget.name.tr,
            method: GetBuilder<ProductFilterScreenController>(builder: (controller) {
              return Row(
                children: [
                  Expanded(
                    child: CustomTextField(
                      filled: true,
                      hintText: EnumLocale.txtMin.name.tr,
                      controller: controller.minPriceController,
                      fillColor: AppColors.white,
                      cursorColor: AppColors.black,
                      fontColor: AppColors.black,
                      fontSize: 15,
                      textInputAction: TextInputAction.next,
                      maxLines: 1,
                      textInputType: TextInputType.number,
                    ),
                  ),
                  18.width,
                  Expanded(
                    child: CustomTextField(
                      filled: true,
                      controller: controller.maxPriceController,
                      hintText: EnumLocale.txtMax.name.tr,
                      fillColor: AppColors.white,
                      cursorColor: AppColors.black,
                      fontColor: AppColors.black,
                      fontSize: 15,
                      textInputAction: TextInputAction.next,
                      maxLines: 1,
                      textInputType: TextInputType.number,
                    ),
                  ),
                ],
              );
            }),
          ).paddingOnly(bottom: 32),
          GetBuilder<ProductFilterScreenController>(
            id: Constant.idFilterUpdate,
            builder: (controller) {
              return CustomTitle(
                title: EnumLocale.txtPostedSince.name.tr,
                method: CustomDropdownField(
                  items: postedSinceOptions,             // 👈 list with label/value
                  value: controller.postedSince,         // 👈 controller stores the value
                  hintText: "Select option",
                  onChanged: (selectedValue) {           // returns the value
                    controller.setPostedSince(selectedValue);
                    Utils.showLog("User selected postedSince VALUE: $selectedValue");
                  },
                  fillColor: AppColors.white,
                  textColor: AppColors.black,
                  fontSize: 15,
                  height: 55,
                ),
              );
            },
          ).paddingOnly(bottom: 32),
        ],
      ).paddingOnly(top: 24, left: 16, right: 16),
    );
  }
}

class BottomButton extends StatelessWidget {
  const BottomButton({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductFilterScreenController>(
      id: Constant.idAllAds,
      builder: (controller) {
        return Container(
          decoration: BoxDecoration(
            color: AppColors.white,
            boxShadow: [
              BoxShadow(
                color: AppColors.black.withValues(alpha: 0.1),
                blurRadius: 12,
                offset: const Offset(0, -2),
                spreadRadius: 0,
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              PrimaryAppButton(
                text: EnumLocale.txtApplyFilter.name.tr,
                height: 54,
                // onTap: controller.isLoading
                //     ? null
                //     : () async {
                //         if (controller.search == true) {
                //           controller.popularProductFilterApi();
                //         }
                //         if (controller.popular == true) {
                //           controller.popularProductFilterApi();
                //         }
                //         if (controller.mostLike == true) {
                //           controller.popularProductFilterApi();
                //         } else {
                //           await controller.categoryWiseFilterProductApi();
                //         }
                //       },

                onTap: controller.isLoading
                    ? null
                    : () async {
                  final usePopular = (controller.search == true) ||
                      (controller.popular == true) ||
                      (controller.mostLike == true);

                  if (usePopular) {
                    await controller.popularProductFilterApi();
                  } else {
                    await controller.categoryWiseFilterProductApi();
                  }
                },

              ).paddingSymmetric(vertical: 12, horizontal: 16),
            ],
          ),
        );
      },
    );
  }
}

class SortByBottomSheet extends StatelessWidget {
  const SortByBottomSheet({super.key});

  @override
  Widget build(BuildContext context) {
    List sortByType = [
      EnumLocale.txtDefault.name.tr,
      EnumLocale.txtNewToOld.name.tr,
      EnumLocale.txtOldToNew.name.tr,
      EnumLocale.txtPriceHighToLow.name.tr,
      EnumLocale.txtPriceLowToHight.name.tr,
    ];
    return Container(
      // height: Get.height * 0.6,
      width: Get.width,
      // padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: Get.width,
            decoration: BoxDecoration(
              color: AppColors.lightGrey100,
              borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Spacer(),
                Text(
                  EnumLocale.txtSortby.name.tr,
                  style: AppFontStyle.fontStyleW700(
                    fontSize: 18,
                    fontColor: AppColors.black,
                  ),
                ).paddingOnly(left: 30, bottom: 19, top: 19),
                Spacer(),
                InkWell(
                  onTap: () {
                    Get.back();
                  },
                  child: Image.asset(
                    AppAsset.closeFillIcon,
                    width: 30,
                  ).paddingOnly(top: 14),
                )
              ],
            ).paddingSymmetric(horizontal: 16),
          ),
          GetBuilder<SubCategoryProductScreenController>(builder: (controller) {
            return Column(
              children: List.generate(
                5,
                (index) {
                  return Column(
                    children: [
                      GestureDetector(
                        onTap: () {
                          final sortKey = controller.getSortKeyFromIndex(index);
                          controller.applySort(sortKey);
                          Get.back();
                        },
                        child: Container(
                          width: Get.width,
                          color: AppColors.transparent,
                          child: Text(
                            sortByType[index],
                            style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
                          ).paddingSymmetric(vertical: 24, horizontal: 20),
                        ),
                      ),
                      Divider(
                        height: 0,
                        thickness: 0.8,
                        color: AppColors.lightGrey100,
                      ),
                    ],
                  );
                },
              ),
            );
          }),
          // Container(
          //   width: Get.width,
          //   color: AppColors.transparent,
          //   child: Text(
          //     EnumLocale.txtDefault.name.tr,
          //     style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
          //   ).paddingSymmetric(vertical: 24, horizontal: 20),
          // ),
          // Divider(
          //   height: 0,
          //   thickness: 0.8,
          //   color: AppColors.lightGrey100,
          // ),
          // Container(
          //   width: Get.width,
          //   color: AppColors.transparent,
          //   child: Text(
          //     EnumLocale.txtNewToOld.name.tr,
          //     style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
          //   ).paddingSymmetric(vertical: 24, horizontal: 20),
          // ),
          // Divider(
          //   height: 0,
          //   thickness: 0.8,
          //   color: AppColors.lightGrey100,
          // ),
          // Container(
          //   width: Get.width,
          //   color: AppColors.transparent,
          //   child: Text(
          //     EnumLocale.txtOldToNew.name.tr,
          //     style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
          //   ).paddingSymmetric(vertical: 24, horizontal: 20),
          // ),
          // Divider(
          //   height: 0,
          //   thickness: 0.8,
          //   color: AppColors.lightGrey100,
          // ),
          // Container(
          //   width: Get.width,
          //   color: AppColors.transparent,
          //   child: Text(
          //     EnumLocale.txtPriceHighToLow.name.tr,
          //     style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
          //   ).paddingSymmetric(vertical: 24, horizontal: 20),
          // ),
          // Divider(
          //   height: 0,
          //   thickness: 0.8,
          //   color: AppColors.lightGrey100,
          // ),
          // Container(
          //   width: Get.width,
          //   color: AppColors.transparent,
          //   child: Text(
          //     EnumLocale.txtPriceLowToHight.name.tr,
          //     style: AppFontStyle.fontStyleW400(fontSize: 17, fontColor: AppColors.black),
          //   ).paddingSymmetric(vertical: 24, horizontal: 20),
          // ),
          // Divider(
          //   height: 0,
          //   thickness: 0.8,
          //   color: AppColors.lightGrey100,
          // ),
          4.height,
        ],
      ),
    );
  }
}

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
          decoration: BoxDecoration(color: AppColors.appRedColor, borderRadius: BorderRadius.circular(12)),
          child: SizedBox(
            height: 30,
            width: 30,
            child: CustomImageView(
              image: image,
            ),
          ),
        ).paddingOnly(right: 14),
        Text(
          text,
          style: AppFontStyle.fontStyleW500(fontSize: 17, fontColor: AppColors.black),
        )
      ],
    ).paddingOnly(left: 16, bottom: 20);
  }
}

/// -------------------- Custom Dropdown --------------------
/*class CustomDropdownField extends StatefulWidget {
  final List<String> items;
  final String? value;
  final String hintText;
  final ValueChanged<String>? onChanged;

  // Styling
  final double height;
  final double borderRadius;
  final Color fillColor;
  final Color textColor;
  final double fontSize;

  const CustomDropdownField({
    super.key,
    required this.items,
    this.value,
    this.hintText = "Select option",
    this.onChanged,
    this.height = 50,
    this.borderRadius = 8,
    this.fillColor = Colors.white,
    this.textColor = Colors.black,
    this.fontSize = 15,
  });

  @override
  State<CustomDropdownField> createState() => _CustomDropdownFieldState();
}

class _CustomDropdownFieldState extends State<CustomDropdownField> {
  final LayerLink _layerLink = LayerLink(); // anchors popup to field
  OverlayEntry? _entry;
  bool _isOpen = false;
  late FocusNode _focus;

  String? _selected;

  @override
  void initState() {
    super.initState();
    _focus = FocusNode();
    _selected = widget.value;
  }

  @override
  void dispose() {
    _removeOverlay();
    _focus.dispose();
    super.dispose();
  }

  void _toggleOverlay() {
    if (_isOpen) {
      _removeOverlay();
    } else {
      _showOverlay();
    }
  }

  void _showOverlay() {
    final overlay = Overlay.of(context);

    _entry = OverlayEntry(
      builder: (context) {
        // position popup right under the field
        return Positioned.fill(
          left: 0,
          right: 35,
          child: GestureDetector(
            behavior: HitTestBehavior.opaque,
            onTap: _removeOverlay, // tap outside closes
            child: Stack(
              children: [
                CompositedTransformFollower(
                  link: _layerLink,
                  showWhenUnlinked: false,
                  offset: Offset(0, widget.height + 6),
                  child: Material(
                    elevation: 8,
                    borderRadius: BorderRadius.circular(widget.borderRadius),
                    clipBehavior: Clip.antiAlias,
                    child: ListView.separated(
                      padding: EdgeInsets.zero,
                      shrinkWrap: true,
                      itemCount: widget.items.length,
                      separatorBuilder: (_, __) => SizedBox(),
                      itemBuilder: (context, i) {
                        final v = widget.items[i];
                        final isSelected = v == _selected;
                        return InkWell(
                          onTap: () {
                            setState(() => _selected = v);
                            widget.onChanged?.call(v);
                            _removeOverlay();
                          },
                          child: Container(
                            color: AppColors.white,
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            child: Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    v,
                                    style: TextStyle(
                                      fontSize: widget.fontSize,
                                      color: widget.textColor,
                                      fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );

    overlay.insert(_entry!);
    setState(() => _isOpen = true);
  }

  void _removeOverlay() {
    _entry?.remove();
    _entry = null;
    if (_isOpen) setState(() => _isOpen = false);
  }

  @override
  Widget build(BuildContext context) {
    final borderRadius = BorderRadius.circular(widget.borderRadius);

    return CompositedTransformTarget(
      link: _layerLink,
      child: Focus(
        focusNode: _focus,
        child: GestureDetector(
          onTap: _toggleOverlay,
          child: Container(
            height: widget.height,
            decoration: BoxDecoration(
              color: widget.fillColor,
              borderRadius: borderRadius,
              border: Border.all(
                color: _isOpen || _focus.hasFocus ? AppColors.txtFieldBorder : AppColors.txtFieldBorder,
                width: 1,
              ),
            ),
            padding: const EdgeInsets.symmetric(horizontal: 12),
            alignment: Alignment.centerLeft,
            child: Row(
              children: [
                Expanded(
                  child: Text(
                    _selected ?? widget.hintText,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      fontSize: widget.fontSize,
                      color: _selected == null ? Colors.black.withValues(alpha: 0.5) : widget.textColor,
                    ),
                  ),
                ),
                AnimatedRotation(
                  turns: _isOpen ? 0.5 : 0,
                  duration: const Duration(milliseconds: 150),
                  child: const Icon(Icons.arrow_drop_down),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}*/


class DropdownOption {
  final String label;
  final String value;
  const DropdownOption({required this.label, required this.value});
}

// Your list (same as you shared, typed)
const postedSinceOptions = <DropdownOption>[
  DropdownOption(label: "All time",     value: "all_time"),
  DropdownOption(label: "Today",        value: "today"),
  DropdownOption(label: "This week",    value: "this_week"),
  DropdownOption(label: "This month",   value: "this_month"),
  DropdownOption(label: "Last 7 days",  value: "7"),
  DropdownOption(label: "Last 30 days", value: "30"),
];



class CustomDropdownField extends StatefulWidget {
  final List<DropdownOption> items;

  /// The *value* to select (e.g., "all_time"), not the label.
  final String? value;

  final String hintText;
  /// onChanged gets the *value* (e.g., "all_time")
  final ValueChanged<String>? onChanged;

  // Styling
  final double height;
  final double borderRadius;
  final Color fillColor;
  final Color textColor;
  final double fontSize;

  const CustomDropdownField({
    super.key,
    required this.items,
    this.value,
    this.hintText = "Select option",
    this.onChanged,
    this.height = 50,
    this.borderRadius = 8,
    this.fillColor = Colors.white,
    this.textColor = Colors.black,
    this.fontSize = 15,
  });

  @override
  State<CustomDropdownField> createState() => _CustomDropdownFieldState();
}

class _CustomDropdownFieldState extends State<CustomDropdownField> {
  final LayerLink _layerLink = LayerLink(); // anchors popup to field
  OverlayEntry? _entry;
  bool _isOpen = false;
  late FocusNode _focus;

  /// Holds the *value* of the current selection (e.g., "all_time")
  String? _selectedValue;

  @override
  void initState() {
    super.initState();
    _focus = FocusNode();
    _selectedValue = widget.value;
  }

  @override
  void didUpdateWidget(covariant CustomDropdownField oldWidget) {
    super.didUpdateWidget(oldWidget);
    // keep in sync if parent updates value
    if (oldWidget.value != widget.value) {
      _selectedValue = widget.value;
    }
  }

  @override
  void dispose() {
    _removeOverlay();
    _focus.dispose();
    super.dispose();
  }

  void _toggleOverlay() {
    if (_isOpen) {
      _removeOverlay();
    } else {
      _showOverlay();
    }
  }

  void _showOverlay() {
    final overlay = Overlay.of(context);

    _entry = OverlayEntry(
      builder: (context) {
        return Positioned.fill(
          left: 0,
          right: 35,
          child: GestureDetector(
            behavior: HitTestBehavior.opaque,
            onTap: _removeOverlay, // tap outside closes
            child: Stack(
              children: [
                CompositedTransformFollower(
                  link: _layerLink,
                  showWhenUnlinked: false,
                  offset: Offset(0, widget.height + 6),
                  child: Material(
                    elevation: 8,
                    borderRadius: BorderRadius.circular(widget.borderRadius),
                    clipBehavior: Clip.antiAlias,
                    child: ListView.separated(
                      padding: EdgeInsets.zero,
                      shrinkWrap: true,
                      itemCount: widget.items.length,
                      separatorBuilder: (_, __) => const SizedBox(),
                      itemBuilder: (context, i) {
                        final option = widget.items[i];
                        final isSelected = option.value == _selectedValue;
                        return InkWell(
                          onTap: () {
                            setState(() => _selectedValue = option.value);
                            widget.onChanged?.call(option.value); // return value
                            _removeOverlay();
                          },
                          child: Container(
                            color: AppColors.white,
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            child: Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    option.label, // 👈 show label
                                    style: TextStyle(
                                      fontSize: widget.fontSize,
                                      color: widget.textColor,
                                      fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );

    overlay.insert(_entry!);
    setState(() => _isOpen = true);
  }

  void _removeOverlay() {
    _entry?.remove();
    _entry = null;
    if (_isOpen) setState(() => _isOpen = false);
  }

  String _labelForValue(String? value) {
    if (value == null) return widget.hintText;
    final found = widget.items.firstWhere(
          (o) => o.value == value,
      orElse: () => DropdownOption(label: widget.hintText, value: ''),
    );
    return found.label;
  }

  @override
  Widget build(BuildContext context) {
    final borderRadius = BorderRadius.circular(widget.borderRadius);
    final displayText = _labelForValue(_selectedValue);

    return CompositedTransformTarget(
      link: _layerLink,
      child: Focus(
        focusNode: _focus,
        child: GestureDetector(
          onTap: _toggleOverlay,
          child: Container(
            height: widget.height,
            decoration: BoxDecoration(
              color: widget.fillColor,
              borderRadius: borderRadius,
              border: Border.all(
                color: _isOpen || _focus.hasFocus ? AppColors.txtFieldBorder : AppColors.txtFieldBorder,
                width: 1,
              ),
            ),
            padding: const EdgeInsets.symmetric(horizontal: 12),
            alignment: Alignment.centerLeft,
            child: Row(
              children: [
                Expanded(
                  child: Text(
                    displayText,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      fontSize: widget.fontSize,
                      color: (_selectedValue == null || _selectedValue!.isEmpty)
                          ? Colors.black.withValues(alpha: 0.5)
                          : widget.textColor,
                    ),
                  ),
                ),
                AnimatedRotation(
                  turns: _isOpen ? 0.5 : 0,
                  duration: const Duration(milliseconds: 150),
                  child: const Icon(Icons.arrow_drop_down),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}


/// -------------------- End Custom Dropdown --------------------

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
          child: GetBuilder<ProductFilterScreenController>(
            // id: Constant.idFilterUpdate,
            builder: (controller) {
              return ListView.builder(
                itemCount: itemCount,
                scrollDirection: Axis.horizontal,
                shrinkWrap: true,
                itemBuilder: (context, index) {
                  final selectedIndex = controller.selectedRadioIndices[attributeIndex];
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
                          color: isSelected ? AppColors.appRedColor : AppColors.txtFieldBorder,
                        ),
                        color: isSelected ? AppColors.appRedColor.withValues(alpha: 0.08) : AppColors.white,
                      ),
                      child: Center(
                        child: Text(
                          valueList[index],
                          style: AppFontStyle.fontStyleW500(
                            fontSize: 15,
                            fontColor: isSelected ? AppColors.appRedColor : AppColors.black,
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
            Utils.showLog('Selected for index ${widget.attributeIndex}: $selectedItems');
          },
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 13, vertical: 10),
            decoration: BoxDecoration(
              color: isSelected ? AppColors.lightRedColor2 : AppColors.white,
              border: Border.all(
                color: isSelected ? AppColors.appRedColor : AppColors.txtFieldBorder,
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
                    fontColor: isSelected ? AppColors.appRedColor : AppColors.black,
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
    selectedValue = widget.initialValue?.isNotEmpty == true ? widget.initialValue : null;
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
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
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
                  fontColor: selectedValue == null ? Colors.grey : AppColors.black,
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
    return GetBuilder<ProductFilterScreenController>(builder: (controller) {
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
            PrimaryAppButton(
              text: EnumLocale.txtNext.name.tr,
              height: 54,
              onTap: () {
                // if (controller.validateRequiredFields(context)) {
                //   controller.printAllEnteredValues();
                // }
              },
            ).paddingSymmetric(vertical: 12, horizontal: 16),
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
    final controller = Get.find<ProductFilterScreenController>();
    return GetBuilder<ProductFilterScreenController>(
        // id: Constant.idFilterUpdate,
        builder: (controller) {
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
                if (widget.maxValue != null) LengthLimitingTextInputFormatter(widget.maxValue),
              ],
              // inputFormatters: widget.isNumeric ? [FilteringTextInputFormatter.digitsOnly] : null,
              decoration: const InputDecoration(
                contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 14),
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
    });
  }
}



// class CategorySelect extends StatelessWidget {
//   final void Function()? onTap;
//   const CategorySelect({super.key, this.onTap});
//
//   @override
//   Widget build(BuildContext context) {
//     return GestureDetector(
//       onTap: onTap,
//       child: GetBuilder<ProductFilterScreenController>(
//         id: Constant.idCategoryHeader,
//         builder: (controller) {
//           final bus = Get.find<SelectionBus>();
//
//           // --- Title resolve ---
//           final String titleFromCtl = (controller.categoryTitle ?? '').toString().trim();
//           final String titleFromBus = (bus.selectedCategoryTitle ?? '').toString().trim();
//
//           // Handle cases where API or data returns literal 'null'
//           final String safeTitleFromCtl =
//           (titleFromCtl.isEmpty || titleFromCtl.toLowerCase() == 'null')
//               ? ''
//               : titleFromCtl;
//           final String safeTitleFromBus =
//           (titleFromBus.isEmpty || titleFromBus.toLowerCase() == 'null')
//               ? ''
//               : titleFromBus;
//
//           final String title = safeTitleFromCtl.isNotEmpty
//               ? safeTitleFromCtl
//               : (safeTitleFromBus.isNotEmpty ? safeTitleFromBus : 'Select category');
//
//           // --- Image resolve ---
//           final String imgFromCtl = (controller.categoryImage ?? '').toString().trim();
//           final String imgFromBus = (bus.selectedCategoryImage ?? '').toString().trim();
//
//           // Sanitize 'null' and empty values
//           final String safeImgFromCtl =
//           (imgFromCtl.isEmpty || imgFromCtl.toLowerCase() == 'null') ? '' : imgFromCtl;
//           final String safeImgFromBus =
//           (imgFromBus.isEmpty || imgFromBus.toLowerCase() == 'null') ? '' : imgFromBus;
//
//           final String imagePath =
//           safeImgFromCtl.isNotEmpty ? safeImgFromCtl : safeImgFromBus;
//           final bool hasImage = imagePath.isNotEmpty;
//           final bool hasSelection = title != 'Select category';
//
//           return Container(
//             height: 54,
//             width: Get.width,
//             decoration: BoxDecoration(
//               color: AppColors.white,
//               borderRadius: BorderRadius.circular(12),
//               border: Border.all(color: AppColors.txtFieldBorder),
//             ),
//             child: Row(
//               children: [
//                 const SizedBox(width: 12),
//                 Container(
//                   height: 36,
//                   width: 36,
//                   decoration: BoxDecoration(
//                     color: AppColors.white,
//                     borderRadius: BorderRadius.circular(8),
//                     border: Border.all(color: AppColors.txtFieldBorder),
//                   ),
//                   child: ClipRRect(
//                     borderRadius: BorderRadius.circular(8),
//                     child: hasImage
//                         ? CustomImageView(
//                       image: imagePath.startsWith('http')
//                           ? imagePath
//                           : "${Api.baseUrl}$imagePath",
//                       fit: BoxFit.cover,
//                     )
//                         : Center(
//                       child: Icon(
//                         Icons.category_outlined,
//                         size: 22,
//                         color: AppColors.black.withValues(alpha: 0.35),
//                       ),
//                     ),
//                   ),
//                 ),
//                 const SizedBox(width: 12),
//                 Expanded(
//                   child: Text(
//                     title,
//                     maxLines: 1,
//                     overflow: TextOverflow.ellipsis,
//                     style: AppFontStyle.fontStyleW500(
//                       fontSize: 15,
//                       fontColor: hasSelection
//                           ? AppColors.black
//                           : AppColors.black.withValues(alpha: 0.35),
//                     ),
//                   ),
//                 ),
//                 const SizedBox(width: 12),
//                 Icon(Icons.keyboard_arrow_right,
//                     color: AppColors.black.withValues(alpha: 0.35)),
//                 const SizedBox(width: 8),
//               ],
//             ),
//           ).paddingOnly(bottom: 25);
//         },
//       ),
//     );
//   }
// }
class CategorySelect extends StatelessWidget {
  final void Function()? onTap;
  const CategorySelect({super.key, this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: GetX<SelectionBus>(
        // 🔁 Rebuild only when bus fields change
        builder: (bus) {
          final String titleRaw = (bus.selectedCategoryTitle.value ?? '').trim();
          final String imgRaw   = (bus.selectedCategoryImage.value ?? '').trim();

          final String title =
          (titleRaw.isEmpty || titleRaw.toLowerCase() == 'null')
              ? 'Select category'
              : titleRaw;

          final String imagePath =
          (imgRaw.isEmpty || imgRaw.toLowerCase() == 'null') ? '' : imgRaw;

          final bool hasImage     = imagePath.isNotEmpty;
          final bool hasSelection = title != 'Select category';

          return Container(
            height: 54,
            width: Get.width,
            decoration: BoxDecoration(
              color: AppColors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.txtFieldBorder),
            ),
            child: Row(
              children: [
                const SizedBox(width: 12),
                Container(
                  height: 36,
                  width: 36,
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: AppColors.txtFieldBorder),
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: hasImage
                        ? CustomImageView(
                      image: imagePath.startsWith('http')
                          ? imagePath
                          : "$imagePath",
                      fit: BoxFit.cover,
                    )
                        : Center(
                      child: Icon(
                        Icons.category_outlined,
                        size: 22,
                        color: AppColors.black.withValues(alpha: 0.35),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 15,
                      fontColor: hasSelection
                          ? AppColors.black
                          : AppColors.black.withValues(alpha: 0.35),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Icon(
                  Icons.keyboard_arrow_right,
                  color: AppColors.black.withValues(alpha: 0.35),
                ),
                const SizedBox(width: 8),
              ],
            ),
          ).paddingOnly(bottom: 25);
        },
      ),
    );
  }
}

