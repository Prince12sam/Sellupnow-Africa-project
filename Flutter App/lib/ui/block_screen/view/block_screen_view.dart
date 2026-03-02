import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/block_screen/widget/block_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class BlockScreenView extends StatelessWidget {
  const BlockScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: BlockScreenAppBar(
          title: EnumLocale.txtBlockUser.name.tr,
        ),
      ),
      body: BlockListView(),
    );
  }
}
