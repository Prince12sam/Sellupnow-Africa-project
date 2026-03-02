// import 'dart:convert';
// import 'package:http/http.dart' as http;
// import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
// import 'package:listify/utils/api_params.dart';
// import 'package:listify/utils/utils.dart';
// import 'package:listify/utils/api.dart';
//
// // class PopularProductApi {
// //   static int startPagination = 0;
// //   static int limitPagination = 10;
// //
// //   static Future<MostLikeResponseModel?> fetchPopularAds({
// //     String? userId,
// //     String? categoryId,
// //     String? country,
// //     String? state,
// //     String? city,
// //     String? minPrice,
// //     String? maxPrice,
// //     String? postedSince,
// //     String? search,
// //     String? latitude,
// //     String? longitude,
// //     String? rangeInKm,
// //     String? sort,
// //     List<Map<String, dynamic>> attributes = const [],
// //     int start = 1, // ✅ pass explicitly
// //     int limit = 10, // ✅ pass explicitly
// //   }) async {
// //     Utils.showLog("Popular Ads API Calling...");
// //     Utils.showLog("rangeInKm:::::: => $rangeInKm");
// //
// //     startPagination += 1;
// //
// //     final headers = {
// //       ApiParams.key: Api.secretKey,
// //       ApiParams.contentType: 'application/json',
// //     };
// //
// //     final uri = Uri.parse(Api.popularProduct);
// //
// //     final body = {
// //       ApiParams.userId: userId ?? "",
// //       ApiParams.categoryId: categoryId ?? "",
// //       ApiParams.country: country ?? "",
// //       ApiParams.state: state ?? "",
// //       ApiParams.city: city ?? "",
// //       ApiParams.minPrice: minPrice ?? "",
// //       ApiParams.maxPrice: maxPrice ?? "",
// //       ApiParams.postedSince: postedSince ?? "",
// //       ApiParams.search: search ?? "",
// //       ApiParams.latitude: latitude ?? "",
// //       ApiParams.longitude: longitude ?? "",
// //       ApiParams.sort: sort ?? "",
// //       ApiParams.attributes: attributes,
// //       "start": start,
// //       "limit": limit,
// //       "rangeInKm": rangeInKm,
// //     };
// //
// //     Utils.showLog("Popular Ads API URL => $uri");
// //     Utils.showLog("Popular Ads API Body => $body");
// //
// //     try {
// //       final request = http.Request('GET', uri);
// //       request.body = json.encode(body);
// //       request.headers.addAll(headers);
// //
// //       final response = await request.send();
// //
// //       if (response.statusCode == 200) {
// //         final responseBody = await response.stream.bytesToString();
// //         Utils.showLog("Popular Ads API Response => $responseBody");
// //
// //         // ✅ Convert to PopularResponseModel
// //         return MostLikeResponseModel.fromJson(json.decode(responseBody));
// //       } else {
// //         Utils.showLog("Popular Ads API Status Code Error => ${response.statusCode}");
// //         return null;
// //       }
// //     } catch (error) {
// //       Utils.showLog("Popular Ads API Error => $error");
// //       return null;
// //     }
// //   }
// // }
//
//
//
// import 'dart:convert';
// import 'package:http/http.dart' as http;
// import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
// import 'package:listify/utils/api_params.dart';
// import 'package:listify/utils/utils.dart';
// import 'package:listify/utils/api.dart';
//
// class PopularProductApi {
//   static int startPagination = 0; // ✅ Start from 1
//   static int limitPagination = 10;
//
//   static Future<MostLikeResponseModel?> fetchPopularAds({
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
//     int? start, // optional - controller will send page number
//     int? limit,
//   }) async {
//     Utils.showLog("Popular Ads API Calling...");
//
//     final headers = {
//       ApiParams.key: Api.secretKey,
//       ApiParams.contentType: 'application/json',
//     };
//
//     final uri = Uri.parse(Api.popularProduct);
//
//     final body = {
//       ApiParams.userId: userId ?? "",
//       ApiParams.categoryId: categoryId ?? "",
//       ApiParams.country: country ?? "",
//       ApiParams.state: state ?? "",
//       ApiParams.city: city ?? "",
//       ApiParams.minPrice: minPrice ?? "",
//       ApiParams.maxPrice: maxPrice ?? "",
//       ApiParams.postedSince: postedSince ?? "",
//       ApiParams.search: search ?? "",
//       ApiParams.latitude: latitude ?? "",
//       ApiParams.longitude: longitude ?? "",
//       ApiParams.sort: sort ?? "",
//       ApiParams.attributes: attributes,
//       "start": start ?? startPagination,
//       "limit": limit ?? limitPagination,
//       "rangeInKm": rangeInKm,
//     };
//
//     Utils.showLog("Popular Ads API Body => $body");
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
//         Utils.showLog("Popular Ads API Response => $responseBody");
//         return MostLikeResponseModel.fromJson(json.decode(responseBody));
//       } else {
//         Utils.showLog("Popular Ads API Status Code Error => ${response.statusCode}");
//       }
//     } catch (error) {
//       Utils.showLog("Popular Ads API Error => $error");
//     }
//     return null;
//   }
// }
//
//
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:listify/ui/home_screen/model/most_like_response_model.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/utils.dart';
import 'package:listify/utils/api.dart';

class PopularProductApi {
  static int startPagination = 0; // ✅ Start from 0
  static int limitPagination = 10;

  static Future<MostLikeResponseModel?> fetchPopularAds({
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
    int? start,
    int? limit,
  }) async {
    Utils.showLog("Popular Ads API Calling...");

    final headers = {
      ApiParams.key: Api.secretKey,
      ApiParams.contentType: 'application/json',
    };

    final uri = Uri.parse(Api.popularProduct);

    final body = {
      ApiParams.userId: userId ?? "",
      ApiParams.categoryId: categoryId ?? "",
      ApiParams.country: country ?? "",
      ApiParams.state: state ?? "",
      ApiParams.city: city ?? "",
      ApiParams.minPrice: minPrice ?? "",
      ApiParams.maxPrice: maxPrice ?? "",
      ApiParams.postedSince: postedSince ?? "",
      ApiParams.search: search ?? "",
      ApiParams.latitude: latitude ?? "",
      ApiParams.longitude: longitude ?? "",
      ApiParams.sort: sort ?? "",
      ApiParams.attributes: attributes,
      "start": start ?? startPagination,
      "limit": limit ?? limitPagination,
      "rangeInKm": rangeInKm,
    };

    Utils.showLog("Popular Ads API Body => $body");

    try {
      final request = http.Request('GET', uri);
      request.body = json.encode(body);
      request.headers.addAll(headers);

      final response = await request.send();

      if (response.statusCode == 200) {
        final responseBody = await response.stream.bytesToString();
        Utils.showLog("Popular Ads API Response => $responseBody");

        final parsed = json.decode(responseBody);
        final model = MostLikeResponseModel.fromJson(parsed);

        // ✅ No fake data duplication anymore
        return model;
      } else {
        Utils.showLog("Popular Ads API Status Code Error => ${response.statusCode}");
      }
    } catch (error) {
      Utils.showLog("Popular Ads API Error => $error");
    }
    return null;
  }
}

