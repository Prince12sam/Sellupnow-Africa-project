import 'package:audioplayers/audioplayers.dart';
import 'package:dismissible_page/dismissible_page.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/custom/custom_audio_time/custom_format_audio_time.dart';
import 'package:listify/custom/custom_chat_time/custom_format_chat_time.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/custom_profile/custom_profile_image.dart';
import 'package:listify/custom/dialog/review_dialog.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/socket/socket_emit.dart';
import 'package:listify/ui/chat_detail_screen/controller/chat_detail_controller.dart';
import 'package:listify/ui/chat_detail_screen/model/chat_history_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/socket_params.dart';
import 'package:listify/utils/utils.dart';
import 'package:vibration/vibration.dart';

/// chat detail app bar
class CustomChatAppBar extends StatelessWidget {
  const CustomChatAppBar({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ChatDetailController>(builder: (controller) {
      return Container(
        color: AppColors.adScreenBgColor,
        child: Column(
          children: [
            GestureDetector(
              onTap: () {
                // Get.toNamed(
                //   AppRoutes.sellerDetailScreenView,
                //   arguments: {
                //     'name': controller.name,
                //     'image': controller.profileImage,
                //     // 'register': controller.registeredAt,
                //     'userId': controller.receiverId,
                //     // 'user': controller.productDetail?.data?.seller,
                //   },
                // );
              },
              child: Container(
                decoration: BoxDecoration(
                  borderRadius:
                      BorderRadius.vertical(bottom: Radius.circular(18)),
                  color: AppColors.white,
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.lightGrey.withValues(alpha: 0.36),
                      spreadRadius: 0,
                      offset: const Offset(0, -1),
                      blurRadius: 14,
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    GestureDetector(
                      onTap: () {
                        Get.back();
                      },
                      child: Image.asset(
                        AppAsset.chatDetailBackIcon,
                        height: 30,
                        width: 30,
                      ),
                    ).paddingOnly(right: 3),
                    Container(
                      padding: EdgeInsets.all(1.3),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        border: Border.all(
                            color: AppColors.chatDetailBorderColor, width: 0.8),
                        shape: BoxShape.circle,
                      ),
                      child: Container(
                        height: Get.height * 0.058,
                        width: Get.height * 0.058,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                        ),
                        child: ClipOval(
                          child: CustomProfileImage(
                            image: controller.profileImage ?? '',
                          ),
                        ),
                      ),
                    ).paddingOnly(right: 8),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          controller.name ?? "",
                          style: AppFontStyle.fontStyleW700(
                            fontSize: 17,
                            fontColor: AppColors.black,
                          ),
                        ).paddingOnly(bottom: 6),
                        controller.isOnline == true
                            ? Container(
                                padding: EdgeInsets.only(
                                    right: 5, bottom: 3, top: 3, left: 5),
                                decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(20),
                                    color: AppColors.lightGreenColor),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Container(
                                      decoration: BoxDecoration(
                                        color: AppColors.white
                                            .withValues(alpha: 0.5),
                                        shape: BoxShape.circle,
                                      ),
                                      child: Container(
                                        height: 7.5,
                                        width: 7.5,
                                        decoration: BoxDecoration(
                                          color: AppColors.green,
                                          shape: BoxShape.circle,
                                        ),
                                      ).paddingAll(1.8),
                                    ).paddingOnly(right: 4),
                                    Text(
                                      EnumLocale.txtOnline.name.tr,
                                      style: AppFontStyle.fontStyleW500(
                                          fontSize: 10,
                                          fontColor: AppColors.green),
                                    ).paddingOnly(right: 4),
                                  ],
                                ),
                              )
                            : Container(
                                padding: EdgeInsets.only(
                                    right: 5, bottom: 3, top: 3, left: 5),
                                decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(20),
                                    color: AppColors.lightGreyBorder),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Container(
                                      decoration: BoxDecoration(
                                        color: AppColors.grey
                                            .withValues(alpha: 0.5),
                                        shape: BoxShape.circle,
                                      ),
                                      child: Container(
                                        height: 7.5,
                                        width: 7.5,
                                        decoration: BoxDecoration(
                                          color: AppColors.grey,
                                          shape: BoxShape.circle,
                                        ),
                                      ).paddingAll(1.8),
                                    ).paddingOnly(right: 4),
                                    Text(
                                      "Offline",
                                      style: AppFontStyle.fontStyleW500(
                                          fontSize: 10,
                                          fontColor: AppColors.darkGrey),
                                    ).paddingOnly(right: 4),
                                  ],
                                ),
                              ),
                      ],
                    ),
                    Spacer(),
                    GestureDetector(
                      onTapDown: (TapDownDetails details) {
                        _showDropdownMenu(
                            context, details.globalPosition, controller);
                      },
                      child: Image.asset(
                        AppAsset.menuIcon,
                        height: 25,
                        width: 25,
                      ),
                    ),
                  ],
                ).paddingOnly(right: 18, left: 10, top: 35, bottom: 10),
              ),
            ),
            GetBuilder<ChatDetailController>(builder: (controller) {
              if (controller.chatOldHistory.isEmpty) {
                return SizedBox.shrink();
              }

              // 🔹 Find the first product message in chat history
              OldChat? productMessage;
              for (var chat in controller.chatOldHistory) {
                if (chat.isInnerMessageType1) {
                  productMessage = chat;
                  break; // Take the first (latest) product message
                }
              }

              // 🔹 If no product message found, try to get from controller directly
              if (productMessage == null) {
                // Show product info from controller arguments if available
                if (controller.productName != null &&
                    controller.productPrice != null) {
                  return Container(
                    width: Get.width,
                    decoration: BoxDecoration(color: AppColors.white),
                    child: Row(
                      children: [
                        ClipRRect(
                          borderRadius: BorderRadius.circular(14),
                          child: Container(
                            height: 60,
                            width: 60,
                            clipBehavior: Clip.hardEdge,
                            decoration: BoxDecoration(
                                color: AppColors.white,
                                borderRadius: BorderRadius.circular(14),
                                border:
                                    Border.all(color: AppColors.borderColor)),
                            child: controller.primaryImage != null &&
                                    controller.primaryImage!.isNotEmpty
                                ? CustomImageView(
                                    image: controller.primaryImage!)
                                : Icon(Icons.image, color: AppColors.grey),
                          ).paddingOnly(right: 16),
                        ),
                        Expanded(
                          child: Text(
                            capitalizeWords(
                                controller.productName ?? "Product Name"),
                            style: AppFontStyle.fontStyleW500(
                                fontSize: 16, fontColor: AppColors.black),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        Text(
                          "${Database.settingApiResponseModel?.data?.currency?.symbol}${controller.productPrice ?? "0"}",
                          style: AppFontStyle.fontStyleW700(
                              fontSize: 18, fontColor: AppColors.black),
                        ),
                      ],
                    ),
                  ).paddingOnly(top: 14, bottom: 8, left: 16, right: 16);
                } else {
                  return SizedBox.shrink();
                }
              }

              // 🔹 Parse product message data
              final data = productMessage.messageData;
              final productName = data["productName"] ??
                  controller.productName ??
                  "Product Name";
              final productPrice =
                  data["productPrice"] ?? controller.productPrice ?? "0";
              final productImage =
                  data["productImage"] ?? controller.primaryImage ?? "";

              return GestureDetector(
                onTap: () {
                  Utils.showLog("kljkjkjkjlj");

                  Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                    'sellerDetail': true,
                    'relatedProduct': true,
                    'viewLikeCount': true,
                    // 'ad': favouriteItem,
                    'adId': controller.adId
                  });
                },
                child: Column(
                  children: [
                    Container(
                      width: Get.width,
                      padding: EdgeInsets.only(
                          top: 14, bottom: 8, left: 16, right: 16),
                      child: Row(
                        children: [
                          ClipRRect(
                            borderRadius: BorderRadius.circular(14),
                            child: Container(
                              height: 60,
                              width: 60,
                              clipBehavior: Clip.hardEdge,
                              decoration: BoxDecoration(
                                  color: AppColors.white,
                                  borderRadius: BorderRadius.circular(14),
                                  border:
                                      Border.all(color: AppColors.borderColor)),
                              child: productImage.isNotEmpty
                                  ? CustomImageView(image: productImage,fit: BoxFit.cover,)
                                  : Icon(Icons.image, color: AppColors.grey),
                            ).paddingOnly(right: 16),
                          ),
                          Expanded(
                            child: Text(
                              productName,
                              style: AppFontStyle.fontStyleW500(
                                  fontSize: 16, fontColor: AppColors.black),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          Text(
                            "${Database.settingApiResponseModel?.data?.currency?.symbol}$productPrice",
                            style: AppFontStyle.fontStyleW700(
                                fontSize: 16, fontColor: AppColors.black),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      color: AppColors.chatDetailBorder,
                      height: 1,
                      width: Get.width,
                    )
                  ],
                ),
              );
            }),
          ],
        ),
      );
    });
  }

  void _showDropdownMenu(BuildContext context, Offset tapPosition,
      ChatDetailController controller) {
    showMenu<String>(
      context: context,
      // position: RelativeRect.fromLTRB(
      //   tapPosition.dx - 100, // Menu left position
      //   tapPosition.dy + 10, // Menu top position
      //   30, // Menu right position
      //   tapPosition.dy + 200, // Menu bottom position
      // ),
      position: RelativeRect.fromLTRB(
        100, // Menu left position
        80, // Menu top position
        30, // Menu right position
        100, // Menu bottom position
      ),
      shadowColor: AppColors.black.withValues(alpha: 0.30),
      color: Colors.white,
      elevation: 8,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.only(
            topLeft: Radius.circular(12),
            bottomLeft: Radius.circular(12),
            bottomRight: Radius.circular(12)),
      ),
      items: [
        PopupMenuItem<String>(
          value: 'review',
          child: Row(
            children: [
              Image.asset(
                AppAsset.reviewIcon,
                width: 19,
                height: 19,
              ),
              const SizedBox(width: 12),
              Text(EnumLocale.txtSellerRateUs.name.tr,
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 13, fontColor: AppColors.black)),
            ],
          ),
        ),
        PopupMenuItem<String>(
          value: 'block',
          child: Row(
            children: [
              Image.asset(
                AppAsset.blockUser,
                width: 19,
                height: 19,
              ),
              const SizedBox(width: 12),
              Text(EnumLocale.txtBlockUser.name.tr,
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 13, fontColor: AppColors.black)),
            ],
          ),
        ),
        PopupMenuItem<String>(
          value: 'report',
          child: Row(
            children: [
              Image.asset(
                AppAsset.reportSpam,
                width: 19,
                height: 19,
              ),
              const SizedBox(width: 12),
              Text(EnumLocale.txtReportSpam.name.tr,
                  style: AppFontStyle.fontStyleW500(
                      fontSize: 13, fontColor: AppColors.black)),
            ],
          ),
        ),
      ],
    ).then((String? value) async {
      if (value != null) {
        switch (value) {
          case 'review':
            Get.dialog(
              barrierColor: AppColors.black.withValues(alpha: 0.8),
              Dialog(
                backgroundColor: AppColors.transparent,
                shadowColor: Colors.transparent,
                surfaceTintColor: Colors.transparent,
                elevation: 0,
                child: ReviewDialog(
                  controllerType: controller,
                  controller: controller.reasonController,
                ),
              ),
            ).then(
              (value) {
                controller.reviewController.clear();
              },
            );
            Utils.showLog('review call');
            break;
          case 'block':
            if (Database.demoUser == true) {
              Utils.showLog("This is demo app");
            } else {
              controller.blockApi();
              Utils.showLog('Block User selected');
            }
            break;
          case 'report':
            showModalBottomSheet(
              context: context,
              isScrollControlled: true,
              backgroundColor: Colors.white,
              barrierColor: AppColors.black.withValues(alpha: 0.8),
              builder: (context) => ReportBottomSheet(
                submitOnTap: () {
                  if (Database.demoUser == true) {
                    Utils.showLog("This is demo app");
                  } else {
                    controller.reportUserApi();
                    controller.reasonController.clear();
                    // Get.back();
                  }
                },
              ),
            );
            Utils.showLog('Report & Spam selected');
            break;
        }
      }
    });
  }
}

/// chat detail bottom bar
class ChatDetailBottomBarView extends StatelessWidget {
  const ChatDetailBottomBarView({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ChatDetailController>(
        id: Constant.idGetOldChat,
        builder: (controller) {
          return Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                decoration: BoxDecoration(
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.black.withValues(alpha: 0.12),
                      blurRadius: 12,
                      offset: Offset(0, -1),
                    ),
                  ],
                  color: AppColors.white,
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Container(
                        // height: 52,
                        decoration: BoxDecoration(
                          color: AppColors.textFieldColor,
                          border: Border.all(
                            color: AppColors.notificationBorderColor,
                            // color: AppColors.textFieldColor,
                          ),
                          borderRadius: BorderRadius.circular(51),
                        ),
                        child: TextField(
                          controller: controller.messageController,
                          cursorColor: AppColors.black,
                          decoration: InputDecoration(
                              hintText: EnumLocale.txtSaySomething.name.tr,
                              hintStyle: AppFontStyle.fontStyleW400(
                                  fontSize: 16,
                                  fontColor: AppColors.popularProductText),
                              disabledBorder: OutlineInputBorder(
                                  borderSide: BorderSide.none),
                              enabledBorder: OutlineInputBorder(
                                  borderSide: BorderSide.none),
                              focusedBorder: OutlineInputBorder(
                                  borderSide: BorderSide.none),
                              suffixIcon: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  GestureDetector(
                                    onTap: () {
                                      // Vibration.vibrate(duration: 50, amplitude: 128);
                                      Utils.showToast(
                                          Get.context!,
                                          EnumLocale
                                              .txtLongPressToEnableAudioRecording
                                              .name
                                              .tr);
                                    },
                                    onLongPressStart: (details) {
                                      if (controller.isSendingAudioFile ==
                                          false) {
                                        Vibration.vibrate(
                                            duration: 50, amplitude: 128);
                                        controller.onLongPressStartMic();
                                      }
                                    },
                                    onLongPressEnd: (details) {
                                      if (controller.isSendingAudioFile ==
                                          false) {
                                        Vibration.vibrate(
                                            duration: 50, amplitude: 128);
                                        controller.onLongPressEndMic();
                                      }
                                    },
                                    child: Image.asset(
                                      AppAsset.purpleMicIcon,
                                      height: 26,
                                      width: 26,
                                    ),
                                  ),
                                  GestureDetector(
                                    onTap: () async {
                                      controller.showImagePickerDialog();
                                    },
                                    child: Image.asset(
                                      AppAsset.purpleAddRound,
                                      height: 26,
                                      width: 26,
                                    ).paddingOnly(right: 15, left: 15),
                                  )
                                ],
                              )),
                        ),
                      ).paddingOnly(left: 16),
                    ),
                    GestureDetector(
                      onTap: () {
                        Utils.showLog("User send message............");

                        controller.sendMessage();
                      },
                      child: Container(
                        height: 52,
                        width: 52,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: AppColors.appRedColor,
                        ),
                        child: Center(
                          child: Image.asset(
                            AppAsset.whiteSendIcon,
                            height: 30.33,
                            width: 30.33,
                          ),
                        ),
                      ).paddingOnly(right: 16, top: 15, bottom: 14, left: 16),
                    )
                  ],
                ),
              )
            ],
          );
        });
  }
}

/// chat detail sender chat view
class SenderChatView extends StatelessWidget {
  final OldChat msg;
  final ChatDetailController controller;

  const SenderChatView(
      {super.key, required this.msg, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Flexible(
          child: Container(
            constraints: BoxConstraints(
                maxWidth: MediaQuery.of(context).size.width * 0.7),
            decoration: BoxDecoration(
              color: AppColors.appRedColor,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(10),
                bottomLeft: Radius.circular(10),
                topRight: Radius.circular(10),
              ),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Flexible(
                  child: Text(
                    msg.message ?? '',
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 15,
                      fontColor: AppColors.white,
                    ),
                  ).paddingSymmetric(horizontal: 10, vertical: 8),
                ),
                Row(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    Text(
                      controller.formatTimeFromDate(msg.date),
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 8, fontColor: AppColors.white),
                    ).paddingOnly(right: 4, bottom: 4),
                  ],
                ).paddingOnly(bottom: 2)
              ],
            ),
          ).paddingOnly(right: 8.9),
        ),
        Container(
          padding: const EdgeInsets.all(1.3),
          decoration: BoxDecoration(
            color: Colors.white,
            border:
                Border.all(color: AppColors.chatDetailBorderColor, width: 0.8),
            shape: BoxShape.circle,
          ),
          child: Container(
            height: 36,
            width: 36,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
            child: ClipOval(
              child: CustomProfileImage(
                image:
                    Database.getUserProfileResponseModel?.user?.profileImage ??
                        '',
                fit: BoxFit.cover,
              ),
            ),
          ),
        ),
      ],
    ).paddingOnly(bottom: 8, top: 8);
  }
}

/// chat detail receiver chat view
class ReceiverChatView extends StatelessWidget {
  final OldChat msg;
  final ChatDetailController controller;

  const ReceiverChatView(
      {super.key, required this.msg, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.start,
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Container(
          padding: const EdgeInsets.all(1.3),
          decoration: BoxDecoration(
            color: Colors.white,
            border:
                Border.all(color: AppColors.chatDetailBorderColor, width: 0.8),
            shape: BoxShape.circle,
          ),
          child: Container(
            height: 36,
            width: 36,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
            child: ClipOval(
              child: CustomProfileImage(
                image: controller.profileImage ?? '',
                fit: BoxFit.cover,
              ),
            ),
          ),
        ).paddingOnly(right: 8),
        Flexible(
          child: Container(
            constraints: BoxConstraints(
                maxWidth: MediaQuery.of(context).size.width * 0.7),
            decoration: BoxDecoration(
              color: AppColors.messageColor,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(10),
                bottomRight: Radius.circular(10),
                topRight: Radius.circular(10),
              ),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.end,
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Flexible(
                  child: Text(
                    msg.message ?? '',
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 15,
                      fontColor: AppColors.white,
                    ),
                  ).paddingSymmetric(horizontal: 12, vertical: 8),
                ),
                Row(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    Text(
                      controller.formatTimeFromDate(msg.date),
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 8, fontColor: AppColors.white),
                    ).paddingOnly(right: 4, bottom: 4),
                  ],
                ).paddingOnly(bottom: 2)
              ],
            ),
          ).paddingOnly(right: 8.9),
        ),
      ],
    ).paddingOnly(bottom: 8, top: 8);
  }
}

/// chat detail sender product chat view
class SenderProductView extends StatelessWidget {
  final OldChat msg;
  final ChatDetailController controller;

  const SenderProductView(
      {super.key, required this.msg, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Flexible(
          child: Container(
            constraints: BoxConstraints(
                maxWidth: MediaQuery.of(context).size.width * 0.7),
            decoration: BoxDecoration(
              color: AppColors.white,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(10),
                bottomLeft: Radius.circular(10),
                topRight: Radius.circular(10),
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  height: 112,
                  width: 182,
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: CustomImageView(
                    image: msg.messageData['productImage'] ?? "",
                  ),
                ).paddingOnly(left: 4, right: 4, top: 4, bottom: 5),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      capitalizeWords(msg.messageData['productName'] ?? ""),
                      style: AppFontStyle.fontStyleW700(
                          fontSize: 14, fontColor: AppColors.black),
                    ).paddingOnly(bottom: 6),
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          "${Database.settingApiResponseModel?.data?.currency?.symbol}${msg.messageData['offerAmount'] ?? ""}",
                          style: AppFontStyle.fontStyleW900(
                              fontSize: 14, fontColor: AppColors.appRedColor),
                        ).paddingOnly(right: 6),
                        // Text(
                        //   "\$ ${msg.messageData['offerAmount'] ?? ""}",
                        //   style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.grey),
                        // ),
                      ],
                    ).paddingOnly(bottom: 6),
                    // Row(
                    //   mainAxisSize: MainAxisSize.min,
                    //   children: [
                    //     Text(
                    //       "Product ID :",
                    //       style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.black),
                    //     ).paddingOnly(right: 2),
                    //     Text(
                    //       msg.messageData['productId'] ?? "",
                    //       style: AppFontStyle.fontStyleW700(fontSize: 10, fontColor: AppColors.black),
                    //     ),
                    //   ],
                    // ).paddingOnly(bottom: 8)
                  ],
                ).paddingOnly(left: 10),
              ],
            ),
          ).paddingOnly(right: 8.9),
        ),
        Container(
          padding: const EdgeInsets.all(1.3),
          decoration: BoxDecoration(
            color: Colors.white,
            border:
                Border.all(color: AppColors.chatDetailBorderColor, width: 0.8),
            shape: BoxShape.circle,
          ),
          child: Container(
            height: 36,
            width: 36,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
            child: ClipOval(
              child: CustomImageView(
                image:
                    "${Database.getUserProfileResponseModel?.user?.profileImage}",
                fit: BoxFit.cover,
              ),
            ),
          ),
        ).paddingOnly(right: 0),
      ],
    );
  }
}

/// chat detail receiver product chat view
class ReceiverProductView extends StatelessWidget {
  final OldChat msg;
  final ChatDetailController controller;
  const ReceiverProductView(
      {super.key, required this.msg, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.start,
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Container(
          padding: const EdgeInsets.all(1.3),
          decoration: BoxDecoration(
            color: Colors.white,
            border:
                Border.all(color: AppColors.chatDetailBorderColor, width: 0.8),
            shape: BoxShape.circle,
          ),
          child: Container(
            height: 36,
            width: 36,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.white,
            ),
            child: ClipOval(
              child: CustomImageView(
                image: controller.profileImage ?? '',
                fit: BoxFit.cover,
              ),
            ),
          ),
        ).paddingOnly(right: 8),
        Flexible(
          child: Container(
            constraints: BoxConstraints(
                maxWidth: MediaQuery.of(context).size.width * 0.7),
            decoration: BoxDecoration(
              color: AppColors.white,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(10),
                bottomRight: Radius.circular(10),
                topRight: Radius.circular(10),
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  height: 112,
                  width: 182,
                  clipBehavior: Clip.hardEdge,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: CustomImageView(
                      image: msg.messageData['productImage'] ?? ""),
                ).paddingOnly(left: 4, right: 4, top: 4, bottom: 5),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      capitalizeWords(msg.messageData['productName'] ?? ""),
                      style: AppFontStyle.fontStyleW700(
                          fontSize: 14, fontColor: AppColors.black),
                    ).paddingOnly(bottom: 6),
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          "${Database.settingApiResponseModel?.data?.currency?.symbol}${msg.messageData['offerAmount'] ?? ""}",
                          style: AppFontStyle.fontStyleW900(
                              fontSize: 14, fontColor: AppColors.appRedColor),
                        ).paddingOnly(right: 6),
                        // Text(
                        //   "\$ 1400.00",
                        //   style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.grey),
                        // ),
                      ],
                    ).paddingOnly(bottom: 6),
                    // Row(
                    //   mainAxisSize: MainAxisSize.min,
                    //   children: [
                    //     Text(
                    //       "Product ID :",
                    //       style: AppFontStyle.fontStyleW500(fontSize: 10, fontColor: AppColors.black),
                    //     ).paddingOnly(right: 2),
                    //     Text(
                    //       "65238GHK",
                    //       style: AppFontStyle.fontStyleW700(fontSize: 10, fontColor: AppColors.black),
                    //     ),
                    //   ],
                    // ).paddingOnly(bottom: 8)
                  ],
                ).paddingOnly(left: 10),
              ],
            ),
          ).paddingOnly(right: 8.9),
        ),
      ],
    );
  }
}

/// sender audio message
class SenderAudioMessageWidget extends StatefulWidget {
  final String audioUrl;
  final String time;
  final String id;
  final dynamic chat;
  final bool isLastMessage;

  const SenderAudioMessageWidget({
    super.key,
    required this.audioUrl,
    required this.time,
    required this.id,
    required this.chat,
    required this.isLastMessage,
  });

  @override
  State<SenderAudioMessageWidget> createState() =>
      _SenderAudioMessageWidgetState();
}

class _SenderAudioMessageWidgetState extends State<SenderAudioMessageWidget> {
  final AudioPlayer player = AudioPlayer();
  bool isPlaying = false;
  Duration duration = Duration.zero;
  Duration position = Duration.zero;

  @override
  void initState() {
    super.initState();

    player.onDurationChanged.listen((newDuration) {
      setState(() => duration = newDuration);
    });

    player.onPositionChanged.listen((newPosition) {
      setState(() => position = newPosition);
      Utils.showLog("position=>$position");
    });

    player.onPlayerComplete.listen((_) {
      setState(() {
        isPlaying = false;
        position = Duration.zero;
      });
    });

    player.onPlayerStateChanged.listen((state) {
      setState(() {
        isPlaying = state == PlayerState.playing;
      });
    });
  }

  void onPlayAudio() async {
    Utils.showLog("uri audio::::::::::${widget.audioUrl}");

    try {
      final sanitizedUrl = widget.audioUrl.replaceAll('\\', '/');
      final fullUrl = '${Api.baseUrl}$sanitizedUrl';

      Utils.showLog("Attempting to play audio: $fullUrl");

      if (!isPlaying) {
        await player.play(UrlSource(fullUrl));
      } else {
        await player.pause();
      }
    } catch (e) {
      Utils.showLog("Audio playback error: $e");
    }
  }

  @override
  void dispose() {
    player.dispose();
    super.dispose();
  }

  String formatTime(Duration duration) {
    String twoDigits(int n) => n.toString().padLeft(2, "0");
    return "${twoDigits(duration.inMinutes)}:${twoDigits(duration.inSeconds.remainder(60))}";
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(top: 7),
      // padding: const EdgeInsets.only(bottom: 3, left: 12, right: 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.end,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Container(
            width: Get.width / 1.6,
            // margin: const EdgeInsets.symmetric(vertical: 10),
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: AppColors.appRedColor,
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(10),
                topRight: Radius.circular(10),
                bottomLeft: Radius.circular(10),
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                SizedBox(
                  height: 70,
                  width: Get.width / 1.6,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      Container(
                        height: 75,
                        width: Get.width / 1.6,
                        margin: const EdgeInsets.only(bottom: 5),
                        padding: const EdgeInsets.symmetric(horizontal: 10),
                        clipBehavior: Clip.antiAlias,
                        decoration: BoxDecoration(
                          color: AppColors.white,
                          borderRadius: BorderRadius.circular(5),
                        ),
                        child: Row(
                          children: [
                            GetBuilder<ChatDetailController>(
                              id: Constant.idGetOldChat,
                              builder: (logic) {
                                return widget.isLastMessage
                                    ? logic.isLoadingAudio
                                        ? CupertinoActivityIndicator(
                                            color: AppColors.appRedColor,
                                            radius: 12,
                                          )
                                        : GestureDetector(
                                            onTap: () => onPlayAudio(),
                                            child: Icon(
                                              isPlaying
                                                  ? Icons.pause_rounded
                                                  : Icons.play_arrow_rounded,
                                              size: 30,
                                              color: AppColors.appRedColor,
                                            )

                                            // Image.asset(isPlaying ? AppAsset.icPause1 : AppAsset.icPlay1, color: AppColors.messageColor, width: 24),
                                            )
                                    : GestureDetector(
                                        onTap: () => onPlayAudio(),
                                        child: Icon(
                                          isPlaying
                                              ? Icons.pause_rounded
                                              : Icons.play_arrow_rounded,
                                          size: 30,
                                          color: AppColors.appRedColor,
                                        )
                                        // Image.asset(isPlaying ? AppAsset.icPause1 : AppAsset.icPlay1, color: AppColors.messageColor, width: 24),
                                        );
                              },
                            ),
                            5.width,
                            Expanded(
                              child: SliderTheme(
                                data: SliderThemeData(
                                  overlayShape: SliderComponentShape.noOverlay,
                                  activeTrackColor: AppColors.adBorderColor,
                                  thumbColor: AppColors.adBorderColor,
                                  thumbShape: const RoundSliderThumbShape(
                                      enabledThumbRadius: 10),
                                  trackHeight: 5,
                                ),
                                child: Slider(
                                  activeColor: AppColors.appRedColor,
                                  min: 0,
                                  max: duration.inSeconds.toDouble() > 0
                                      ? duration.inSeconds.toDouble()
                                      : 1,
                                  value: position.inSeconds
                                      .toDouble()
                                      .clamp(0, duration.inSeconds.toDouble()),
                                  onChanged: (value) {
                                    player
                                        .seek(Duration(seconds: value.toInt()));
                                  },
                                ),
                              ),
                            ),
                            3.width,
                            Container(
                              height: 44,
                              width: 44,
                              alignment: Alignment.center,
                              decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: AppColors.appRedColor),
                              child: Image.asset(
                                AppAsset.purpleMicIcon,
                                width: 20,
                                color: AppColors.white,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Positioned(
                        bottom: 20,
                        right: 70,
                        child: Text(
                          formatTime(position),
                          style: AppFontStyle.fontStyleW600(
                              fontColor: AppColors.appRedColor, fontSize: 9),
                        ),
                      ),
                    ],
                  ),
                ),
                GetBuilder<ChatDetailController>(
                  builder: (logic) {
                    return Text(
                      logic.formatTimeFromDate(widget.chat.date),
                      style: AppFontStyle.fontStyleW600(
                          fontColor: AppColors.white, fontSize: 8),
                    );
                  },
                ),
              ],
            ),
          ),
          // const SizedBox(width: 10),
          // GetBuilder<ChatController>(
          //   builder: (logic) {
          //     return Container(
          //       padding: const EdgeInsets.all(2),
          //       decoration: BoxDecoration(
          //         shape: BoxShape.circle,
          //         border: Border.all(color: AppColors.borderColor),
          //       ),
          //       child: Container(
          //         clipBehavior: Clip.antiAlias,
          //         height: 35,
          //         width: 35,
          //         decoration: const BoxDecoration(
          //           shape: BoxShape.circle,
          //         ),
          //         child: CustomProfileImage(
          //           image: Database.profileImage,
          //           fit: BoxFit.cover,
          //         ),
          //       ),
          //     ).paddingOnly(bottom: 10);
          //   },
          // ),
        ],
      ).paddingOnly(bottom: 15),
    );
  }
}

class UploadAudioUi extends StatelessWidget {
  const UploadAudioUi({super.key});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Container(
          height: 75,
          width: Get.width / 1.6,
          margin: EdgeInsets.only(bottom: 15),
          decoration: BoxDecoration(
            color: AppColors.grey,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(5),
              topRight: Radius.circular(5),
              bottomLeft: Radius.circular(5),
            ),
          ),
          child: Stack(
            alignment: Alignment.center,
            children: [
              Container(
                height: 75,
                width: Get.width / 1.6,
                margin: EdgeInsets.only(left: 6, right: 6, top: 6, bottom: 15),
                padding: EdgeInsets.symmetric(horizontal: 10),
                clipBehavior: Clip.antiAlias,
                decoration: BoxDecoration(
                  color: AppColors.white,
                  borderRadius: BorderRadius.circular(0),
                ),
                child: Row(
                  children: [
                    // Lottie.asset(AppAsset.lottieUpload, width: 35),
                    5.width,
                    Expanded(
                      child: SliderTheme(
                        data: SliderThemeData(
                          overlayShape: SliderComponentShape.noOverlay,
                          activeTrackColor: AppColors.grey,
                          thumbColor: AppColors.grey,
                          thumbShape:
                              RoundSliderThumbShape(enabledThumbRadius: 10),
                          trackHeight: 5,
                        ),
                        child: Slider(
                          min: 0,
                          max: 10,
                          value: 0,
                          onChanged: (value) {},
                        ),
                      ),
                    ),
                    3.width,
                    Container(
                      height: 40,
                      width: 40,
                      alignment: Alignment.center,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: AppColors.grey,
                      ),
                      child: Image.asset(
                        AppAsset.purpleMicIcon,
                        width: 20,
                        color: AppColors.white,
                      ),
                    ),
                  ],
                ),
              ),
              Positioned(
                bottom: 20,
                right: 70,
                child: Text(
                  CustomFormatAudioTime.convert(0),
                  style: AppFontStyle.fontStyleW500(
                      fontColor: AppColors.appRedColor, fontSize: 9),
                ),
              ),
              Positioned(
                bottom: 3,
                right: 8,
                child: Text(
                  CustomFormatChatTime.convert(DateTime.now().toString()),
                  style: AppFontStyle.fontStyleW500(
                      fontColor: AppColors.white, fontSize: 8),
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

/// receiver audio message
class ReceiverAudioMessageWidget extends StatefulWidget {
  final String audioUrl;
  final String time;
  final String id;
  final dynamic chat;
  const ReceiverAudioMessageWidget({
    super.key,
    required this.audioUrl,
    required this.time,
    required this.id,
    required this.chat,
  });

  @override
  State<ReceiverAudioMessageWidget> createState() =>
      _ReceiverAudioMessageWidgetState();
}

class _ReceiverAudioMessageWidgetState
    extends State<ReceiverAudioMessageWidget> {
  final AudioPlayer player = AudioPlayer();
  bool isPlaying = false;
  Duration duration = Duration.zero;
  Duration position = Duration.zero;

  @override
  void initState() {
    super.initState();

    player.onDurationChanged.listen((newDuration) {
      setState(() => duration = newDuration);
    });

    player.onPositionChanged.listen((newPosition) {
      setState(() => position = newPosition);
    });

    player.onPlayerComplete.listen((_) {
      setState(() {
        isPlaying = false;
        position = Duration.zero;
      });
    });

    player.onPlayerStateChanged.listen((state) {
      isPlaying = state == PlayerState.playing;
    });
  }

  void onPlayAudio() async {
    try {
      final sanitizedUrl = widget.audioUrl.replaceAll('\\', '/');
      final fullUrl = '${Api.baseUrl}$sanitizedUrl';

      Utils.showLog("Attempting to play audio: $fullUrl");

      if (!isPlaying) {
        await player.play(UrlSource(fullUrl));
      } else {
        await player.pause();
      }
    } catch (e) {
      Utils.showLog("Audio playback error: $e");
    }
  }

  @override
  void dispose() {
    player.dispose();
    super.dispose();
  }

  String formatTime(Duration duration) {
    String twoDigits(int n) => n.toString().padLeft(2, "0");
    return "${twoDigits(duration.inMinutes)}:${twoDigits(duration.inSeconds.remainder(60))}";
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(top: 7),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Container(
            width: Get.width / 1.6,
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: AppColors.messageColor,
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(10),
                topRight: Radius.circular(10),
                bottomRight: Radius.circular(10),
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                SizedBox(
                  height: 70,
                  width: Get.width / 1.6,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      Container(
                        height: 75,
                        width: Get.width / 1.6,
                        margin: const EdgeInsets.only(bottom: 5),
                        padding: EdgeInsets.symmetric(horizontal: 10),
                        clipBehavior: Clip.antiAlias,
                        decoration: BoxDecoration(
                          color: AppColors.white,
                          borderRadius: BorderRadius.circular(5),
                        ),
                        child: Row(
                          children: [
                            GestureDetector(
                                onTap: () => onPlayAudio(),
                                child: Icon(
                                  isPlaying
                                      ? Icons.pause_rounded
                                      : Icons.play_arrow_rounded,
                                  size: 30,
                                  color: AppColors.messageColor,
                                )

                                // child: Image.asset(isPlaying ? AppAsset.icPause1 : AppAsset.icPlay1, color: AppColors.pinkMessageColor, width: 24),
                                ),
                            5.width,
                            Expanded(
                              child: SliderTheme(
                                data: SliderThemeData(
                                  overlayShape: SliderComponentShape.noOverlay,
                                  activeTrackColor: AppColors.adBorderColor,
                                  thumbColor: AppColors.messageColor,
                                  thumbShape: const RoundSliderThumbShape(
                                      enabledThumbRadius: 10),
                                  trackHeight: 5,
                                ),
                                child: Slider(
                                  activeColor: AppColors.messageColor,
                                  min: 0,
                                  max: duration.inSeconds.toDouble() > 0
                                      ? duration.inSeconds.toDouble()
                                      : 1,
                                  value: position.inSeconds
                                      .toDouble()
                                      .clamp(0, duration.inSeconds.toDouble()),
                                  onChanged: (value) {
                                    player
                                        .seek(Duration(seconds: value.toInt()));
                                  },
                                ),
                              ),
                            ),
                            3.width,
                            Container(
                              height: 44,
                              width: 44,
                              alignment: Alignment.center,
                              decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: AppColors.messageColor),
                              child: Image.asset(
                                AppAsset.purpleMicIcon,
                                width: 20,
                                color: AppColors.white,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Positioned(
                        bottom: 20,
                        right: 70,
                        child: Text(
                          formatTime(position),
                          style: AppFontStyle.fontStyleW600(
                              fontColor: AppColors.messageColor, fontSize: 9),
                        ),
                      ),
                    ],
                  ),
                ),
                GetBuilder<ChatDetailController>(
                  builder: (logic) {
                    return Text(
                      logic.formatTimeFromDate(widget.chat.date),
                      style: AppFontStyle.fontStyleW600(
                          fontColor: AppColors.white, fontSize: 8),
                    );
                  },
                ),
              ],
            ),
          ),
        ],
      ).paddingOnly(bottom: 15),
    );
  }
}

class ChatImageWidget extends StatelessWidget {
  final OldChat msg;
  final ChatDetailController controller;

  const ChatImageWidget({
    super.key,
    required this.msg,
    required this.controller,
  });

  @override
  Widget build(BuildContext context) {
    final isSender =
        msg.senderId == Database.getUserProfileResponseModel?.user?.id;

    return Row(
      crossAxisAlignment: CrossAxisAlignment.end,
      mainAxisAlignment:
          isSender ? MainAxisAlignment.end : MainAxisAlignment.start,
      children: [
        if (!isSender) ...[
          Container(
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppColors.grey.withValues(alpha: 0.5),
              ),
              child: Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.grey.withValues(alpha: 0.5),
                ),
                child: Container(
                  // clipBehavior: Clip.hardEdge,
                  height: 36,
                  width: 36,
                  decoration: BoxDecoration(
                    border: Border.all(color: AppColors.white, width: 1),
                    shape: BoxShape.circle,
                  ),
                  child: ClipOval(
                    child: CustomProfileImage(
                        image: controller.profileImage.toString()),
                  ),
                ).paddingAll(1),
              )).paddingOnly(bottom: 17),
          const SizedBox(width: 6),
        ],
        Container(
          margin: const EdgeInsets.symmetric(vertical: 7),
          child: Column(
            crossAxisAlignment: msg.senderId == Database.loginUserId
                ? CrossAxisAlignment.end
                : CrossAxisAlignment.start,
            children: [
              GestureDetector(
                onTap: () {
                  Get.to(() =>
                      FullScreenImageView(imageUrl: msg.image.toString()));
                },
                child: Container(
                  width: 150,
                  height: 200,
                  decoration:
                      BoxDecoration(borderRadius: BorderRadius.circular(16)),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(16),
                    child: SendMessageImage(
                        image: "msg.image.toString()", fit: BoxFit.cover),
                  ),
                ),
              ),
              const SizedBox(height: 4),
              SizedBox(
                width: 150,
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    const Spacer(),
                    Text(
                      controller.formatTimeFromDate(msg.date),
                      style: AppFontStyle.fontStyleW500(
                          fontSize: 8, fontColor: AppColors.black),
                    ).paddingOnly(right: 2),
                  ],
                ),
              )
            ],
          ),
        ),
        if (isSender) ...[
          const SizedBox(width: 6),
          Container(
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppColors.grey.withValues(alpha: 0.5),
              ),
              child: Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.grey.withValues(alpha: 0.5),
                ),
                child: Container(
                  // clipBehavior: Clip.hardEdge,
                  height: 36,
                  width: 36,
                  decoration: BoxDecoration(
                    border: Border.all(color: AppColors.white, width: 1),
                    shape: BoxShape.circle,
                  ),
                  child: ClipOval(
                    child: CustomProfileImage(
                        image: Database
                                .getUserProfileResponseModel?.user?.profileImage
                                .toString() ??
                            ''),
                  ),
                ).paddingAll(1),
              )).paddingOnly(bottom: 11),
        ],
      ],
    );
  }
}

/// image full screen view
class FullScreenImageView extends StatelessWidget {
  final String imageUrl;

  const FullScreenImageView({super.key, required this.imageUrl});

  @override
  Widget build(BuildContext context) {
    return DismissiblePage(
      direction: DismissiblePageDismissDirection.down, // drag down to dismiss
      onDismissed: () => Get.back(), // go back on dismiss
      isFullScreen: true, // optional, ensures it covers the full screen
      child: Scaffold(
        backgroundColor: Colors.black,
        body: Stack(
          children: [
            Center(
              child: InteractiveViewer(
                child: SendMessageImageFullScreen(
                  image: imageUrl,
                  fit: BoxFit.contain,
                ),
              ),
            ),
            Positioned(
              top: 40,
              right: 20,
              child: GestureDetector(
                onTap: () => Get.back(),
                child: const CircleAvatar(
                  backgroundColor: Colors.black54,
                  child: Icon(Icons.close, color: Colors.white),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class ReportBottomSheet extends StatelessWidget {
  final Function()? submitOnTap;
  const ReportBottomSheet({super.key, this.submitOnTap});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ChatDetailController>(
      // id: Constant.idReportReason,
      init: ChatDetailController(),
      builder: (controller) {
        return Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(28),
            color: AppColors.white,
          ),
          // height: Get.height * 0.8,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              // Header
              Row(
                children: [
                  GestureDetector(
                    onTap: () {
                      Get.back();
                    },
                    child: Container(
                      height: 44,
                      width: 44,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: AppColors.categoriesBgColor,
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(7),
                        child: Image.asset(
                          AppAsset.backArrowIcon,
                          color: AppColors.black,
                        ),
                      ),
                    ),
                  ).paddingOnly(right: 18),
                  Text(
                    EnumLocale.txtReportThisAds.name.tr,
                    style: AppFontStyle.fontStyleW500(
                        fontSize: 22, fontColor: AppColors.black),
                  ).paddingOnly(right: 44),
                  Spacer(),
                ],
              ).paddingOnly(bottom: 24, left: 16, right: 16, top: 18),

              Flexible(
                child: ListView.builder(
                  shrinkWrap: true,
                  itemCount: controller.reportReasonList.length +
                      1, // +1 for Other Reason
                  itemBuilder: (_, index) {
                    bool isOther = index == controller.reportReasonList.length;

                    return Padding(
                      padding: const EdgeInsets.symmetric(vertical: 6),
                      child: InkWell(
                        onTap: () {
                          if (isOther) {
                            controller.toggleOtherSelection();
                          } else {
                            controller.toggleSelection(index);
                          }
                        },
                        borderRadius: BorderRadius.circular(12),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 20),
                          decoration: BoxDecoration(
                            border: Border.all(
                                color: AppColors.borderColor
                                    .withValues(alpha: 0.4)),
                            color: isOther
                                ? (controller.isOtherSelected
                                    ? AppColors.lightRed100
                                    : AppColors.reportAdContainer)
                                : (controller.selectedReasons.contains(index)
                                    ? AppColors.lightRed100
                                    : AppColors.reportAdContainer),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  isOther
                                      ? "Other Reason"
                                      : (controller
                                              .reportReasonList[index].title ??
                                          ''),
                                  style: AppFontStyle.fontStyleW400(
                                    fontSize: 16,
                                    fontColor: isOther
                                        ? (controller.isOtherSelected
                                            ? AppColors.appRedColor
                                            : AppColors.searchText)
                                        : (controller.selectedReasons
                                                .contains(index)
                                            ? AppColors.appRedColor
                                            : AppColors.searchText),
                                  ),
                                ),
                              ),
                              Container(
                                height: 21,
                                width: 21,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: isOther
                                        ? (controller.isOtherSelected
                                            ? AppColors.appRedColor
                                            : AppColors.grey300
                                                .withValues(alpha: 0.5))
                                        : (controller.selectedReasons
                                                .contains(index)
                                            ? AppColors.appRedColor
                                            : AppColors.grey300
                                                .withValues(alpha: 0.5)),
                                  ),
                                ),
                                child: (isOther
                                        ? controller.isOtherSelected
                                        : controller.selectedReasons
                                            .contains(index))
                                    ? Center(
                                        child: Container(
                                          decoration: const BoxDecoration(
                                            color: Colors.red,
                                            shape: BoxShape.circle,
                                          ),
                                        ).paddingAll(0.6),
                                      )
                                    : null,
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ).paddingOnly(left: 16, right: 16),
              ),

              8.height,
              // TextField and Buttons
              Container(
                decoration: BoxDecoration(
                  color: AppColors.white,
                  boxShadow: [
                    BoxShadow(
                      offset: Offset(0, -2),
                      color: AppColors.black.withValues(alpha: 0.1),
                      spreadRadius: 0,
                      blurRadius: 14,
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (controller.isOtherSelected) ...[
                      8.height,
                      Text(
                        EnumLocale.txtWriteHere.name.tr,
                        style: AppFontStyle.fontStyleW500(
                            fontSize: 13,
                            fontColor: AppColors.popularProductText),
                      ).paddingOnly(top: 13, bottom: 8),
                      TextField(
                        controller: controller.reasonController,
                        maxLines: 3,
                        decoration: InputDecoration(
                          filled: true,
                          fillColor: AppColors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide:
                                BorderSide(color: AppColors.borderColor),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderSide:
                                BorderSide(color: AppColors.borderColor),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide:
                                BorderSide(color: AppColors.borderColor),
                          ),
                        ),
                      ).paddingOnly(bottom: 17),
                    ],

                    // Buttons
                    Row(
                      children: [
                        Expanded(
                          child: PrimaryAppButton(
                            height: 52,
                            fontColor: AppColors.appRedColor,
                            color: AppColors.lightRed100,
                            text: EnumLocale.txtCancel.name.tr,
                          ),
                        ),
                        14.width,
                        Expanded(
                          child: PrimaryAppButton(
                            // onTap: () {
                            //   controller.adReportUserApi();
                            // },

                            onTap: submitOnTap,
                            height: 52,
                            text: EnumLocale.txtSubmit.name.tr,
                          ),
                        ),
                      ],
                    ).paddingOnly(bottom: 10, top: 17)
                  ],
                ).paddingOnly(left: 16, right: 16),
              ),
            ],
          ),
        );
      },
    );
  }
}
