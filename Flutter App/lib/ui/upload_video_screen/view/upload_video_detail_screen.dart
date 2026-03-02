import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/upload_video_screen/widget/upload_video_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class UploadVideoDetailScreen extends StatelessWidget {
  const UploadVideoDetailScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      bottomNavigationBar: UploadVideoDetailBottomButton(),
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: UploadVideoScreenAppBar(
          title: EnumLocale.txtUploadVideo.name.tr,
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            UploadVideoDetailScreenWidget(),
          ],
        ),
      ),
    );
  }
}
