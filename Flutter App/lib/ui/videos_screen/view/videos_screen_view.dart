import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/bottom_bar/controller/bottom_bar_controller.dart';
import 'package:listify/ui/videos_screen/controller/videos_screen_controller.dart';
import 'package:listify/ui/videos_screen/shimmer/video_screen_shimmer.dart';
import 'package:listify/ui/videos_screen/widget/videos_screen_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:preload_page_view/preload_page_view.dart';

// class VideosScreenView extends StatelessWidget {
//   const VideosScreenView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<VideosScreenController>(
//       init: VideosScreenController(),
//       builder: (controller) {
//         return PopScope(
//           canPop: false,
//           onPopInvokedWithResult: (didPop, result) {
//             Get.find<BottomBarController>().onClick(0);
//             if (didPop) return;
//           },
//           child: Scaffold(
//             backgroundColor: AppColors.white,
//             body: SafeArea(
//               child: controller.isLoading
//                   ? const VideoScreenShimmer()
//                   : controller.myVideosList.isEmpty
//                       ? NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: "No Data Found")
//                       : PreloadPageView.builder(
//                           controller: controller.preloadPageController,
//                           scrollDirection: Axis.vertical,
//                           onPageChanged: controller.onPageChanged,
//                           itemCount: controller.myVideosList.length,
//                           itemBuilder: (context, index) {
//                             return VideoScreenWidget(
//                               index: index,
//                               isCurrentReel: index == controller.currentIndex,
//                             );
//                           },
//                         ),
//             ),
//           ),
//         );
//       },
//     );
//   }
// }
class VideosScreenView extends StatelessWidget {
  const VideosScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VideosScreenController>(
      builder: (controller) {
        return PopScope(
          canPop: false,
          onPopInvokedWithResult: (didPop, result) {
            Get.find<BottomBarController>().onClick(0);
            if (didPop) return;
          },
          child: Scaffold(
            backgroundColor: AppColors.white,
            body: SafeArea(
              child: controller.isLoading
                  ? const VideoScreenShimmer()
                  : controller.myVideosList.isEmpty
                  ? NoDataFound(image: AppAsset.noProductFound, imageHeight: 180, text: "No Data Found")
                  : PreloadPageView.builder(
                controller: controller.preloadPageController,
                preloadPagesCount: 1,
                scrollDirection: Axis.vertical,
                onPageChanged: (value) async {
                  controller.onPagination(value);
                  controller.onPageChanged(value);
                },
                // onPageChanged: controller.onPageChanged,
                itemCount: controller.myVideosList.length,
                itemBuilder: (context, index) {
                  return VideoScreenWidget(
                    index: index,
                    isCurrentReel: index == controller.currentIndex,
                  );
                },
              ),
            ),

            // bottomNavigationBar: GetBuilder<VideosScreenController>(
            //   id: Constant.idPagination,
            //   builder: (controller) => Visibility(
            //     visible: controller.isPaginationLoading,
            //     child: LinearProgressIndicator(color: AppColors.appRedColor),
            //   ),
            // ),
          ),
        );
      },
    );
  }
}
