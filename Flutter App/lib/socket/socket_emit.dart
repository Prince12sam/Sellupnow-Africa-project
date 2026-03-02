import 'package:listify/socket/socket_service.dart';
import 'package:listify/utils/socket_events.dart';
import 'package:listify/utils/utils.dart';

class SocketEmit {
  static void sendMessage(Map<String, dynamic> message) {
    if (socket != null && socket?.connected == true) {
      socket?.emit(SocketEvents.messageSent, message);
      Utils.showLog("Emitting message ::::: $message");
    } else {
      Utils.showLog("Socket Not Connected!!");
    }
  }

  static void offerPlacedMessage(Map<String, dynamic> message) {
    if (socket != null && socket?.connected == true) {
      socket?.emit(SocketEvents.offerPlaced, message);
      Utils.showLog("Emitting offerPlacedMessage ::::: $message");
    } else {
      Utils.showLog("Socket Not Connected!!");
    }
  }

  static void seenMessage(Map<String, dynamic> message) {
    if (socket != null && socket?.connected == true) {
      socket?.emit(SocketEvents.messageSeen, message);
      Utils.showLog("Emitting messageSeen ::::: $message");
    } else {
      Utils.showLog("Socket Not Connected!!");
    }
  }
}
