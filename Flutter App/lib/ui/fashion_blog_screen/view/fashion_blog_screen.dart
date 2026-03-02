import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/fashion_blog_screen/widget/fashion_blog_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/enums.dart';

class FashionBlogScreen extends StatelessWidget {
  const FashionBlogScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: FashionBlogScreenAppBar(
          title: EnumLocale.txtOurFashionBlogs.name.tr,
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            FashionBlogScreenWidget(),
          ],
        ),
      ),
    );
  }
}
