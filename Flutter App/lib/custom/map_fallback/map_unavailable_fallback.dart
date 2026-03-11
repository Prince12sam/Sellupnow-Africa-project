import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/google_maps_runtime.dart';

class MapUnavailableFallback extends StatelessWidget {
  final BorderRadius? borderRadius;
  final EdgeInsetsGeometry? padding;
  final bool expand;

  const MapUnavailableFallback({
    super.key,
    this.borderRadius,
    this.padding,
    this.expand = true,
  });

  @override
  Widget build(BuildContext context) {
    final child = Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: AppColors.categoriesBgColor,
        borderRadius: borderRadius ?? BorderRadius.circular(16),
      ),
      padding: padding ?? const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.map_outlined,
            size: 38,
            color: AppColors.black,
          ).paddingOnly(bottom: 10),
          Text(
            'Map preview unavailable on this device',
            textAlign: TextAlign.center,
            style: AppFontStyle.fontStyleW700(
              fontSize: 15,
              fontColor: AppColors.black,
            ),
          ).paddingOnly(bottom: 6),
          Text(
            'Google Maps is using the ${GoogleMapsRuntime.rendererLabel} renderer here, which is unstable on this Android environment. Location details remain available.',
            textAlign: TextAlign.center,
            style: AppFontStyle.fontStyleW500(
              fontSize: 12,
              fontColor: AppColors.black,
            ),
          ),
        ],
      ),
    );

    if (!expand) {
      return child;
    }

    return SizedBox.expand(child: child);
  }
}