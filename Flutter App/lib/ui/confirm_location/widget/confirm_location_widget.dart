import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/confirm_location/controller/confirm_location_controller.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';
import 'package:map_location_picker/map_location_picker.dart';

class ConfirmLocationAppBar extends StatelessWidget {
  final String? title;
  const ConfirmLocationAppBar({super.key, this.title});

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

class ConfirmLocationWidget extends StatelessWidget {
  const ConfirmLocationWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ConfirmLocationScreenController>(builder: (controller) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            EnumLocale.txtConfirmLocationMainTxt.name.tr,
            style: AppFontStyle.fontStyleW700(fontSize: 18, fontColor: AppColors.appRedColor),
          ).paddingOnly(top: 18, right: 30),
          Text(
            EnumLocale.txtConfirmLocationSubTxt.name.tr,
            style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.searchText),
          ).paddingOnly(top: 6, bottom: 16),
          GestureDetector(
            onTap: () {
              controller.navigateLocationScreen();
            },
            child: Container(
              padding: EdgeInsets.symmetric(vertical: 16),
              decoration: BoxDecoration(
                  color: AppColors.lightRed100, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.appRedColor)),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Image.asset(
                    AppAsset.editIcon,
                    height: 20,
                    width: 20,
                  ).paddingOnly(right: 10),
                  Text(
                    EnumLocale.txtSomewhereElse.name.tr,
                    style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.appRedColor),
                  ),
                ],
              ),
            ),
          ),

        ],
      ).paddingOnly(left: 16, right: 16);
    });
  }
}

// class MapView extends StatelessWidget {
//   const MapView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return Column(
//       children: [
//         Stack(
//           children: [
//             Container(
//               height: Get.height * 0.4,
//               width: Get.width,
//               decoration: BoxDecoration(borderRadius: BorderRadius.circular(12)),
//               child: ClipRRect(
//                 borderRadius: BorderRadius.circular(12),
//                 // child: Image.network(
//                 //   "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcREhHMIzIwexH-oMixmyVfNA77q-_2VgmYV0g&ss",
//                 //   fit: BoxFit.fill,
//                 // ),
//
//                 child: GetBuilder<MapController>(
//                   id: Constant.location,
//                   builder: (mapController) {
//                     return mapController.latitude != null && mapController.longitude != null
//                         ? GoogleMap(
//                             markers: Set<Marker>.from(mapController.markers),
//                             initialCameraPosition: CameraPosition(
//                               target: LatLng(mapController.latitude!, mapController.longitude!),
//                               zoom: 18.0,
//                             ),
//                             myLocationEnabled: true,
//                             myLocationButtonEnabled: false,
//                             mapType: MapType.normal,
//                             zoomGesturesEnabled: !mapController.isLoading,
//                             zoomControlsEnabled: false,
//                             // onMapCreated: (GoogleMapController controller) {
//                             //   nearController.mapController = controller;
//                             // },
//                             onMapCreated: (GoogleMapController controller) async {
//                               mapController.mapController = controller;
//
//                               // ⚠️ Wait a little so camera is initialized before calling marker logic
//                               await Future.delayed(Duration(milliseconds: 300));
//
//                               // Only add red marker if not already added (optional but safe)
//                               if (mapController.markers.isEmpty && mapController.latitude != null && mapController.longitude != null) {
//                                 await mapController.onHandleTapPoint(
//                                   LatLng(mapController.latitude!, mapController.longitude!),
//                                 );
//                               }
//                             },
//
//                             onTap: mapController.isLoading ? null : mapController.onHandleTapPoint,
//                           )
//                         : Center(child: CupertinoActivityIndicator()); // 👈 Show loader until coordinates are ready
//                   },
//                 ),
//               ),
//             ),
//             Positioned(
//               bottom: 16,
//               right: 16,
//               child: GetBuilder<MapController>(
//                   id: Constant.location,
//                   builder: (controller) {
//                     return GestureDetector(
//                       onTap: () async {
//                         await controller.getUserLocationPosition().then((value) {
//                           controller.mapController?.animateCamera(
//                             CameraUpdate.newCameraPosition(
//                               CameraPosition(
//                                 target: LatLng(value.latitude, value.longitude),
//                                 zoom: 18.0,
//                               ),
//                             ),
//                           );
//
//                           controller.onHandleTapPoint(
//                             LatLng(value.latitude, value.longitude),
//                           );
//                         });
//                       },
//                       child: Container(
//                         color: Colors.transparent,
//                         child: Image.asset(
//                           AppAsset.gpsTrackerIcon,
//                           height: 58,
//                           width: 58,
//                         ),
//                       ),
//                     );
//                   }),
//             ),
//           ],
//         )
//       ],
//     ).paddingOnly(top: 18, left: 12, right: 12);
//   }
// }


class MapView extends StatelessWidget {
  const MapView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<MapController>();

    return Column(
      children: [
        Stack(
          children: [
            Container(
              height: Get.height * 0.43,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(12),
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: GetBuilder<MapController>(
                  id: Constant.location,
                  builder: (mapController) {
                    if (mapController.latitude == null ||
                        mapController.longitude == null) {
                      return const Center(child: CupertinoActivityIndicator());
                    }

                    return GoogleMap(
                      markers: mapController.markers,
                      circles: mapController.circles,
                      initialCameraPosition: CameraPosition(
                        target: LatLng(
                          mapController.latitude!,
                          mapController.longitude!,
                        ),
                        zoom: 15.5,
                      ),
                      myLocationEnabled: true,
                      myLocationButtonEnabled: true,
                      mapType: MapType.normal,
                      zoomGesturesEnabled: true, // ✅ always allow zoom
                      scrollGesturesEnabled: true, // ✅ enable drag
                      rotateGesturesEnabled: true, // ✅ enable rotate
                      tiltGesturesEnabled: true, // ✅ enable tilt
                      zoomControlsEnabled: false,
                      // onMapCreated: (GoogleMapController gctl) async {
                      //   mapController.mapController = gctl;
                      //
                      //   if (mapController.center == null &&
                      //       mapController.latitude != null &&
                      //       mapController.longitude != null) {
                      //     final c = LatLng(
                      //       mapController.latitude!,
                      //       mapController.longitude!,
                      //     );
                      //     await mapController.onHandleTapPoint(c);
                      //   }
                      // },

                      onMapCreated: (gctl) async {
                        mapController.mapController = gctl;

                        if (mapController.center == null &&
                            mapController.latitude != null &&
                            mapController.longitude != null) {
                          final c = LatLng(mapController.latitude!, mapController.longitude!);
                          await mapController.onHandleTapPoint(c); // this fits bounds
                        } else {
                          await mapController.fitCircleInView(paddingPx: 60);
                        }
                      },

                      onTap: mapController.onHandleTapPoint, // ✅ always active
                    );
                  },
                ),
              ),
            ),
          ],
        )
      ],
    ).paddingOnly(top: 18, left: 12, right: 12);
  }
}

class RePostButton extends StatelessWidget {
  const RePostButton({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ConfirmLocationScreenController>(builder: (controller) {
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
            MapLocationShowView().paddingOnly(top: 15),

            PrimaryAppButton(
              text: EnumLocale.txtNext.name.tr,
              height: 54,
              onTap: () {
                Utils.showLog("addressStreet${controller.addressStreet}");
                Utils.showLog("addressCity${controller.addressCity}");
                Utils.showLog("addressState${controller.addressState}");
                Utils.showLog("addressCountry${controller.addressCountry}");
                Utils.showLog("addressPostalCode${controller.addressPostalCode}");
                Utils.showLog("addressName${controller.addressName}");
                Utils.showLog("finalAddress${controller.finalAddress}");
                final locationData = controller.locationDataForApi();
                Utils.showLog("locationDataForApi::::::::::::: $locationData");
                Utils.showLog("controller.adsData?.id::::::::::::: ${controller.adsData?.id}");
                controller.arguments.addAll({
                  "locationData": locationData,
                  'editApi': controller.isEdit,
                  'adId': controller.adsData?.id,
                  'ad': controller.adsData,
                });
                Utils.showLog("controller.arguments${controller.arguments}");

                Utils.showLog("finalAddress::::::::::::::${Get.find<MapController>().finalAddress}");
                Utils.showLog("addressCity::::::::::::::${Get.find<MapController>().addressCity}");
                Get.toNamed(AppRoutes.productPricingScreen, arguments: controller.arguments);
              },
            ).paddingSymmetric(vertical: 12, horizontal: 16),
          ],
        ),
      );
    });
  }
}

class MapLocationShowView extends StatelessWidget {
  const MapLocationShowView({super.key});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          padding: EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: AppColors.lightRed100,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Image.asset(
            AppAsset.locationIcon,
            height: 30,
            width: 30,
            color: AppColors.appRedColor,
          ),
        ).paddingOnly(right: 8),
        // GetBuilder<MapController>(
        //     id: Constant.location,
        //     builder: (mapController) {
        //       return mapController.isLoading
        //           ? Text("Getting Location.......",style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),)
        //           : SizedBox(
        //               width: Get.width * 0.76,
        //               child: Text(
        //                 mapController.finalAddress ?? "",
        //                 style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),
        //                 overflow: TextOverflow.ellipsis,
        //               ),
        //             );
        //     }),


    // SizedBox(
    //               width: Get.width * 0.76,
    //               child: Text(
    //                 Get.find<MapController>().finalAddress ?? "",
    //                 style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),
    //                 overflow: TextOverflow.ellipsis,
    //               ),
    //             ),



           GetBuilder<MapController>(
             id: Constant.location,
             builder: (controller) {
               return SizedBox(
                width: Get.width * 0.76,
                child: Text(
                  controller.finalAddress ?? "",
                  style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),
                  overflow: TextOverflow.ellipsis,
                ),
                         );
             }
           ),

        // GetBuilder<MapController>(
        //   id: Constant.location,
        //   builder: (controller) {
        //     return Obx(() {
        //       return SizedBox(
        //         width: Get.width * 0.76,
        //         child: Text(
        //           Get.find<MapController>().finalAddress ?? "",
        //           style: AppFontStyle.fontStyleW700(fontSize: 16, fontColor: AppColors.black),
        //           overflow: TextOverflow.ellipsis,
        //         ),
        //       );
        //     });
        //   },
        // ),

      ],
    ).paddingOnly(left: 16, right: 16);
  }
}
