import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/custom/app_button/primary_app_button.dart';
import 'package:listify/custom/text_field/custom_text_field.dart';
import 'package:listify/ui/change_password_screen/controller/change_password_controller.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class ChangePasswordView extends StatelessWidget {
  const ChangePasswordView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: PreferredSize(
          preferredSize: const Size.fromHeight(300),
          child: CustomAppBar(
            title: 'Change Password',
            showLeadingIcon: true,
          ),
        ),
      ),
      body: GestureDetector(
        onTap: () {
          FocusScopeNode currentFocus = FocusScope.of(context);
          if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
            currentFocus.focusedChild?.unfocus();
          }
        },
        child: GetBuilder<ChangePasswordController>(
          builder: (controller) {
            return SingleChildScrollView(
              child: Form(
                key: controller.formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 30),

                    // Current Password
                    Text(
                      'Current Password',
                      style: AppFontStyle.fontStyleW500(
                        fontSize: 14,
                        fontColor: AppColors.profileTxtColor,
                      ),
                    ).paddingOnly(left: 20, bottom: 8),
                    Obx(() => CustomTextField(
                          filled: true,
                          fillColor: AppColors.editTextFieldColor,
                          hintText: 'Enter current password',
                          controller: controller.currentPasswordController,
                          obscureText: controller.obscureCurrent.value,
                          maxLines: 1,
                          textInputAction: TextInputAction.next,
                          suffixIcon: GestureDetector(
                            onTap: () => controller.obscureCurrent.toggle(),
                            child: Icon(
                              controller.obscureCurrent.value
                                  ? Icons.visibility_off_outlined
                                  : Icons.visibility_outlined,
                              color: AppColors.grey300,
                              size: 22,
                            ),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Please enter your current password';
                            }
                            if (value.length < 6) {
                              return 'Password must be at least 6 characters';
                            }
                            return null;
                          },
                        )).paddingSymmetric(horizontal: 20),

                    const SizedBox(height: 20),

                    // New Password
                    Text(
                      'New Password',
                      style: AppFontStyle.fontStyleW500(
                        fontSize: 14,
                        fontColor: AppColors.profileTxtColor,
                      ),
                    ).paddingOnly(left: 20, bottom: 8),
                    Obx(() => CustomTextField(
                          filled: true,
                          fillColor: AppColors.editTextFieldColor,
                          hintText: 'Enter new password',
                          controller: controller.newPasswordController,
                          obscureText: controller.obscureNew.value,
                          maxLines: 1,
                          textInputAction: TextInputAction.next,
                          suffixIcon: GestureDetector(
                            onTap: () => controller.obscureNew.toggle(),
                            child: Icon(
                              controller.obscureNew.value
                                  ? Icons.visibility_off_outlined
                                  : Icons.visibility_outlined,
                              color: AppColors.grey300,
                              size: 22,
                            ),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Please enter a new password';
                            }
                            if (value.length < 6) {
                              return 'Password must be at least 6 characters';
                            }
                            return null;
                          },
                        )).paddingSymmetric(horizontal: 20),

                    const SizedBox(height: 20),

                    // Confirm Password
                    Text(
                      'Confirm Password',
                      style: AppFontStyle.fontStyleW500(
                        fontSize: 14,
                        fontColor: AppColors.profileTxtColor,
                      ),
                    ).paddingOnly(left: 20, bottom: 8),
                    Obx(() => CustomTextField(
                          filled: true,
                          fillColor: AppColors.editTextFieldColor,
                          hintText: 'Re-enter new password',
                          controller: controller.confirmPasswordController,
                          obscureText: controller.obscureConfirm.value,
                          maxLines: 1,
                          textInputAction: TextInputAction.done,
                          suffixIcon: GestureDetector(
                            onTap: () => controller.obscureConfirm.toggle(),
                            child: Icon(
                              controller.obscureConfirm.value
                                  ? Icons.visibility_off_outlined
                                  : Icons.visibility_outlined,
                              color: AppColors.grey300,
                              size: 22,
                            ),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Please confirm your new password';
                            }
                            if (value != controller.newPasswordController.text) {
                              return 'Passwords do not match';
                            }
                            return null;
                          },
                        )).paddingSymmetric(horizontal: 20),

                    const SizedBox(height: 40),

                    // Submit Button
                    Obx(() => PrimaryAppButton(
                          height: 55,
                          text: controller.isLoading.value ? 'Updating...' : 'Update Password',
                          color: AppColors.appRedColor,
                          onTap: controller.isLoading.value ? null : () => controller.changePassword(),
                        )).paddingSymmetric(horizontal: 20),

                    const SizedBox(height: 30),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
