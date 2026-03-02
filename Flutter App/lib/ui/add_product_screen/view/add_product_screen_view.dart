import 'package:flutter/material.dart';
import 'package:get/get_utils/get_utils.dart';
import 'package:listify/ui/add_product_screen/widget/add_product_detail_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class AddProductScreenView extends StatelessWidget {
  const AddProductScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.adScreenBgColor,
      bottomNavigationBar: AddProductDetailBottomButton(),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: AddProductDetailScreenAppBar(
          title: EnumLocale.txtProductDetail.name.tr,
        ),
      ),
      body: GestureDetector(
        onTap: () {
          FocusScopeNode currentFocus = FocusScope.of(context);
          if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
            currentFocus.focusedChild?.unfocus();
          }
        },
        child: GestureDetector(
          onTap: () {
            FocusScopeNode currentFocus = FocusScope.of(context);
            if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
              currentFocus.focusedChild?.unfocus();
            }
          },
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                AddProductDetailScreenWidget(),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
