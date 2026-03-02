import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/custom_audio_time/custom_format_audio_time.dart';
import 'package:listify/socket/socket_emit.dart';
import 'package:listify/ui/chat_detail_screen/controller/chat_detail_controller.dart';
import 'package:listify/ui/chat_detail_screen/model/chat_history_model.dart';
import 'package:listify/ui/chat_detail_screen/shimmer/personal_chat_screen_shimmer.dart';
import 'package:listify/ui/chat_detail_screen/widget/chat_detail_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/socket_params.dart';
import 'package:listify/utils/utils.dart';

class ChatDetailScreenView extends StatelessWidget {
  const ChatDetailScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      // bottomNavigationBar: ChatDetailBottomBarView(),
      // appBar: AppBar(
      //   automaticallyImplyLeading: false,
      //   flexibleSpace: CustomChatAppBar(),
      // ),
      body: Stack(
        alignment: Alignment.center,
        children: [
          GetBuilder<ChatDetailController>(
              id: Constant.idGetOldChat,
              builder: (controller) {
                return Column(
                  children: [
                    CustomChatAppBar(),
                    GetBuilder<ChatDetailController>(
                      id: Constant.idPagination,
                      builder: (controller) => Visibility(
                        visible: controller.isPaginationLoading,
                        child: LinearProgressIndicator(color: AppColors.grey300),
                      ),
                    ),
                    Container(
                      child: Expanded(
                        child: Container(
                          decoration: BoxDecoration(image: DecorationImage(image: AssetImage(AppAsset.chatDetailBg), fit: BoxFit.cover)),
                          child: SizedBox(
                            height: Get.height - 100,
                            child: controller.isLoading
                                ? PersonalChatScreenShimmer()
                                : SingleChildScrollView(
                                    controller: controller.scrollController,
                                    child: ListView.builder(
                                        reverse: true,
                                        shrinkWrap: true,
                                        physics: NeverScrollableScrollPhysics(),
                                        // controller: controller.scrollController,
                                        padding: EdgeInsets.symmetric(vertical: 10, horizontal: 8),
                                        itemCount: controller.chatOldHistory.length,
                                        itemBuilder: (context, index) {
                                          final isLastMessage = index == 0;

                                          final msg = controller.chatOldHistory[index];

                                          // 🔹 Skip product messages
                                          if (msg.isInnerMessageType1) {
                                            return const SizedBox.shrink();
                                          }

                                          Widget messageWidget;

                                          if (msg.isInnerMessageType2) {
                                            messageWidget = msg.senderId == Database.getUserProfileResponseModel?.user?.id
                                                ? SenderProductView(msg: msg, controller: controller)
                                                : ReceiverProductView(msg: msg, controller: controller);
                                          } else {
                                            messageWidget = msg.messageType == 1
                                                ? msg.senderId == Database.getUserProfileResponseModel?.user?.id
                                                    ? SenderChatView(
                                                        msg: msg,
                                                        controller: controller,
                                                      )
                                                    : ReceiverChatView(
                                                        msg: msg,
                                                        controller: controller,
                                                      )
                                                : msg.messageType == 2
                                                    ? ChatImageWidget(msg: msg, controller: controller)
                                                    : msg.messageType == 3
                                                        ? msg.senderId == Database.loginUserId
                                                            ? SenderAudioMessageWidget(
                                                                audioUrl: msg.audio ?? "",
                                                                time: msg.date ?? "",
                                                                id: msg.id ?? "",
                                                                chat: msg,
                                                                isLastMessage: isLastMessage,
                                                              )
                                                            : ReceiverAudioMessageWidget(
                                                                audioUrl: msg.audio ?? "",
                                                                time: msg.date ?? "",
                                                                id: msg.id ?? "",
                                                                chat: msg,
                                                              )
                                                        : SizedBox();
                                          }

                                          return Align(
                                            alignment: msg.senderId == Database.getUserProfileResponseModel?.user?.id
                                                ? Alignment.centerRight
                                                : Alignment.centerLeft,
                                            child: messageWidget,
                                          );
                                        }),
                                  ),
                          ),
                        ),
                      ),
                    ),
                    ChatDetailBottomBarView()
                  ],
                );
              }),
          Positioned(
            bottom: 80,
            child: GetBuilder<ChatDetailController>(
              id: Constant.idChangeAudioRecordingEvent,
              builder: (controller) => Visibility(
                visible: controller.isRecordingAudio,
                child: Container(
                  height: 40,
                  width: 110,
                  padding: EdgeInsets.symmetric(horizontal: 10),
                  decoration: BoxDecoration(
                    color: AppColors.grey300.withValues(alpha: 0.7),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    children: [
                      Image.asset(
                        AppAsset.purpleMicIcon,
                        color: AppColors.grey300,
                        width: 20,
                      ),
                      5.width,
                      Text(
                        CustomFormatAudioTime.convert(controller.countTime),
                        style: AppFontStyle.fontStyleW500(fontColor: AppColors.black, fontSize: 13),
                      )
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
// floatingActionButtonLocation: FloatingActionButtonLocation.centerTop,
//       floatingActionButton: Column(
//         children: [
//           GetBuilder<ChatDetailController>(
//             builder: (controller) {
//               return FloatingActionButton(
//
//                 backgroundColor: AppColors.appRedColor,
//                 child: const Text("first"),
//                 onPressed: () {
//                   if (controller.chatOldHistory.isNotEmpty) {
//                     Utils.showLog("message.................${controller.chatOldHistory.length}");
//                     Utils.showLog("message.................${controller.chatOldHistory.first.id}");
//                     Utils.showLog("message.................${controller.chatOldHistory.first.message}");
//                     SocketEmit.seenMessage({
//                       SocketParams.messageId:
//                       controller.chatOldHistory.first.id ?? '',
//                       SocketParams.senderId:
//                       Database.getUserProfileResponseModel?.user?.id ?? '',
//                     });
//
//                     Utils.showLog("👉 SeenMessage Emit Called");
//                   } else {
//                     Utils.showLog("⚠️ No messages available");
//                   }
//                 },
//               );
//             },
//           ),
//           GetBuilder<ChatDetailController>(
//             builder: (controller) {
//               return FloatingActionButton(
//
//                 backgroundColor: AppColors.white,
//                 child: const Text("last"),
//                 onPressed: () {
//                   if (controller.chatOldHistory.isNotEmpty) {
//                     Utils.showLog("message.................${controller.chatOldHistory.length}");
//                     Utils.showLog("message.................${controller.chatOldHistory.last.id}");
//                     Utils.showLog("message.................${controller.chatOldHistory.last.message}");
//                     SocketEmit.seenMessage({
//                       SocketParams.messageId:
//                       controller.chatOldHistory.last.id ?? '',
//                       SocketParams.senderId:
//                       Database.getUserProfileResponseModel?.user?.id ?? '',
//                     });
//
//                     Utils.showLog("👉 SeenMessage Emit Called");
//                   } else {
//                     Utils.showLog("⚠️ No messages available");
//                   }
//                 },
//               );
//             },
//           ),
//         ],
//       ),

    );
  }
}
