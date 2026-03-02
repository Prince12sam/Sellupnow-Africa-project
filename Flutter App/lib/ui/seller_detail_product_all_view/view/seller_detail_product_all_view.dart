import 'package:flutter/material.dart';
import 'package:listify/ui/seller_detail_product_all_view/widget/seller_detail_product_all_view_widget.dart';
import 'package:listify/utils/app_color.dart';

class SellerDetailProductAllView extends StatelessWidget {
  const SellerDetailProductAllView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: SellerDetailProductAllViewAppBar(
          title: "All Product",
        ),
      ),
      body: SellerDetailProductAllViewWidget(),
    );
  }
}
// import 'package:flutter/material.dart';
// import 'package:flutter/cupertino.dart';
// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:listify/ui/most_liked_view_all/controller/most_liked_view_all_controller.dart';
// import 'package:listify/ui/most_liked_view_all/widget/most_liked_view_all_widget.dart';
// import 'package:listify/utils/app_color.dart';
// import 'package:listify/utils/constant.dart';
// import 'package:listify/utils/enums.dart';
//
// class SellerDetailProductAllView extends StatelessWidget {
//   const SellerDetailProductAllView({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       backgroundColor: AppColors.white,
//       appBar: AppBar(
//         automaticallyImplyLeading: false,
//         flexibleSpace: MostLikedViewAllAppBar(
//           title: EnumLocale.txtMostLikedProduct.name.tr,
//         ),
//       ),
//       body: GetBuilder<MostLikedViewAllController>(
//           id: Constant.idViewType,
//           builder: (controller) {
//             return Column(
//               children: [
//                 MostLikedViewAllSearchView(),
//                 // controller.selectedView == ViewType.grid ? const GridProductView() : const ListProductView(),
//               ],
//             );
//           }),
//     );
//   }
// }
