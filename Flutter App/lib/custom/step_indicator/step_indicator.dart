import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class StepProgressIndicator extends StatelessWidget {
  final int currentStep;
  final Function(int index)? onStepTap; // <-- add callback

  const StepProgressIndicator({
    super.key,
    required this.currentStep,
    this.onStepTap,
  });

  @override
  Widget build(BuildContext context) {
    const steps = [
      "Personal\nInformation",
      "ID Proof\nVerification",
    ];

    return SizedBox(
      height: 90,
      child: Stack(
        alignment: Alignment.centerLeft,
        children: [
          // Background line
          Positioned(
            top: 15,
            left: 0,
            right: 0,
            child: Container(
              height: 4,
              decoration: BoxDecoration(
                color: AppColors.lightGreyColor,
                borderRadius: BorderRadius.circular(10),
              ),
            ),
          ),

          // Red progress line
          if (currentStep > 0)
            Positioned(
              top: 15,
              left: 0,
              right: (2 - currentStep) / 2.2 * Get.width,
              child: Container(
                height: 4,
                decoration: BoxDecoration(
                  color: AppColors.appRedColor,
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
            ),

          // Step indicators
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: List.generate(steps.length, (index) {
              bool isCompleted = index < currentStep;
              bool isCurrent = index == currentStep;

              Color? circleColor;
              if (isCurrent) {
                circleColor = AppColors.lightPurple;
              } else if (isCompleted) {
                circleColor = AppColors.appRedColor;
              } else {
                circleColor = AppColors.appRedColor;
              }

              return Column(
                children: [
                  Container(
                    height: 34,
                    width: 34,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: circleColor,
                      border: Border.all(color: AppColors.white, width: 2),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.black.withValues(alpha: 0.2),
                          blurRadius: 3,
                        ),
                      ],
                    ),
                    child: Center(
                      child: Text(
                        "${index + 1}",
                        style: AppFontStyle.fontStyleW500(
                          fontSize: 18,
                          fontColor: isCompleted ? AppColors.white : AppColors.searchText,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    steps[index],
                    textAlign: TextAlign.center,
                    style: AppFontStyle.fontStyleW500(
                      fontSize: 11,
                      fontColor: isCompleted ? AppColors.appRedColor : AppColors.searchText,
                    ),
                  ),
                ],
              );
            }),
          ),
        ],
      ),
    );
  }
}
