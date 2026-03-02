import 'package:get/get.dart';
import 'package:listify/ui/blog_screen/api/blog_api.dart';
import 'package:listify/ui/blog_screen/api/trending_blog_api.dart';
import 'package:listify/ui/blog_screen/model/blog_response_model.dart';
import 'package:listify/ui/blog_screen/model/trending_blog_response_model.dart';
import 'package:listify/utils/constant.dart';

class BlogScreenController extends GetxController {
  bool isLoading = false;
  BlogResponseModel? blogResponseModel;
  TrendingBlogResponse? trendingBlogResponse;

  @override
  void onInit() {
    getAllBlog();
    getTrendingBlog();
    super.onInit();
  }

  /// get all blog data
  getAllBlog() async {
    isLoading = true;
    update([Constant.idBlog]); // notify UI
    blogResponseModel = await BlogApi.callApi();

    isLoading = false;
    update([Constant.idBlog]); // notify UI
  }

  /// get trending blog data
  getTrendingBlog() async {
    isLoading = true;
    update([Constant.idBlog]); // notify UI
    trendingBlogResponse = await TrendingBlogApi.callApi();

    isLoading = false;
    update([Constant.idBlog]); // notify UI
  }

  /// on refresh
  onRefresh() async {
    getTrendingBlog();
    getAllBlog();
    update([Constant.idBlog]); // notify UI
  }
}
