import 'package:flutter/material.dart';
import 'package:get/get_utils/src/extensions/export.dart';
import 'package:listify/ui/start_user_verification_screen/widget/start_user_verification_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class StartUserVerificationView extends StatelessWidget {
  const StartUserVerificationView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: StartUserVerificationAppBar(
          title: EnumLocale.txtVerification.name.tr,
        ),
      ),
      backgroundColor: AppColors.white,
      bottomNavigationBar: StartUserVerificationBottomBar(),
      body: Column(
        children: [
          StartUserVerificationCenterView(),
        ],
      ),
    );
  }
}
