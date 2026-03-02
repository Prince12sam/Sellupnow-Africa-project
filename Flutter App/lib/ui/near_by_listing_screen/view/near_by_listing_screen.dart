import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/near_by_listing_screen/widget/near_by_listing_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class NearByListingScreen extends StatelessWidget {
  const NearByListingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: NearByListingScreenAppBar(
          title: EnumLocale.txtNearbyListings.name.tr,
        ),
      ),
      bottomNavigationBar: BottomBarWidget(),
      body: Column(
        children: [
          MapView(),
          // Expanded(
          //   child: SingleChildScrollView(
          //     child: BottomDetailsView(),
          //   ),
          // ),
        ],
      ),

      // body: CustomScrollView(
      //   slivers: [
      //     SliverToBoxAdapter(
      //       child: MapView(), // Should have fixed height
      //     ),
      //     SliverToBoxAdapter(
      //       child: BottomDetailsView(),
      //     ),
      //   ],
      // ),
    );
  }
}
