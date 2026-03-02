import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:listify/ui/fashion_blog_screen/api/blog_by_id_api.dart';
import 'package:listify/ui/fashion_blog_screen/model/blog_by_id_response_model.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/utils.dart';

class FashionBlogScreenController extends GetxController {
  String? blogId;
  bool isLoading = false;
  BlogByIdResponse? blogByIdResponse;

  @override
  void onInit() {
    blogId = Get.arguments;
    Utils.showLog('Received Blog ID: $blogId');
    getBlogById();
    super.onInit();
  }

  /// get blog by id
  getBlogById() async {
    isLoading = true;
    update([Constant.idBlog]);
    blogByIdResponse = await BlogByIdApi.callApi(blogId: blogId ?? '');

    isLoading = false;
    update([Constant.idBlog]);
  }

  /// format blog date
  String formatBlogDate(String? isoDateString) {
    if (isoDateString == null) return '';

    try {
      final dateTime = DateTime.parse(isoDateString).toLocal(); // Convert from UTC to local
      final formatter = DateFormat('MMM dd, yyyy'); // e.g., Jul 23, 2025
      return formatter.format(dateTime);
    } catch (e) {
      return ''; // Return blank or fallback on parse failure
    }
  }

  /// on refresh
  onRefresh() async {
    await getBlogById();
  }
}
