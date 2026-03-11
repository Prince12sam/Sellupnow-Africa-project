// To parse this JSON data, do
//
//     final categoryAttributesResponseModel = categoryAttributesResponseModelFromJson(jsonString);

import 'dart:convert';

CategoryAttributesResponseModel categoryAttributesResponseModelFromJson(String str) => CategoryAttributesResponseModel.fromJson(json.decode(str));

String categoryAttributesResponseModelToJson(CategoryAttributesResponseModel data) => json.encode(data.toJson());

class CategoryAttributesResponseModel {
  bool? status;
  String? message;
  List<Attribute>? data;

  CategoryAttributesResponseModel({
    this.status,
    this.message,
    this.data,
  });

  factory CategoryAttributesResponseModel.fromJson(Map<String, dynamic> json) => CategoryAttributesResponseModel(
        status: json["status"],
        message: json["message"],
        data: _extractAttributes(json),
      );

  static List<Attribute> _extractAttributes(Map<String, dynamic> json) {
    final dynamic root = json["data"] ?? json["attributes"];
    List<Map<String, dynamic>> nodes = [];

    if (root is List) {
      nodes = root.map((x) => Map<String, dynamic>.from(x as Map)).toList();
    } else if (root is Map && root["attributes"] is List) {
      final list = root["attributes"] as List;
      nodes = list.map((x) => Map<String, dynamic>.from(x as Map)).toList();
    }

    if (nodes.isEmpty) {
      return [];
    }

    final normalized = _normalizeTreeToFields(nodes);
    return normalized.map(Attribute.fromJson).toList();
  }

  static List<Map<String, dynamic>> _normalizeTreeToFields(List<Map<String, dynamic>> nodes) {
    final productNode = nodes.firstWhere(
      (node) {
        final n = (node['name'] ?? '').toString().toLowerCase();
        return n.contains('product detail') || n == 'product details';
      },
      orElse: () => <String, dynamic>{},
    );

    final hasProductNode = productNode.isNotEmpty;
    final sourceNodes = hasProductNode ? _subAttrMaps(productNode) : nodes;

    final out = <Map<String, dynamic>>[];
    for (final node in sourceNodes) {
      out.addAll(_expandNodeToFields(node));
    }

    // De-duplicate by name while preserving order.
    final seen = <String>{};
    final unique = <Map<String, dynamic>>[];
    for (final item in out) {
      final key = (item['name'] ?? '').toString().trim().toLowerCase();
      if (key.isEmpty || seen.contains(key)) continue;
      seen.add(key);
      unique.add(item);
    }

    return unique;
  }

  static List<Map<String, dynamic>> _expandNodeToFields(Map<String, dynamic> node) {
    final children = _subAttrMaps(node);
    if (children.isEmpty) {
      return [_asTextField(node)];
    }

    final allLeafChildren = children.every((c) => _subAttrMaps(c).isEmpty);
    if (allLeafChildren) {
      final options = children
          .map((c) => (c['name'] ?? '').toString().trim())
          .where((name) => name.isNotEmpty)
          .toList();

      // Parent becomes a selectable field with child names as options.
      return [
        {
          ...node,
          'fieldType': 5,
          'values': options,
          'isActive': true,
        }
      ];
    }

    // Group/container node: recursively flatten descendants.
    final out = <Map<String, dynamic>>[];
    for (final child in children) {
      out.addAll(_expandNodeToFields(child));
    }
    return out;
  }

  static Map<String, dynamic> _asTextField(Map<String, dynamic> node) {
    return {
      ...node,
      'fieldType': node['fieldType'] ?? 1,
      'values': node['values'] ?? const [],
      'isActive': true,
    };
  }

  static List<Map<String, dynamic>> _subAttrMaps(Map<String, dynamic> node) {
    final raw = node['sub_attributes'];
    if (raw is! List) return const [];
    return raw.whereType<Map>().map((e) => Map<String, dynamic>.from(e)).toList();
  }

  Map<String, dynamic> toJson() => {
        "status": status,
        "message": message,
        "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
      };
}

class Attribute {
  String? id;
  String? name;
  String? image;
  int? fieldType;
  List<String>? values;
  int? minLength;
  int? maxLength;
  bool? isRequired;
  bool? isActive;
  String? categoryId;
  DateTime? createdAt;
  DateTime? updatedAt;

  Attribute({
    this.id,
    this.name,
    this.image,
    this.fieldType,
    this.values,
    this.minLength,
    this.maxLength,
    this.isRequired,
    this.isActive,
    this.categoryId,
    this.createdAt,
    this.updatedAt,
  });

  factory Attribute.fromJson(Map<String, dynamic> json) => Attribute(
        id: (json["_id"] ?? json["id"])?.toString(),
        name: json["name"],
        image: (json["image"] ?? '').toString(),
        fieldType: _resolveFieldType(json),
        values: _resolveValues(json),
        minLength: _asInt(json["minLength"] ?? json["min_length"]),
        maxLength: _asInt(json["maxLength"] ?? json["max_length"]),
        isRequired: _asBool(json["isRequired"] ?? json["is_required"]) ?? false,
        isActive: _asBool(json["isActive"] ?? json["is_active"] ?? json["status"]) ?? true,
        categoryId: (json["categoryId"] ?? json["category_id"])?.toString(),
        createdAt: json["createdAt"] == null ? null : DateTime.parse(json["createdAt"]),
        updatedAt: json["updatedAt"] == null ? null : DateTime.parse(json["updatedAt"]),
      );

  static int _resolveFieldType(Map<String, dynamic> json) {
    final explicit = _asInt(json["fieldType"] ?? json["field_type"]);
    if (explicit != null && explicit > 0) return explicit;

    final values = _resolveValues(json);
    // If the API gives child options (sub_attributes), render as dropdown by default.
    return values.isNotEmpty ? 5 : 1;
  }

  static List<String> _resolveValues(Map<String, dynamic> json) {
    final dynamic rawValues = json["values"] ?? json["options"];
    if (rawValues is List) {
      return rawValues.map((e) => e.toString()).where((e) => e.trim().isNotEmpty).toList();
    }

    final dynamic rawSubAttrs = json["sub_attributes"];
    if (rawSubAttrs is List) {
      return rawSubAttrs
          .map((e) => e is Map ? (e["name"] ?? '').toString() : e.toString())
          .where((e) => e.trim().isNotEmpty)
          .toList();
    }

    return [];
  }

  static int? _asInt(dynamic value) {
    if (value is int) return value;
    if (value is String) return int.tryParse(value);
    return null;
  }

  static bool? _asBool(dynamic value) {
    if (value is bool) return value;
    if (value is int) return value == 1;
    if (value is String) {
      final v = value.toLowerCase();
      if (v == '1' || v == 'true') return true;
      if (v == '0' || v == 'false') return false;
    }
    return null;
  }

  Map<String, dynamic> toJson() => {
        "_id": id,
        "name": name,
        "image": image,
        "fieldType": fieldType,
        "values": values == null ? [] : List<dynamic>.from(values!.map((x) => x)),
        "minLength": minLength,
        "maxLength": maxLength,
        "isRequired": isRequired,
        "isActive": isActive,
        "categoryId": categoryId,
        "createdAt": createdAt?.toIso8601String(),
        "updatedAt": updatedAt?.toIso8601String(),
      };
}
