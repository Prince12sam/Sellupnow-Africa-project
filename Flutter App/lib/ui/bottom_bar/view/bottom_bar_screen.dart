import 'dart:io';

import 'package:get/get.dart';
import 'package:flutter/material.dart';
import 'package:listify/custom/dialog/purchase_product_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/bottom_bar/controller/bottom_bar_controller.dart';
import 'package:listify/ui/bottom_bar/widget/bottom_bar_widget.dart';
import 'package:listify/ui/videos_screen/controller/videos_screen_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/utils.dart';

// class BottomBarScreen extends StatelessWidget {
//   const BottomBarScreen({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<BottomBarController>(
//       id: Constant.idBottomBar,
//       init: BottomBarController(),
//       builder: (controller) {
//         return Scaffold(
//           backgroundColor: AppColors.lightPurple,
//           body: controller.pages[controller.selectIndex],
//           bottomNavigationBar: BottomAppBar(
//             height: Platform.isIOS ? 100 : 70,
//             color: AppColors.white,
//             elevation: 18,
//             shadowColor: AppColors.black,
//             child: Row(
//               mainAxisAlignment: MainAxisAlignment.spaceBetween,
//               children: [
//                 GestureDetector(
//                   onTap: () => controller.onClick(0),
//                   child: NavBarItem(
//                     image: AppAsset.homeIcon,
//                     label: EnumLocale.txtHome.name.tr,
//                     isActive: controller.selectIndex == 0,
//                   ),
//                 ),
//                 GestureDetector(
//                   onTap: () => controller.onClick(1),
//                   child: NavBarItem(
//                     image: AppAsset.myAdsIcon,
//                     label: EnumLocale.txtMyAds.name.tr,
//                     isActive: controller.selectIndex == 1,
//                   ),
//                 ),
//                 // SizedBox(width: 36),
//                 SizedBox(width: Get.width * 0.09),
//                 GestureDetector(
//                   onTap: () => controller.onClick(2),
//                   child: NavBarItem(
//                     image: AppAsset.videosIcon,
//                     label: EnumLocale.txtVideos.name.tr,
//                     isActive: controller.selectIndex == 2,
//                   ),
//                 ),
//                 GestureDetector(
//                   onTap: () => controller.onClick(3),
//                   child: NavBarItem(
//                     image: AppAsset.messageIcon,
//                     label: EnumLocale.txtMessages.name.tr,
//                     isActive: controller.selectIndex == 3,
//                   ),
//                 ),
//               ],
//             ),
//           ),
//           // resizeToAvoidBottomInset: false,
//           floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
//           floatingActionButton: GestureDetector(
//               onTap: () {
//                 if (Database.getUserProfileResponseModel?.user?.isSubscriptionExpired == true) {
//                   print("jjjjjjjjjjjjjjjjjjjjjj");
//
//                   Get.dialog(
//                     barrierColor: AppColors.black.withValues(alpha: 0.8),
//                     Dialog(
//                       backgroundColor: AppColors.transparent,
//                       shadowColor: Colors.transparent,
//                       surfaceTintColor: Colors.transparent,
//                       elevation: 0,
//                       child: PurchaseProductDialog(
//                         subscribeOnTap: () {
//                           Get.back();
//                           Get.toNamed(AppRoutes.subscriptionPlanScreen);
//                         },
//                         cancelOnTap: () {
//                           Get.back();
//                         },
//                       ),
//                     ),
//                   );
//                 } else {
//                   Get.toNamed(AppRoutes.addListingScreen);
//                 }
//               },
//               child: HexagonButton()),
//         );
//       },
//     );
//   }
// }
///

// class BottomBarScreen extends StatelessWidget {
//   const BottomBarScreen({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<BottomBarController>(
//       id: Constant.idBottomBar,
//       init: BottomBarController(),
//       builder: (controller) {
//         final showBar = controller.isBottomBarVisible;
//
//         return Scaffold(
//           backgroundColor: AppColors.lightPurple,
//           extendBody: true,
//           body: controller.pages[controller.selectIndex],
//
//           // NEW: hide/show bottom bar
//           bottomNavigationBar: showBar
//               ? BottomAppBar(
//             height: Platform.isIOS ? 100 : 70,
//             color: AppColors.white,
//             elevation: 18,
//             shadowColor: AppColors.black,
//             child: Row(
//               mainAxisAlignment: MainAxisAlignment.spaceBetween,
//               children: [
//                 GestureDetector(
//                   onTap: () => controller.onClick(0),
//                   child: NavBarItem(
//                     image: AppAsset.homeIcon,
//                     label: EnumLocale.txtHome.name.tr,
//                     isActive: controller.selectIndex == 0,
//                   ),
//                 ),
//                 GestureDetector(
//                   onTap: () => controller.onClick(1),
//                   child: NavBarItem(
//                     image: AppAsset.myAdsIcon,
//                     label: EnumLocale.txtMyAds.name.tr,
//                     isActive: controller.selectIndex == 1,
//                   ),
//                 ),
//                 SizedBox(width: Get.width * 0.09),
//                 GestureDetector(
//                   onTap: () => controller.onClick(2),
//                   child: NavBarItem(
//                     image: AppAsset.videosIcon,
//                     label: EnumLocale.txtVideos.name.tr,
//                     isActive: controller.selectIndex == 2,
//                   ),
//                 ),
//                 GestureDetector(
//                   onTap: () => controller.onClick(3),
//                   child: NavBarItem(
//                     image: AppAsset.messageIcon,
//                     label: EnumLocale.txtMessages.name.tr,
//                     isActive: controller.selectIndex == 3,
//                   ),
//                 ),
//               ],
//             ),
//           )
//               : null,
//
//           floatingActionButtonLocation:
//           showBar ? FloatingActionButtonLocation.centerDocked : null,
//
//           // NEW: FAB પણ hide/show
//           floatingActionButton: showBar
//               ? GestureDetector(
//             onTap: () {
//               if (Database.getUserProfileResponseModel?.user?.isSubscriptionExpired == true) {
//                 Get.dialog(
//                   barrierColor: AppColors.black.withValues(alpha: 0.8),
//                   Dialog(
//                     backgroundColor: AppColors.transparent,
//                     shadowColor: Colors.transparent,
//                     surfaceTintColor: Colors.transparent,
//                     elevation: 0,
//                     child: PurchaseProductDialog(
//                       subscribeOnTap: () {
//                         Get.back();
//                         Get.toNamed(AppRoutes.subscriptionPlanScreen);
//                       },
//                       cancelOnTap: () {
//                         Get.back();
//                       },
//                     ),
//                   ),
//                 );
//               } else {
//                 Get.toNamed(AppRoutes.addListingScreen);
//               }
//             },
//             child: HexagonButton(),
//           )
//               : null,
//         );
//       },
//     );
//   }
// }
class BottomBarScreen extends StatelessWidget {
  const BottomBarScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<BottomBarController>(
      id: Constant.idBottomBar,
      init: BottomBarController(),
      builder: (controller) {
        final showBar = controller.isBottomBarVisible;

        return Scaffold(
          backgroundColor: AppColors.lightPurple,
          extendBody: true,
          body: controller.pages[controller.selectIndex],

          bottomNavigationBar: showBar
              ? BottomAppBar(
            height: Platform.isIOS ? 100 : 70,
            color: AppColors.white,
            elevation: 18,
            shadowColor: AppColors.black,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                GestureDetector(
                  onTap: () => controller.onClick(0),
                  child: NavBarItem(
                    image: AppAsset.homeIcon,
                    label: EnumLocale.txtHome.name.tr,
                    isActive: controller.selectIndex == 0,
                  ),
                ),
                GestureDetector(
                  onTap: () => controller.onClick(1),
                  child: NavBarItem(
                    image: AppAsset.myAdsIcon,
                    label: EnumLocale.txtMyAds.name.tr,
                    isActive: controller.selectIndex == 1,
                  ),
                ),
                SizedBox(width: Get.width * 0.09),
                GestureDetector(
                  onTap: () => controller.onClick(2),
                  child: NavBarItem(
                    image: AppAsset.videosIcon,
                    label: EnumLocale.txtVideos.name.tr,
                    isActive: controller.selectIndex == 2,
                  ),
                ),
                GestureDetector(
                  onTap: () => controller.onClick(3),
                  child: NavBarItem(
                    image: AppAsset.messageIcon,
                    label: EnumLocale.txtMessages.name.tr,
                    isActive: controller.selectIndex == 3,
                  ),
                ),
              ],
            ),
          )
              : null,

          floatingActionButtonLocation:
          showBar ? FloatingActionButtonLocation.centerDocked : null,

          // ==== NEW: FAB pehla videos pause ====
          floatingActionButton: showBar
              ? GestureDetector(
            onTap: () {
              // hard stop any playing videos
              if (Get.isRegistered<VideosScreenController>()) {
                Get.find<VideosScreenController>().pauseAll();
              }

              Utils.showLog("Database.getUserProfileResponseModel?.user?.isSubscriptionExpired${Database.getUserProfileResponseModel?.user?.isSubscriptionExpired}");

              if (Database.getUserProfileResponseModel?.user?.isSubscriptionExpired == true) {
                Get.dialog(
                  barrierColor: AppColors.black.withValues(alpha: 0.8),
                  Dialog(
                    backgroundColor: AppColors.transparent,
                    shadowColor: Colors.transparent,
                    surfaceTintColor: Colors.transparent,
                    elevation: 0,
                    child: PurchaseProductDialog(
                      subscribeOnTap: () {
                        Get.back();
                        Get.toNamed(AppRoutes.subscriptionPlanScreen);
                      },
                      cancelOnTap: () {
                        Get.back();
                      },
                    ),
                  ),
                );
              } else {
                Get.toNamed(AppRoutes.addListingScreen);
              }
            },
            child: HexagonButton(),
          )
              : null,
        );
      },
    );
  }
}


