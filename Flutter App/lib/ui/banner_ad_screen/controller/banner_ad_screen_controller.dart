import 'package:get/get.dart';
import 'package:listify/ui/banner_ad_screen/api/banner_ad_api.dart';
import 'package:listify/ui/banner_ad_screen/model/banner_ad_response_model.dart';

class BannerAdScreenController extends GetxController {
  static const idList = 'banner_ad_list';
  static const idSubmit = 'banner_ad_submit';

  List<BannerAd> ads = [];
  bool isLoading = false;
  bool isSubmitting = false;
  String? submitError;
  String? submitSuccess;

  // Available slots
  static const List<Map<String, String>> slotOptions = [
    {'value': 'homepage_hero_banner', 'label': 'Homepage Hero Banner'},
    {'value': 'homepage_footer_banner', 'label': 'Homepage Footer Banner'},
    {'value': 'listing_details_left', 'label': 'Listing Details – Left'},
    {'value': 'listing_details_right', 'label': 'Listing Details – Right'},
    {'value': 'listing_details_under_gallery', 'label': 'Listing Details – Under Gallery'},
    {'value': 'user_profile_under_header', 'label': 'Profile Under Header'},
    {'value': 'listings_under_image', 'label': 'Listings – Under Image'},
  ];

  @override
  void onInit() {
    super.onInit();
    fetchAds();
  }

  Future<void> fetchAds() async {
    isLoading = true;
    update([idList]);
    final res = await BannerAdApi.callListApi();
    ads = res?.data ?? [];
    isLoading = false;
    update([idList]);
  }

  Future<bool> submitAd({
    required String title,
    required String requestedSlot,
    required String redirectUrl,
    required String bannerImagePath,
  }) async {
    submitError = null;
    submitSuccess = null;
    isSubmitting = true;
    update([idSubmit]);

    final result = await BannerAdApi.callSubmitApi(
      title: title,
      requestedSlot: requestedSlot,
      redirectUrl: redirectUrl,
      bannerImagePath: bannerImagePath,
    );

    isSubmitting = false;
    if (result['status'] == true || result['status'] == 1) {
      submitSuccess = result['message']?.toString() ?? 'Request submitted successfully';
      await fetchAds();
      update([idSubmit]);
      return true;
    } else {
      submitError = result['message']?.toString() ?? 'Submission failed. Please try again.';
      update([idSubmit]);
      return false;
    }
  }
}
