import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';

class LoginBg extends StatelessWidget {
  final Widget child;
  const LoginBg({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      constraints: BoxConstraints(minHeight: Get.height),
      width: Get.width,
      decoration: BoxDecoration(
        color: AppColors.white,
        image: DecorationImage(
          image: AssetImage(AppAsset.loginBgImage),
          fit: BoxFit.cover,
        ),
      ),
      child: child,
    );
  }
}
