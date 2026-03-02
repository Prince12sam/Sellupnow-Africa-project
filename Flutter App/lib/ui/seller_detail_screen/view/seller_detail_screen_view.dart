// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:listify/ui/seller_detail_screen/widget/seller_detail_screen_widget.dart';
// import 'package:listify/utils/app_asset.dart';
// import 'package:listify/utils/app_color.dart';
// import 'package:listify/utils/enums.dart';
//
// class SellerDetailScreenView extends StatelessWidget {
//   const SellerDetailScreenView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       backgroundColor: AppColors.white,
//       appBar: AppBar(
//         automaticallyImplyLeading: false,
//         flexibleSpace: SellerDetailScreenAppBar(
//           title: EnumLocale.txtSellerDetails.name.tr,
//         ),
//         actions: [
//           GestureDetector(
//             onTap: () {
//               // Share action
//             },
//             child: Container(
//               // height: 40,
//               // width: 40,
//               decoration: BoxDecoration(
//                 shape: BoxShape.circle,
//                 color: AppColors.categoriesBgColor,
//               ),
//               child: Padding(
//                 padding: const EdgeInsets.all(7),
//                 child: Image.asset(
//                   height: 22,
//                   width: 22,
//                   AppAsset.blackShareIcon,
//                   color: AppColors.black,
//                 ),
//               ),
//             ).paddingOnly(right: 17),
//           ),
//         ],
//       ),
//       body: Column(
//         children: [
//           SellerDetailScreenTopView(),
//           Expanded(
//             child: SellerDetailTabView(),
//           ),
//         ],
//       ),
//     );
//   }
// }

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/seller_detail_screen/widget/seller_detail_screen_widget.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class SellerDetailScreenView extends StatelessWidget {
  const SellerDetailScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        backgroundColor: AppColors.white,
        body: NestedScrollView(
          headerSliverBuilder: (context, innerBoxIsScrolled) => [
            SliverAppBar(
              surfaceTintColor: AppColors.white,
              pinned: true,
              backgroundColor: AppColors.white,
              automaticallyImplyLeading: true,
              elevation: innerBoxIsScrolled ? 2 : 0,
              title: Text(
                EnumLocale.txtSellerDetails.name.tr,
                style: const TextStyle(fontWeight: FontWeight.w700),
              ),
              actions: [
                GestureDetector(
                  onTap: () {
                    // TODO: Share action
                  },
                  child: Container(
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppColors.categoriesBgColor,
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(7),
                      child: Image.asset(
                        AppAsset.blackShareIcon,
                        height: 22,
                        width: 22,
                        color: AppColors.black,
                      ),
                    ),
                  ).paddingOnly(right: 17),
                ),
              ],
            ),

            // Top profile / header block scrolls away
            SliverToBoxAdapter(
              child: const SellerDetailScreenTopView(),
            ),

            // Pinned TabBar just below AppBar
            SliverPersistentHeader(
              pinned: true,
              delegate: _TabBarHeaderDelegate(
                TabBar(
                  indicatorSize: TabBarIndicatorSize.tab,
                  indicatorColor: AppColors.appRedColor,
                  labelColor: AppColors.appRedColor,
                  unselectedLabelColor: AppColors.searchText,
                  dividerColor: AppColors.lightDividerColor,
                  dividerHeight: 2,
                  tabs: [
                    Tab(text: EnumLocale.txtSellerProduct.name.tr),
                    Tab(text: "Ratings"),
                  ],
                ),
              ),
            ),
          ],

          // Only tab content scrolls now
          body: const TabBarView(
            children: [
              SellerProductTab(), // sliver-based tab
              NotesTab(), // sliver-based tab
            ],
          ),
        ),
      ),
    );
  }
}

/// Delegate to pin the TabBar (SliverPersistentHeader)
class _TabBarHeaderDelegate extends SliverPersistentHeaderDelegate {
  final TabBar tabBar;
  _TabBarHeaderDelegate(this.tabBar);

  @override
  double get minExtent => tabBar.preferredSize.height;
  @override
  double get maxExtent => tabBar.preferredSize.height;

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Container(
      color: Colors.white,
      child: tabBar,
    );
  }

  @override
  bool shouldRebuild(covariant _TabBarHeaderDelegate oldDelegate) {
    return oldDelegate.tabBar != tabBar;
  }
}
