
import 'package:get/get.dart';

class LikeManager extends GetxController {
  static LikeManager get to => Get.find<LikeManager>();

  final Map<String, bool> _likeState = {};

  /// get like state
  bool getLikeState(String adId, {bool? fallback}) {
    return _likeState[adId] ?? fallback ?? false;
  }

  /// update like state
  void updateLikeState(String adId, bool isLiked) {
    _likeState[adId] = isLiked;
    update(); // notify all GetBuilders listening to LikeManager
  }
  void clearLikes() {
    _likeState.clear();
    update();
  }
}
