import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/ui/support_ticket_screen/controller/support_ticket_controller.dart';
import 'package:listify/ui/support_ticket_screen/model/support_ticket_response_model.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';

// ── Ticket list ────────────────────────────────────────────────────────────

class SupportTicketListBody extends StatelessWidget {
  const SupportTicketListBody({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SupportTicketController>(builder: (c) {
      if (c.isLoading) {
        return const Center(child: CircularProgressIndicator());
      }
      if (c.tickets.isEmpty) {
        return RefreshIndicator(
          color: AppColors.appRedColor,
          onRefresh: c.onRefresh,
          child: ListView(
            children: [
              SizedBox(height: MediaQuery.of(context).size.height * 0.25),
              NoDataFound(
                image: AppAsset.noHistoryFound,
                imageHeight: 150,
                text: EnumLocale.txtNoDataFound.name.tr,
              ),
            ],
          ),
        );
      }
      return RefreshIndicator(
        color: AppColors.appRedColor,
        onRefresh: c.onRefresh,
        child: ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: c.tickets.length,
          itemBuilder: (_, i) => TicketCard(
            item: c.tickets[i],
            controller: c,
          ),
        ),
      );
    });
  }
}

class TicketCard extends StatelessWidget {
  final SupportTicketItem item;
  final SupportTicketController controller;
  const TicketCard({super.key, required this.item, required this.controller});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => controller.openTicket(item),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
                color: AppColors.black.withOpacity(0.07),
                blurRadius: 8,
                offset: const Offset(0, 2))
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Text(
                    item.subject ?? '',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: AppFontStyle.fontStyleW600(
                        fontSize: 14, fontColor: AppColors.black),
                  ),
                ),
                _StatusBadge(
                    status: item.status, controller: controller),
              ],
            ),
            if (item.ticketNumber != null)
              Padding(
                padding: const EdgeInsets.only(top: 4),
                child: Text(
                  '#${item.ticketNumber}',
                  style: AppFontStyle.fontStyleW400(
                      fontSize: 12,
                      fontColor: AppColors.black.withOpacity(0.5)),
                ),
              ),
            Padding(
              padding: const EdgeInsets.only(top: 6),
              child: Text(
                controller.formatDate(item.createdAt),
                style: AppFontStyle.fontStyleW400(
                    fontSize: 11,
                    fontColor: AppColors.black.withOpacity(0.4)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _StatusBadge extends StatelessWidget {
  final String? status;
  final SupportTicketController controller;
  const _StatusBadge({this.status, required this.controller});

  @override
  Widget build(BuildContext context) {
    final color = controller.statusColor(status);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        (status ?? '').toUpperCase(),
        style: TextStyle(
            fontSize: 10, fontWeight: FontWeight.w700, color: color),
      ),
    );
  }
}

// ── Ticket detail ─────────────────────────────────────────────────────────

class TicketDetailBody extends StatelessWidget {
  final SupportTicketDetail ticket;
  final SupportTicketController controller;
  const TicketDetailBody(
      {super.key, required this.ticket, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Status bar
        Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
          color: AppColors.lightWhiteColor,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                '#${ticket.ticketNumber ?? ticket.id}',
                style: AppFontStyle.fontStyleW500(
                    fontSize: 13, fontColor: AppColors.black),
              ),
              _StatusBadge(status: ticket.status, controller: controller),
            ],
          ),
        ),
        // Original message
        if (ticket.message != null)
          Container(
            width: double.infinity,
            margin: const EdgeInsets.all(12),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppColors.lightWhiteColor,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Original Message',
                  style: AppFontStyle.fontStyleW600(
                      fontSize: 12, fontColor: AppColors.black.withOpacity(0.5)),
                ),
                const SizedBox(height: 4),
                Text(
                  ticket.message!,
                  style: AppFontStyle.fontStyleW400(
                      fontSize: 13, fontColor: AppColors.black),
                ),
              ],
            ),
          ),
        // Messages
        Expanded(
          child: ListView.builder(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            itemCount: ticket.messages?.length ?? 0,
            itemBuilder: (_, i) {
              final msg = ticket.messages![i];
              final isMe = controller.isMyMessage(msg);
              return _MessageBubble(
                  message: msg, isMe: isMe, controller: controller);
            },
          ),
        ),
        // Reply input
        if (ticket.status?.toLowerCase() != 'completed' &&
            ticket.status?.toLowerCase() != 'cancel')
          _ReplyBar(ticket: ticket, controller: controller),
      ],
    );
  }
}

class _MessageBubble extends StatelessWidget {
  final TicketMessage message;
  final bool isMe;
  final SupportTicketController controller;
  const _MessageBubble(
      {required this.message, required this.isMe, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 8),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        constraints:
            BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: isMe ? AppColors.appRedColor : AppColors.lightWhiteColor,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(16),
            topRight: const Radius.circular(16),
            bottomLeft: Radius.circular(isMe ? 16 : 0),
            bottomRight: Radius.circular(isMe ? 0 : 16),
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (!isMe && message.senderName != null)
              Padding(
                padding: const EdgeInsets.only(bottom: 4),
                child: Text(
                  message.senderName!,
                  style: AppFontStyle.fontStyleW600(
                      fontSize: 11,
                      fontColor: AppColors.black.withOpacity(0.6)),
                ),
              ),
            Text(
              message.message ?? '',
              style: AppFontStyle.fontStyleW400(
                fontSize: 13,
                fontColor: isMe ? AppColors.white : AppColors.black,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              controller.formatDate(message.createdAt),
              style: TextStyle(
                fontSize: 10,
                color: isMe
                    ? AppColors.white.withOpacity(0.7)
                    : AppColors.black.withOpacity(0.4),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ReplyBar extends StatelessWidget {
  final SupportTicketDetail ticket;
  final SupportTicketController controller;
  const _ReplyBar({required this.ticket, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(
          left: 12,
          right: 8,
          top: 8,
          bottom: MediaQuery.of(context).viewInsets.bottom + 8),
      decoration: BoxDecoration(
        color: AppColors.white,
        boxShadow: [
          BoxShadow(
              color: AppColors.black.withOpacity(0.07),
              blurRadius: 8,
              offset: const Offset(0, -2))
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: TextField(
              controller: controller.replyController,
              decoration: InputDecoration(
                hintText: 'Type a message...',
                filled: true,
                fillColor: AppColors.lightWhiteColor,
                contentPadding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(24),
                    borderSide: BorderSide.none),
              ),
              maxLines: null,
            ),
          ),
          const SizedBox(width: 8),
          GetBuilder<SupportTicketController>(builder: (c) {
            return GestureDetector(
              onTap: c.isSendingReply
                  ? null
                  : () => c.sendReply(ticket.id ?? 0),
              child: CircleAvatar(
                backgroundColor: AppColors.appRedColor,
                child: c.isSendingReply
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                            color: Colors.white, strokeWidth: 2))
                    : const Icon(Icons.send, color: Colors.white, size: 20),
              ),
            );
          }),
        ],
      ),
    );
  }
}

// ── Create ticket bottom sheet ────────────────────────────────────────────

class CreateTicketForm extends StatelessWidget {
  const CreateTicketForm({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SupportTicketController>(builder: (c) {
      return Form(
        key: c.createFormKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'New Support Ticket',
              style: AppFontStyle.fontStyleW700(
                  fontSize: 16, fontColor: AppColors.black),
            ),
            const SizedBox(height: 16),
            _field(
              controller: c.subjectController,
              label: 'Subject',
              validator: (v) =>
                  (v == null || v.isEmpty) ? 'Subject is required' : null,
            ),
            const SizedBox(height: 10),
            _field(
              controller: c.issueTypeController,
              label: 'Issue Type (optional)',
            ),
            const SizedBox(height: 10),
            _field(
              controller: c.messageController,
              label: 'Describe your issue',
              maxLines: 4,
              validator: (v) =>
                  (v == null || v.isEmpty) ? 'Message is required' : null,
            ),
            const SizedBox(height: 16),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.appRedColor,
                  foregroundColor: AppColors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10)),
                ),
                onPressed: c.isCreating ? null : c.createTicket,
                child: c.isCreating
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(
                            color: Colors.white, strokeWidth: 2))
                    : const Text('Submit Ticket'),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      );
    });
  }

  Widget _field({
    required TextEditingController controller,
    required String label,
    TextInputType? keyboardType,
    String? Function(String?)? validator,
    int maxLines = 1,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      maxLines: maxLines,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        filled: true,
        fillColor: const Color(0xffEEF1F7),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: BorderSide.none,
        ),
      ),
    );
  }
}
