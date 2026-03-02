import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/ui/blog_screen/controller/blog_screen_controller.dart';
import 'package:listify/ui/blog_screen/widget/blog_screen_widget.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/enums.dart';

class BlogScreen extends StatelessWidget {
  const BlogScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: BlogScreenAppBar(
          title: EnumLocale.txtBlogs.name.tr,
        ),
      ),
      body: GetBuilder<BlogScreenController>(
          id: Constant.idBlog,
          builder: (controller) {
            return RefreshIndicator(
              color: AppColors.appRedColor,
              onRefresh: () => controller.onRefresh(),
              child: SingleChildScrollView(
                child: Column(
                  children: [
                    BlogScreenWidget1(),
                  ],
                ),
              ),
            );
          }),
    );
  }
}
