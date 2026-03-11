import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';
import 'package:listify/utils/api.dart';


// class MostLikedProductApi {
//   static int startPagination = 0;
//   static int limitPagination = 20;
//   static Future<MostLikeResponseModel?> fetchMostLikedAds({
//     String? userId,
//     String? categoryId,
//     String? country,
//     String? state,
//     String? city,
//     String? minPrice,
//     String? maxPrice,
//     String? postedSince,
//     String? search,
//     String? latitude,
//     String? longitude,
//     String? rangeInKm,
//     String? sort,
//     List<Map<String, dynamic>> attributes = const [],
//     int start = 1,
//     int limit = 10,
//   }) async {
//     Utils.showLog("Most Liked Ads API Calling...");
//
//     startPagination += 1;
//     final headers = {
//       'key': Api.secretKey,
//       'Content-Type': 'application/json',
//     };
//
//     final uri = Uri.parse(Api.fetchMostLikedAdsApi);
//
//     final body = {
//       "userId": userId,
//       "categoryId": categoryId,
//       "country": country,
//       "state": state,
//       "city": city,
//       "minPrice": minPrice,
//       "maxPrice": maxPrice,
//       "postedSince": postedSince,
//       "search": search,
//       "latitude": latitude,
//       "longitude": longitude,
//       "rangeInKm": rangeInKm,
//       "sort": sort,
//       "attributes": attributes,
//       "start": start,
//       "limit": limit,
//     };
//
//     Utils.showLog("Most Liked Ads API URL => $uri");
//     Utils.showLog("Most Liked Ads API Body => $body");
//
//     try {
//       final request = http.Request('GET', uri);
//       request.body = json.encode(body);
//       request.headers.addAll(headers);
//
//       final response = await request.send();
//
//       if (response.statusCode == 200) {
//         final responseBody = await response.stream.bytesToString();
//         Utils.showLog("Most Liked Ads API Response => $responseBody");
//         return MostLikeResponseModel.fromJson(json.decode(responseBody));
//       } else {
//         Utils.showLog("Most Liked Ads API Status Code Error => ${response.statusCode}");
//         return null;
//       }
//     } catch (error) {
//       Utils.showLog("Most Liked Ads API Error => $error");
//       return null;
//     }
//   }
// }
class MostLikedProductApi {
  static int startPagination = 1;
  static int limitPagination = 10;
  static Future<MostLikeResponseModel?> fetchMostLikedAds({
    String? userId,
    String? categoryId,
    String? country,
    String? state,
    String? city,
    String? minPrice,
    String? maxPrice,
    String? postedSince,
    String? search,
    String? latitude,
    String? longitude,
    String? rangeInKm,
    String? sort,
    List<Map<String, dynamic>> attributes = const [],
    bool isRefresh = false,
  }) async {
    Utils.showLog("Most Liked Ads API Calling...");

    if (isRefresh) {
      startPagination = 0;
    } else {
      startPagination += 1;
    }
    final headers = {
      'key': Api.secretKey,
      'Content-Type': 'application/json',
    };

    final queryParams = <String, String>{
      "userId": userId ?? "",
      "categoryId": categoryId ?? "",
      "country": country ?? "",
      "state": state ?? "",
      "city": city ?? "",
      "minPrice": minPrice ?? "",
      "maxPrice": maxPrice ?? "",
      "postedSince": postedSince ?? "",
      "search": search ?? "",
      "latitude": latitude ?? "",
      "longitude": longitude ?? "",
      "rangeInKm": rangeInKm ?? "",
      "sort": sort ?? "",
      ApiParams.start: startPagination.toString(),
      ApiParams.limit: limitPagination.toString(),
    };

    if (attributes.isNotEmpty) {
      queryParams["attributes"] = json.encode(attributes);
    }

    final uri = Uri.parse(Api.fetchMostLikedAdsApi).replace(queryParameters: queryParams);

    Utils.showLog("Most Liked Ads API URL => $uri");
    Utils.showLog("🔍 Search: $search | Start: $startPagination  | limitPagination : $limitPagination  | Refresh: $isRefresh");
    Utils.showLog("Most Liked Ads API Query => $queryParams");

    try {
      final response = await http.get(uri, headers: headers);

      if (response.statusCode == 200) {
        final responseBody = response.body;
        Utils.showLog("Most Liked Ads API Response => $responseBody");
        return MostLikeResponseModel.fromJson(json.decode(responseBody));
      } else {
        Utils.showLog("Most Liked Ads API Status Code Error => ${response.statusCode}");
        return null;
      }
    } catch (error) {
      Utils.showLog("Most Liked Ads API Error => $error");
      return null;
    }
  }
}
