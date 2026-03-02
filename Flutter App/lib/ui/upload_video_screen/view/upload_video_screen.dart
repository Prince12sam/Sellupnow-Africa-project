import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/upload_video_screen/widget/upload_video_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class UploadVideoScreen extends StatelessWidget {
  const UploadVideoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      bottomNavigationBar: UploadVideoBottomButton(),
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: UploadVideoScreenAppBar(
          title: EnumLocale.txtUploadVideo.name.tr,
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
            children: [
              UploadVideoScreenWidget(),
            ],
          ),
        ),
      ),
    );
  }
}
