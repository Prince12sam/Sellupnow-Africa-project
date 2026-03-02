import 'package:flutter/material.dart';
import 'package:get/get_utils/src/extensions/export.dart';
import 'package:listify/ui/fill_profile_screen/widget/fill_profile_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class FillProfileView extends StatelessWidget {
  const FillProfileView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      bottomNavigationBar: FillEditProfileBottomBar(),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: FillProfileAppBar(
          title: EnumLocale.txtMyProfile.name.tr,
        ),
      ),
      body: GestureDetector(
        onTap: () {
          FocusScopeNode currentFocus = FocusScope.of(context);
          if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
            currentFocus.focusedChild?.unfocus();
          }
        },
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(child: FillEditImageView()).paddingOnly(top: 30),
              FillEditTextFieldView(),
            ],
          ),
        ),
      ),
    );
  }
}
