import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/product_pricing_screen/controller/product_pricing_screen_controller.dart';
import 'package:listify/ui/product_pricing_screen/widget/product_pricing_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class ProductPricingScreen extends StatelessWidget {
  const ProductPricingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      bottomNavigationBar: SaveChangeButton(),
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: ProductPricingScreenAppBar(
          title: EnumLocale.txtProductPricing.name.tr,
        ),
      ),
      // floatingActionButton: FloatingActionButton(
      //   onPressed: () {
      //     Get.find<ProductPricingScreenController>().editApiCall();
      //   },
      // ),
      body: GestureDetector(
        onTap: () {
          FocusScopeNode currentFocus = FocusScope.of(context);
          if (!currentFocus.hasPrimaryFocus && currentFocus.focusedChild != null) {
            currentFocus.focusedChild?.unfocus();
          }
        },
        child: GetBuilder<ProductPricingScreenController>(builder: (controller) {
          return SingleChildScrollView(
            child: Stack(
              children: [
                ProductPricingScreenWidget(),
              ],
            ),
          );
        }),
      ),
    );
  }
}
