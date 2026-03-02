import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/message_screen/api/chat_list_api.dart';
import 'package:listify/ui/message_screen/model/chat_list_model.dart';
import 'package:listify/utils/constant.dart';



// class MessageScreenController extends GetxController {
//   int tabIndex = 0;
//   bool isLoading = false;
//   bool isPaginationLoading = false;
//   bool hasMoreData = true;
//
//   ChatListResponseModel? chatListResponseModel;
//   List<ChatList> chatList = [];
//
//   final PageController pageController = PageController(initialPage: 0);
//   final ScrollController scrollController = ScrollController();
//
//   @override
//   void onInit() {
//     super.onInit();
//     init();
//   }
//
//   void init() {
//     scrollController.addListener(onTopPagination);
//     ChatListApi.startPagination = 0;
//     hasMoreData = true;
//     getChatList();
//   }
//
//   void onTapTab(int index) {
//     if (tabIndex == index) return;
//     tabIndex = index;
//     update([Constant.idTabChange]);
//
//     ChatListApi.startPagination = 0;
//     hasMoreData = true;
//     getChatList();
//
//     pageController.animateToPage(
//       index,
//       duration: const Duration(milliseconds: 260),
//       curve: Curves.easeOut,
//     );
//   }
//
//   void onSwipeTo(int index) {
//     if (tabIndex == index) return;
//     tabIndex = index;
//     update([Constant.idTabChange]);
//     fetchForCurrentTab();
//   }
//
//   Future<void> fetchForCurrentTab() async {
//     ChatListApi.startPagination = 0;
//     hasMoreData = true;
//     await getChatList();
//   }
//
//   Future<void> getChatList() async {
//     isLoading = true;
//     update([Constant.idChatList]);
//
//     final chatType = tabIndex == 0 ? 1 : 2;
//     chatListResponseModel = await ChatListApi.callApi(chatType: chatType);
//
//     // ✅ Demo pagination duplication logic
//     List<ChatList> apiData = chatListResponseModel?.chatList ?? [];
//
//     if (apiData.isNotEmpty) {
//       // 👇 Duplicate the data 4–5 times for pagination testing
//       List<ChatList> demoList = [];
//       for (int i = 0; i < 5; i++) {
//         demoList.addAll(apiData.map((e) {
//           // clone item with slight variation for uniqueness
//           return ChatList(
//             name: "${e.name ?? 'User'} ${i + 1}",
//             message: e.message,
//             productImage: e.productImage,
//             profileImage: e.profileImage,
//             adId: e.adId,
//             receiverId: e.receiverId,
//             productPrice: e.productPrice,
//             productTitle: e.productTitle,
//             unreadCount: e.unreadCount,
//             isOnline: e.isOnline,
//             lastChatMessageTime: e.lastChatMessageTime,
//           );
//         }).toList());
//       }
//       apiData = demoList;
//     }
//
//     chatList
//       ..clear()
//       ..addAll(apiData);
//
//     hasMoreData = apiData.isNotEmpty;
//     isLoading = false;
//     update([Constant.idChatList]);
//   }
//
//   Future<void> refreshCurrentTab() async {
//     ChatListApi.startPagination = 0;
//     hasMoreData = true;
//     await getChatList();
//   }
//   Future<void> onTopPagination() async {
//     if (!hasMoreData ||
//         isPaginationLoading ||
//         !scrollController.hasClients) return;
//
//     if (scrollController.position.pixels >=
//         scrollController.position.maxScrollExtent - 100) {
//       isPaginationLoading = true;
//       update([Constant.idPagination, Constant.idChatList]); // 👈 update both
//
//       final chatType = tabIndex == 0 ? 1 : 2;
//       final response = await ChatListApi.callApi(chatType: chatType);
//       List<ChatList> newData = response?.chatList ?? [];
//
//       // Demo pagination duplication for testing
//       if (newData.isNotEmpty) {
//         List<ChatList> demoList = [];
//         for (int i = 0; i < 3; i++) {
//           demoList.addAll(newData.map((e) {
//             return ChatList(
//               name:
//               "${e.name ?? 'User'} (Page ${ChatListApi.startPagination + i + 1})",
//               message: e.message,
//               productImage: e.productImage,
//               profileImage: e.profileImage,
//               adId: e.adId,
//               receiverId: e.receiverId,
//               productPrice: e.productPrice,
//               productTitle: e.productTitle,
//               unreadCount: e.unreadCount,
//               isOnline: e.isOnline,
//               lastChatMessageTime: e.lastChatMessageTime,
//             );
//           }).toList());
//         }
//         newData = demoList;
//       }
//
//       if (newData.isNotEmpty) {
//         ChatListApi.startPagination += newData.length;
//         chatList.addAll(newData);
//         log("✅ Added ${newData.length} new chats | Total: ${chatList.length}");
//       } else {
//         hasMoreData = false;
//         log("⚠️ No more data from API — stopping pagination");
//       }
//
//       isPaginationLoading = false;
//       update([Constant.idChatList, Constant.idPagination]);
//     }
//   }
//
//
//   @override
//   void onClose() {
//     scrollController.removeListener(onTopPagination);
//     pageController.dispose();
//     scrollController.dispose();
//     super.onClose();
//   }
// }




class MessageScreenController extends GetxController {
  int tabIndex = 0;
  bool isLoading = false;
  bool isPaginationLoading = false;
  bool hasMoreData = true;

  ChatListResponseModel? chatListResponseModel;
  List<ChatList> chatList = [];

  final PageController pageController = PageController(initialPage: 0);
  final ScrollController scrollController = ScrollController();

  @override
  void onInit() {
    super.onInit();
    init();
  }

  void init() {
    scrollController.addListener(onTopPagination);
    ChatListApi.startPagination = 0;
    hasMoreData = true;
    getChatList();
  }

  void onTapTab(int index) {
    if (tabIndex == index) return;
    tabIndex = index;
    update([Constant.idTabChange]);

    ChatListApi.startPagination = 0;
    hasMoreData = true;
    getChatList();

    pageController.animateToPage(
      index,
      duration: const Duration(milliseconds: 260),
      curve: Curves.easeOut,
    );
  }

  void onSwipeTo(int index) {
    if (tabIndex == index) return;
    tabIndex = index;
    update([Constant.idTabChange]);
    fetchForCurrentTab();
  }

  Future<void> fetchForCurrentTab() async {
    ChatListApi.startPagination = 0;
    hasMoreData = true;
    await getChatList();
  }

  Future<void> getChatList() async {
    isLoading = true;
    update([Constant.idChatList]);

    final chatType = tabIndex == 0 ? 1 : 2;
    chatListResponseModel = await ChatListApi.callApi(chatType: chatType);

    List<ChatList> apiData = chatListResponseModel?.chatList ?? [];

    chatList
      ..clear()
      ..addAll(apiData);

    hasMoreData = apiData.isNotEmpty;
    isLoading = false;
    update([Constant.idChatList]);
  }

  Future<void> refreshCurrentTab() async {
    ChatListApi.startPagination = 0;
    hasMoreData = true;
    await getChatList();
  }

  Future<void> onTopPagination() async {
    if (!hasMoreData ||
        isPaginationLoading ||
        !scrollController.hasClients) return;

    if (scrollController.position.pixels >=
        scrollController.position.maxScrollExtent - 100) {
      isPaginationLoading = true;
      update([Constant.idPagination, Constant.idChatList]);

      final chatType = tabIndex == 0 ? 1 : 2;
      final response = await ChatListApi.callApi(chatType: chatType);
      List<ChatList> newData = response?.chatList ?? [];

      if (newData.isNotEmpty) {
        ChatListApi.startPagination += newData.length;
        chatList.addAll(newData);
        log("✅ Added ${newData.length} new chats | Total: ${chatList.length}");
      } else {
        hasMoreData = false;
        log("⚠️ No more data from API — stopping pagination");
      }

      isPaginationLoading = false;
      update([Constant.idChatList, Constant.idPagination]);
    }
  }

  @override
  void onClose() {
    scrollController.removeListener(onTopPagination);
    pageController.dispose();
    scrollController.dispose();
    super.onClose();
  }
}
