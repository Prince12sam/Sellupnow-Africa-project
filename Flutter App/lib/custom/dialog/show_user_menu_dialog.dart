import 'package:flutter/material.dart';

void showUserMenuDialog(BuildContext context) {
  showDialog(
    context: context,
    builder: (ctx) {
      return Dialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.only(bottomRight: Radius.circular(16), bottomLeft: Radius.circular(16), topLeft: Radius.circular(16)),
        ),
        insetPadding: const EdgeInsets.only(right: 16, top: 60),
        alignment: Alignment.topRight,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            _menuItem(
              icon: Icons.person_off_outlined,
              text: "Block User",
              onTap: () {
                Navigator.pop(ctx);
              },
            ),
            const Divider(height: 1),
            _menuItem(
              icon: Icons.report_gmailerrorred_outlined,
              text: "Report & Spam",
              onTap: () {
                Navigator.pop(ctx);
              },
            ),
          ],
        ),
      );
    },
  );
}

Widget _menuItem({
  required IconData icon,
  required String text,
  required VoidCallback onTap,
}) {
  return InkWell(
    onTap: onTap,
    child: Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 22, color: Colors.black87),
          const SizedBox(width: 12),
          Text(
            text,
            style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w500),
          ),
        ],
      ),
    ),
  );
}
