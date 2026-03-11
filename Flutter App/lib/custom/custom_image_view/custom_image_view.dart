import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

// class CustomImageView extends StatelessWidget {
//   final String image;
//   final BoxFit? fit;
//   final EdgeInsetsGeometry? padding;
//   const CustomImageView({
//     super.key,
//     required this.image,
//     this.fit,
//     this.padding,
//   });
//
//   @override
//   Widget build(BuildContext context) {
//     return (image.startsWith("http"))
//         ? CachedNetworkImage(
//             imageUrl: image,
//             fit: fit ?? BoxFit.cover,
//             placeholder: (context, url) {
//               return Padding(
//                 padding: padding ?? EdgeInsets.all(20),
//                 child: Image.asset(
//                   AppAsset.imagePlaceHolder,
//                   fit: BoxFit.fill,
//                 ),
//               );
//             },
//             errorWidget: (context, url, error) {
//               return Padding(
//                 padding: padding ?? EdgeInsets.all(10),
//                 child: Image.asset(
//                   AppAsset.imagePlaceHolder,
//                   fit: BoxFit.fill,
//                 ),
//               );
//             },
//           )
//         : image == ''
//             ? Padding(
//                 padding: padding ?? EdgeInsets.all(10),
//                 child: Image.asset(
//                   AppAsset.imagePlaceHolder,
//                   fit: BoxFit.fill,
//                 ),
//               )
//             : CachedNetworkImage(
//                 imageUrl: "${Api.baseUrl}$image",
//                 fit: fit ?? BoxFit.cover,
//                 placeholder: (context, url) {
//                   return Padding(
//                     padding: padding ?? EdgeInsets.all(10),
//                     child: Image.asset(
//                       AppAsset.imagePlaceHolder,
//                       fit: BoxFit.fill,
//                     ),
//                   );
//                 },
//                 errorWidget: (context, url, error) {
//                   return Image.asset(
//                     AppAsset.imagePlaceHolder,
//                     fit: BoxFit.contain,
//                   ).paddingAll(10);
//                 },
//               );
//   }
// }

// class CustomProfileImageView extends StatelessWidget {
//   final String image;
//   final BoxFit? fit;
//   const CustomProfileImageView({
//     super.key,
//     required this.image,
//     required this.fit,
//   });
//
//   @override
//   Widget build(BuildContext context) {
//     return (image.startsWith("http"))
//         ? CachedNetworkImage(
//             imageUrl: image,
//             fit: fit ?? BoxFit.cover,
//             placeholder: (context, url) {
//               return Image.asset(
//                 AppAsset.imagePlaceHolder,
//                 fit: BoxFit.fill,
//               );
//             },
//             errorWidget: (context, url, error) {
//               return Image.asset(
//                 AppAsset.imagePlaceHolder,
//                 fit: BoxFit.fill,
//               );
//             },
//           )
//         : CachedNetworkImage(
//             imageUrl: "${Api.baseUrl}$image",
//             fit: fit ?? BoxFit.cover,
//             placeholder: (context, url) {
//               return Image.asset(
//                 AppAsset.imagePlaceHolder,
//                 fit: BoxFit.fill,
//               );
//             },
//             errorWidget: (context, url, error) {
//               return Image.asset(
//                 AppAsset.imagePlaceHolder,
//                 fit: BoxFit.contain,
//               );
//             },
//           );
//   }
// }

///==============================================  image show widget all image   ====================================================///
class CustomImageView extends StatelessWidget {
  const CustomImageView({super.key, this.image, this.fit, this.isShowPlaceHolder, this.color});

  final String? image;
  final BoxFit? fit;
  final Color? color;

  final bool? isShowPlaceHolder;

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(builder: (context, box) {
      final cacheHeight = (box.maxHeight * 2).toInt();
      final cacheWidth = (box.maxWidth * 2).toInt();

      return (image != null && image != "")
          ? image!.trim().startsWith("http")
          ? CachedNetworkImage(
        color: color,
            imageUrl: image ?? "",
            fit: fit ?? BoxFit.cover,
            errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            // memCacheHeight: cacheHeight,
            // maxHeightDiskCache: cacheHeight,
            // memCacheWidth: cacheWidth,
            // maxWidthDiskCache: cacheWidth,
          )
          : Database.networkImage(Api.baseUrl + image!) != null
          ? CachedNetworkImage(
        color: color,
            imageUrl: (Api.baseUrl + (image ?? "")),
            fit: fit ?? BoxFit.cover,
            errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            // memCacheHeight: cacheHeight,
            // maxHeightDiskCache: cacheHeight,
            // memCacheWidth: cacheWidth,
            // maxWidthDiskCache: cacheWidth,
          )
          : FutureBuilder(
        future: _onCheckImage(Api.baseUrl + image!),
        builder: (BuildContext context, AsyncSnapshot<bool> snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder();
          } else if (snapshot.hasError) {
            return isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder();
          } else {
            if (snapshot.data == true) {
              Database.onSetNetworkImage(Api.baseUrl + image!);
              return CachedNetworkImage(
                color: color,
                imageUrl: Api.baseUrl + image!,
                fit: fit ?? BoxFit.cover,
                placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
                errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
                // memCacheHeight: cacheHeight,
                // maxHeightDiskCache: cacheHeight,
                // memCacheWidth: cacheWidth,
                // maxWidthDiskCache: cacheWidth,
              );
            } else {
              return isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder();
            }
          }
        },
      )
          : ProfileImagePlaceHolder();
    });
  }
}

class ProfileImagePlaceHolder extends StatelessWidget {
  const ProfileImagePlaceHolder({super.key});

  @override
  Widget build(BuildContext context) {
    return Image.asset(AppAsset.imagePlaceHolder, fit: BoxFit.contain).paddingAll(10);
  }
}

Future<bool> _onCheckImage(String image) async {
  try {
    final response = await http.head(Uri.parse(image));

    return response.statusCode == 200;
  } catch (e) {
    Utils.showLog('Check Profile Image Filed !! => $e');
    return false;
  }
}

