import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/near_by_listing_screen/controller/map_controller.dart';
import 'package:listify/ui/near_by_listing_screen/controller/near_by_listing_screen_controller.dart';
import 'package:listify/ui/sub_category_product_screen/controller/gloable_controller.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class NearByListingScreenAppBar extends StatelessWidget {
  final String? title;
  const NearByListingScreenAppBar({super.key, this.title});

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
              height: Get.height * 0.49,
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


// class MapView extends StatelessWidget {
//   const MapView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     final controller = Get.find<MapController>();
//
//     return Column(
//       children: [
//         Stack(
//           children: [
//             Container(
//               height: Get.height * 0.49,
//               decoration: BoxDecoration(
//                 borderRadius: BorderRadius.circular(12),
//               ),
//               child: ClipRRect(
//                 borderRadius: BorderRadius.circular(12),
//                 child: GetBuilder<MapController>(
//                   id: Constant.location,
//                   builder: (mapController) {
//                     if (mapController.latitude == null ||
//                         mapController.longitude == null) {
//                       return const Center(child: CupertinoActivityIndicator());
//                     }
//
//                     return GoogleMap(
//                       markers: mapController.markers,
//                       circles: mapController.circles,
//                       initialCameraPosition: CameraPosition(
//                         target: LatLng(mapController.latitude!, mapController.longitude!),
//                         zoom: 15.5,
//                       ),
//                       myLocationEnabled: true,
//                       myLocationButtonEnabled: true,
//                       mapType: MapType.normal,
//                       zoomGesturesEnabled: true,
//                       scrollGesturesEnabled: true,
//                       rotateGesturesEnabled: true,
//                       tiltGesturesEnabled: true,
//                       zoomControlsEnabled: false,
//                       onMapCreated: (gctl) async {
//                         mapController.mapController = gctl;
//
//                         if (mapController.center == null &&
//                             mapController.latitude != null &&
//                             mapController.longitude != null) {
//                           final c = LatLng(mapController.latitude!, mapController.longitude!);
//                           await mapController.onHandleTapPoint(c); // fits bounds
//                         } else {
//                           await mapController.fitCircleInView(paddingPx: 60);
//                         }
//                       },
//                       onTap: mapController.onHandleTapPoint,
//                     );
//                   },
//                 ),
//               ),
//             ),
//           ],
//         )
//       ],
//     ).paddingOnly(top: 18, left: 12, right: 12);
//   }
// }


class BottomDetailsView extends StatelessWidget {
  const BottomDetailsView({super.key});

  @override
  Widget build(BuildContext context) {
    final ctrl = Get.find<MapController>();

    return GetBuilder<MapController>(
      id: Constant.location,
      builder: (c) {
        final double value = c.radiusKm; // always non-null (default 20)
        return Container(
          decoration: BoxDecoration(
            color: AppColors.white,
            boxShadow: [
              BoxShadow(
                color: AppColors.black.withValues(alpha: 0.1),
                blurRadius: 12,
                offset: const Offset(0, -2),
              ),
            ],
          ),
          child: Column(
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    EnumLocale.txtSelectAreaRange.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 16, fontColor: AppColors.black),
                  ).paddingOnly(top: 15),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: AppColors.lightRed100,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      "${value.toInt()} Km",
                      style: AppFontStyle.fontStyleW700(
                          fontSize: 14, fontColor: AppColors.appRedColor),
                    ),
                  ),
                ],
              ),
              Text(
                EnumLocale.txtRangeSelectTxt.name.tr,
                style: AppFontStyle.fontStyleW500(
                    fontSize: 12, fontColor: AppColors.grey300),
              ),
              SliderTheme(
                data: SliderTheme.of(context).copyWith(
                  thumbShape: const RoundSliderThumbShape(
                      enabledThumbRadius: 12, elevation: 2),
                  overlayShape:
                      const RoundSliderOverlayShape(overlayRadius: 20),
                  thumbColor: AppColors.appRedColor,
                  activeTrackColor: AppColors.appRedColor,
                  inactiveTrackColor: AppColors.lightRed100,
                  trackHeight: 3,
                ),
                child: Slider(
                  value: value, // default 20 દેખાશે
                  onChanged: (val) => ctrl.setRadiusKm(val),
                  min: 0,
                  max: 100,
                ),
              ).paddingOnly(top: 10, bottom: 10),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text("${value.toInt()} Km",
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 14, fontColor: AppColors.black)),
                  Text("100 Km",
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 14, fontColor: AppColors.black)),
                ],
              ).paddingOnly(left: 16, right: 16, bottom: 20),
            ],
          ).paddingOnly(left: 16, right: 16),
        ).paddingOnly(top: 0);
      },
    );
  }
}


// class BottomDetailsView extends StatelessWidget {
//   const BottomDetailsView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     final ctrl = Get.find<MapController>();
//
//     return GetBuilder<MapController>(
//       id: Constant.location,
//       builder: (c) {
//         final double value = c.radiusKm;
//         return Container(
//           decoration: BoxDecoration(
//             color: AppColors.white,
//             boxShadow: [
//               BoxShadow(
//                 color: AppColors.black.withValues(alpha: 0.1),
//                 blurRadius: 12,
//                 offset: const Offset(0, -2),
//               ),
//             ],
//           ),
//           child: Column(
//             children: [
//               Row(
//                 mainAxisAlignment: MainAxisAlignment.spaceBetween,
//                 children: [
//                   Text(
//                     EnumLocale.txtSelectAreaRange.name.tr,
//                     style: AppFontStyle.fontStyleW500(fontSize: 16, fontColor: AppColors.black),
//                   ).paddingOnly(top: 15),
//                   Container(
//                     padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
//                     decoration: BoxDecoration(
//                       color: AppColors.lightRed100,
//                       borderRadius: BorderRadius.circular(4),
//                     ),
//                     child: Text(
//                       "${value.toInt()} Km",
//                       style: AppFontStyle.fontStyleW700(fontSize: 14, fontColor: AppColors.appRedColor),
//                     ),
//                   ),
//                 ],
//               ),
//               Text(
//                 EnumLocale.txtRangeSelectTxt.name.tr,
//                 style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.grey300),
//               ),
//               SliderTheme(
//                 data: SliderTheme.of(context).copyWith(
//                   thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 12, elevation: 2),
//                   overlayShape: const RoundSliderOverlayShape(overlayRadius: 20),
//                   thumbColor: AppColors.appRedColor,
//                   activeTrackColor: AppColors.appRedColor,
//                   inactiveTrackColor: AppColors.lightRed100,
//                   trackHeight: 3,
//                 ),
//                 child: Slider(
//                   value: value,
//                   onChanged: (val) => ctrl.setRadiusKm(val),
//                   min: 0,
//                   max: 100,
//                 ),
//               ).paddingOnly(top: 10, bottom: 10),
//               Row(
//                 mainAxisAlignment: MainAxisAlignment.spaceBetween,
//                 children: [
//                   Text("${value.toInt()} Km", style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.black)),
//                   Text("100 Km", style: AppFontStyle.fontStyleW500(fontSize: 14, fontColor: AppColors.black)),
//                 ],
//               ).paddingOnly(left: 16, right: 16, bottom: 20),
//             ],
//           ).paddingOnly(left: 16, right: 16),
//         ).paddingOnly(top: 0);
//       },
//     );
//   }
// }

class BottomBarWidget extends StatelessWidget {
  const BottomBarWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: AppColors.white,
      padding: EdgeInsets.only(top: 10, bottom: 10),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          BottomDetailsView(),
          Container(
            color: AppColors.white,
            child: Row(
              children: [
                Expanded(
                  child: GetBuilder<MapController>(
                      id: Constant.location,
                      builder: (controller) {
                        return PrimaryAppButton(
                          onTap: () async {
                            // await controller.getUserLocationPosition().then((value) {
                            //   controller.mapController?.animateCamera(
                            //     CameraUpdate.newCameraPosition(
                            //       CameraPosition(
                            //         target: LatLng(value.latitude, value.longitude),
                            //         zoom: 18.0,
                            //       ),
                            //     ),
                            //   );
                            //
                            //   controller.onHandleTapPoint(
                            //     LatLng(value.latitude, value.longitude),
                            //   );
                            // });
                            await controller
                                .resetToCurrent(); // current + 20 km
                          },
                          height: 54,
                          fontColor: AppColors.appRedColor,
                          color: AppColors.lightRed100,
                          text: EnumLocale.txtReset.name.tr,
                        );
                      }),
                ),
                14.width,
                Expanded(
                  child: GetBuilder<NearByListingScreenController>(
                      id: Constant.location,
                      builder: (controller) {
                        return PrimaryAppButton(
                          height: 54,
                          // onTap: () {
                          //   final locationData =
                          //       controller.locationDataForApi();
                          //   Utils.showLog("locationDataForApi: $locationData");
                          //
                          //   Utils.showLog(">>>>>>>>>>>>${controller.latitude}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.longitude}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.arguments}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.finalAddress}");
                          //   Utils.showLog(
                          //       "finalAddress>>>>>>>>>>>>${controller.finalAddress}");
                          //   Utils.showLog(">>>>>>>>>>>>${controller.markers}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.addressStreet}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.addressCity}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.addressState}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.addressCountry}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.addressPostalCode}");
                          //   Utils.showLog(
                          //       ">>>>>>>>>>>>${controller.addressName}");
                          //   Utils.showLog(
                          //       "ne_lat>>>>>>>>>>>>${locationData['ne_lat']}");
                          //   Utils.showLog(
                          //       "ne_lng>>>>>>>>>>>>${locationData['ne_lng']}");
                          //   Utils.showLog(
                          //       "sw_lat>>>>>>>>>>>>${locationData['sw_lat']}");
                          //   Utils.showLog(
                          //       "sw_lng>>>>>>>>>>>>${locationData['sw_lng']}");
                          //   Utils.showLog(
                          //       "range>>>>>>>>>>>>${Get.find<MapController>().radiusKm}");
                          //
                          //   if (controller.filterScreen == true) {
                          //     final args = {
                          //       'selectedCity': locationData['city'] ??
                          //           controller.addressCity ??
                          //           controller.finalAddress,
                          //       'selectedCountry': locationData['country'] ??
                          //           controller.addressCountry,
                          //       'selectedState': locationData['state'] ??
                          //           controller.addressState,
                          //       'latitude': locationData['latitude'],
                          //       'longitude': locationData['longitude'],
                          //       'selectCityScreen': true,
                          //       'filterScreen': controller.filterScreen,
                          //       'locationData': locationData,
                          //       'range': locationData['range'],
                          //       'ne_lat': locationData['ne_lat'],
                          //       'ne_lng': locationData['ne_lng'],
                          //       'sw_lat': locationData['sw_lat'],
                          //       'sw_lng': locationData['sw_lng'],
                          //     };
                          //
                          //     GlobalController.updateLocation(args);
                          //
                          //     int count = 0;
                          //     Get.until((route) {
                          //       count++;
                          //       return count >= 3;
                          //     });
                          //   } else if (controller.homeLocation == true) {
                          //     final args = {
                          //       'selectedCity': locationData['city'] ??
                          //           controller.addressCity ??
                          //           controller.finalAddress,
                          //       'selectedCountry': locationData['country'] ??
                          //           controller.addressCountry,
                          //       'selectedState': locationData['state'] ??
                          //           controller.addressState,
                          //       'latitude': locationData['latitude'],
                          //       'longitude': locationData['longitude'],
                          //       'selectCityScreen': true,
                          //       'filterScreen': controller.filterScreen,
                          //       'locationData': locationData,
                          //       'range': locationData['range'],
                          //       'ne_lat': locationData['ne_lat'],
                          //       'ne_lng': locationData['ne_lng'],
                          //       'sw_lat': locationData['sw_lat'],
                          //       'sw_lng': locationData['sw_lng'],
                          //     };
                          //
                          //     GlobalController.updateLocation(args);
                          //
                          //     // 👉 force update Database here
                          //     Database.hasSelectedLocation.value = true;
                          //     Database.setSelectedLocationText(
                          //         "${locationData['city'] ?? controller.addressCity}, "
                          //             "${locationData['state'] ?? controller.addressState}",
                          //         locationData['city'],locationData['country'],locationData['range'],locationData['state']
                          //     );
                          //
                          //     // locationData = controller.locationDataForApi();
                          //     Database.setSelectedLocationFromMap(
                          //       latitude   : locationData['latitude'],
                          //       longitude  : locationData['longitude'],
                          //       fullAddress: locationData['address'] ?? controller.finalAddress ?? '',
                          //       city       : locationData['city'] ?? controller.addressCity,
                          //       state      : locationData['state'] ?? controller.addressState,
                          //       country    : locationData['country'] ?? controller.addressCountry,
                          //       rangeKm    : (locationData['range']),
                          //       neLat      : locationData['ne_lat'],
                          //       neLng      : locationData['ne_lng'],
                          //       swLat      : locationData['sw_lat'],
                          //       swLng      : locationData['sw_lng'],
                          //     );
                          //
                          //
                          //     int count = 0;
                          //     Get.until((route) {
                          //       count++;
                          //       return count >= 3;
                          //     });
                          //   }
                          //   else {
                          //     controller.arguments.addAll({
                          //       "locationData": locationData,
                          //       'editApi': controller.isEdit,
                          //     });
                          //
                          //     Utils.showLog(
                          //         "Arguments: ${controller.arguments}");
                          //
                          //     Get.toNamed(
                          //       AppRoutes.productPricingScreen,
                          //       arguments: controller.arguments,
                          //     );
                          //   }
                          // },


                          onTap: () {
                            final locationData = controller.locationDataForApi();
                            Utils.showLog("Get.find<MapController>().radiusKm: $locationData");

                            // strong casts for safety (String/num -> double)
                            double? _asD(dynamic v) {
                              if (v == null) return null;
                              if (v is num) return v.toDouble();
                              if (v is String) return double.tryParse(v);
                              return null;
                            }

                            final double? lat    = _asD(locationData['latitude']);
                            final double? lng    = _asD(locationData['longitude']);
                            final double? range  = _asD(locationData['range']);
                            final double? neLat  = _asD(locationData['ne_lat']);
                            final double? neLng  = _asD(locationData['ne_lng']);
                            final double? swLat  = _asD(locationData['sw_lat']);
                            final double? swLng  = _asD(locationData['sw_lng']);

                            // Build args (keep as-is if other screens need them)
                            final args = {
                              'selectedCity'   : locationData['city'] ?? controller.addressCity ?? controller.finalAddress,
                              'selectedCountry': locationData['country'] ?? controller.addressCountry,
                              'selectedState'  : locationData['state'] ?? controller.addressState,
                              'latitude'       : lat,
                              'longitude'      : lng,
                              'selectCityScreen': true,
                              // 'filterScreen'   : controller.filterScreen,
                              'locationData'   : locationData,
                              'range'          : range,
                              'ne_lat'         : neLat,
                              'ne_lng'         : neLng,
                              'sw_lat'         : swLat,
                              'sw_lng'         : swLng,
                            };

                            /*if (controller.filterScreen == true) {
                              Utils.showLog("filter controller.arguments..........${args}");

                              GlobalController.updateLocation(args);


                              // ✅ persist ONCE with lat/lng + bounds + range (remove the old setSelectedLocationText call)
                              if (lat != null && lng != null && range != null) {
                                Database.setSelectedLocationFromMap(
                                  latitude   : lat,
                                  longitude  : lng,
                                  fullAddress: locationData['address'] ?? controller.finalAddress ?? '',
                                  city       : locationData['city'] ?? controller.addressCity,
                                  state      : locationData['state'] ?? controller.addressState,
                                  country    : locationData['country'] ?? controller.addressCountry,
                                  rangeKm    : range,
                                  neLat      : neLat,
                                  neLng      : neLng,
                                  swLat      : swLat,
                                  swLng      : swLng,
                                );
                              }
                              int count = 0;
                              Get.until((route) => ++count >= 3);

                            } else */



                              if (controller.homeLocation == true || controller.popular||controller.mostLike||controller.search||controller.subcategory) {
                              Utils.showLog("home controller.arguments..........${args}");

                              GlobalController.updateLocation(args);

                              // ✅ persist ONCE with lat/lng + bounds + range (remove the old setSelectedLocationText call)
                              if (lat != null && lng != null && range != null) {
                                Database.setSelectedLocationFromMap(
                                  latitude   : lat,
                                  longitude  : lng,
                                  fullAddress: locationData['address'] ?? controller.finalAddress ?? '',
                                  city       : locationData['city'] ?? controller.addressCity,
                                  state      : locationData['state'] ?? controller.addressState,
                                  country    : locationData['country'] ?? controller.addressCountry,
                                  rangeKm    : range,
                                  neLat      : neLat,
                                  neLng      : neLng,
                                  swLat      : swLat,
                                  swLng      : swLng,
                                );
                              }

                              int count = 0;
                              Get.until((route) => ++count >= 3);

                            }


                            else {

                              Utils.showLog("else controller.arguments..........${controller.arguments}");
                              controller.arguments.addAll({
                                "locationData": locationData,
                                'editApi': controller.isEdit,
                              });
                              Get.toNamed(AppRoutes.productPricingScreen, arguments: controller.arguments);
                            }
                          },

                          color: AppColors.appRedColor,
                          text: EnumLocale.txtApply.name.tr,
                        );
                      }),
                ),
              ],
            ).paddingOnly(left: 10, right: 10),
          ),
        ],
      ),
    );
  }
}


// class BottomBarWidget extends StatelessWidget {
//   const BottomBarWidget({super.key});
//
//   double? _asD(dynamic v) {
//     if (v == null) return null;
//     if (v is num) return v.toDouble();
//     if (v is String) return double.tryParse(v);
//     return null;
//   }
//
//   @override
//   Widget build(BuildContext context) {
//     return Container(
//       color: AppColors.white,
//       padding: const EdgeInsets.only(top: 10, bottom: 10),
//       child: Column(
//         mainAxisSize: MainAxisSize.min,
//         children: [
//           const BottomDetailsView(),
//           Container(
//             color: AppColors.white,
//             child: Row(
//               children: [
//                 // RESET → GPS + default radius
//                 Expanded(
//                   child: GetBuilder<MapController>(
//                     id: Constant.location,
//                     builder: (controller) {
//                       return PrimaryAppButton(
//                         onTap: () async {
//                           await controller.resetToCurrent();
//                         },
//                         height: 54,
//                         fontColor: AppColors.appRedColor,
//                         color: AppColors.lightRed100,
//                         text: EnumLocale.txtReset.name.tr,
//                       );
//                     },
//                   ),
//                 ),
//                 14.width,
//                 // APPLY → ensureReadyLocation first
//                 Expanded(
//                   child: GetBuilder<MapController>(
//                     id: Constant.location,
//                     builder: (mc) {
//                       return PrimaryAppButton(
//                         height: 54,
//                         onTap: () async {
//                           final ok = await mc.ensureReadyLocation();      // ✅ KEY LINE
//                           if (!ok) {
//                             Utils.showToast(Get.context!, "Unable to get location. Please enable GPS.");
//                             return;
//                           }
//
//                           final locationData = mc.locationDataForApi();
//
//                           final double? lat   = _asD(locationData['latitude']);
//                           final double? lng   = _asD(locationData['longitude']);
//                           final double? range = _asD(locationData['range']);
//                           final double? neLat = _asD(locationData['ne_lat']);
//                           final double? neLng = _asD(locationData['ne_lng']);
//                           final double? swLat = _asD(locationData['sw_lat']);
//                           final double? swLng = _asD(locationData['sw_lng']);
//
//                           final args = {
//                             'selectedCity'    : locationData['city'] ?? mc.addressCity ?? mc.finalAddress,
//                             'selectedCountry' : locationData['country'] ?? mc.addressCountry,
//                             'selectedState'   : locationData['state'] ?? mc.addressState,
//                             'latitude'        : lat,
//                             'longitude'       : lng,
//                             'selectCityScreen': true,
//                             'filterScreen'    : false, // set according to your flow if needed
//                             'locationData'    : locationData,
//                             'range'           : range,
//                             'ne_lat'          : neLat,
//                             'ne_lng'          : neLng,
//                             'sw_lat'          : swLat,
//                             'sw_lng'          : swLng,
//                           };
//
//                           // Persist selection (optional if your flow needs it here)
//                           if (lat != null && lng != null && range != null) {
//                             Database.setSelectedLocationFromMap(
//                               latitude   : lat,
//                               longitude  : lng,
//                               fullAddress: locationData['address'] ?? mc.finalAddress ?? '',
//                               city       : locationData['city'] ?? mc.addressCity,
//                               state      : locationData['state'] ?? mc.addressState,
//                               country    : locationData['country'] ?? mc.addressCountry,
//                               rangeKm    : range,
//                               neLat      : neLat,
//                               neLng      : neLng,
//                               swLat      : swLat,
//                               swLng      : swLng,
//                             );
//                             Database.hasSelectedLocation.value = true;
//                           }
//
//                           // 🔁 Do your navigation or callback here
//                           // Example: GlobalController.updateLocation(args);
//                           // Then pop stack or navigate
//                           // Get.back(result: args); // or custom flow
//
//
//
//                           if (Get.find<NearByListingScreenController>().filterScreen == true) {
//                               Utils.showLog("filter controller.arguments..........${args}");
//
//                               GlobalController.updateLocation(args);
//
//
//                               // ✅ persist ONCE with lat/lng + bounds + range (remove the old setSelectedLocationText call)
//                               if (lat != null && lng != null && range != null) {
//                                 Database.setSelectedLocationFromMap(
//                                   latitude   : lat,
//                                   longitude  : lng,
//                                   fullAddress: locationData['address'] ?? Get.find<NearByListingScreenController>().finalAddress ?? '',
//                                   city       : locationData['city'] ?? Get.find<NearByListingScreenController>().addressCity,
//                                   state      : locationData['state'] ?? Get.find<NearByListingScreenController>().addressState,
//                                   country    : locationData['country'] ?? Get.find<NearByListingScreenController>().addressCountry,
//                                   rangeKm    : range,
//                                   neLat      : neLat,
//                                   neLng      : neLng,
//                                   swLat      : swLat,
//                                   swLng      : swLng,
//                                 );
//                               }
//                               int count = 0;
//                               Get.until((route) => ++count >= 3);
//
//                             } else if (Get.find<NearByListingScreenController>().homeLocation == true || Get.find<NearByListingScreenController>().popular||Get.find<NearByListingScreenController>().mostLike||Get.find<NearByListingScreenController>().search) {
//                               Utils.showLog("home controller.arguments..........${args}");
//
//                               GlobalController.updateLocation(args);
//
//                               // ✅ persist ONCE with lat/lng + bounds + range (remove the old setSelectedLocationText call)
//                               if (lat != null && lng != null && range != null) {
//                                 Database.setSelectedLocationFromMap(
//                                   latitude   : lat,
//                                   longitude  : lng,
//                                   fullAddress: locationData['address'] ?? Get.find<NearByListingScreenController>().finalAddress ?? '',
//                                   city       : locationData['city'] ?? Get.find<NearByListingScreenController>().addressCity,
//                                   state      : locationData['state'] ?? Get.find<NearByListingScreenController>().addressState,
//                                   country    : locationData['country'] ?? Get.find<NearByListingScreenController>().addressCountry,
//                                   rangeKm    : range,
//                                   neLat      : neLat,
//                                   neLng      : neLng,
//                                   swLat      : swLat,
//                                   swLng      : swLng,
//                                 );
//                               }
//
//                               int count = 0;
//                               Get.until((route) => ++count >= 3);
//
//                             }
//
//
//                             else {
//
//                               Utils.showLog("else controller.arguments..........${Get.find<NearByListingScreenController>().arguments}");
//                               Get.find<NearByListingScreenController>().arguments.addAll({
//                                 "locationData": locationData,
//                                 'editApi': Get.find<NearByListingScreenController>().isEdit,
//                               });
//                               Get.toNamed(AppRoutes.productPricingScreen, arguments: Get.find<NearByListingScreenController>().arguments);
//                             }
//
//
//                         },
//                         color: AppColors.appRedColor,
//                         text: EnumLocale.txtApply.name.tr,
//                       );
//                     },
//                   ),
//                 ),
//               ],
//             ).paddingOnly(left: 10, right: 10),
//           ),
//         ],
//       ),
//     );
//   }
// }
