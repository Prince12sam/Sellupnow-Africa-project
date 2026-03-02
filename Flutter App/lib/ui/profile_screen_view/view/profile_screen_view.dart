import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/profile_screen_view/widget/profile_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class ProfileScreenView extends StatelessWidget {
  const ProfileScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        backgroundColor: AppColors.white,
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: ProfileAppBar(
            title: EnumLocale.txtMyProfile.name.tr,
          ),
        ),
        body: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              ProfileTopView(),
              ProfileGeneralView(),
              SubscriptionView(),
              SettingView(),
            ],
          ),
        ));
  }
}
