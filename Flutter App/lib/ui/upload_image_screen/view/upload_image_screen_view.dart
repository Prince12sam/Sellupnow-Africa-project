import 'package:flutter/material.dart';
import 'package:get/get_utils/src/extensions/export.dart';
import 'package:listify/ui/upload_image_screen/widget/upload_image_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class UploadImageScreenView extends StatelessWidget {
  const UploadImageScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      bottomNavigationBar: UploadImageBottomView(),
      backgroundColor: AppColors.adScreenBgColor,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: UploadImageScreenAppBar(
          title: EnumLocale.txtUploadImage.name.tr,
        ),
      ),
      body: Column(
        children: [
          UploadImageScreenTopView(),
          MainCoverImageView(),
        ],
      ),
    );
  }
}
