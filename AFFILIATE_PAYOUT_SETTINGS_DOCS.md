# BEENZO Affiliate Payout Settings & Bank/UPI Storage Guide

> **For Mobile (Flutter / React Native) & Web Developers**  
> **Base Domain:** `https://benzoo.in`  
> **GraphQL Endpoint:** `https://benzoo.in/api/graphql`  
> **REST Endpoint:** `https://benzoo.in/api/v1/affiliate/payout-settings`  
> **Authentication:** `Authorization: Bearer <sanctum_token>`

---

## 📌 Overview
Affiliate partners earn commission on referral orders and can withdraw their available wallet balance directly to:
1. **UPI (Instant)**: GooglePay, PhonePe, Paytm, BHIM, etc.
2. **Direct Bank Transfer (NEFT/IMPS)**: Account number, IFSC code, Account Holder Name, Bank Name.

This document explains how to **Save, Update, and Retrieve** user Payout Settings so users don't have to re-enter their details every time they withdraw.

---

## 1. GraphQL API (Primary for Mobile App)

### Headers
```http
POST /api/graphql HTTP/1.1
Host: benzoo.in
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_CUSTOMER_SANCTUM_TOKEN
```

---

### 1.1 Save / Update Payout Settings Mutation

Aap GraphQL me **Variables** use karke bhi bhej sakte hain aur **Inline Arguments** ke through bhi. Dono fully supported hain.

#### Option A: Save UPI ID

**Format 1 (Recommended — With Variables):**
```graphql
mutation SaveUPI($payoutMethod: String!, $upiId: String!) {
  updateAffiliatePayoutSettings(payoutMethod: $payoutMethod, upiId: $upiId) {
    success
    message
    payoutMethod
    details
  }
}
```
*Variables:*
```json
{
  "payoutMethod": "upi",
  "upiId": "rahulsharma@okhdfcbank"
}
```

**Format 2 (Inline Query — Direct):**
```graphql
mutation {
  updateAffiliatePayoutSettings(payoutMethod: "upi", upiId: "rahulsharma@okhdfcbank") {
    success
    message
    payoutMethod
    details
  }
}
```

---

#### Option B: Save Bank Transfer Details

**Format 1 (Recommended — With Variables):**
```graphql
mutation SaveBank(
  $payoutMethod: String!
  $accountNumber: String!
  $ifscCode: String!
  $accountHolder: String!
  $bankName: String
) {
  updateAffiliatePayoutSettings(
    payoutMethod: $payoutMethod
    accountNumber: $accountNumber
    ifscCode: $ifscCode
    accountHolder: $accountHolder
    bankName: $bankName
  ) {
    success
    message
    payoutMethod
    details
  }
}
```
*Variables:*
```json
{
  "payoutMethod": "bank_transfer",
  "accountNumber": "50100234567890",
  "ifscCode": "HDFC0001234",
  "accountHolder": "Rahul Sharma",
  "bankName": "HDFC Bank"
}
```

**Format 2 (Inline Query — Direct):**
```graphql
mutation {
  updateAffiliatePayoutSettings(
    payoutMethod: "bank_transfer"
    accountNumber: "50100234567890"
    ifscCode: "HDFC0001234"
    accountHolder: "Rahul Sharma"
    bankName: "HDFC Bank"
  ) {
    success
    message
    payoutMethod
    details
  }
}
```

---

### 1.2 Success Response Example
```json
{
  "data": {
    "updateAffiliatePayoutSettings": {
      "success": true,
      "message": "Payout settings saved successfully.",
      "payoutMethod": "upi",
      "details": {
        "upi_id": "rahulsharma@okhdfcbank"
      }
    }
  }
}
```

---

### 1.3 How to Fetch Saved Payout Profile
User ke pehle se saved payout details screen load par dikhane ke liye `affiliateDashboard` query call karein:

```graphql
query GetSavedPayoutProfile {
  affiliateDashboard {
    payoutProfile {
      method
      upiId
      accountNumber
      ifscCode
      accountHolder
      bankName
    }
  }
}
```

**Response Example:**
```json
{
  "data": {
    "affiliateDashboard": {
      "payoutProfile": {
        "method": "upi",
        "upiId": "rahulsharma@okhdfcbank",
        "accountNumber": null,
        "ifscCode": null,
        "accountHolder": null,
        "bankName": null
      }
    }
  }
}
```

---

## 2. REST API (Alternative)

Agar aap REST API use karte hain, toh endpoint aur payload format niche diya gaya hai.

### Endpoint
`POST https://benzoo.in/api/v1/affiliate/payout-settings`

### Headers
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_CUSTOMER_SANCTUM_TOKEN
```

---

### Request Body (UPI)
> Note: Backend accepts both camelCase (`payoutMethod`, `upiId`) and snake_case (`payout_method`, `upi_id`).

```json
{
  "payout_method": "upi",
  "upi_id": "rahulsharma@okhdfcbank"
}
```

### Request Body (Bank Transfer)
```json
{
  "payout_method": "bank_transfer",
  "account_holder": "Rahul Sharma",
  "account_number": "50100234567890",
  "bank_name": "HDFC Bank",
  "ifsc_code": "HDFC0001234"
}
```

### Response (200 OK)
```json
{
  "success": true,
  "message": "Payout details saved successfully.",
  "data": {
    "payout_method": "upi",
    "payout_details": {
      "upi_id": "rahulsharma@okhdfcbank"
    }
  }
}
```

---

## 3. Withdrawal Request & Auto-Save Feature

Jab customer withdrawal request lagata hai (`requestAffiliatePayout`):
- Agar user ne pehle se settings save kar rakhi hain, toh use dobara UPI ID ya Bank Details daalne ki zaroorat nahi hai; system uske saved details automatically use kar leta hai.
- Agar user withdrawal form me naya UPI ID ya Bank details daalta hai, toh backend us details ko withdrawal ke sath-sath **affiliate profile me bhi auto-save** kar deta hai.

### Withdrawal Mutation:
```graphql
mutation RequestPayout($amount: Float!, $payoutMethod: String, $upiId: String) {
  requestAffiliatePayout(amount: $amount, payoutMethod: $payoutMethod, upiId: $upiId) {
    success
    message
    payoutNumber
    amountFormatted
    status
    availableBalance
    lockedBalance
  }
}
```

*Variables:*
```json
{
  "amount": 500.0,
  "payoutMethod": "upi",
  "upiId": "rahulsharma@okhdfcbank"
}
```

---

## 4. Flutter / Dart Ready Code Snippet

Aap apne Flutter project me is service function ko directly use kar sakte hain:

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class PayoutSettingsService {
  static const String graphqlUrl = 'https://benzoo.in/api/graphql';
  final String authToken;

  PayoutSettingsService({required this.authToken});

  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': 'Bearer $authToken',
  };

  /// 1. Save UPI Details
  Future<bool> saveUpiDetails(String upiId) async {
    const query = r'''
      mutation SaveUPI($payoutMethod: String!, $upiId: String!) {
        updateAffiliatePayoutSettings(payoutMethod: $payoutMethod, upiId: $upiId) {
          success
          message
        }
      }
    ''';

    final res = await http.post(
      Uri.parse(graphqlUrl),
      headers: _headers,
      body: jsonEncode({
        'query': query,
        'variables': {
          'payoutMethod': 'upi',
          'upiId': upiId.trim(),
        },
      }),
    );

    final data = jsonDecode(res.body);
    if (data['errors'] != null && (data['errors'] as List).isNotEmpty) {
      throw Exception(data['errors'][0]['message'] ?? 'Failed to save UPI');
    }
    return data['data']?['updateAffiliatePayoutSettings']?['success'] ?? false;
  }

  /// 2. Save Bank Account Details
  Future<bool> saveBankDetails({
    required String accountHolder,
    required String accountNumber,
    required String ifscCode,
    String? bankName,
  }) async {
    const query = r'''
      mutation SaveBank(
        $payoutMethod: String!
        $accountHolder: String!
        $accountNumber: String!
        $ifscCode: String!
        $bankName: String
      ) {
        updateAffiliatePayoutSettings(
          payoutMethod: $payoutMethod
          accountHolder: $accountHolder
          accountNumber: $accountNumber
          ifscCode: $ifscCode
          bankName: $bankName
        ) {
          success
          message
        }
      }
    ''';

    final res = await http.post(
      Uri.parse(graphqlUrl),
      headers: _headers,
      body: jsonEncode({
        'query': query,
        'variables': {
          'payoutMethod': 'bank_transfer',
          'accountHolder': accountHolder.trim(),
          'accountNumber': accountNumber.trim(),
          'ifscCode': ifscCode.trim().toUpperCase(),
          'bankName': bankName?.trim() ?? '',
        },
      }),
    );

    final data = jsonDecode(res.body);
    if (data['errors'] != null && (data['errors'] as List).isNotEmpty) {
      throw Exception(data['errors'][0]['message'] ?? 'Failed to save bank details');
    }
    return data['data']?['updateAffiliatePayoutSettings']?['success'] ?? false;
  }
}
```

---

## 5. Validation & Error Handling Matrix

| Error Message | Cause | Solution for Mobile App |
|---|---|---|
| `Unauthenticated. Please provide a valid Bearer token.` | Bearer token missing ya expire ho gaya. | User ko Login screen par redirect karein. |
| `UPI ID is required for UPI payout method.` | UPI method select kiya par `upiId` empty bheja. | Input field me "Please enter a valid UPI ID" alert show karein. |
| `Account number, IFSC code, and account holder name are required.` | Bank Transfer me required fields missing hain. | Form validation me sabhi mandatory fields check karein. |
| `Minimum withdrawal amount is ₹500.` | Withdrawal amount ₹500 se kam daala. | Client-side amount validator lagayein (`amount >= 500`). |
| `Requested amount exceeds available balance of ₹X.` | User ke pass balance se zyada amount manga. | Client-side check karein ki amount `<= availableBalance`. |

---

## 6. Developer Checklist

- [x] Endpoints point to `https://benzoo.in`
- [x] Pass `Authorization: Bearer <sanctum_token>`
- [x] Supports both GraphQL `$variables` and inline mutation arguments
- [x] Supports both camelCase (`upiId`) and snake_case (`upi_id`)
- [x] Auto-populates saved details when requesting withdrawal
- [x] Auto-saves details when requesting payout with new details
