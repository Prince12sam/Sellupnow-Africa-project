// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'package:listify/custom/text_field/custom_text_field.dart';
// import 'package:listify/custom/title/custom_title.dart';
// import 'package:listify/utils/enums.dart';
// import 'package:listify/utils/font_style.dart';
//
// class LoginScreen extends StatelessWidget {
//   const LoginScreen({super.key});
//
//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       body: Column(
//         children: [
//           Center(
//             child: Text(
//               "Listify",
//               style: AppFontStyle.fontStyleW900(fontSize: 30, fontColor: Colors.black),
//             ),
//           ).paddingOnly(top: 50, bottom: 40),
//           CustomTitle(
//             title: EnumLocale.txtEnterYourEmailId.name.tr,
//             method: CustomTextField(filled: true),
//           ).paddingOnly(bottom: 20),
//           CustomTitle(
//             title: EnumLocale.txtEnterYourEmailId.name.tr,
//             method: CustomTextField(filled: true),
//           ),
//         ],
//       ).paddingSymmetric(horizontal: 15),
//     );
//   }
// }
import 'package:flutter/material.dart';
import 'package:listify/ui/login_screen/widget/login_screen_widget.dart';

class LoginScreen extends StatelessWidget {
  const LoginScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SingleChildScrollView(
        child: SafeArea(child: LoginScreenView()),
      ),
    );
  }
}
