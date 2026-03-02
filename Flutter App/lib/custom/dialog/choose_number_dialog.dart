// import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:listify/custom/app_button/primary_app_button.dart';
// import 'package:listify/ui/contact_us_screen/controller/contact_us_screen_controller.dart';
// import 'package:listify/utils/app_color.dart';
// import 'package:listify/utils/enums.dart';
// import 'package:listify/utils/font_style.dart';
// import 'package:listify/utils/utils.dart';
//
// class ChooseNumberDialog extends StatelessWidget {
//   final List<String> numbers = ["+91 99988 77666", "+91 88877 66555"];
//   final ContactUsScreenController controller;
//   ChooseNumberDialog({super.key, required this.controller});
//
//   @override
//   Widget build(BuildContext context) {
//     return SizedBox(
//       // height: 365,
//       // width: 60,
//       child: Material(
//         shape: const SquircleBorder(
//           radius: BorderRadius.all(
//             Radius.circular(58),
//           ),
//         ),
//         color: AppColors.white,
//         child: Column(
//           crossAxisAlignment: CrossAxisAlignment.start,
//           mainAxisSize: MainAxisSize.min,
//           children: [
//             Row(
//               children: [
//                 Spacer(),
//                 Text(
//                   EnumLocale.txtChooseNumber.name.tr,
//                   style: AppFontStyle.fontStyleW500(fontSize: 22, fontColor: AppColors.black),
//                 ),
//                 Spacer(),
//               ],
//             ).paddingOnly(bottom: 49),
//             GetBuilder<ContactUsScreenController>(
//                 init: ContactUsScreenController(),
//                 builder: (controller) {
//                   return Column(
//                     children: List.generate(
//                       2,
//                       (index) {
//                         bool isSelected = controller.selectedIndex == index;
//
//                         return GestureDetector(
//                           onTap: () => controller.selectNumber(index),
//                           child: Container(
//                             color: AppColors.transparent,
//                             child: Row(
//                               children: [
//                                 Container(
//                                   height: 28,
//                                   width: 28,
//                                   decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.unSelected),
//                                   child: ClipOval(
//                                     child: Image.network(
//                                       'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcROq-SYaDgMMSV_qZgrj06YB0X0-6JtAzIHZg&s',
//                                       fit: BoxFit.cover,
//                                     ),
//                                   ),
//                                 ).paddingOnly(right: 14),
//                                 Text(
//                                   numbers[index],
//                                   style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
//                                 ),
//                                 Spacer(),
//                                 Container(
//                                   height: 22,
//                                   width: 22,
//                                   decoration: BoxDecoration(
//                                     shape: BoxShape.circle,
//                                     border: Border.all(
//                                       color: isSelected ? AppColors.appRedColor : AppColors.grey300.withValues(alpha: 0.5),
//                                     ),
//                                   ),
//                                   child: isSelected
//                                       ? Center(
//                                           child: Container(
//                                             decoration: const BoxDecoration(
//                                               color: Colors.red,
//                                               shape: BoxShape.circle,
//                                             ),
//                                           ).paddingAll(0.6),
//                                         )
//                                       : null,
//                                 ),
//                               ],
//                             ).paddingOnly(bottom: 46),
//                           ),
//                         );
//                       },
//                     ),
//                   );
//                 }),
//             PrimaryAppButton(
//               onTap: () async {
//                 Get.back();
//
//
//                 final phone = "" ??"";
//
//                 Utils.showLog("phone:::::::::::::::::::::$phone");
//                 await controller.openDialer(phone);
//               },
//               height: 54,
//               text: EnumLocale.txtDone.name.tr,
//             )
//           ],
//         ).paddingAll(17),
//       ),
//     );
//   }
// }
import 'package:cupertino_rounded_corners/cupertino_rounded_corners.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/ui/contact_us_screen/controller/contact_us_screen_controller.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class ChooseNumberDialog extends StatelessWidget {
  final List<String> numbers = ["+91 99988 77666", "+91 88877 66555"];
  final ContactUsScreenController controller;

  ChooseNumberDialog({super.key, required this.controller});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      child: Material(
        shape: const SquircleBorder(
          radius: BorderRadius.all(Radius.circular(58)),
        ),
        color: AppColors.white,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              children: [
                const Spacer(),
                Text(
                  EnumLocale.txtChooseNumber.name.tr,
                  style: AppFontStyle.fontStyleW500(fontSize: 22, fontColor: AppColors.black),
                ),
                const Spacer(),
              ],
            ).paddingOnly(bottom: 49),

            // Use the same controller passed in constructor
            GetBuilder<ContactUsScreenController>(
              init: controller,
              builder: (c) {
                return Column(
                  children: List.generate(numbers.length, (index) {
                    final isSelected = c.selectedIndex == index;

                    return GestureDetector(
                      onTap: () => c.selectNumber(index),
                      child: Container(
                        color: AppColors.transparent,
                        child: Row(
                          children: [
                          Container(
                                  height: 28,
                                  width: 28,
                                  decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.unSelected),
                                  child: ClipOval(
                                    child: Image.network(
                                      'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcROq-SYaDgMMSV_qZgrj06YB0X0-6JtAzIHZg&s',
                                      fit: BoxFit.cover,
                                    ),
                                  ),
                                ).paddingOnly(right: 14),
                            Text(
                              numbers[index],
                              style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
                            ),
                            const Spacer(),
                            Container(
                              height: 22,
                              width: 22,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                border: Border.all(
                                  color: isSelected
                                      ? AppColors.appRedColor
                                      : AppColors.grey300.withValues(alpha: 0.5),
                                ),
                              ),
                              child: isSelected
                                  ? Center(
                                child: Container(
                                  height: 12,
                                  width: 12,
                                  decoration: const BoxDecoration(
                                    color: Colors.red,
                                    shape: BoxShape.circle,
                                  ),
                                ),
                              )
                                  : null,
                            ),
                          ],
                        ).paddingOnly(bottom: 46),
                      ),
                    );
                  }),
                );
              },
            ),

            PrimaryAppButton(
              onTap: () async {
                // selected number -> phone
                String phone = "";
                final idx = controller.selectedIndex;

                if (numbers.isNotEmpty && idx >= 0 && idx < numbers.length) {
                  phone = numbers[idx];
                }

                // Log & close dialog
                Utils.showLog("phone:::::::::::::::::::::$phone");
                Get.back();

                // Open dialer with selected phone
                await controller.openDialer(phone);
              },
              height: 54,
              text: EnumLocale.txtDone.name.tr,
            ),
          ],
        ).paddingAll(17),
      ),
    );
  }
}
