import 'dart:developer';

import 'package:get/get.dart';
import 'package:listify/socket/socket_listen.dart';
import 'package:listify/socket/socket_service.dart';
import 'package:listify/ui/home_screen/view/home_screen.dart';
import 'package:listify/ui/message_screen/view/message_screen_view.dart';
import 'package:listify/ui/my_ads_screen/view/my_ads_screen_view.dart';
import 'package:listify/ui/videos_screen/controller/videos_screen_controller.dart';
import 'package:listify/ui/videos_screen/view/videos_screen_view.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

// class BottomBarController extends GetxController {
//   bool checkScreen = false;
//   int selectIndex = 0;
//
//   @override
//   void onInit() {
//     init();
//     super.onInit();
//   }
//
//   init() async {
//     log("Enter user bottomBar Controller");
//     await SocketService.socketDisConnect();
//     await SocketService.socketConnect().then((_) {
//       Utils.showLog("Socket connect User");
//       SocketListen.registerListeners();
//     });
//   }
//
//   final pages = [
//     HomeScreen(),
//     MyAdsScreenView(),
//     VideosScreenView(),
//     MessageScreenView(),
//   ];
//
//   bool isBottomBarVisible = true;
//   void setBottomBarVisible(bool visible) {
//     isBottomBarVisible = visible;
//     update([Constant.idBottomBar]);
//   }
//
//   onClick(value) async {
//     if (value != null) {
//       selectIndex = value;
//       update([Constant.idBottomBar]);
//     }
//   }
// }
class BottomBarController extends GetxController {
  bool checkScreen = false;
  int selectIndex = 0;

  @override
  void onInit() {
    init();
    super.onInit();
  }

  init() async {
    log("Enter user bottomBar Controller");
    await SocketService.socketDisConnect();
    await SocketService.socketConnect().then((_) {
      Utils.showLog("Socket connect User");
      SocketListen.registerListeners();
    });
  }

  final pages = [
    HomeScreen(),
    MyAdsScreenView(),
    VideosScreenView(),
    MessageScreenView(),
  ];

  bool isBottomBarVisible = true;
  void setBottomBarVisible(bool visible) {
    isBottomBarVisible = visible;
    update([Constant.idBottomBar]);
  }

  // ==== NEW: Tab switch par videos pause if leaving index 2 ====
  // onClick(value) async {
  //   if (value != null) {
  //     if (selectIndex == 2 && value != 2) {
  //       // leaving Videos tab → pause all
  //       if (Get.isRegistered<VideosScreenController>()) {
  //         Get.find<VideosScreenController>().pauseAll();
  //       }
  //     }
  //     selectIndex = value;
  //     update([Constant.idBottomBar]);
  //   }
  // }

  onClick(value) async {
    if (value != null) {
      // leaving Videos tab -> pause (already in your code)
      if (selectIndex == 2 && value != 2) {
        if (Get.isRegistered<VideosScreenController>()) {
          final v = Get.find<VideosScreenController>();
          v.pauseAll();
          v.setImmersive(false);

        }
      }

      selectIndex = value;
      update([Constant.idBottomBar]);

      // NEW: Videos tab par aavta j resume
      if (value == 2 && Get.isRegistered<VideosScreenController>()) {
        final v = Get.find<VideosScreenController>();
        v.resumeCurrent();
        v.setImmersive(false);
      }
    }
  }

}
