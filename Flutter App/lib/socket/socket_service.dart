import 'package:listify/utils/api.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';
import 'package:socket_io_client/socket_io_client.dart' as io;

io.Socket? socket;

class SocketService {
  io.Socket? getSocket() => socket;

  static Future<void> socketDisConnect() async {
    socket?.disconnect();
    socket?.onDisconnect(
      (data) => Utils.showLog("Socket Listen => Socket Disconnected Called : ${socket?.id}"),
    );
  }

  static Future<void> socketConnect() async {
    Utils.showLog("Socket connect user id :::::: ${Database.getUserProfileResponseModel?.user?.id}");

    try {
      socket = io.io(
        Api.baseUrl,
        io.OptionBuilder()
            .setTransports(['websocket']).setQuery({"globalRoom": "globalRoom:${Database.getUserProfileResponseModel?.user?.id}"}).build(),
      );

      socket?.connect();

      socket?.onConnect((_) {
        Utils.showLog("Socket Listen => Socket Connected : ${socket?.id}");
      });

      socket?.on("error", (error) {
        Utils.showLog("Socket Listen => Socket Error : $error");
      });

      socket?.on("connect_error", (error) {
        Utils.showLog("Socket Listen => Socket Connection Error : $error");
      });

      socket?.on("connect_timeout", (timeout) {
        Utils.showLog("Socket Listen => Socket Connection Timeout : $timeout");
      });

      socket?.on("disconnect", (reason) {
        Utils.showLog("Socket Listen => Socket Disconnected : $reason");
      });

      Utils.showLog("Socket Listen => Socket Connected : ${socket?.connected}");
    } catch (e) {
      Utils.showLog("Socket Listen => Socket Connection Error: $e");
    }
  }
}
