import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/custom_chat_time/custom_format_chat_time.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/message_screen/controller/message_screen_controller.dart';
import 'package:listify/ui/message_screen/shimmer/chat_list_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';

class MessageScreenAppBar extends StatelessWidget {
  final String? title;

  const MessageScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: false,
      ),
    );
  }
}

// class MessageScreenTabBar extends StatelessWidget {
//   const MessageScreenTabBar({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<MessageScreenController>(
//       id: Constant.idTabChange, // Listen for tab switch and content change
//       builder: (controller) {
//         return Column(
//           crossAxisAlignment: CrossAxisAlignment.start,
//           children: [
//             Container(
//               margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
//               height: Get.height * 0.06,
//               decoration: BoxDecoration(
//                 color: AppColors.buyingBgColor,
//                 border: Border.all(color: AppColors.appRedColor, width: 0.4),
//                 borderRadius: BorderRadius.circular(24),
//               ),
//               child: Row(
//                 children: [
//                   Expanded(
//                     child: GestureDetector(
//                       onTap: () => controller.changeTab(0),
//                       child: Container(
//                         decoration: BoxDecoration(
//                           color: controller.tabIndex == 0 ? AppColors.appRedColor : Colors.transparent,
//                           borderRadius: BorderRadius.circular(24),
//                         ),
//                         alignment: Alignment.center,
//                         child: Text(
//                           EnumLocale.txtSelling.name.tr,
//                           style: controller.tabIndex == 0
//                               ? AppFontStyle.fontStyleW600(
//                                   fontSize: 14,
//                                   fontColor: AppColors.white,
//                                 )
//                               : AppFontStyle.fontStyleW500(
//                                   fontSize: 14,
//                                   fontColor: AppColors.black,
//                                 ),
//                         ),
//                       ).paddingAll(controller.tabIndex == 0 ? 2 : 0),
//                     ),
//                   ),
//                   Expanded(
//                     child: GestureDetector(
//                       onTap: () => controller.changeTab(1),
//                       child: Container(
//                         decoration: BoxDecoration(
//                           color: controller.tabIndex == 1 ? AppColors.appRedColor : Colors.transparent,
//                           borderRadius: BorderRadius.circular(24),
//                         ),
//                         alignment: Alignment.center,
//                         child: Text(
//                           EnumLocale.txtBuying.name.tr,
//                           style: controller.tabIndex == 1
//                               ? AppFontStyle.fontStyleW600(
//                                   fontSize: 14,
//                                   fontColor: AppColors.white,
//                                 )
//                               : AppFontStyle.fontStyleW500(
//                                   fontSize: 14,
//                                   fontColor: AppColors.black,
//                                 ),
//                         ),
//                       ).paddingAll(controller.tabIndex == 1 ? 2 : 0),
//                     ),
//                   ),
//                 ],
//               ),
//             ),
//             10.height,
//           ],
//         );
//       },
//     );
//   }
// }
class MessageScreenTabBar extends StatelessWidget {
  const MessageScreenTabBar({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MessageScreenController>(
      id: Constant.idTabChange,
      builder: (controller) {
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              height: Get.height * 0.06,
              decoration: BoxDecoration(
                color: AppColors.buyingBgColor,
                border: Border.all(color: AppColors.appRedColor, width: 0.4),
                borderRadius: BorderRadius.circular(24),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: GestureDetector(
                      onTap: () => controller.onTapTab(0), // CHANGED
                      child: Container(
                        decoration: BoxDecoration(
                          color: controller.tabIndex == 0
                              ? AppColors.appRedColor
                              : Colors.transparent,
                          borderRadius: BorderRadius.circular(24),
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          EnumLocale.txtSelling.name.tr,
                          style: controller.tabIndex == 0
                              ? AppFontStyle.fontStyleW600(
                                  fontSize: 14, fontColor: AppColors.white)
                              : AppFontStyle.fontStyleW500(
                                  fontSize: 14, fontColor: AppColors.black),
                        ),
                      ).paddingAll(controller.tabIndex == 0 ? 2 : 0),
                    ),
                  ),
                  Expanded(
                    child: GestureDetector(
                      onTap: () => controller.onTapTab(1), // CHANGED
                      child: Container(
                        decoration: BoxDecoration(
                          color: controller.tabIndex == 1
                              ? AppColors.appRedColor
                              : Colors.transparent,
                          borderRadius: BorderRadius.circular(24),
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          EnumLocale.txtBuying.name.tr,
                          style: controller.tabIndex == 1
                              ? AppFontStyle.fontStyleW600(
                                  fontSize: 14, fontColor: AppColors.white)
                              : AppFontStyle.fontStyleW500(
                                  fontSize: 14, fontColor: AppColors.black),
                        ),
                      ).paddingAll(controller.tabIndex == 1 ? 2 : 0),
                    ),
                  ),
                ],
              ),
            ),
            10.height,
          ],
        );
      },
    );
  }
}

class ChatViewItem extends StatelessWidget {
  final String name;
  final String image;
  final String profileImage;
  final String? lastMsgTime;
  final String? lastMsg;
  final int index;
  final int unReadCount;
  final void Function()? onTap;

  const ChatViewItem(
      {super.key,
      required this.name,
      required this.image,
      required this.index,
      this.onTap,
      required this.unReadCount,
      this.lastMsgTime,
      this.lastMsg,
      required this.profileImage});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        color: Colors.transparent,
        child: Row(
          children: [
            Stack(
              children: [
                Container(
                  height: 56,
                  width: 56,
                  decoration: BoxDecoration(
                    border: Border.all(color: AppColors.borderColor, width: 1),
                    shape: BoxShape.circle,
                  ),
                  child: ClipOval(
                    child: CustomImageView(
                      image: image,
                      fit: BoxFit.cover,
                    ),
                  ),
                ).paddingAll(1).paddingOnly(right: 12),
                Positioned(
                  right: 8,
                  bottom: 6,
                  child: Container(
                    height: 30,
                    width: 30,
                    decoration: BoxDecoration(
                        color: AppColors.white,
                        shape: BoxShape.circle,
                        border: Border.all(color: AppColors.borderColor)),
                    child: ClipOval(
                        child: CustomImageView(
                            image: profileImage, fit: BoxFit.cover)),
                  ),
                )
              ],
            ),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      Text(
                        name,
                        style: AppFontStyle.fontStyleW700(
                            fontSize: 16, fontColor: AppColors.black),
                      ).paddingOnly(right: 8),
                    ],
                  ).paddingOnly(bottom: 4),
                  Text(
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    lastMsg ?? '',
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 14, fontColor: AppColors.popularProductText),
                  ).paddingOnly(right: 15)
                ],
              ),
            ),
            // Spacer(),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                unReadCount > 0
                    ? Container(
                        height: 24,
                        width: 24,
                        // padding: EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(30),
                          color: AppColors.appRedColor,
                        ),
                        child: Center(
                          child: Text(
                            unReadCount.toString(),
                            style: AppFontStyle.fontStyleW600(
                                fontSize: 15, fontColor: AppColors.white),
                          ),
                        ),
                      ).paddingOnly(bottom: 8)
                    : SizedBox(
                        height: 24,
                        width: 24,
                      ),
                Text(
                  CustomFormatChatTime.convert(lastMsgTime ?? ''),
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 12, fontColor: AppColors.unSelected),
                ),
              ],
            ),
          ],
        ),
      ),
    ).paddingOnly(bottom: 12, top: 12);
  }
}

/*class BuyingChat extends StatelessWidget {
  const BuyingChat({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MessageScreenController>(
        id: Constant.idChatList,
        builder: (controller) {
          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () => controller.refreshCurrentTab(),
            child: controller.isLoading
                ? ChatListShimmer()
                : controller.chatList.isEmpty
                    ? NoDataFound(
                        image: AppAsset.noChatFound, imageHeight: 180, text: '')
                    : Column(
                        children: [
                          ListView.builder(
                            shrinkWrap: true,
                            controller: controller.scrollController,
                            itemCount: controller.chatList.length,
                            physics: const AlwaysScrollableScrollPhysics(),
                            padding: EdgeInsets.zero,
                            itemBuilder: (context, index) {
                              final chatList = controller.chatList[index];
                              return Column(
                                children: [
                                  ChatViewItem(
                                    onTap: () {
                                      Get.toNamed(
                                          AppRoutes.chatDetailScreenView,
                                          arguments: {
                                            'name': chatList.name,
                                            'image': chatList.productImage,
                                            'profileImage':
                                                chatList.profileImage,
                                            'adId': chatList.adId,
                                            'receiverId': chatList.receiverId,
                                            'isOnline': chatList.isOnline,
                                            'productPrice':
                                                chatList.productPrice,
                                            'productName':
                                                chatList.productTitle,
                                            'primaryImage':
                                                chatList.productImage,
                                          })?.then(
                                        (value) {
                                          controller.init();
                                        },
                                      );
                                    },
                                    index: index,
                                    name: chatList.name ?? '',
                                    lastMsg: (chatList.message != null &&
                                            chatList.message!
                                                .contains("productName:"))
                                        ? ''
                                        : chatList.message ?? '',
                                    image: chatList.productImage ?? '',
                                    profileImage: chatList.profileImage ?? '',
                                    unReadCount: chatList.unreadCount ?? 0,
                                    lastMsgTime:
                                        chatList.lastChatMessageTime.toString(),
                                  ).paddingOnly(left: 14, right: 14),
                                  Divider(
                                    color: AppColors.chatDividerColor,
                                    height: 0,
                                  ),
                                ],
                              );
                            },
                          ),
                          GetBuilder<MessageScreenController>(
                            id: Constant.idPagination,
                            builder: (controller) => Visibility(
                              visible: controller.isPaginationLoading,
                              child: CircularProgressIndicator(
                                  color: AppColors.appRedColor),
                            ),
                          ),
                        ],
                      ),
          );
        });
  }
}*/
class BuyingChat extends StatelessWidget {
  const BuyingChat({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MessageScreenController>(
      id: Constant.idChatList,
      builder: (controller) {
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: () => controller.refreshCurrentTab(),
          child: controller.isLoading
              ? ChatListShimmer()
              : controller.chatList.isEmpty
              ? NoDataFound(
            image: AppAsset.noChatFound,
            imageHeight: 180,
            text: '',
          )
              : GetBuilder<MessageScreenController>(
            id: Constant.idPagination, // 👈 listen for pagination changes
            builder: (_) {
              return ListView.builder(
                controller: controller.scrollController,
                physics: const BouncingScrollPhysics(),
                padding: EdgeInsets.zero,
                itemCount: controller.chatList.length +
                    (controller.isPaginationLoading ? 1 : 0),
                itemBuilder: (context, index) {
                  // 👇 Show bottom loader item
                  if (index == controller.chatList.length &&
                      controller.isPaginationLoading) {
                    return Padding(
                      padding:
                      const EdgeInsets.symmetric(vertical: 20),
                      child: Center(
                        child: CircularProgressIndicator(
                          color: AppColors.appRedColor,
                        ),
                      ),
                    );
                  }

                  final chatList = controller.chatList[index];
                  return Column(
                    children: [
                      ChatViewItem(
                        onTap: () {
                          Get.toNamed(
                            AppRoutes.chatDetailScreenView,
                            arguments: {
                              'name': chatList.name,
                              'image': chatList.productImage,
                              'profileImage': chatList.profileImage,
                              'adId': chatList.adId,
                              'receiverId': chatList.receiverId,
                              'isOnline': chatList.isOnline,
                              'productPrice': chatList.productPrice,
                              'productName': chatList.productTitle,
                              'primaryImage': chatList.productImage,
                            },
                          )?.then((value) {
                            controller.init();
                          });
                        },
                        index: index,
                        name: chatList.name ?? '',
                        lastMsg: (chatList.message != null &&
                            chatList.message!
                                .contains("productName:"))
                            ? ''
                            : chatList.message ?? '',
                        image: chatList.productImage ?? '',
                        profileImage: chatList.profileImage ?? '',
                        unReadCount: chatList.unreadCount ?? 0,
                        lastMsgTime:
                        chatList.lastChatMessageTime.toString(),
                      ).paddingOnly(left: 14, right: 14),
                      Divider(
                        color: AppColors.chatDividerColor,
                        height: 0,
                      ),
                    ],
                  );
                },
              ).paddingOnly(bottom: 90);
            },
          ),
        );
      },
    );
  }
}

/*
class SellingChat extends StatelessWidget {
  const SellingChat({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MessageScreenController>(
        id: Constant.idChatList,
        builder: (controller) {
          return RefreshIndicator(
            color: AppColors.appRedColor,
            onRefresh: () => controller.refreshCurrentTab(),
            child: controller.isLoading
                ? ChatListShimmer()
                : controller.chatList.isEmpty
                    ? NoDataFound(
                        image: AppAsset.noChatFound, imageHeight: 180, text: '')
                    : Column(
                        children: [
                          ListView.builder(
                            shrinkWrap: true,
                            controller: controller.scrollController,
                            itemCount: controller.chatList.length,
                            physics: const AlwaysScrollableScrollPhysics(),
                            padding: EdgeInsets.zero,
                            itemBuilder: (context, index) {
                              final chatList = controller.chatList[index];
                              return Column(
                                children: [
                                  ChatViewItem(
                                    onTap: () {
                                      Get.toNamed(
                                          AppRoutes.chatDetailScreenView,
                                          arguments: {
                                            'name': chatList.name,
                                            'image': chatList.productImage,
                                            'profileImage':
                                                chatList.profileImage,
                                            'adId': chatList.adId,
                                            'receiverId': chatList.receiverId,
                                            'isOnline': chatList.isOnline,
                                            'productPrice':
                                                chatList.productPrice,
                                            'productName':
                                                chatList.productTitle,
                                            'primaryImage':
                                                chatList.productImage,
                                          })?.then(
                                        (value) {
                                          controller.init();
                                        },
                                      );
                                    },
                                    index: index,
                                    name: chatList.name ?? '',
                                    lastMsg: (chatList.message != null &&
                                            chatList.message!
                                                .contains("productName:"))
                                        ? ''
                                        : chatList.message ?? '',
                                    image: chatList.productImage ?? '',
                                    profileImage: chatList.profileImage ?? '',
                                    unReadCount: chatList.unreadCount ?? 0,
                                    lastMsgTime:
                                        chatList.lastChatMessageTime.toString(),
                                  ).paddingOnly(left: 14, right: 14),
                                  Divider(
                                    color: AppColors.chatDividerColor,
                                    height: 0,
                                  ),
                                ],
                              );
                            },
                          ),
                          GetBuilder<MessageScreenController>(
                            id: Constant.idPagination,
                            builder: (controller) => Visibility(
                              visible: controller.isPaginationLoading,
                              child: CircularProgressIndicator(
                                  color: AppColors.appRedColor),
                            ),
                          ),
                        ],
                      ),
          );
        });
  }
}*/

class SellingChat extends StatelessWidget {
  const SellingChat({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MessageScreenController>(
      id: Constant.idChatList,
      builder: (controller) {
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: () => controller.refreshCurrentTab(),
          child: controller.isLoading
              ? ChatListShimmer()
              : controller.chatList.isEmpty
              ? NoDataFound(
            image: AppAsset.noChatFound,
            imageHeight: 180,
            text: '',
          )
              : ListView.builder(
            controller: controller.scrollController,
            physics: const BouncingScrollPhysics(),
            padding: EdgeInsets.zero,
            itemCount: controller.chatList.length + 1, // 👈 +1 for pagination loader
            itemBuilder: (context, index) {
              if (index == controller.chatList.length) {
                // 👇 Show bottom loader when loading next page
                return GetBuilder<MessageScreenController>(
                  id: Constant.idPagination,
                  builder: (controller) {
                    return Visibility(
                      visible: controller.isPaginationLoading,
                      child: Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Center(
                          child: CircularProgressIndicator(
                            color: AppColors.appRedColor,
                          ),
                        ),
                      ),
                    );
                  },
                );
              }

              final chatList = controller.chatList[index];
              return Column(
                children: [
                  ChatViewItem(
                    onTap: () {
                      Get.toNamed(
                        AppRoutes.chatDetailScreenView,
                        arguments: {
                          'name': chatList.name,
                          'image': chatList.productImage,
                          'profileImage': chatList.profileImage,
                          'adId': chatList.adId,
                          'receiverId': chatList.receiverId,
                          'isOnline': chatList.isOnline,
                          'productPrice': chatList.productPrice,
                          'productName': chatList.productTitle,
                          'primaryImage': chatList.productImage,
                        },
                      )?.then((value) => controller.init());
                    },
                    index: index,
                    name: chatList.name ?? '',
                    lastMsg: (chatList.message != null &&
                        chatList.message!.contains("productName:"))
                        ? ''
                        : chatList.message ?? '',
                    image: chatList.productImage ?? '',
                    profileImage: chatList.profileImage ?? '',
                    unReadCount: chatList.unreadCount ?? 0,
                    lastMsgTime:
                    chatList.lastChatMessageTime.toString(),
                  ).paddingOnly(left: 14, right: 14),
                  Divider(
                    color: AppColors.chatDividerColor,
                    height: 0,
                  ),
                ],
              );
            },
          ).paddingOnly(bottom: 90),
        );
      },
    );
  }
}




// class TabBarScreen extends StatelessWidget {
//   const TabBarScreen({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return GetBuilder<MessageScreenController>(
//       id: Constant.idTabChange,
//       builder: (controller) {
//         // Ensure that the content is wrapped with an Expanded widget properly.
//         return controller.tabIndex == 0 ? BuyingChat() : SellingChat();
//       },
//     );
//   }
// }
class TabBarScreen extends StatelessWidget {
  const TabBarScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MessageScreenController>(
      id: Constant.idTabChange,
      builder: (controller) {
        return PageView(
          controller: controller.pageController,
          onPageChanged: controller.onSwipeTo, // swipe → change tab + fetch
          children: const [
            BuyingChat(),
            SellingChat(),
          ],
        );
      },
    );
  }
}
