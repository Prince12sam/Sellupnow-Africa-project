import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/support_ticket_screen/api/support_ticket_api.dart';
import 'package:listify/ui/support_ticket_screen/model/support_ticket_response_model.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class SupportTicketController extends GetxController {
  // ── List state ──────────────────────────────────────────────────────────
  bool isLoading = false;
  List<SupportTicketItem> tickets = [];

  // ── Detail state ─────────────────────────────────────────────────────────
  bool isDetailLoading = false;
  SupportTicketDetail? currentTicket;
  final replyController = TextEditingController();
  bool isSendingReply = false;

  // ── Create form ──────────────────────────────────────────────────────────
  bool isCreating = false;
  final createFormKey = GlobalKey<FormState>();
  final subjectController = TextEditingController();
  final messageController = TextEditingController();
  final issueTypeController = TextEditingController();

  @override
  void onInit() {
    super.onInit();
    SupportTicketApi.startPagination = 0;
    fetchTickets();
  }

  @override
  void onClose() {
    replyController.dispose();
    subjectController.dispose();
    messageController.dispose();
    issueTypeController.dispose();
    super.onClose();
  }

  // ── List ──────────────────────────────────────────────────────────────────
  Future<void> fetchTickets() async {
    isLoading = true;
    update();
    final result = await SupportTicketApi.fetchList();
    if (result != null) {
      tickets.clear();
      tickets.addAll(result.data ?? []);
    }
    isLoading = false;
    update();
  }

  Future<void> onRefresh() async {
    SupportTicketApi.startPagination = 0;
    tickets.clear();
    await fetchTickets();
  }

  // ── Detail ────────────────────────────────────────────────────────────────
  Future<void> openTicket(SupportTicketItem item) async {
    Get.toNamed(AppRoutes.supportTicketDetailScreen, arguments: item.id);
  }

  Future<void> fetchDetail(int id) async {
    isDetailLoading = true;
    currentTicket = null;
    update();
    final result = await SupportTicketApi.fetchDetail(id);
    currentTicket = result?.data;
    isDetailLoading = false;
    update();
  }

  Future<void> sendReply(int ticketId) async {
    final msg = replyController.text.trim();
    if (msg.isEmpty) return;

    isSendingReply = true;
    update();

    final ok = await SupportTicketApi.sendReply(ticketId, msg);
    if (ok) {
      replyController.clear();
      await fetchDetail(ticketId);
    } else {
      Get.snackbar('Error', 'Could not send reply.',
          backgroundColor: Colors.red, colorText: Colors.white);
    }

    isSendingReply = false;
    update();
  }

  // ── Create ────────────────────────────────────────────────────────────────
  Future<void> createTicket() async {
    if (!createFormKey.currentState!.validate()) return;

    isCreating = true;
    update();

    final ok = await SupportTicketApi.createTicket(
      subject: subjectController.text.trim(),
      message: messageController.text.trim(),
      issueType: issueTypeController.text.trim(),
      email: Database.getUserProfileResponseModel?.user?.email,
    );

    isCreating = false;
    update();

    if (ok) {
      subjectController.clear();
      messageController.clear();
      issueTypeController.clear();
      Get.back();
      Get.snackbar('Success', 'Ticket created successfully.',
          backgroundColor: Colors.green, colorText: Colors.white);
      SupportTicketApi.startPagination = 0;
      tickets.clear();
      await fetchTickets();
    } else {
      Get.snackbar('Error', 'Failed to create ticket.',
          backgroundColor: Colors.red, colorText: Colors.white);
    }
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  String formatDate(String? iso) {
    if (iso == null || iso.isEmpty) return '';
    try {
      return DateFormat('dd MMM yyyy, hh:mm a')
          .format(DateTime.parse(iso).toLocal());
    } catch (_) {
      return iso;
    }
  }

  Color statusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'open':
      case 'pending':
        return Colors.orange;
      case 'confirm':
        return Colors.blue;
      case 'completed':
        return Colors.green;
      case 'cancel':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  bool isMyMessage(TicketMessage msg) {
    final myId = Database.getUserProfileResponseModel?.user?.id;
    return msg.senderId != null && msg.senderId.toString() == myId?.toString();
  }
}
