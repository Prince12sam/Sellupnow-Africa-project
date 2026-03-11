import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/product_pricing_screen/model/add_listing_api_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class AddListingApi {
  static Future<CreateAdListingResponseModel?> callApi({
    required String uid,
    required String primaryImagePath,
    required List<String> galleryImagePaths,
    required String categoryId,
    required String productName,
    required String subTitle,
    required String description,
    required String contactNumber,
    required String location,
    required String price,
    required String minimumOffer,
    required String scheduledPublishDate,
    required String availableUnits,
    required String auctionStartingPrice,
    required String saleType,
    // required String auctionEndDate,
    required String auctionDurationDays,
    required String reservePriceAmount,
    required bool isReservePriceEnabled,
    required bool isOfferAllowed,
    required bool isAuctionEnabled,
    required List<Map<String, dynamic>> attributes,
  }) async {
    Utils.showLog("Create Ad Listing API Calling...");

    final token = await FirebaseAccessToken.onGet();

    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse(Api.addListingApi),
      );

      Utils.showLog("uid::::::::::::::$uid");

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
      };

      request.headers.addAll(headers);

      Utils.showLog("attributes============ $attributes");
      Utils.showLog("jsonEncode(attributes)============ ${jsonEncode(attributes)}");
      Utils.showLog("categoryId====================== $categoryId");
      Utils.showLog("productName====================== $productName");
      Utils.showLog("subTitle====================== $subTitle");
      Utils.showLog("description====================== $description");
      Utils.showLog("contactNumber====================== $contactNumber");
      Utils.showLog("location====================== $location");
      Utils.showLog("price====================== $price");
      Utils.showLog("minimumOffer====================== $minimumOffer");
      Utils.showLog("scheduledPublishDate====================== $scheduledPublishDate");
      Utils.showLog("availableUnits====================== ${availableUnits.toString()}");
      Utils.showLog("saleType====================== $saleType");
      Utils.showLog("jsonEncode(attributes)============ ${jsonEncode(attributes).toString()}");
      Utils.showLog("reservePriceAmount============ $reservePriceAmount");
      Utils.showLog("auctionStartingPrice============ $auctionStartingPrice");
      // Utils.showLog("auctionEndDate============ ${auctionEndDate}");

      request.fields.addAll({
        ApiParams.category: categoryId,
        ApiParams.title: productName,
        ApiParams.subTitle: subTitle,
        ApiParams.description: description,
        ApiParams.contactNumber: contactNumber,
        ApiParams.location: location,
        ApiParams.saleType: saleType,
        ApiParams.price: price,
        ApiParams.isOfferAllowed: isOfferAllowed ? '1' : '0',
        ApiParams.minimumOffer: minimumOffer,
        ApiParams.isAuctionEnabled: isAuctionEnabled ? '1' : '0',
        ApiParams.scheduledPublishDate: scheduledPublishDate,
        ApiParams.isReservePriceEnabled: isReservePriceEnabled ? '1' : '0',
        ApiParams.availableUnits: availableUnits,
        ApiParams.attributes: jsonEncode(attributes).toString(),

        ///

        ApiParams.reservePriceAmount: reservePriceAmount,
        ApiParams.auctionStartingPrice: auctionStartingPrice,
        // ApiParams.auctionEndDate: auctionEndDate,
        ApiParams.auctionDurationDays: auctionDurationDays,
        // ApiParams.attributes: attributes.join(',') // Or keep as is if list of strings
      });

      // Attach primary image

      Utils.showLog("image:::::::::::::::::::$primaryImagePath");
      request.files.add(await http.MultipartFile.fromPath('primaryImage', primaryImagePath));

      // Attach multiple gallery images

      Utils.showLog("image:::::::::::::::::::$galleryImagePaths");

      for (String path in galleryImagePaths) {
        Utils.showLog("image:::::::::::::::::::$path");

        request.files.add(await http.MultipartFile.fromPath('galleryImages[]', path));
      }

      Utils.showLog("Create Ad Listing Request Fields => ${request.fields}");
      Utils.showLog("Create Ad Listing Files => ${request.files.length}");

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      log("Create Ad Listing Response Code => ${response.statusCode}");
      log("Create Ad Listing Response => $responseBody");

      if (response.statusCode >= 200 && response.statusCode < 300) {
        final dynamic decoded = jsonDecode(responseBody);
        if (decoded is Map<String, dynamic>) {
          final map = Map<String, dynamic>.from(decoded);
          map['status'] = map['status'] ?? true;
          return CreateAdListingResponseModel.fromJson(map);
        }

        return CreateAdListingResponseModel(status: true, message: 'Listing created successfully');
      }

      try {
        final dynamic decoded = jsonDecode(responseBody);
        if (decoded is Map<String, dynamic>) {
          final map = Map<String, dynamic>.from(decoded);
          map['status'] = map['status'] ?? false;
          return CreateAdListingResponseModel.fromJson(map);
        }
      } catch (_) {}

      return CreateAdListingResponseModel(status: false, message: 'Failed to create listing');
    } catch (e) {
      Utils.showLog("Create Ad Listing Error => $e");
      return null;
    }
  }
}
