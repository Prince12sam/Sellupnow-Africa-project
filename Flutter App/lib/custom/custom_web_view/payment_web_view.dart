import 'package:flutter/material.dart';
import 'package:listify/custom/app_bar/custom_app_bar.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/utils.dart';
import 'package:webview_flutter/webview_flutter.dart';

class PaymentWebView extends StatefulWidget {
  final String initialUrl;
  final String successUrlPrefix;
  final String? cancelUrlPrefix;
  final String title;

  const PaymentWebView({
    super.key,
    required this.initialUrl,
    required this.successUrlPrefix,
    this.cancelUrlPrefix,
    this.title = "Payment",
  });

  @override
  State<PaymentWebView> createState() => _PaymentWebViewState();
}

class _PaymentWebViewState extends State<PaymentWebView> {
  WebViewController? controller;
  bool isLoading = true;

  bool _matchesPrefix(String url, String? prefix) {
    if (prefix == null || prefix.isEmpty) return false;
    return url.toLowerCase().startsWith(prefix.toLowerCase());
  }

  @override
  void initState() {
    super.initState();
    Utils.showLog("PaymentWebView init: ${widget.initialUrl}");

    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onNavigationRequest: (request) {
            final url = request.url;
            if (_matchesPrefix(url, widget.successUrlPrefix)) {
              Navigator.of(context).pop(url);
              return NavigationDecision.prevent;
            }
            if (_matchesPrefix(url, widget.cancelUrlPrefix)) {
              Navigator.of(context).pop(null);
              return NavigationDecision.prevent;
            }
            return NavigationDecision.navigate;
          },
          onPageStarted: (_) => setState(() => isLoading = true),
          onPageFinished: (_) => setState(() => isLoading = false),
        ),
      )
      ..loadRequest(Uri.parse(widget.initialUrl));
  }

  @override
  void dispose() {
    controller?.clearLocalStorage();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        flexibleSpace: WebViewAppBar(title: widget.title),
      ),
      body: Stack(
        children: [
          SafeArea(child: WebViewWidget(controller: controller!)),
          if (isLoading)
            Center(
              child: CircularProgressIndicator(color: AppColors.appRedColor),
            ),
        ],
      ),
    );
  }
}

class WebViewAppBar extends StatelessWidget {
  final String? title;
  const WebViewAppBar({super.key, this.title});

  @override
  Widget build(BuildContext context) {
    return PreferredSize(
      preferredSize: const Size.fromHeight(120),
      child: CustomAppBar(
        title: title,
        showLeadingIcon: true,
      ),
    );
  }
}
