import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/favourrite_screen/controller/favourite_screen_controller.dart';
import 'package:listify/ui/favourrite_screen/widget/favourite_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';

class FavoriteScreen extends StatelessWidget {
  const FavoriteScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: FavoriteScreenAppBar(
          title: EnumLocale.txtFavorites.name.tr,
        ),
      ),
      body: GetBuilder<FavoriteScreenController>(
          id: Constant.idViewType,
          builder: (controller) {
            return Column(
              children: [
                FavScreenSearchView(),
                controller.selectedView == ViewType.grid ? const GridProductView() : const ListProductView(),
              ],
            );
          }),
    );
  }
}
