import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/bottom_bar/controller/bottom_bar_controller.dart';
import 'package:listify/ui/message_screen/widget/message_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

// class MessageScreenView extends StatelessWidget {
//   const MessageScreenView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return PopScope(
//       canPop: false,
//       onPopInvokedWithResult: (didPop, result) {
//         Get.find<BottomBarController>().onClick(0);
//         if (didPop) return;
//       },
//       child: Scaffold(
//         appBar: AppBar(
//           automaticallyImplyLeading: false,
//           flexibleSpace: MessageScreenAppBar(
//             title: EnumLocale.txtMessages.name.tr,
//           ),
//         ),
//         backgroundColor: AppColors.white,
//         body: Column(
//           children: [
//             MessageScreenTabBar(),
//             TabBarScreen(),
//           ],
//         ),
//       ),
//     );
//   }
// }
class MessageScreenView extends StatelessWidget {
  const MessageScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        Get.find<BottomBarController>().onClick(0);
        if (didPop) return;
      },
      child: Scaffold(
        appBar: AppBar(
          automaticallyImplyLeading: false,
          flexibleSpace: MessageScreenAppBar(
            title: EnumLocale.txtMessages.name.tr,
          ),
        ),
        backgroundColor: AppColors.white,
        body: Column(
          children: const [
            MessageScreenTabBar(),
            Expanded(child: TabBarScreen()), // CHANGED
          ],
        ),
      ),
    );
  }
}
