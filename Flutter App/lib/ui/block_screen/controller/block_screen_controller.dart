import 'package:get/get.dart';
import 'package:listify/ui/block_screen/api/get_block_list_api.dart';
import 'package:listify/ui/block_screen/model/get_block_list_response_model.dart';
import 'package:listify/ui/chat_detail_screen/api/user_block_api.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class BlockScreenController extends GetxController {
  List<BlockedUser> blockedUsers = [];

  bool isLoading = false;

  @override
  void onInit() {
    super.onInit();
    getBlockList();
  }

  /// get block list

  Future<void> getBlockList() async {
    try {
      isLoading = true;

      final response = await BlockListApi.getBlockedUsers();

      if (response != null && response.status == true) {
        blockedUsers = response.blockedUsers ?? [];
        update([Constant.blockList]);
      } else {
        blockedUsers.clear();
        update([Constant.blockList]);
      }
    } finally {
      isLoading = false;
    }
  }

  ///unblock api
  unBlockApi(String blockerId) async {
    final response = await BlockUserApi.toggleBlockUser(
      blockedId: blockerId,
      uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId,
    );

    if (response != null) {
      if (response.status == true) {
        Get.back();
        Utils.showToast(Get.context!, response.message ?? "Success");
      } else {
        Utils.showToast(Get.context!, response.message ?? "Failed");
      }
    } else {
      Utils.showToast(Get.context!, "No Response from server");
    }
  }
}
