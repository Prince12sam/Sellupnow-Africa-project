import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/common.dart';
import 'package:listify/custom/custom_image_view/custom_image_view.dart';
import 'package:listify/custom/no_data_found/no_data_found_widget.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/my_ads_screen/controller/my_ads_screen_controller.dart';
import 'package:listify/ui/my_ads_screen/shimmer/my_ads_screen_shimmer.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/font_style.dart';
import 'package:listify/utils/utils.dart';
import 'package:preload_page_view/preload_page_view.dart';

class AdsTabBar extends StatefulWidget {
  const AdsTabBar({super.key});

  @override
  AdsTabBarState createState() => AdsTabBarState();
}

class AdsTabBarState extends State<AdsTabBar> with TickerProviderStateMixin {
  late TabController _tabController;
  late PreloadPageController _pageController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: Get.find<MyAdsScreenController>().tabs.length, vsync: this);
    _pageController = PreloadPageController(initialPage: _tabController.index);

    Get.find<MyAdsScreenController>().init();
    Get.find<MyAdsScreenController>().update([Constant.idAllAds]);

    _tabController.addListener(() {
      if (_tabController.indexIsChanging) {
        _pageController.jumpToPage(_tabController.index);

        final controller = Get.find<MyAdsScreenController>();
        controller.onTabChanged(_tabController.index);
      }
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    var tabs = [
      Tab(text: "All Ads"),
      Tab(text: "Featured"),
      Tab(text: "Live"),
      Tab(text: "Deactivate"),
      Tab(text: "Under Review"),
      Tab(text: "Sold Out"),
      Tab(text: "Permanent Rejected"),
      Tab(text: "Soft Rejected"),
      Tab(text: "Resubmitted"),
      Tab(text: "Expired"),
    ];
    return Expanded(
      child: Column(
        children: [
          TabBar(
            controller: _tabController,
            tabs: tabs,
            labelStyle: AppFontStyle.fontStyleW500(
              fontSize: 13,
              fontColor: AppColors.white,
            ),
            physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            indicatorPadding: const EdgeInsets.all(5),
            indicator: BoxDecoration(
              borderRadius: BorderRadius.circular(8),
              color: AppColors.appRedColor,
            ),
            indicatorSize: TabBarIndicatorSize.tab,
            labelColor: AppColors.white,
            unselectedLabelStyle: AppFontStyle.fontStyleW500(
              fontSize: 13,
              fontColor: AppColors.greyTxtColor,
            ),
            isScrollable: true,
            tabAlignment: TabAlignment.start,
            dividerColor: Colors.transparent,
            overlayColor: WidgetStatePropertyAll(AppColors.transparent),
          ),
          Expanded(
            child: GetBuilder<MyAdsScreenController>(builder: (controller) {
              return PreloadPageView.builder(
                physics: BouncingScrollPhysics(),
                controller: _pageController,
                preloadPagesCount: tabs.length,
                itemCount: tabs.length,
                onPageChanged: (index) {
                  _tabController.animateTo(index);
                  Utils.showLog("Page changed to index: $index, type: ${controller.type[index]}");
                },
                itemBuilder: (context, index) {
                  return ProductView(index: index);
                },
              );
            }),
          ),
        ],
      ),
    );
  }
}

class AdsScreenAppBar extends StatelessWidget {
  final String? title;
  const AdsScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: false,
      ),
    );
  }
}

class ProductView extends StatelessWidget {
  const ProductView({super.key, required this.index});

  final int index;

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MyAdsScreenController>(
        id: Constant.idAllAds,
        builder: (controller) {
          // Loading state
          if (controller.isLoading) {
            return MyAdsScreenShimmer();
          }

          // Data available state
          return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.init(),
              child: controller.allAdsList.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          NoDataFound(image: AppAsset.noMyAdsFound, imageHeight: 180, text: EnumLocale.txtEmptyMyAds.name.tr),
                        ],
                      ),
                    )
                  : ListView.builder(
                      padding: EdgeInsets.only(bottom: 120),
                      itemCount: controller.allAdsList.length,
                      shrinkWrap: true,
                      itemBuilder: (context, index) {
                        return Column(
                          children: [
                            GestureDetector(
                              onTap: () {
                                final ad = controller.allAdsList[index];

                                Get.toNamed(AppRoutes.productDetailScreen, arguments: {
                                  'edit': true,
                                  'adId': ad.id,
                                  // 'ad': ad,
                                  'viewLikeCount': true,
                                })?.then(
                                  (value) {
                                    controller.init();
                                    // controller.getAllAds(adType: controller.currentAdType);
                                  },
                                );
                              },
                              child: Container(
                                decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(15),
                                    border: Border.all(color: AppColors.lightGreyBorder),
                                    color: AppColors.white),
                                child: Row(
                                  children: [
                                    Container(
                                      height: Get.height * 0.16,
                                      width: Get.height * 0.16,
                                      decoration: BoxDecoration(
                                        color: AppColors.white,
                                        borderRadius: BorderRadius.only(
                                          bottomLeft: Radius.circular(12),
                                          topLeft: Radius.circular(12),
                                        ),
                                      ),
                                      child: ClipRRect(
                                        borderRadius: BorderRadius.only(
                                          bottomLeft: Radius.circular(12),
                                          topLeft: Radius.circular(12),
                                        ),
                                        child: 

                                        CustomImageView(
                                          image: controller.allAdsList[index].primaryImage.toString(),
                                          fit: BoxFit.cover,
                                        ),
                                      ),
                                    ).paddingOnly(bottom: 2, left: 2, top: 2),
                                    10.width,
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          buildAdStatus(controller.allAdsList[index].status??0),
                                          // 8.height,
                                          Text(
                                            capitalizeWords(controller.allAdsList[index].title??"") ,
                                            style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.black),
                                            overflow: TextOverflow.ellipsis,
                                          ).paddingOnly(bottom: 8, right: 10),

                                          // buildAdStatus(controller.allAdsList[index].status??0),
                                          // 8.height,
                                          Row(
                                            children: [
                                              Text(
                                                "${Database.settingApiResponseModel?.data?.currency?.symbol} ${controller.allAdsList[index].price.toString()}",
                                                style: AppFontStyle.fontStyleW700(fontSize: 15, fontColor: AppColors.appRedColor),
                                              ).paddingOnly(right: 6),
                                            ],
                                          ).paddingOnly(bottom: 8),
                                          Row(
                                            children: [
                                              Image.asset(
                                                AppAsset.eyeIcon,
                                                height: 13,
                                                width: 16,
                                              ),
                                              5.width,
                                              Text(
                                                "${EnumLocale.txtViews.name.tr} : ",
                                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.grey),
                                              ),
                                              Text(
                                                controller.allAdsList[index].viewsCount.toString(),
                                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.black),
                                              ),
                                              14.width,
                                              Image.asset(
                                                AppAsset.favouriteIcon,
                                                width: 14,
                                                height: 14,
                                              ),
                                              5.width,
                                              Text(
                                                "${EnumLocale.txtLikes.name.tr} : ",
                                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.grey),
                                              ),
                                              Text(
                                                controller.allAdsList[index].likesCount.toString(),
                                                style: AppFontStyle.fontStyleW500(fontSize: 12, fontColor: AppColors.black),
                                              ),
                                            ],
                                          ).paddingOnly(bottom: 8)
                                        ],
                                      ),
                                    )
                                  ],
                                ),
                              ).paddingOnly(bottom: 10),
                            ),
                          ],
                        ).paddingSymmetric(horizontal: 14);
                      },
                    ));
        });
  }

  Widget buildAdStatus(num status) {
    String text = "";
    Color bgColor = AppColors.lightPurpleBorder;
    Color textColor = AppColors.purpleBorder;

    switch (status) {
      case 1:
        text = "Pending";
        bgColor = AppColors.lightPurpleBorder;
        textColor = AppColors.purpleBorder;
        break;
      case 2:
        text = "Approved";
        bgColor = Colors.green.shade50;
        textColor = Colors.green;
        break;
      case 3:
        text = "Permanent Rejected";
        bgColor = Colors.red.shade50;
        textColor = Colors.red;
        break;
      case 4:
        text = "Soft Rejected";
        bgColor = Colors.orange.shade50;
        textColor = Colors.orange;
        break;
      case 5:
        text = "Featured";
        bgColor = Colors.yellow.shade50;
        textColor = Colors.amber;
        break;
      case 6:
        text = "Deactivated";
        bgColor = Colors.grey.shade300;
        textColor = Colors.grey.shade700;
        break;
      case 7:
        text = "Sold Out";
        bgColor = Colors.black12;
        textColor = Colors.black;
        break;
      case 8:
        text = "Resubmitted";
        bgColor = Colors.blue.shade50;
        textColor = Colors.blue;
        break;
      case 9:
        text = "Expired";
        bgColor = Colors.brown.shade50;
        textColor = Colors.brown;
        break;
      default:
        text = "";
    }

    if (text.isEmpty) return const SizedBox.shrink();

    return Container(
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(6),
      ),
      child: Text(
        text,
        style: AppFontStyle.fontStyleW500(
          fontSize: 12,
          fontColor: textColor,
        ),
      ).paddingSymmetric(horizontal: 10, vertical: 4),
    ).paddingOnly(bottom: 8, top: 6);
  }

}
