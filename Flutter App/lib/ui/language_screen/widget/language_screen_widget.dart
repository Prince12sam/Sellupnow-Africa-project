import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/localization/localizations_delegate.dart';
import 'package:listify/ui/language_screen/controller/language_screen_controller.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class LanguageScreenAppBar extends StatelessWidget {
  final String? title;
  const LanguageScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(300),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class LanguageView extends StatelessWidget {
  const LanguageView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<LanguageScreenController>(
        id: 'language',
        builder: (controller) {
          return ListView.builder(
            padding: EdgeInsets.only(top: 14),
            itemCount: Constant.countryList.length,
            itemBuilder: (context, index) {
              // final lang = controller.languages[index];
              return LanguageItemView(
                // imageUrl: "Constant.countryLis",
                imageUrl: Constant.countryList[index]["image"],
                name: Constant.countryList[index]["country"],
                index: index,
                selectedIndex: controller.checkedValue,
                onTap: () {
                  Utils.showLog("hhhhhhhhhhhhhhhhhhh");
                  controller.onChangeLanguage(languages[index], index);
                },
              );

              //   Container(
              //   color: Colors.white,
              //   child: ListTile(
              //     leading: ClipRRect(
              //       borderRadius: BorderRadius.circular(4),
              //       child: Image.network(
              //         lang.imageUrl,
              //         width: 30,
              //         height: 30,
              //         fit: BoxFit.cover,
              //         errorBuilder: (context, error, stackTrace) => const Icon(Icons.language),
              //       ),
              //     ),
              //     title: Text(lang.name),
              //     trailing: Container(
              //       width: 24,
              //       height: 24,
              //       decoration: BoxDecoration(
              //         shape: BoxShape.circle,
              //         border: Border.all(
              //           color: controller.selectedIndex == index ? Colors.red : Colors.grey,
              //           width: 2,
              //         ),
              //       ),
              //       child: controller.selectedIndex == index
              //           ? Center(
              //               child: Container(
              //                 width: 12,
              //                 height: 12,
              //                 decoration: const BoxDecoration(
              //                   shape: BoxShape.circle,
              //                   color: Colors.red,
              //                 ),
              //               ),
              //             )
              //           : null,
              //     ),
              //     onTap: () {
              //       controller.selectedIndex = index;
              //
              //       controller.update();
              //     },
              //   ),
              // );
            },
          );
        });
  }
}

class LanguageItemView extends StatelessWidget {
  final String? imageUrl;
  final String? name;
  final int? selectedIndex;
  final int? index;
  final void Function()? onTap;
  const LanguageItemView({super.key, this.imageUrl, this.name, this.selectedIndex, this.index, this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        // height: 55,
        width: Get.width,
        decoration: BoxDecoration(
            color: AppColors.languageBgColor,
            border: Border.all(
              color: AppColors.languageBorderColor,
            ),
            borderRadius: BorderRadius.circular(14)),
        child: Row(
          children: [
            Container(
              height: 54,
              width: 54,
              decoration: BoxDecoration(color: AppColors.languageContainerColor, borderRadius: BorderRadius.circular(14)),
              child: Center(
                child: Container(
                  clipBehavior: Clip.hardEdge,
                  height: 38,
                  width: 38,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.white,
                  ),
                  child: Image.asset(
                    imageUrl!,
                    width: 30,
                    height: 30,
                    fit: BoxFit.cover,
                  ),
                ),
              ),
            ).paddingOnly(top: 4, bottom: 4, left: 5, right: 20),
            Text(
              name!,
              style: AppFontStyle.fontStyleW500(
                fontSize: 16,
                fontColor: AppColors.faqTxt,
              ),
            ),
            Spacer(),
            selectedIndex == index
                ? Container(
                    padding: EdgeInsets.all(1),
                    clipBehavior: Clip.hardEdge,
                    decoration:
                        BoxDecoration(shape: BoxShape.circle, color: AppColors.languageBgColor, border: Border.all(color: AppColors.appRedColor)),
                    child: Container(
                      clipBehavior: Clip.hardEdge,
                      height: 24,
                      width: 24,
                      decoration: BoxDecoration(
                        color: AppColors.appRedColor,
                        shape: BoxShape.circle,
                      ),
                    ),
                  ).paddingOnly(right: 18)
                : Container(
                    clipBehavior: Clip.hardEdge,
                    height: 26,
                    width: 26,
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      border: Border.all(color: AppColors.languageUnselectBorderColor),
                      shape: BoxShape.circle,
                    ),
                  ).paddingOnly(right: 18),
          ],
        ),
      ).paddingOnly(right: 14, left: 14, bottom: 14),
    );
  }
}
