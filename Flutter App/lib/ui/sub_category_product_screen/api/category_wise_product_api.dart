import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

// class CategoryWiseProductApi {
//   static int startPagination = 0;
//   static int limitPagination = 20;
//
//   static Future<AllAdsResponseModel?> callApi({
//     required String userId,
//     String? categoryId,
//     required String uid,
//     String? country,
//     String? state,
//     String? city,
//     String? minPrice,
//     String? maxPrice,
//     String? postedSince,
//     String? search,
//     String? latitude,
//     String? longitude,
//     String? sort,
//     List<Map<String, dynamic>> attributes = const [],
//   }) async {
//     Utils.showLog("Category wise product Api Calling...");
//     startPagination += 1;
//
//     final token = await FirebaseAccessToken.onGet();
//
//     final headers = {
//       ApiParams.key: Api.secretKey,
//       ApiParams.authToken: 'Bearer $token',
//       ApiParams.authUid: uid,
//       ApiParams.contentType: 'application/json',
//     };
//
//     // Build query parameters
//     final Map<String, dynamic> queryParameters = {
//       ApiParams.start: startPagination.toString(),
//       ApiParams.limit: limitPagination.toString(),
//     };
//     String query = Uri(queryParameters: queryParameters).query;
//
//     var request = http.Request('GET', Uri.parse(Api.categoryWiseProduct + (query.isNotEmpty ? query : '')));
//
//     request.body = json.encode({
//       ApiParams.userId: userId,
//       ApiParams.categoryId: categoryId,
//       ApiParams.country: country,
//       ApiParams.state: state,
//       ApiParams.city: city,
//       ApiParams.minPrice: minPrice,
//       ApiParams.maxPrice: maxPrice,
//       ApiParams.postedSince: postedSince,
//       ApiParams.search: search,
//       ApiParams.latitude: latitude,
//       ApiParams.longitude: longitude,
//       ApiParams.sort: sort,
//       ApiParams.attributes: attributes,
//       ApiParams.start: startPagination,
//       ApiParams.limit: limitPagination,
//     });
//     request.headers.addAll(headers);
//
//     Utils.showLog("Category Wise Product URL => ${request.url}");
//     Utils.showLog("Category Wise Product Body => ${request.body}");
//
//     try {
//       http.StreamedResponse response = await request.send();
//
//       if (response.statusCode == 200) {
//         final responseBody = await response.stream.bytesToString();
//         Utils.showLog("Category Wise Product Response => $responseBody");
//
//         final Map<String, dynamic> jsonMap = json.decode(responseBody);
//         final model = AllAdsResponseModel.fromJson(jsonMap);
//         return model;
//       } else {
//         Utils.showLog("Category Wise Product API Status Code Error => ${response.statusCode}");
//         return null;
//       }
//     } catch (error) {
//       Utils.showLog("Category Wise Product API Error => $error");
//       return null;
//     }
//   }
// }
class CategoryWiseProductApi {
  static int startPagination = 0;
  static int limitPagination = 20;

  static Future<AllAdsResponseModel?> callApi({
    required String userId,
    String? categoryId,
    required String uid,
    String? country,
    String? state,
    String? city,
    String? minPrice,
    String? maxPrice,
    String? postedSince,
    String? search,
    String? latitude,
    String? longitude,
    String? sort,
    List<Map<String, dynamic>> attributes = const [],
    bool resetPagination = false,
  }) async {
    Utils.showLog("Category wise product Api Calling...");

    if (resetPagination) {
      startPagination = 0;
    }

    final token = await FirebaseAccessToken.onGet();

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.authToken: 'Bearer $token',
      ApiParams.authUid: uid,
      ApiParams.contentType: 'application/json',
    };

    var request = http.Request('POST', Uri.parse(Api.categoryWiseProduct));

    request.body = json.encode({
      ApiParams.userId: userId,
      ApiParams.categoryId: categoryId ?? "",
      ApiParams.country: country,
      ApiParams.state: state,
      ApiParams.city: city,
      ApiParams.minPrice: minPrice,
      ApiParams.maxPrice: maxPrice,
      ApiParams.postedSince: postedSince,
      ApiParams.search: search,
      ApiParams.latitude: latitude,
      ApiParams.longitude: longitude,
      ApiParams.sort: sort,
      ApiParams.attributes: attributes,
      ApiParams.start: startPagination,
      ApiParams.limit: limitPagination,
    });
    request.headers.addAll(headers);

    Utils.showLog("Category Wise Product URL => ${request.url}");
    Utils.showLog("Category Wise Product Body => ${request.body}");

    try {
      http.StreamedResponse response = await request.send();

      if (response.statusCode == 200) {
        final responseBody = await response.stream.bytesToString();
        Utils.showLog("Category Wise Product Response => $responseBody");

        final Map<String, dynamic> jsonMap = json.decode(responseBody);
        return AllAdsResponseModel.fromJson(jsonMap);
      } else {
        Utils.showLog("Category Wise Product API Error => ${response.statusCode}");
        return null;
      }
    } catch (error) {
      Utils.showLog("Category Wise Product API Exception => $error");
      return null;
    }
  }
}

