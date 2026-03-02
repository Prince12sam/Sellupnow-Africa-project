import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/my_videos_screen/controller/my_videos_screen_controller.dart';
import 'package:listify/ui/my_videos_screen/widget/my_videos_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';

class MyVideosScreen extends StatelessWidget {
  const MyVideosScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MyVideosScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          return Scaffold(
            backgroundColor: AppColors.white,
            bottomNavigationBar: MyVideosBottomButton(),
            appBar: AppBar(
              automaticallyImplyLeading: false,
              flexibleSpace: MyVideosScreenAppBar(
                title: '${EnumLocale.txtMyVideos.name.tr}(${controller.sellerVideoList.length})',
              ),
            ),
            body: RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () async => controller.onRefresh(),
              child: SingleChildScrollView(
                physics: AlwaysScrollableScrollPhysics(),
                child: Column(
                  children: [
                    MyVideosScreenWidget(),
                  ],
                ),
              ),
            ),
          );
        });
  }
}
