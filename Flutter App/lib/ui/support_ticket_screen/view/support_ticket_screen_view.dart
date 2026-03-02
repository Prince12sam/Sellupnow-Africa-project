import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/support_ticket_screen/controller/support_ticket_controller.dart';
import 'package:listify/ui/support_ticket_screen/widget/support_ticket_widget.dart';
import 'package:listify/utils/app_color.dart';

class SupportTicketScreenView extends StatelessWidget {
  const SupportTicketScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        backgroundColor: AppColors.appRedColor,
        foregroundColor: AppColors.white,
        title: const Text('Support Tickets'),
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.add),
            tooltip: 'New Ticket',
            onPressed: () => _showCreateSheet(context),
          ),
        ],
      ),
      body: GetBuilder<SupportTicketController>(
        builder: (c) => SupportTicketListBody(),
      ),
    );
  }

  void _showCreateSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => Padding(
        padding: EdgeInsets.only(
            bottom: MediaQuery.of(context).viewInsets.bottom,
            left: 16,
            right: 16,
            top: 20),
        child: const CreateTicketForm(),
      ),
    );
  }
}
