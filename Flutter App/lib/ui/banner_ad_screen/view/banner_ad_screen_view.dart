import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/banner_ad_screen/controller/banner_ad_screen_controller.dart';
import 'package:listify/ui/banner_ad_screen/model/banner_ad_response_model.dart';
import 'package:listify/utils/app_color.dart';

class BannerAdScreenView extends StatelessWidget {
  const BannerAdScreenView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.white,
      appBar: AppBar(
        backgroundColor: AppColors.appRedColor,
        foregroundColor: AppColors.white,
        title: const Text('Banner Ads'),
        centerTitle: true,
        actions: [
          IconButton(
            tooltip: 'Request New Ad',
            icon: const Icon(Icons.add),
            onPressed: () => Get.toNamed(AppRoutes.bannerAdSubmitScreen),
          ),
        ],
      ),
      body: GetBuilder<BannerAdScreenController>(
        id: BannerAdScreenController.idList,
        builder: (c) {
          if (c.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }
          if (c.ads.isEmpty) {
            return Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.campaign_outlined, size: 60, color: Colors.grey.shade300),
                  const SizedBox(height: 12),
                  const Text('No banner ads yet.',
                      style: TextStyle(color: Colors.grey, fontSize: 14)),
                  const SizedBox(height: 16),
                  ElevatedButton.icon(
                    onPressed: () => Get.toNamed(AppRoutes.bannerAdSubmitScreen),
                    icon: const Icon(Icons.add),
                    label: const Text('Request a Banner Ad'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.appRedColor,
                      foregroundColor: AppColors.white,
                    ),
                  ),
                ],
              ),
            );
          }
          return RefreshIndicator(
            onRefresh: c.fetchAds,
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: c.ads.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (_, i) => _AdCard(ad: c.ads[i]),
            ),
          );
        },
      ),
    );
  }
}

class _AdCard extends StatelessWidget {
  final BannerAd ad;
  const _AdCard({required this.ad});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.profileItemBgColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            ad.title ?? 'Untitled Ad',
            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
          ),
          const SizedBox(height: 6),
          Text(
            ad.slotLabel,
            style: TextStyle(fontSize: 12, color: AppColors.searchText),
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _StatusBadge(status: ad.status ?? 'pending'),
              Text(
                _formatDate(ad.createdAt),
                style: TextStyle(fontSize: 11, color: AppColors.searchText),
              ),
            ],
          ),
        ],
      ),
    );
  }

  String _formatDate(String? dt) {
    if (dt == null) return '';
    try {
      final d = DateTime.parse(dt);
      return '${d.day} ${_months[d.month - 1]} ${d.year}';
    } catch (_) {
      return dt;
    }
  }

  static const _months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
}

class _StatusBadge extends StatelessWidget {
  final String status;
  const _StatusBadge({required this.status});

  Color get _color {
    switch (status) {
      case 'approved': return Colors.green;
      case 'rejected': return Colors.red;
      default: return Colors.orange;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: _color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: _color.withOpacity(0.4)),
      ),
      child: Text(
        status[0].toUpperCase() + status.substring(1),
        style: TextStyle(fontSize: 11, color: _color, fontWeight: FontWeight.w600),
      ),
    );
  }
}
