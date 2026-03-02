import 'package:flutter/material.dart';
import 'package:get/get_utils/src/extensions/export.dart';
import 'package:listify/ui/edit_profile_screen/widget/edit_profile_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class EditProfileView extends StatelessWidget {
  const EditProfileView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      bottomNavigationBar: EditProfileBottomBar(),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: EditProfileAppBar(
          title: EnumLocale.txtMyProfile.name.tr,
        ),
      ),
      body: Stack(
        clipBehavior: Clip.none,
        children: [
          GestureDetector(
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
                  Center(child: EditImageView()).paddingOnly(top: 30),
                  EditTextFieldView(),
                ],
              ),
            ),
          ),
          // LoadingWidget(),
        ],
      ),
    );
  }
}
