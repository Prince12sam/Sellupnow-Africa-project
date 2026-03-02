import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/support_ticket_screen/controller/support_ticket_controller.dart';
import 'package:listify/ui/support_ticket_screen/widget/support_ticket_widget.dart';
import 'package:listify/utils/app_color.dart';

class SupportTicketDetailView extends StatefulWidget {
  const SupportTicketDetailView({super.key});

  @override
  State<SupportTicketDetailView> createState() =>
      _SupportTicketDetailViewState();
}

class _SupportTicketDetailViewState extends State<SupportTicketDetailView> {
  @override
  void initState() {
    super.initState();
    final id = Get.arguments as int?;
    if (id != null) {
      Get.find<SupportTicketController>().fetchDetail(id);
    }
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SupportTicketController>(builder: (c) {
      final ticket = c.currentTicket;
      return Scaffold(
        backgroundColor: AppColors.white,
        appBar: AppBar(
          backgroundColor: AppColors.appRedColor,
          foregroundColor: AppColors.white,
          title: Text(ticket?.subject ?? 'Ticket Detail'),
          centerTitle: true,
        ),
        body: c.isDetailLoading
            ? const Center(child: CircularProgressIndicator())
            : ticket == null
                ? const Center(child: Text('Ticket not found'))
                : TicketDetailBody(ticket: ticket, controller: c),
      );
    });
  }
}
