import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/socket/socket_emit.dart';
import 'package:listify/socket/socket_service.dart';
import 'package:listify/ui/chat_detail_screen/controller/chat_detail_controller.dart';
import 'package:listify/ui/chat_detail_screen/model/chat_history_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/socket_events.dart';
import 'package:listify/utils/socket_params.dart';
import 'package:listify/utils/utils.dart';

class SocketListen {
  static void registerListeners() {
    if (socket == null) return;

    socket?.once("connect", (_) {
      Utils.showLog("Socket connected, registering listeners...");

      socket?.on(SocketEvents.messageSent, handleSendMessage);
      socket?.on(SocketEvents.offerConfirmed, handleOfferConfirmed);
      socket?.on(SocketEvents.offerReceived, handleOfferReceived);
      socket?.on(SocketEvents.messageSeen, handleSeenMessage);
    });
  }

  static void handleSendMessageOld(dynamic message) {
    Utils.showLog("Received messageDispatched: $message");

    try {
      final Map<String, dynamic> data = message['data'];
      final String chatTopicId = data['chatTopicId'] ?? '';
      //     final parsed = jsonDecode(data);
      //     final messageId = parsed['messageId'];

      if (Get.isRegistered<ChatDetailController>()) {
        final userController = Get.find<ChatDetailController>();
        if (chatTopicId == userController.chatTopicId) {
          final newMsg = OldChat.fromJson(data);

          if (data["senderId"] == Database.loginUserId &&
              data['messageType'] == 3) {
            userController.chatOldHistory.removeAt(0);
          }

          userController.isLoadingAudio = false;
          userController.update([Constant.idGetOldChat]);

          // userController.oldChat.insert(0, newMsg);

          // Try to find and replace the optimistic message
          final index = userController.chatOldHistory.indexWhere((msg) =>
                  msg.senderId == Database.loginUserId &&
                  msg.message == newMsg.message &&
                  msg.id?.length == 13 // temporary ID is a timestamp
              );

          if (index != -1) {
            userController.chatOldHistory[index] = newMsg;
          } else {
            userController.chatOldHistory.insert(0, newMsg);
          }

          userController.onScrollDown();
          userController.update([Constant.idGetOldChat]);

          if (Get.currentRoute == AppRoutes.chatDetailScreenView) {

            Utils.showLog("data:::::::::::::${data}");
            Utils.showLog("data['messageId']:::::::::::::${data['messageId']}");
            Utils.showLog("data Id:::::::::::::${data['senderId']}");
            Utils.showLog("userController.chatOldHistory.first.id:::::::::::::${userController.chatOldHistory.first.id}");

            if (data["senderId"] !=
                    Database.getUserProfileResponseModel?.user?.id &&
                data['receiverId'] ==
                    Database.getUserProfileResponseModel?.user?.id) {
              SocketEmit.seenMessage({
                SocketParams.messageId:
                    data['messageId'] ?? '',
                SocketParams.senderId: data["senderId"],
              });
            } else {
              Utils.showLog("Message not for current user");
            }
          }

          return;
        }
      }
    } catch (e) {
      Utils.showLog(" Error parsing socket message: $e");
    }
  }

  static void handleSendMessage(dynamic message) {
    Utils.showLog("Received messageDispatched: $message");

    try {
      // 👇 outer messageId
      final String messageId = message['messageId'] ?? '';

      // 👇 inner data
      final Map<String, dynamic> data = message['data'];
      final String chatTopicId = data['chatTopicId'] ?? '';

      if (Get.isRegistered<ChatDetailController>()) {
        final userController = Get.find<ChatDetailController>();

        if (chatTopicId == userController.chatTopicId) {
          final newMsg = OldChat.fromJson(data);

          if (data["senderId"] == Database.loginUserId &&
              data['messageType'] == 3) {
            userController.chatOldHistory.removeAt(0);
          }

          userController.isLoadingAudio = false;
          userController.update([Constant.idGetOldChat]);

          // Replace optimistic message if found
          final index = userController.chatOldHistory.indexWhere((msg) =>
          msg.senderId == Database.loginUserId &&
              msg.message == newMsg.message &&
              msg.id?.length == 13 // temporary ID check
          );

          if (index != -1) {
            userController.chatOldHistory[index] = newMsg;
          } else {
            userController.chatOldHistory.insert(0, newMsg);
          }

          userController.onScrollDown();
          userController.update([Constant.idGetOldChat]);

          // 👇 Seen message emit
          if (Get.currentRoute == AppRoutes.chatDetailScreenView) {
            Utils.showLog("data:::::::::::::$data");
            Utils.showLog("outer messageId:::::::::::::$messageId");
            Utils.showLog("senderId:::::::::::::${data['senderId']}");
            Utils.showLog("receiverId:::::::::::::${data['receiverId']}");
            Utils.showLog("currentUser:::::::::::::${Database.getUserProfileResponseModel?.user?.id}");

            if (data["senderId"] != Database.getUserProfileResponseModel?.user?.id &&
                data['receiverId'] == Database.getUserProfileResponseModel?.user?.id) {
              SocketEmit.seenMessage({
                SocketParams.messageId: messageId,
                SocketParams.senderId: data["senderId"],
              });
              Utils.showLog("✅ SeenMessage event emitted for $messageId");
            } else {
              Utils.showLog("ℹ️ Message not for current user, skipping seen event");
            }
          }

          return;
        }
      }
    } catch (e) {
      Utils.showLog("❌ Error parsing socket message: $e");
    }
  }


  static void handleOfferConfirmed(dynamic message) {
    Utils.showLog("Received offerConfirmed Event: $message");
  }

  static void handleOfferReceived(dynamic message) {
    Utils.showLog("Received offerReceived Event: $message");
  }

  static void handleSeenMessage(dynamic message) {
    Utils.showLog("Received handleSeenMessage Event: $message");
  }
}
