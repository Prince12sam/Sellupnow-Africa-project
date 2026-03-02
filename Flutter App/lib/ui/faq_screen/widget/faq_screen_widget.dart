import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/ui/faq_screen/controller/faq_screen_controller.dart';
import 'package:listify/ui/faq_screen/shimmer/faq_shimmer.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/font_style.dart';

class FaqScreenAppBar extends StatelessWidget {
  final String? title;
  const FaqScreenAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: Size.fromHeight(120),
      child: CustomAppBar(
        // iconColor: AppColors.black,
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}

class FaqScreenWidget extends StatelessWidget {
  const FaqScreenWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<FaqScreenController>(
        id: Constant.idFaq,
        builder: (controller) {
          return Column(
            children: [
              controller.isLoading
                  ? HelpCenterShimmer()
                  : ListView.builder(
                      itemCount: controller.faqApiResponseModel?.data?.length ?? 0,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemBuilder: (context, index) {
                        final faq = controller.faqApiResponseModel?.data?[index];
                        return Container(
                          decoration: BoxDecoration(
                            color: AppColors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: AppColors.borderColor, width: 0.8),
                          ),
                          child: ExpansionTile(
                            key: UniqueKey(),
                            // initiallyExpanded: isExpanded,
                            onExpansionChanged: (expanded) {
                              // onTap();
                            },
                            shape: Border.all(color: AppColors.transparent),
                            childrenPadding: const EdgeInsets.symmetric(horizontal: 8, vertical: 0),
                            collapsedIconColor: AppColors.popularProductText,

                            iconColor: AppColors.popularProductText,
                            title: Text(
                              faq?.question ?? '',
                              style: AppFontStyle.fontStyleW700(
                                fontSize: 16,
                                fontColor: AppColors.faqTxt,
                              ),
                            ),
                            children: [
                              Text(
                                faq?.answer ?? '',
                                style: AppFontStyle.fontStyleW400(
                                  fontColor: AppColors.popularProductText,
                                  fontSize: 13,
                                ),
                              ).paddingOnly(bottom: 10),
                            ],
                          ),
                        ).paddingOnly(bottom: 18);
                      },
                    )
            ],
          ).paddingOnly(left: 14, right: 14, top: 22);
        });
  }
}
