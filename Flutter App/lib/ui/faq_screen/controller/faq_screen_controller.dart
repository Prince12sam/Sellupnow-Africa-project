import 'package:get/get.dart';
import 'package:listify/ui/faq_screen/api/faq_api.dart';
import 'package:listify/ui/faq_screen/model/faq_response_model.dart';
import 'package:listify/utils/constant.dart';

class FaqScreenController extends GetxController {
  bool isLoading = false;
  FaqApiResponseModel? faqApiResponseModel;

  @override
  void onInit() {
    getFAQ();
    super.onInit();
  }

  /// get FAQ
  getFAQ() async {
    isLoading = true;
    update([Constant.idFaq]); // notify UI
    faqApiResponseModel = await FaqApi.callApi();

    isLoading = false;
    update([Constant.idFaq]); // notify UI
  }
}
