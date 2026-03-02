import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class NavBarItem extends StatelessWidget {
  final String image;
  final String label;
  final bool isActive;
  const NavBarItem({super.key, required this.image, required this.label, required this.isActive});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.transparent,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Image.asset(
            image,
            height: 25,
            width: 25,
            fit: BoxFit.contain,
            color: isActive ? AppColors.appRedColor : AppColors.unSelected,
          ).paddingOnly(bottom: Get.height * 0.005),
          Text(label, style: AppFontStyle.fontStyleW700(fontSize: 11, fontColor: isActive ? AppColors.appRedColor : AppColors.unSelected)),
        ],
      ).paddingSymmetric(horizontal: 3),
    );
  }
}

class HexagonButton extends StatelessWidget {
  const HexagonButton({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 70,
      width: 70,
      decoration: BoxDecoration(
        shape: BoxShape.circle, // or BoxShape.rectangle if not circular
        boxShadow: [
          BoxShadow(
            color: AppColors.black.withValues(alpha: 0.1), // Shadow color
            spreadRadius: 0,
            blurRadius: 4,
            offset: Offset(0, -1),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(35), // Half of width/height
        child: Image.asset(
          AppAsset.addBottomBarIcon,
          height: 70,
          width: 70,
          fit: BoxFit.cover,
        ),
      ),
    );
  }
}
