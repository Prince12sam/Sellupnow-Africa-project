import 'package:flutter/material.dart';
import 'package:get/get_utils/src/extensions/export.dart';
import 'package:listify/ui/review_screen/widget/review_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class ReviewScreenView extends StatelessWidget {
  const ReviewScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        backgroundColor: AppColors.white,
        appBar: ReviewScreenAppbar(
          title: EnumLocale.txtMyReviews.name.tr,
        ),
        body: NestedScrollView(
          headerSliverBuilder: (context, innerBoxIsScrolled) => [
            SliverToBoxAdapter(
              child: const ReviewScreenTopView(),
            ),
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
                  tabs: const [
                    Tab(text: "My Product"),
                    Tab(text: "My Ratings"),
                  ],
                ),
              ),
            ),
          ],
          body: const TabBarView(
            children: [
              MyProductView(),
              MyRatingTabView(),
            ],
          ),
        ),
      ),
    );
  }
}

class _TabBarHeaderDelegate extends SliverPersistentHeaderDelegate {
  final TabBar _tabBar;
  _TabBarHeaderDelegate(this._tabBar);

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Container(
      color: Colors.white,
      child: _tabBar,
    );
  }

  @override
  double get maxExtent => _tabBar.preferredSize.height;

  @override
  double get minExtent => _tabBar.preferredSize.height;

  @override
  bool shouldRebuild(covariant _TabBarHeaderDelegate oldDelegate) {
    return false;
  }
}
