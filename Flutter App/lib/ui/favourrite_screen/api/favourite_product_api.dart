import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

// class FavouriteProductApi {
//   static Future<AllAdsResponseModel?> callApi({
//     int start = 0,
//     int limit = 20,
//     String? userId,
//     String? search, // NEW
//   }) async {
//     Utils.showLog("Favourite Ads Api Calling...");
//     final token = await FirebaseAccessToken.onGet();
//
//     // Build query safely (auto-encodes)
//     final query = <String, String>{
//       'search': (search == null || search.isEmpty) ? 'All' : search,
//       'start': '$start',
//       'limit': '$limit',
//       'userId': userId ?? '',
//     };
//
//     final uri = Uri.parse(Api.userLikedAdListApi).replace(queryParameters: query);
//
//     final headers = {
//       ApiParams.key: Api.secretKey,
//       ApiParams.authToken: 'Bearer $token',
//       ApiParams.authUid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
//       ApiParams.contentType: 'application/json',
//     };
//
//     try {
//       final response = await http.get(uri, headers: headers);
//       log('Favourite Ads API STATUS CODE :: ${response.statusCode}'
//           '\n Favourite Ads API STATUS RESPONSE :: ${response.body}');
//       if (response.statusCode == 200) {
//         final jsonResponse = json.decode(response.body);
//         return AllAdsResponseModel.fromJson(jsonResponse);
//       }
//     } catch (e) {
//       log("Favourite Ads Api Exception :: $e");
//     }
//     return null;
//   }
// }


///
// class FavouriteProductApi {
//   static int startPagination = 0;
//   static int limitPagination = 10;
//
//   static Future<AllAdsResponseModel?> callApi({
//     // int start = 0,
//     // int limit = 10,
//     String? userId,
//     String? search,
//   }) async {
//     Utils.showLog("❤️ Favourite Ads Api Calling...");
//     final token = await FirebaseAccessToken.onGet();
//
//     // final query = <String, String>{
//     //   'search': (search == null || search.isEmpty) ? 'All' : search,
//     //   'start': '$start',
//     //   'limit': '$limit',
//     //   'userId': userId ?? '',
//     // };
//     startPagination += 1;
//     final queryParameters = {
//       ApiParams.search: (search == null || search.isEmpty) ? 'All' : search,
//       ApiParams.start: startPagination.toString(),
//       ApiParams.limit: limitPagination.toString(),
//       ApiParams.userId: userId,
//     };
//
//
//     final uri =
//     Uri.parse(Api.userLikedAdListApi).replace(queryParameters: queryParameters);
//
//     Utils.showLog("uri favourite api >>>>>>>>${uri}");
//
//     final headers = {
//       ApiParams.key: Api.secretKey,
//       ApiParams.authToken: 'Bearer $token',
//       ApiParams.authUid:
//       Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
//       ApiParams.contentType: 'application/json',
//     };
//
//     try {
//       final response = await http.get(uri, headers: headers);
//       log('Favourite Ads API STATUS CODE :: ${response.statusCode}'
//           '\n Favourite Ads API STATUS RESPONSE :: ${response.body}');
//       if (response.statusCode == 200) {
//         final jsonResponse = json.decode(response.body);
//         return AllAdsResponseModel.fromJson(jsonResponse);
//       }
//     } catch (e) {
//       log("Favourite Ads Api Exception :: $e");
//     }
//     return null;
//   }
// }



class FavouriteProductApi {
  static int startPagination = 0;
  static int limitPagination = 10;

  static Future<AllAdsResponseModel?> callApi({
    String? userId,
    String? search,
    bool isRefresh = false,
  }) async {
    Utils.showLog("❤️ Favourite Ads Api Calling...");
    final token = await FirebaseAccessToken.onGet();

    // 🔥 જો refresh/search છે તો start = 0, pagination માટે વધારો
    if (isRefresh) {
      startPagination = 0;
    } else {
      startPagination += 1;
    }

    final queryParameters = {
      ApiParams.search: (search == null || search.isEmpty) ? 'All' : search,
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
      ApiParams.userId: userId,
    };

    final uri = Uri.parse(Api.userLikedAdListApi).replace(queryParameters: queryParameters);

    Utils.showLog("uri favourite api >>>>>>>>${uri}");
    Utils.showLog("🔍 Search: $search | Start: $startPagination | Refresh: $isRefresh");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? "",
      ApiParams.contentType: 'application/json',
    };

    try {
      final response = await http.get(uri, headers: headers);
      log('Favourite Ads API STATUS CODE :: ${response.statusCode}'
          '\n Favourite Ads API STATUS RESPONSE :: ${response.body}');
      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);
        return AllAdsResponseModel.fromJson(jsonResponse);
      }
    } catch (e) {
      log("Favourite Ads Api Exception :: $e");
    }
    return null;
  }
}


