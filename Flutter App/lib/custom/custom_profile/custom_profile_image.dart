// ignore_for_file: must_be_immutable

import 'package:blurrycontainer/blurrycontainer.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;
import 'package:listify/utils/api.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

// class CustomProfileImage extends StatelessWidget {
//   final String image;
//   BoxFit? fit;
//   CustomProfileImage({
//     super.key,
//     required this.image,
//     this.fit,
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
//                 AppAsset.profilePlaceHolder,
//                 fit: BoxFit.cover,
//               );
//             },
//             errorWidget: (context, url, error) {
//               return Image.asset(
//                 AppAsset.profilePlaceHolder,
//                 fit: BoxFit.cover,
//               );
//             },
//           )
//         : CachedNetworkImage(
//             imageUrl: "${Api.baseUrl}$image",
//             fit: fit ?? BoxFit.cover,
//             placeholder: (context, url) {
//               return Image.asset(
//                 AppAsset.profilePlaceHolder,
//                 fit: BoxFit.cover,
//               );
//             },
//             errorWidget: (context, url, error) {
//               return Image.asset(
//                 AppAsset.profilePlaceHolder,
//                 fit: BoxFit.cover,
//               );
//             },
//           );
//   }
// }

///============================================================

class CustomProfileImage extends StatelessWidget {
  const CustomProfileImage({super.key, this.image, this.fit, this.isShowPlaceHolder});

  final String? image;
  final BoxFit? fit;

  final bool? isShowPlaceHolder;

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(builder: (context, box) {
      final cacheHeight = (box.maxHeight * 2).toInt();
      final cacheWidth = (box.maxWidth * 2).toInt();

      return (image != null && image != "")
           ? image!.trim().startsWith("http")
          ? Stack(
        fit: StackFit.expand,
        children: [
          CachedNetworkImage(
            imageUrl: image ?? "",
            fit: fit ?? BoxFit.cover,
            errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            memCacheHeight: cacheHeight,
            maxHeightDiskCache: cacheHeight,
            memCacheWidth: cacheWidth,
            maxWidthDiskCache: cacheWidth,
          ),

        ],
      )
          : Database.networkImage(Api.baseUrl + image!) != null
          ? Stack(
        fit: StackFit.expand,
        children: [
          CachedNetworkImage(
            imageUrl: (Api.baseUrl + (image ?? "")),
            fit: fit ?? BoxFit.cover,
            errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
            memCacheHeight: cacheHeight,
            maxHeightDiskCache: cacheHeight,
            memCacheWidth: cacheWidth,
            maxWidthDiskCache: cacheWidth,
          ),
        ],
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
              return Stack(
                fit: StackFit.expand,
                children: [
                  CachedNetworkImage(
                    imageUrl: Api.baseUrl + image!,
                    fit: fit ?? BoxFit.cover,
                    placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
                    errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ProfileImagePlaceHolder(),
                    memCacheHeight: cacheHeight,
                    maxHeightDiskCache: cacheHeight,
                    memCacheWidth: cacheWidth,
                    maxWidthDiskCache: cacheWidth,
                  ),

                ],
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
    return Image.asset(AppAsset.profilePlaceHolder, fit: BoxFit.contain);
  }
}


///============================================================
Future<bool> _onCheckImage(String image) async {
  try {
    final response = await http.head(Uri.parse(image));

    return response.statusCode == 200;
  } catch (e) {
    Utils.showLog('Check Profile Image Filed !! => $e');
    return false;
  }
}
///============================================================
class CustomListenerProfileImage extends StatelessWidget {
  final String image;
  BoxFit? fit;
  CustomListenerProfileImage({
    super.key,
    required this.image,
    this.fit,
  });

  @override
  Widget build(BuildContext context) {
    return (image.startsWith("http"))
        ? CachedNetworkImage(
            imageUrl: image,
            fit: fit ?? BoxFit.cover,
            placeholder: (context, url) {
              return Image.asset(
                AppAsset.profilePlaceHolder,
                fit: BoxFit.cover,
              );
            },
            errorWidget: (context, url, error) {
              return Image.asset(
                AppAsset.profilePlaceHolder,
                fit: BoxFit.cover,
              );
            },
          )
        : CachedNetworkImage(
            imageUrl: "${Api.baseUrl}$image",
            fit: fit ?? BoxFit.cover,
            placeholder: (context, url) {
              return Image.asset(
                AppAsset.profilePlaceHolder,
                fit: BoxFit.cover,
              );
            },
            errorWidget: (context, url, error) {
              return Image.asset(
                AppAsset.profilePlaceHolder,
                fit: BoxFit.cover,
              );
            },
          );
  }
}

// class SendMessageImage extends StatelessWidget {
//   final String image;
//   BoxFit? fit;
//   SendMessageImage({
//     super.key,
//     required this.image,
//     this.fit,
//   });
//
//   @override
//   Widget build(BuildContext context) {
//     return (image.startsWith("http"))
//         ? CachedNetworkImage(
//             imageUrl: image,
//             fit: fit ?? BoxFit.cover,
//             placeholder: (context, url) {
//               return Container(
//                 width: 200,
//                 height: 200,
//                 color: AppColors.lightGrey.withValues(alpha: 0.6),
//                 // padding: const EdgeInsets.symmetric(horizontal: 30),
//                 child: Center(
//                     child: Image.asset(
//                   AppAsset.imagePlaceHolder,
//                   height: 50,
//                   color: AppColors.appRedColor.withValues(alpha: 0.6),
//                 )),
//               );
//             },
//             errorWidget: (context, url, error) {
//               return Container(
//                 width: 200,
//                 height: 200,
//                 color: AppColors.lightGrey.withValues(alpha: 0.6),
//
//                 // padding: const EdgeInsets.symmetric(horizontal: 30),
//                 child: Center(
//                     child: Image.asset(
//                   AppAsset.imagePlaceHolder,
//                   height: 50,
//                   color: AppColors.appRedColor.withValues(alpha: 0.6),
//                 )),
//               );
//             },
//           )
//         : CachedNetworkImage(
//             imageUrl: "${Api.baseUrl}$image",
//             fit: fit ?? BoxFit.cover,
//             placeholder: (context, url) {
//               return Container(
//                 width: 200,
//                 height: 200,
//                 color: AppColors.lightGrey.withValues(alpha: 0.6),
//
//                 // padding: const EdgeInsets.symmetric(horizontal: 30),
//                 child: Center(
//                     child: Image.asset(
//                   AppAsset.imagePlaceHolder,
//                   height: 50,
//                   color: AppColors.appRedColor.withValues(alpha: 0.6),
//                 )),
//               );
//             },
//             errorWidget: (context, url, error) {
//               return Container(
//                 width: 200,
//                 height: 200,
//                 color: AppColors.lightGrey.withValues(alpha: 0.6),
//
//                 // padding: const EdgeInsets.symmetric(horizontal: 30),
//                 child: Center(
//                     child: Image.asset(
//                   AppAsset.imagePlaceHolder,
//                   height: 50,
//                   color: AppColors.appRedColor.withValues(alpha: 0.6),
//                 )),
//               );
//             },
//           );
//   }
// }


///========================================================
class SendMessageImage extends StatelessWidget {
  const SendMessageImage({super.key, this.image, this.fit, this.isShowPlaceHolder});

  final String? image;
  final BoxFit? fit;

  final bool? isShowPlaceHolder;

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(builder: (context, box) {
      final cacheHeight = (box.maxHeight * 2).toInt();
      final cacheWidth = (box.maxWidth * 2).toInt();

      return (image != null && image != "")
           ? image!.trim().startsWith("http")
          ? Stack(
        fit: StackFit.expand,
        children: [
          CachedNetworkImage(
            imageUrl: image ?? "",
            fit: fit ?? BoxFit.cover,
            errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder(),
            placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder(),
            memCacheHeight: cacheHeight,
            maxHeightDiskCache: cacheHeight,
            memCacheWidth: cacheWidth,
            maxWidthDiskCache: cacheWidth,
          ),

        ],
      )
          : Database.networkImage(Api.baseUrl + image!) != null
          ? Stack(
        fit: StackFit.expand,
        children: [
          CachedNetworkImage(
            imageUrl: (Api.baseUrl + (image ?? "")),
            fit: fit ?? BoxFit.cover,
            errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder(),
            placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder(),
            memCacheHeight: cacheHeight,
            maxHeightDiskCache: cacheHeight,
            memCacheWidth: cacheWidth,
            maxWidthDiskCache: cacheWidth,
          ),

        ],
      )
          : FutureBuilder(
        future: _onCheckImage(Api.baseUrl + image!),
        builder: (BuildContext context, AsyncSnapshot<bool> snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder();
          } else if (snapshot.hasError) {
            return isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder();
          } else {
            if (snapshot.data == true) {
              Database.onSetNetworkImage(Api.baseUrl + image!);
              return Stack(
                fit: StackFit.expand,
                children: [
                  CachedNetworkImage(
                    imageUrl: Api.baseUrl + image!,
                    fit: fit ?? BoxFit.cover,
                    placeholder: (context, url) => isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder(),
                    errorWidget: (context, url, error) => isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder(),
                    memCacheHeight: cacheHeight,
                    maxHeightDiskCache: cacheHeight,
                    memCacheWidth: cacheWidth,
                    maxWidthDiskCache: cacheWidth,
                  ),

                ],
              );
            } else {
              return isShowPlaceHolder == false ? Offstage() : ImagePlaceHolder();
            }
          }
        },
      )
          : ImagePlaceHolder();
    });
  }
}



class ImagePlaceHolder extends StatelessWidget {
  const ImagePlaceHolder({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(

        color: AppColors.white,
        child: Image.asset(AppAsset.imagePlaceHolder, fit: BoxFit.contain).paddingAll(20));
  }
}

///===========================================================

class SendMessageImageFullScreen extends StatelessWidget {
  final String image;
  BoxFit? fit;
  SendMessageImageFullScreen({
    super.key,
    required this.image,
    this.fit,
  });

  @override
  Widget build(BuildContext context) {
    return (image.startsWith("http"))
        ? CachedNetworkImage(
            imageUrl: image,
            fit: fit ?? BoxFit.cover,
            placeholder: (context, url) {
              return SizedBox(
                width: Get.height,
                height: Get.width,
                // color: AppColors.lightGrey.withValues(alpha: 0.6),
                // padding: const EdgeInsets.symmetric(horizontal: 30),
                child: Center(
                    child: Image.asset(
                  AppAsset.imagePlaceHolder,
                  height: 160,
                )),
              );
            },
            errorWidget: (context, url, error) {
              return Center(
                  child: Image.asset(
                AppAsset.imagePlaceHolder,
                height: 160,
              ));
            },
          )
        : CachedNetworkImage(
            imageUrl: "${Api.baseUrl}$image",
            fit: fit ?? BoxFit.cover,
            placeholder: (context, url) {
              return Center(
                  child: Image.asset(
                AppAsset.imagePlaceHolder,
                height: 160,
              ));
            },
            errorWidget: (context, url, error) {
              return Center(
                  child: Image.asset(
                AppAsset.imagePlaceHolder,
                height: 160,
              ));
            },
          );
  }
}
