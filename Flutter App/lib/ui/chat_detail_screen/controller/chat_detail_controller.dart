import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:listify/socket/socket_emit.dart';
import 'package:listify/ui/chat_detail_screen/api/chat_history_api.dart';
import 'package:listify/ui/chat_detail_screen/api/give_review_api.dart';
import 'package:listify/ui/chat_detail_screen/api/report_user_api.dart';
import 'package:listify/ui/chat_detail_screen/api/send_image_audio_api.dart';
import 'package:listify/ui/chat_detail_screen/api/user_block_api.dart';
import 'package:listify/ui/chat_detail_screen/model/chat_history_model.dart';
import 'package:listify/ui/chat_detail_screen/model/send_image_audio_model.dart';
import 'package:listify/ui/message_screen/controller/message_screen_controller.dart';
import 'package:listify/ui/product_detail_screen/api/report_reasons_api.dart';
import 'package:listify/ui/product_detail_screen/model/report_reasons_model.dart';
import 'package:listify/ui/product_detail_screen/model/safety_tips_response_model.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/socket_params.dart';
import 'package:listify/utils/utils.dart';
import 'package:path_provider/path_provider.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:record/record.dart';

class ChatDetailController extends GetxController {
  /// ------------ route args / user & product ------------
  String? name;
  String? image;
  String? profileImage;
  String? adId;
  String? receiverId;
  String? productName;
  String? productPrice;
  String? primaryImage;
  String? offerAmount;
  String? chatTopic;
  String? senderId;
  String? reviewProductId;
  String? reviewOrderId;

  bool? isOnline = false;
  bool isViewed = false;

  /// ------------ chat state ------------
  final TextEditingController messageController = TextEditingController();
  final ScrollController scrollController = ScrollController();

  List<OldChat> chatOldHistory = [];
  final Set<String> _seenMsgIds = <String>{}; // de-dup by msg id

  ChatOldHistoryResponseModel? chatOldHistoryResponseModel;
  String? chatTopicId;
  String? chatRoomId;

  /// ------------ pagination state ------------
  int pageStart = 0;
  final int pageLimit = 20;
  bool hasMore = true;
  bool isPaginating = false;

  /// ------------ UI flags ------------
  bool isLoading = false;
  bool isPaginationLoading = false;
  bool isLoadingAudio = false;
  bool isLoadingImage = false;
  bool isRecordingAudio = false;
  bool isSendingAudioFile = false;

  /// ------------ audio/image ------------
  final ImagePicker imagePicker = ImagePicker();
  XFile? pickedImage;

  AudioRecorder audioRecorder = AudioRecorder();
  Timer? timer;
  int countTime = 0;

  SendImageAudioModel? sendImageAudioModel;

  /// ------------ report/review ------------
  TextEditingController offerPriceController = TextEditingController();
  TextEditingController reasonController = TextEditingController();
  TextEditingController reviewController = TextEditingController();
  var rating = 0.0.obs;

  ReportReasonsModel? reportReasonsModel;
  List<Datum> reportReasonList = [];
  List<SafetyTips> safetyTipsList = [];
  List<int> selectedReasons = [];
  bool isOtherSelected = false;

  /// ------------ arguments ------------
  Map<String, dynamic> arguments = Get.arguments ?? {};

  /// ------------ lifecycle ------------
  @override
  void onInit() {
    init();
    getReportReason();
    super.onInit();
  }

  @override
  void dispose() {
    scrollController.removeListener(onPagination);
    scrollController.dispose();
    reasonController.dispose();
    reviewController.dispose();
    offerPriceController.dispose();
    super.dispose();
  }

  /// ------------ init ------------
  Future<void> init() async {
    ChatHistoryApi.startPagination=1;
    scrollController.addListener(onPagination);

    bool toBool(dynamic v, {bool fallback = false}) {
      if (v is bool) return v;
      if (v is String) {
        final s = v.trim().toLowerCase();
        if (s == 'true') return true;
        if (s == 'false') return false;
      }
      return fallback;
    }

    name = arguments['name'];
    image = arguments['image'];
    profileImage = arguments['profileImage'];
    adId = arguments['adId'];
    chatTopic = arguments['chatTopic'];
    receiverId = arguments['receiverId'];
    productName = arguments['productName'];
    senderId = arguments['senderId'];
    reviewProductId = (arguments['productId'] ?? arguments['product_id'] ?? arguments['adId'])?.toString();
    reviewOrderId = (arguments['orderId'] ?? arguments['order_id'])?.toString();
    productPrice = arguments['productPrice']?.toString();
    primaryImage = arguments['primaryImage']?.toString();

    isOnline = toBool(arguments['isOnline'], fallback: false);
    isViewed = toBool(arguments['isViewed'], fallback: true);

    Utils.showLog(
        "isViewed = $isViewed | senderId = $senderId | receiverId = $receiverId |||||  chatTopic   $chatTopic");

    if ((receiverId ?? '').isNotEmpty) {
      // Reset pagination
      chatRoomId = null;
      chatOldHistory.clear();
      _seenMsgIds.clear();
      pageStart = 0;
      hasMore = true;

      isLoading = true;
      update([Constant.idGetOldChat]);

      await getOldChats(); // first page

      isLoading = false;
      update([Constant.idGetOldChat]);
    }



    Utils.showLog("isViewed>>>>>>>>>>>>>>${isViewed}");

    // Product-top message emit once when opening (if not viewed yet)
    if (isViewed == false) {
      final messageValue = {
        "productName": productName,
        "productPrice": productPrice,
        "productImage": primaryImage,
        "message": 1,
      };

      final String finalPayload =
          messageValue.entries.map((e) => "${e.key}: ${e.value}").join(", ");

      final messageData = {
        SocketParams.chatTopicId: chatTopicId,
        SocketParams.senderId: Database.getUserProfileResponseModel?.user?.id,
        SocketParams.receiverId: receiverId,
        SocketParams.message: finalPayload,
        SocketParams.messageType: 5,
        SocketParams.view: true,
        SocketParams.adId: adId,
        SocketParams.price: productPrice,
        SocketParams.title: productName,
        SocketParams.primaryImage: primaryImage,
        SocketParams.date:
            DateFormat('M/d/yyyy, h:mm:ss a').format(DateTime.now()),
      };

      // optimistic insert
      chatOldHistory.insert(
        0,
        OldChat(
          id: DateTime.now().millisecondsSinceEpoch.toString(),
          message: finalPayload,
          date: DateFormat('M/d/yyyy, h:mm:ss a').format(DateTime.now()),
          messageType: 5,
          senderId: Database.getUserProfileResponseModel?.user?.id,
        ),
      );
      update([Constant.idGetOldChat]);

      SocketEmit.sendMessage(messageData);
    }
  }

  /// ------------ pagination fetch ------------

  // getOldChats() async {
  //   update([Constant.idGetOldChat]);
  //
  //   final prevPage = ChatHistoryApi.startPagination;
  //
  //   final response = await ChatHistoryApi.callApi(
  //     adId: adId ?? '',
  //     receiverId: receiverId.toString(),
  //   );
  //
  //   final newChats = response?.chat ?? [];
  //
  //   Utils.showLog(
  //       "🧩 Pagination => Page: ${ChatHistoryApi.startPagination}, New Data: ${newChats.length}");
  //
  //   if (newChats.isNotEmpty) {
  //     final existingIds = chatOldHistory.map((e) => e.id).toSet();
  //     final filteredChats =
  //         newChats.where((e) => !existingIds.contains(e.id)).toList();
  //
  //     if (filteredChats.isNotEmpty) {
  //       chatOldHistory.addAll(filteredChats);
  //       ChatHistoryApi.startPagination++;
  //     } else {
  //       ChatHistoryApi.startPagination = prevPage;
  //       Utils.showLog("⚠️ Duplicate data detected — page not incremented");
  //     }
  //
  //     print("response?.chatTopic>>>>>>>>>>>>>>>${response?.chatTopic}");
  //
  //     chatTopicId = response?.chatTopic;
  //   } else {
  //     ChatHistoryApi.startPagination = prevPage;
  //     Utils.showLog("🚫 No more chats found — pagination stopped");
  //   }
  //
  //   update([Constant.idGetOldChat]);
  //
  //   if (chatRoomId == null) {
  //     chatRoomId = response?.chatTopic;
  //     if (chatOldHistory.isNotEmpty) {
  //       SocketEmit.seenMessage({
  //         SocketParams.messageId: chatOldHistory.first.id ?? '',
  //         SocketParams.senderId:
  //             Database.getUserProfileResponseModel?.user?.id ?? Database.loginUserId,
  //       });
  //       onScrollDown();
  //     }
  //   }
  // }

  getOldChats() async {
    update([Constant.idGetOldChat]);
    Utils.showLog("📥 Fetching old chats... Current Page => ${ChatHistoryApi.startPagination}");

    final prevPage = ChatHistoryApi.startPagination;

    final response = await ChatHistoryApi.callApi(
      adId: adId ?? '',
      receiverId: receiverId.toString(),
    );

    if (response == null) {
      Utils.showLog("❌ ChatHistoryApi returned null response");
      ChatHistoryApi.startPagination = prevPage;
      update([Constant.idGetOldChat]);
      return;
    }

    // ✅ Safely extract chatTopic
    if (response.chatTopic != null && response.chatTopic!.isNotEmpty) {
      chatTopicId = response.chatTopic!;
      Utils.showLog("🧩 Chat Topic ID Saved => $chatTopicId");
    } else {
      Utils.showLog("⚠️ No chatTopic found in response");
    }

    final newChats = response.chat ?? [];

    Utils.showLog(
      "🧾 Chat Page => ${ChatHistoryApi.startPagination}, New Chats => ${newChats.length}",
    );

    if (newChats.isNotEmpty) {
      // Prevent duplicates using message ID
      final existingIds = chatOldHistory.map((e) => e.id).toSet();
      final filteredChats =
      newChats.where((e) => !existingIds.contains(e.id)).toList();

      if (filteredChats.isNotEmpty) {
        chatOldHistory.addAll(filteredChats);
        ChatHistoryApi.startPagination++;
        Utils.showLog("✅ Added ${filteredChats.length} new chat(s). Next Page => ${ChatHistoryApi.startPagination}");
      } else {
        ChatHistoryApi.startPagination = prevPage;
        Utils.showLog("⚠️ Duplicate chats detected — page not incremented");
      }
    } else {
      ChatHistoryApi.startPagination = prevPage;
      Utils.showLog("🚫 No chats found for this page");
    }

    update([Constant.idGetOldChat]);

    // ✅ Assign chatRoomId only once (when it's null)
    if (chatRoomId == null) {
      chatRoomId = chatTopicId ?? response.chatTopic;
      Utils.showLog("💬 Chat Room ID set => $chatRoomId");

      if (chatOldHistory.isNotEmpty) {
        final firstMessageId = chatOldHistory.first.id ?? '';
        final senderId = Database.getUserProfileResponseModel?.user?.id ?? Database.loginUserId;

        SocketEmit.seenMessage({
          SocketParams.messageId: firstMessageId,
          SocketParams.senderId: senderId,
        });

        onScrollDown();
        Utils.showLog("👁️ Seen message emitted for ID: $firstMessageId");
      }
    }
  }


  /// ------------ scroll listener (load older) ------------
  Future<void> onPagination() async {
    if (scrollController.position.pixels ==
        scrollController.position.minScrollExtent) {
      isPaginationLoading = true;
      update([Constant.idPagination]);
      await getOldChats();
      isPaginationLoading = false;
      update([Constant.idPagination]);
    }
  }

  /// ------------ send text message ------------
  void sendMessage() {
    String message = messageController.text;
    Utils.showLog('chat topic id ::::>>> $chatTopicId');

    if (message.isEmpty) return;

    if (containsDangerousScript(message)) {
      Utils.showToast(
          Get.context!, "Script tags are not allowed in the message.");
      messageController.clear();
      return;
    }

    message = sanitizeUserInput(message);

    if (message.length > 1000) {
      Utils.showToast(Get.context!, "Message too long. Max 1000 characters.");
      return;
    }

    final tempId = DateTime.now().millisecondsSinceEpoch.toString();

    // optimistic
    final optimistic = OldChat(
      id: tempId,
      message: message,
      date: DateFormat('M/d/yyyy, h:mm:ss a').format(DateTime.now()),
      messageType: 1,
      senderId: Database.getUserProfileResponseModel?.user?.id,
    );

    chatOldHistory.insert(0, optimistic);
    _seenMsgIds.add(tempId);

    update([Constant.idGetOldChat]);
    onScrollDown();

    final messageData = {
      SocketParams.chatTopicId: chatTopicId ?? "",
      SocketParams.senderId: Database.getUserProfileResponseModel?.user?.id,
      SocketParams.receiverId: receiverId,
      SocketParams.message: message,
      SocketParams.messageType: 1,
      SocketParams.adId: adId,
      SocketParams.price: productPrice,
      SocketParams.title: productName,
      SocketParams.primaryImage: primaryImage,
      SocketParams.view: true,
      SocketParams.isOnline: isOnline ?? false,
      SocketParams.date:
          DateFormat('M/d/yyyy, h:mm:ss a').format(DateTime.now()),
    };


    Utils.showLog('message sent ::::::::: $messageData');

    SocketEmit.sendMessage(messageData);


    messageController.clear();
    update([Constant.idSendMsg]);
  }

  /// ------------ text sanitation helpers ------------
  String sanitizeUserInput(String input) {
    if (input.isEmpty) return input;

    try {
      // Remove dangerous HTML/JavaScript while preserving emojis and Unicode
      String sanitized = input
          // Remove script tags
          .replaceAll(
              RegExp(r'<script[^>]*>.*?</script>',
                  caseSensitive: false, multiLine: true, dotAll: true),
              '')
          // Remove javascript: protocol
          .replaceAll(RegExp(r'javascript\s*:', caseSensitive: false), '')
          // Remove other script-related content
          .replaceAll(
              RegExp(r'<iframe[^>]*>.*?</iframe>',
                  caseSensitive: false, multiLine: true, dotAll: true),
              '')
          .replaceAll(
              RegExp(r'<object[^>]*>.*?</object>',
                  caseSensitive: false, multiLine: true, dotAll: true),
              '')
          .replaceAll(RegExp(r'<embed[^>]*>', caseSensitive: false), '')
          // Clean up extra whitespace
          .replaceAll(RegExp(r'\s+'), ' ')
          .trim();

      debugPrint("🧹 Sanitization: '$input' -> '$sanitized'");
      return sanitized;
    } catch (e) {
      debugPrint("❌ Sanitization error: $e");
      // Return original input if sanitization fails
      return input.trim();
    }
  }

// Emoji-safe dangerous script detection
  bool containsDangerousScript(String message) {
    if (message.isEmpty) return false;

    try {
      // List of dangerous patterns that won't interfere with emojis
      final List<RegExp> dangerousPatterns = [
        // Script tags
        RegExp(r'<script[^>]*>', caseSensitive: false),
        RegExp(r'</script>', caseSensitive: false),

        // JavaScript protocol
        RegExp(r'javascript\s*:', caseSensitive: false),

        // Event handlers
        RegExp(
            r'on(click|load|error|focus|blur|change|submit|keydown|keyup|mouseover|mouseout|resize)\s*=',
            caseSensitive: false),

        // Other dangerous tags
        RegExp(r'<iframe[^>]*>', caseSensitive: false),
        RegExp(r'<object[^>]*>', caseSensitive: false),
        RegExp(r'<embed[^>]*>', caseSensitive: false),
        RegExp(r'<form[^>]*>', caseSensitive: false),

        // Data URLs with JavaScript
        RegExp(r'data\s*:\s*text/html', caseSensitive: false),

        // Expression() CSS
        RegExp(r'expression\s*\(', caseSensitive: false),

        // vbscript protocol
        RegExp(r'vbscript\s*:', caseSensitive: false),
      ];

      // Check each pattern
      for (RegExp pattern in dangerousPatterns) {
        if (pattern.hasMatch(message)) {
          debugPrint("⚠️ Dangerous pattern found: ${pattern.pattern}");
          return true;
        }
      }

      return false;
    } catch (e) {
      debugPrint("❌ Script detection error: $e");
      // Be conservative - if we can't check, assume it might be dangerous
      return message.contains('<') && message.contains('>');
    }
  }

  /// ------------ scroll to bottom ------------

  Future<void> onScrollDown() async {
    try {
      await 10.milliseconds.delay();
      scrollController.animateTo(
        scrollController.position.maxScrollExtent,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeOut,
      );
    } catch (e) {
      Utils.showLog("Scroll Down Failed => $e");
    }
  }

  /// ------------ audio recording/send ------------
  Future<void> onStartAudioRecording() async {
    Utils.showLog("Audio Recording Start");
    Directory appDocDir = await getApplicationDocumentsDirectory();
    String filePath =
        "${appDocDir.path}/audio_${DateTime.now().millisecondsSinceEpoch}.mp4";

    await audioRecorder.start(const RecordConfig(), path: filePath);

    isRecordingAudio = true;
    update([Constant.idChangeAudioRecordingEvent]);
    onChangeTimer();
  }

  Future<void> onLongPressStartMic() async {
    FocusManager.instance.primaryFocus?.unfocus();
    PermissionStatus status = await Permission.microphone.status;

    if (status.isDenied) {
      PermissionStatus request = await Permission.microphone.request();
      if (request == PermissionStatus.denied) {
        Utils.showToast(
            Get.context!, EnumLocale.txtPleaseAllowPermission.name.tr);
      }
    } else {
      onStartAudioRecording();
    }
  }

  Future<void> onLongPressEndMic() async {
    PermissionStatus status = await Permission.microphone.status;
    if (isRecordingAudio && status.isGranted) {
      onStopAudioRecording();
    }
  }

  Future<void> onStopAudioRecording() async {
    try {
      Utils.showLog("Audio Recording Stop");

      isLoadingAudio = true;
      isSendingAudioFile = true;

      final audioPath = await audioRecorder.stop();

      isRecordingAudio = false;
      update([Constant.idChangeAudioRecordingEvent]);
      onChangeTimer();

      if (audioPath != null) {
        final tempId = DateTime.now().millisecondsSinceEpoch.toString();

        // optimistic audio bubble
        chatOldHistory.insert(
          0,
          OldChat(
            id: tempId,
            messageType: 3,
            createdAt: DateTime.now(),
            senderId: Database.getUserProfileResponseModel?.user?.id,
            audio: audioPath,
          ),
        );
        isLoadingAudio = true;
        update([Constant.idGetOldChat]);
        onScrollDown();

        // Upload
        sendImageAudioModel = await SendImageAudioApi.callApi(
          adId: adId.toString(),
          receiverId: receiverId.toString(),
          messageType: 3,
          filePath: audioPath,
        );

        // Replace optimistic with actual
        final index = chatOldHistory.indexWhere((c) => c.id == tempId);
        if (index != -1 && sendImageAudioModel?.chat?.audio != null) {
          chatOldHistory[index] = OldChat(
            id: sendImageAudioModel?.chat?.id,
            audio: sendImageAudioModel?.chat?.audio,
            date: formatTime(),
            messageType: 3,
            senderId: Database.getUserProfileResponseModel?.user?.id,
          );

          // socket emit
          final messageData = {
            SocketParams.chatTopicId:
                chatTopicId ?? "",
            SocketParams.senderId:
                Database.getUserProfileResponseModel?.user?.id,
            SocketParams.receiverId: receiverId,
            SocketParams.message: sendImageAudioModel?.chat?.message ?? '',
            SocketParams.messageType: 3,
            SocketParams.audio: sendImageAudioModel?.chat?.audio,
            SocketParams.date:
                DateFormat('M/d/yyyy, h:mm:ss a').format(DateTime.now()),
          };
          SocketEmit.sendMessage(messageData);

          // mark seen
          _seenMsgIds.add(sendImageAudioModel?.chat?.id ?? '');
          update([Constant.idGetOldChat]);
        }
      }
      isSendingAudioFile = false;
      updateAudioUI();
    } catch (e) {
      isSendingAudioFile = false;
      Utils.showLog("Audio Recording Stop Failed => $e");
    }
  }

  Future<void> updateAudioUI() async {
    await Future.delayed(const Duration(milliseconds: 100));
    update([Constant.idChangeAudioRecordingEvent]);
  }

  Future<void> onChangeTimer() async {
    if (isRecordingAudio && countTime == 0) {
      timer = Timer.periodic(
        const Duration(seconds: 1),
        (timer) async {
          countTime++;
          update([Constant.idChangeAudioRecordingEvent]);
          if (isRecordingAudio == false) {
            countTime = 0;
            this.timer?.cancel();
            update([Constant.idChangeAudioRecordingEvent]);
          }
        },
      );
    } else {
      countTime = 0;
      timer?.cancel();
      update([Constant.idChangeAudioRecordingEvent]);
    }
  }

  /// ------------ image send ------------
  Future<void> sendImageMessage() async {
    if (pickedImage == null || receiverId == null) {
      Utils.showToast(
          Get.context!, "No image selected or receiver ID is missing");
      return;
    }
    try {
      final sendImageAudioModel = await SendImageAudioApi.callApi(
        messageType: 2,
        adId: adId ?? '',
        receiverId: receiverId ?? '',
        imagePath: pickedImage!.path,
      );

      if (sendImageAudioModel != null && sendImageAudioModel.chat != null) {
        final messageData = {
          SocketParams.chatTopicId:
              chatTopicId ?? '',
          SocketParams.senderId: Database.getUserProfileResponseModel?.user?.id,
          SocketParams.receiverId: receiverId,
          SocketParams.message: sendImageAudioModel.chat?.message ?? '',
          SocketParams.image: sendImageAudioModel.chat?.image ?? '',
          SocketParams.messageType: 2,
          SocketParams.date:
              DateFormat('M/d/yyyy, h:mm:ss a').format(DateTime.now()),
        };

        SocketEmit.sendMessage(messageData);
        pickedImage = null;
        onScrollDown();
        update();
      } else {
        Utils.showToast(Get.context!, "Failed to send image.");
      }
    } catch (e) {
      Utils.showToast(Get.context!, "Error sending image: $e");
      Utils.showLog("Error in sendImageMessage: $e");
    }
  }

  Future<bool> pickImageFromCamera() async {
    pickedImage = await imagePicker.pickImage(
        source: ImageSource.camera, imageQuality: 100);
    update();
    return pickedImage != null;
  }

  Future<bool> pickImageFromGallery() async {
    pickedImage = await imagePicker.pickImage(
        source: ImageSource.gallery, imageQuality: 100);
    update();
    return pickedImage != null;
  }

  Future<void> showImagePickerDialog() async {
    Get.defaultDialog(
      backgroundColor: AppColors.white,
      title: EnumLocale.changeYourImage.name.tr,
      titlePadding: const EdgeInsets.only(top: 30),
      titleStyle: AppFontStyle.fontStyleW700(
          fontSize: 16, fontColor: AppColors.darkGrey),
      content: Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 8),
            child: Divider(thickness: 1, color: Colors.grey.shade100),
          ),
          GestureDetector(
            onTap: () async {
              Get.back();
              if (await pickImageFromCamera()) await sendImageMessage();
            },
            child: Container(
              height: 60,
              decoration: BoxDecoration(borderRadius: BorderRadius.circular(8)),
              child: Row(
                children: [
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Image(
                        color: AppColors.darkGrey,
                        image: AssetImage(AppAsset.cameraFlipIcon),
                        height: 20),
                  ),
                  Text(
                    EnumLocale.txtTakeAphoto.name.tr,
                    style: AppFontStyle.fontStyleW700(
                        fontSize: 15, fontColor: AppColors.darkGrey),
                  )
                ],
              ),
            ),
          ),
          GestureDetector(
            onTap: () async {
              Get.back();
              if (await pickImageFromGallery()) await sendImageMessage();
            },
            child: Container(
              height: 60,
              decoration: BoxDecoration(borderRadius: BorderRadius.circular(8)),
              child: Row(
                children: [
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Image(
                        color: AppColors.darkGrey,
                        image: AssetImage(AppAsset.messageIcon),
                        height: 20),
                  ),
                  Text(
                    EnumLocale.txtChooseFromYourFile.name.tr,
                    style: AppFontStyle.fontStyleW700(
                        fontSize: 15, fontColor: AppColors.darkGrey),
                  )
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  /// ------------ format time helpers ------------
  String formatTime() {
    try {
      return DateFormat('hh:mm a').format(DateTime.now());
    } catch (e) {
      Utils.showLog("Error in format time :: $e");
      return "";
    }
  }

  String formatTimeFromDate(String? rawDate) {
    if (rawDate == null || rawDate.isEmpty) return '';
    try {
      final parsedDate = DateFormat('M/d/yyyy, hh:mm:ss a').parse(rawDate);
      return DateFormat('hh:mm a').format(parsedDate);
    } catch (e) {
      return '';
    }
  }

  /// ------------ block/report/review ------------
  Future<void> blockApi() async {
    final response = await BlockUserApi.toggleBlockUser(
      blockedId: receiverId ?? "",
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
    );

    if (response != null) {
      Get.back();
      Get.find<MessageScreenController>().init();
      Utils.showToast(
          Get.context!,
          response.message ??
              (response.status == true
                  ? "User Blocked Successfully"
                  : "User Block Failed"));
    } else {
      Utils.showToast(Get.context!, "No Response from server");
    }
  }

  Future<void> getReportReason() async {
    isLoading = true;
    update([Constant.idReportReason]);
    reportReasonsModel = await ReportReasonsApi.callApi();
    reportReasonList
      ..clear()
      ..addAll(reportReasonsModel?.data ?? []);
    isLoading = false;
    update([Constant.idReportReason]);
  }

  void toggleOtherSelection() {
    isOtherSelected = !isOtherSelected;
    update();
  }

  void toggleSelection(int index) {
    if (selectedReasons.contains(index)) {
      selectedReasons.remove(index);
    } else {
      selectedReasons.add(index);
    }
    update();
  }

  ///----------------user report api-------------------
  Future<void> reportUserApi() async {
    // titles
    final selectedTitles = selectedReasons
        .map((i) => reportReasonList[i].title ?? "")
        .where((t) => t.isNotEmpty)
        .toList();

    final extraReason = reasonController.text.trim();

    String finalReason = "";
    if (selectedTitles.isNotEmpty && extraReason.isNotEmpty) {
      finalReason = "${selectedTitles.join(", ")} | $extraReason";
    } else if (selectedTitles.isNotEmpty) {
      finalReason = selectedTitles.join(", ");
    } else if (extraReason.isNotEmpty) {
      finalReason = extraReason;
    }

    if (finalReason.isEmpty) {
      Utils.showToast(Get.context!, "Please select or enter a reason ❌");
      return;
    }

    final result = await ReportUserApi.reportUser(
      reportedUserId: receiverId ?? "",
      reason: finalReason,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
    );

    if (result != null && result.status == true) {
      Utils.showToast(
          Get.context!, result.message ?? "Ad reported successfully");
      Get.back();
    } else {
      Utils.showToast(Get.context!, result?.message ?? "Failed to report Ad");
      Get.back();
    }
  }
  ///---------------------get review api-----------------------

  Future<void> giveReview() async {
    final trimmedReview = reviewController.text.trim();
    final normalizedProductId = (reviewProductId ?? '').trim();
    final normalizedOrderId = (reviewOrderId ?? '').trim();

    if (rating.value < 1) {
      Utils.showToast(Get.context!, 'Please select a star rating.');
      return;
    }

    if (trimmedReview.isEmpty) {
      Utils.showToast(Get.context!, 'Please enter a review.');
      return;
    }

    if (normalizedProductId.isEmpty || normalizedOrderId.isEmpty) {
      Utils.showToast(
        Get.context!,
        'Review submission is only available when the product and order details are present.',
      );
      Utils.showLog(
        'Review submit blocked: missing product/order context. productId=$normalizedProductId orderId=$normalizedOrderId adId=$adId receiverId=$receiverId',
      );
      return;
    }

    final response = await GiveReviewApi.giveReview(
      revieweeId: receiverId ?? "",
      rating: rating.value,
      reviewText: trimmedReview,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
      productId: normalizedProductId,
      orderId: normalizedOrderId,
    );

    if (response != null && response.status == true) {
      Utils.showLog("Review submitted: ${response.message}");
      Utils.showToast(Get.context!, response.message ?? 'Review submitted successfully.');
      Get.back();
      reviewController.clear();
      rating.value = 0;
    } else {
      Utils.showLog("Failed to submit review: ${response?.message}");
      Utils.showToast(Get.context!, response?.message ?? 'Failed to submit review.');
    }
  }
}
