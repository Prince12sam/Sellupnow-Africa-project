import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:listify/ui/banner_ad_screen/controller/banner_ad_screen_controller.dart';
import 'package:listify/utils/app_color.dart';

class BannerAdSubmitView extends StatefulWidget {
  const BannerAdSubmitView({super.key});

  @override
  State<BannerAdSubmitView> createState() => _BannerAdSubmitViewState();
}

class _BannerAdSubmitViewState extends State<BannerAdSubmitView> {
  final _formKey = GlobalKey<FormState>();
  final _titleCtrl = TextEditingController();
  final _urlCtrl = TextEditingController();
  final _imagePicker = ImagePicker();
  String? _selectedSlot;
  String? _selectedImagePath;

  @override
  void dispose() {
    _titleCtrl.dispose();
    _urlCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        backgroundColor: AppColors.appRedColor,
        foregroundColor: AppColors.white,
        title: const Text('Request Banner Ad'),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Submit an ad request and our team will review it for approval.',
                style: TextStyle(color: Colors.grey, fontSize: 13),
              ),
              const SizedBox(height: 24),

              // Title Field
              _FieldLabel('Ad Title'),
              const SizedBox(height: 6),
              TextFormField(
                controller: _titleCtrl,
                decoration: _inputDecoration('e.g. Summer Sale Promotion'),
                validator: (v) =>
                    (v == null || v.trim().isEmpty) ? 'Title is required' : null,
              ),
              const SizedBox(height: 18),

              // Slot Dropdown
              _FieldLabel('Ad Slot'),
              const SizedBox(height: 6),
              DropdownButtonFormField<String>(
                value: _selectedSlot,
                items: BannerAdScreenController.slotOptions
                    .map((s) => DropdownMenuItem(
                          value: s['value'],
                          child: Text(s['label']!, overflow: TextOverflow.ellipsis),
                        ))
                    .toList(),
                onChanged: (v) => setState(() => _selectedSlot = v),
                validator: (v) =>
                    (v == null || v.isEmpty) ? 'Please select a slot' : null,
                decoration: _inputDecoration('Select placement'),
                isExpanded: true,
              ),
              const SizedBox(height: 18),

              // Redirect URL Field
              _FieldLabel('Destination URL'),
              const SizedBox(height: 6),
              TextFormField(
                controller: _urlCtrl,
                keyboardType: TextInputType.url,
                decoration: _inputDecoration('https://example.com/landing-page'),
                validator: (v) {
                  if (v == null || v.trim().isEmpty) return 'Destination URL is required';
                  final uri = Uri.tryParse(v.trim());
                  if (uri == null || !uri.hasScheme) return 'Enter a valid URL';
                  return null;
                },
              ),
              const SizedBox(height: 18),

              _FieldLabel('Banner Image'),
              const SizedBox(height: 6),
              InkWell(
                onTap: _pickImage,
                borderRadius: BorderRadius.circular(10),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: AppColors.profileItemBgColor,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: Colors.grey.shade300),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        _selectedImagePath == null ? 'Tap to choose a banner image' : 'Tap to replace selected banner image',
                        style: TextStyle(
                          color: _selectedImagePath == null ? Colors.grey.shade700 : Colors.black87,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      const SizedBox(height: 6),
                      const Text(
                        'Accepted formats: JPG, PNG, GIF, WebP. Max size: 2 MB.',
                        style: TextStyle(color: Colors.grey, fontSize: 12),
                      ),
                      if (_selectedImagePath != null) ...[
                        const SizedBox(height: 12),
                        ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: Image.file(
                            File(_selectedImagePath!),
                            height: 140,
                            width: double.infinity,
                            fit: BoxFit.cover,
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 32),

              // Submit Button
              GetBuilder<BannerAdScreenController>(
                id: BannerAdScreenController.idSubmit,
                builder: (c) => SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: c.isSubmitting ? null : _submit,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.appRedColor,
                      foregroundColor: AppColors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10)),
                    ),
                    child: c.isSubmitting
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                                color: Colors.white, strokeWidth: 2),
                          )
                        : const Text('Submit Request',
                            style: TextStyle(
                                fontSize: 15, fontWeight: FontWeight.w600)),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  InputDecoration _inputDecoration(String hint) {
    return InputDecoration(
      hintText: hint,
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide(color: Colors.grey.shade300),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide(color: Colors.grey.shade300),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide(color: AppColors.appRedColor),
      ),
      filled: true,
      fillColor: AppColors.profileItemBgColor,
    );
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedImagePath == null) {
      Get.snackbar(
        'Error',
        'Please choose a banner image.',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
      return;
    }

    final ok = await Get.find<BannerAdScreenController>().submitAd(
      title: _titleCtrl.text.trim(),
      requestedSlot: _selectedSlot!,
      redirectUrl: _urlCtrl.text.trim(),
      bannerImagePath: _selectedImagePath!,
    );
    if (ok) {
      Get.back();
      Get.snackbar(
        'Submitted!',
        'Your banner ad request has been submitted for review.',
        backgroundColor: Colors.green,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    } else {
      final msg = Get.find<BannerAdScreenController>().submitError ?? 'Submission failed.';
      Get.snackbar(
        'Error',
        msg,
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }

  Future<void> _pickImage() async {
    final image = await _imagePicker.pickImage(source: ImageSource.gallery);
    if (!mounted || image == null) {
      return;
    }

    setState(() {
      _selectedImagePath = image.path;
    });
  }
}

class _FieldLabel extends StatelessWidget {
  final String text;
  const _FieldLabel(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(text,
        style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13));
  }
}
