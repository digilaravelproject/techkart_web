# BEENZO Affiliate, Referral & Wallet System — Flutter / Mobile App Integration Guide

> **Version:** 2.0 (Production Ready)  
> **Base Domain:** `https://benzoo.in`  
> **Primary Endpoint (GraphQL):** `https://benzoo.in/api/graphql`  
> **Alternative Endpoint (REST):** `https://benzoo.in/api/v1/affiliate`  
> **Authentication:** Sanctum Bearer Token (`Authorization: Bearer <token>`)

---

## 📌 Executive Summary for Flutter Developers

1. **100% Instant Auto-Activation:** No admin approval needed. When a user opens the Affiliate/Wallet screen or taps "Join Program", calling `registerAffiliate` (or even `affiliateDashboard`) automatically activates their affiliate account, assigns a unique referral code (`BNZ...`), and sets up their zero-balance wallet immediately.
2. **Complete Wallet Architecture:**
   - **Available Balance:** Ready to be withdrawn.
   - **Pending Balance:** Commission earned but within the return/clearance window (e.g. 7 days).
   - **Locked Balance:** Money in an active withdrawal request awaiting admin bank/UPI payout.
   - **Lifetime Earned & Total Withdrawn:** Lifetime metrics.
3. **Dual API Support:** You can use **GraphQL** (recommended for speed and single-query screens) or standard **REST API**. Both are 100% supported and live.

---

## 🚀 Quick Navigation
1. [Authentication & Request Headers](#1-authentication--request-headers)
2. [App User Journey & Screen Flows](#2-app-user-journey--screen-flows)
3. [GraphQL API Reference (Queries & Mutations)](#3-graphql-api-reference)
4. [REST API Reference (Alternative)](#4-rest-api-reference)
5. [Complete Withdrawal & Payout Workflow](#5-complete-withdrawal--payout-workflow)
6. [Ready-to-Use Flutter / Dart Code](#6-ready-to-use-flutter--dart-code)
7. [Error Handling & Validation Matrix](#7-error-handling--validation-matrix)

---

## 1. Authentication & Request Headers

Har API call me authenticated customer ka Sanctum token pass karna zaroori hai.

### Standard Request Headers
```http
POST /api/graphql HTTP/1.1
Host: benzoo.in
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_CUSTOMER_SANCTUM_TOKEN
```

> **Developer Tip for Testing:** Dev/Staging testing me bina login kiye agar test karna ho, toh header me `X-Customer-Id: <id>` (e.g. `X-Customer-Id: 1`) pass karke bhi request test ki ja sakti hai.

---

## 2. App User Journey & Screen Flows

```mermaid
graph TD
    A[User Opens App via Link/Invite] -->|Deep Link ref=CODE| B[Track Referral Click]
    C[User on Product Details Screen] --> D[Fetch productShareInfo]
    D -->|Tap 'Share & Earn'| E[Native Share WhatsApp / Social]
    F[User in Profile / Account Menu] -->|Tap 'Refer & Earn / Wallet'| G[Get affiliateDashboard]
    G -->|New User| H[Auto-Activate Account & Wallet]
    G -->|Existing User| I[View Available, Pending & Locked Balances]
    I --> J[View Passbook / Transactions]
    I --> K[View Referral Orders / Commissions]
    I -->|Tap 'Withdraw Funds'| L[Check Min ₹500 & Save UPI/Bank]
    L --> M[Submit Withdrawal Request]
    M --> N[Balance moves Available -> Locked]
    N --> O[Track Payout Status in History]
```

### Screen 1: Product Detail Page (PDP) — "Share & Earn"
1. Product load hone par `productShareInfo(productId: 123)` call karein.
2. Agar `isAffiliate == true`, screen par **"Share & Earn ₹149.00 (10%)"** badge ya button show karein.
3. User tap kare toh Flutter ka `Share.share(data.shareMessage)` trigger ho, jisme product link ke sath user ka referral code `?ref=CODE` attach hoga.

### Screen 2: "Refer & Earn / Wallet" Dashboard (in Profile)
1. `affiliateDashboard` query call karein.
2. User ka **Referral Code** (Copy button ke sath) aur **WhatsApp Share Link** display karein.
3. Top Wallet Card me 3 main balances show karein:
   - **Available for Withdrawal:** `₹1,250.00`
   - **Pending Clearance:** `₹350.00`
   - **In Withdrawal Process (Locked):** `₹500.00`
4. Action buttons:
   - **"Withdraw Money"** (Button opens withdrawal bottom sheet).
   - **"Passbook / Transactions"** (Opens transaction history).
   - **"Earnings from Orders"** (Opens referral commission list).
   - **"Payout Settings"** (Setup UPI / Bank Details).

---

## 3. GraphQL API Reference

### 3.1 Queries

#### 1. `affiliateDashboard` (Main Wallet & Stats)
Customer ka complete wallet, stats, saved payout details aur minimum withdrawal config fetch karta hai.

**GraphQL Query:**
```graphql
query GetAffiliateDashboard {
  affiliateDashboard {
    affiliateId
    status
    referralCode
    referralLink
    wallet {
      availableBalance
      availableFormatted
      pendingBalance
      pendingFormatted
      lockedBalance
      lockedFormatted
      lifetimeEarned
      lifetimeFormatted
      totalWithdrawn
      currency
      currencySymbol
    }
    stats {
      totalClicks
      totalOrders
      conversionRate
    }
    payoutProfile {
      method
      upiId
      accountNumber
      ifscCode
      accountHolder
      bankName
    }
    config {
      minWithdrawalAmount
      clearanceDays
      defaultCommissionType
      defaultCommissionValue
    }
  }
}
```

**JSON Response Example:**
```json
{
  "data": {
    "affiliateDashboard": {
      "affiliateId": 1,
      "status": "active",
      "referralCode": "BNZ8X9K2L",
      "referralLink": "https://benzoo.in?ref=BNZ8X9K2L",
      "wallet": {
        "availableBalance": 1250.0,
        "availableFormatted": "₹1,250.00",
        "pendingBalance": 350.0,
        "pendingFormatted": "₹350.00",
        "lockedBalance": 0.0,
        "lockedFormatted": "₹0.00",
        "lifetimeEarned": 1600.0,
        "lifetimeFormatted": "₹1,600.00",
        "totalWithdrawn": 0.0,
        "currency": "INR",
        "currencySymbol": "₹"
      },
      "stats": {
        "totalClicks": 84,
        "totalOrders": 6,
        "conversionRate": 7.14
      },
      "payoutProfile": {
        "method": "upi",
        "upiId": "rahul@okhdfcbank"
      },
      "config": {
        "minWithdrawalAmount": 500.0,
        "clearanceDays": 7,
        "defaultCommissionType": "percentage",
        "defaultCommissionValue": 10.0
      }
    }
  }
}
```

---

#### 2. `productShareInfo` (For Product Detail Screen)
Product screen par commission amount calculate karne aur share message generate karne ke liye.

**GraphQL Query:**
```graphql
query GetProductShareInfo($productId: Int!) {
  productShareInfo(productId: $productId) {
    productId
    name
    urlKey
    price
    priceFormatted
    commission {
      isExcluded
      type
      rate
      estimatedAmount
      displayText
    }
    isAffiliate
    referralCode
    shareUrl
    shareMessage
  }
}
```

**Variables:**
```json
{
  "productId": 1
}
```

**JSON Response Example:**
```json
{
  "data": {
    "productShareInfo": {
      "productId": 1,
      "name": "Men Casual Sneakers",
      "price": 1490.0,
      "priceFormatted": "₹1,490.00",
      "commission": {
        "isExcluded": false,
        "type": "percentage",
        "rate": 10.0,
        "estimatedAmount": 149.0,
        "displayText": "Earn ₹149.00 (10%)"
      },
      "isAffiliate": true,
      "referralCode": "BNZ8X9K2L",
      "shareUrl": "https://benzoo.in/men-casual-sneakers?ref=BNZ8X9K2L",
      "shareMessage": "Check out Men Casual Sneakers on BEENZO! Buy now: https://benzoo.in/men-casual-sneakers?ref=BNZ8X9K2L"
    }
  }
}
```

---

#### 3. `affiliateTransactions` (Wallet Passbook)
Wallet me hone wale sabhi credits, debits aur withdrawals ki paginated list.

**GraphQL Query:**
```graphql
query GetAffiliateTransactions($page: Int, $limit: Int) {
  affiliateTransactions(page: $page, limit: $limit) {
    data {
      id
      transactionNumber
      type
      amount
      amountFormatted
      balanceAfter
      balanceAfterFormatted
      status
      title
      description
      createdAt
    }
    meta {
      currentPage
      lastPage
      perPage
      total
    }
  }
}
```

**Variables:**
```json
{
  "page": 1,
  "limit": 15
}
```

**JSON Response Example:**
```json
{
  "data": {
    "affiliateTransactions": {
      "data": [
        {
          "id": 12,
          "transactionNumber": "TXN-20261027-0012",
          "type": "credit",
          "amount": 149.0,
          "amountFormatted": "₹149.00",
          "balanceAfter": 1250.0,
          "balanceAfterFormatted": "₹1,250.00",
          "status": "completed",
          "title": "Commission Cleared",
          "description": "Commission for Order #10042 cleared to wallet",
          "createdAt": "2026-10-27 12:30:00"
        }
      ],
      "meta": {
        "currentPage": 1,
        "lastPage": 1,
        "perPage": 15,
        "total": 1
      }
    }
  }
}
```

---

#### 4. `affiliateCommissions` (Order Level Earnings)
Konsi order se kitna commission bana aur kab clear hoga (`clearsAt`).

**GraphQL Query:**
```graphql
query GetAffiliateCommissions($page: Int, $limit: Int) {
  affiliateCommissions(page: $page, limit: $limit) {
    data {
      id
      orderId
      orderNumber
      itemPrice
      itemQty
      commissionType
      commissionRate
      commissionAmount
      commissionFormatted
      status
      clearsAt
      createdAt
    }
    meta {
      currentPage
      lastPage
      perPage
      total
    }
  }
}
```

---

#### 5. `affiliatePayouts` (Withdrawal Requests History)
User dwara lagayi gayi withdrawals ki list, current status (`pending`, `processing`, `paid`, `rejected`) aur bank UTR number.

**GraphQL Query:**
```graphql
query GetAffiliatePayouts($page: Int, $limit: Int) {
  affiliatePayouts(page: $page, limit: $limit) {
    data {
      id
      payoutNumber
      amount
      amountFormatted
      payoutMethod
      status
      transactionReference
      adminNotes
      createdAt
    }
    meta {
      currentPage
      lastPage
      perPage
      total
    }
  }
}
```

---

### 3.2 GraphQL Mutations

#### 1. `registerAffiliate` (1-Click Auto Activation)
Agar user abhi tak affiliate nahi bana, toh yeh mutation call karte hi automatically account active ho jata hai.

**Mutation:**
```graphql
mutation RegisterAffiliate {
  registerAffiliate {
    success
    message
    affiliateId
    referralCode
    referralLink
    status
  }
}
```

**Response Example:**
```json
{
  "data": {
    "registerAffiliate": {
      "success": true,
      "message": "Affiliate account activated successfully.",
      "affiliateId": 1,
      "referralCode": "BNZ8X9K2L",
      "referralLink": "https://benzoo.in?ref=BNZ8X9K2L",
      "status": "active"
    }
  }
}
```

---

#### 2. `updateAffiliatePayoutSettings` (Save UPI or Bank Details)
Withdrawal se pehle user apna UPI ID ya Bank details save kar sakta hai.

**Option A: UPI ID Save Karna**
```graphql
mutation SaveUPIPayout($payoutMethod: String!, $upiId: String!) {
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
  "upiId": "rahul@okhdfcbank"
}
```

**Option B: Bank Account Details Save Karna**
```graphql
mutation SaveBankPayout(
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
  "accountNumber": "987654321012",
  "ifscCode": "HDFC0001234",
  "accountHolder": "Rahul Sharma",
  "bankName": "HDFC Bank"
}
```

---

#### 3. `requestAffiliatePayout` (Withdrawal Request Lagana)
Available Balance me se withdrawal request lagane ke liye.

**Validation Rules:**
- `amount` must be `>= config.minWithdrawalAmount` (e.g. ₹500).
- `amount` must be `<= wallet.availableBalance`.
- Valid `upiId` ya bank details honi chahiye.

**Mutation:**
```graphql
mutation RequestPayout($amount: Float!, $payoutMethod: String, $upiId: String) {
  requestAffiliatePayout(amount: $amount, payoutMethod: $payoutMethod, upiId: $upiId) {
    success
    message
    payoutId
    payoutNumber
    amount
    amountFormatted
    method
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
  "upiId": "rahul@okhdfcbank"
}
```

**Response Example:**
```json
{
  "data": {
    "requestAffiliatePayout": {
      "success": true,
      "message": "Payout request submitted successfully.",
      "payoutId": 4,
      "payoutNumber": "PO-20261027-0004",
      "amount": 500.0,
      "amountFormatted": "₹500.00",
      "method": "upi",
      "status": "pending",
      "availableBalance": 750.0,
      "lockedBalance": 500.0
    }
  }
}
```

---

#### 4. `trackAffiliateReferral` (Deep Link Tracking)
Jab koi new customer kisi referral link (`https://benzoo.in?ref=BNZ8X9K2L` ya deep link `myapp://open?ref=BNZ8X9K2L`) se app open karta hai, toh referral click record karne ke liye yeh mutation call karein.

**Mutation:**
```graphql
mutation TrackReferral($referralCode: String!) {
  trackAffiliateReferral(referralCode: $referralCode) {
    success
    message
    referralCode
  }
}
```

*Variables:*
```json
{
  "referralCode": "BNZ8X9K2L"
}
```

---

## 4. REST API Reference (Alternative)

Agar aapke app me REST API client use hota hai, toh saare features REST par bhi live hain:

| Feature | Method | Endpoint | Description |
|---|---|---|---|
| **Dashboard** | `GET` | `/api/v1/affiliate/dashboard` | Balances, stats & payout profile |
| **Activate** | `POST` | `/api/v1/affiliate/register` | 1-Click activate affiliate |
| **PDP Share Info** | `GET` | `/api/v1/affiliate/product-share-info/{productId}` | Product commission & share text |
| **Custom Link** | `POST` | `/api/v1/affiliate/generate-link` | Generate link for any URL/Product |
| **Save Payout** | `POST` | `/api/v1/affiliate/payout-settings` | Save UPI ID or Bank Account |
| **Passbook** | `GET` | `/api/v1/affiliate/wallet/transactions?page=1&limit=15` | Wallet transactions passbook |
| **Commissions** | `GET` | `/api/v1/affiliate/commissions?page=1&limit=15` | Order commission earnings |
| **Withdrawal Request** | `POST` | `/api/v1/affiliate/payout-request` | Submit withdrawal request |
| **Payout History** | `GET` | `/api/v1/affiliate/payouts?page=1&limit=15` | History of payouts with UTR ref |
| **Track Click** | `POST` | `/api/v1/affiliate/track-referral` | Record referral click code |

---

## 5. Complete Withdrawal & Payout Workflow

```text
[Customer Wallet]
Available: ₹1,200 | Locked: ₹0 | Total Withdrawn: ₹0
        │
        ▼ (Customer requests ₹500 via UPI)
[Transaction Created]
Available: ₹700  | Locked: ₹500 (Status: Pending)
        │
        ├──────────────────────────┐
        ▼ (Admin Approves & Transfers via Bank/UPI)  ▼ (Admin Rejects)
[Status: Paid, UTR Ref: 9812739812]        [Status: Rejected with reason]
Available: ₹700                            Available: ₹1,200 (₹500 refunded to Available)
Locked: ₹0                                 Locked: ₹0
Total Withdrawn: ₹500
```

1. **Available Balance:** Orders ka return period expire hote hi commission automatically available balance me aa jata hai.
2. **Withdrawal Request:** Customer jab withdraw request lagata hai, amount turant `available_balance` se hat kar `locked_balance` me chala jata hai taaki double spending na ho sake.
3. **Admin Payout:** Admin apne panel se UPI ya Netbanking se transfer karke UTR daal kar status **Paid** karta hai. Agar reject hota hai toh paise wapas `available_balance` me refund ho jate hain.

---

## 6. Ready-to-Use Flutter / Dart Code

### 6.1 `AffiliateApiService.dart` (Full GraphQL & REST Service)

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class AffiliateApiService {
  static const String baseUrl = 'https://benzoo.in';
  static const String graphqlUrl = '$baseUrl/api/graphql';

  final String authToken;

  AffiliateApiService({required this.authToken});

  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': 'Bearer $authToken',
  };

  /// Helper to send GraphQL queries
  Future<Map<String, dynamic>> _sendGraphQL(String query, [Map<String, dynamic>? variables]) async {
    final response = await http.post(
      Uri.parse(graphqlUrl),
      headers: _headers,
      body: jsonEncode({
        'query': query,
        if (variables != null) 'variables': variables,
      }),
    );

    final resData = jsonDecode(response.body);

    if (resData['errors'] != null && (resData['errors'] as List).isNotEmpty) {
      throw Exception(resData['errors'][0]['message'] ?? 'GraphQL Error');
    }

    return resData['data'] ?? {};
  }

  /// 1. Get Wallet & Dashboard Data
  Future<Map<String, dynamic>> getDashboard() async {
    const query = r'''
      query GetAffiliateDashboard {
        affiliateDashboard {
          affiliateId
          status
          referralCode
          referralLink
          wallet {
            availableBalance
            availableFormatted
            pendingBalance
            pendingFormatted
            lockedBalance
            lockedFormatted
            lifetimeEarned
            lifetimeFormatted
            totalWithdrawn
            currencySymbol
          }
          stats {
            totalClicks
            totalOrders
            conversionRate
          }
          payoutProfile {
            method
            upiId
            accountNumber
            ifscCode
            accountHolder
            bankName
          }
          config {
            minWithdrawalAmount
            clearanceDays
          }
        }
      }
    ''';
    final data = await _sendGraphQL(query);
    return data['affiliateDashboard'];
  }

  /// 2. Get Product Share Info for PDP
  Future<Map<String, dynamic>> getProductShareInfo(int productId) async {
    const query = r'''
      query GetProductShareInfo($productId: Int!) {
        productShareInfo(productId: $productId) {
          name
          priceFormatted
          commission {
            isExcluded
            displayText
            estimatedAmount
          }
          isAffiliate
          referralCode
          shareUrl
          shareMessage
        }
      }
    ''';
    final data = await _sendGraphQL(query, {'productId': productId});
    return data['productShareInfo'];
  }

  /// 3. Save UPI Payout Settings
  Future<void> saveUpiPayout(String upiId) async {
    const query = r'''
      mutation SaveUPI($payoutMethod: String!, $upiId: String!) {
        updateAffiliatePayoutSettings(payoutMethod: $payoutMethod, upiId: $upiId) {
          success
          message
        }
      }
    ''';
    await _sendGraphQL(query, {'payoutMethod': 'upi', 'upiId': upiId});
  }

  /// 4. Save Bank Account Payout Settings
  Future<void> saveBankPayout({
    required String accountNumber,
    required String ifscCode,
    required String accountHolder,
    String? bankName,
  }) async {
    const query = r'''
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
        }
      }
    ''';
    await _sendGraphQL(query, {
      'payoutMethod': 'bank_transfer',
      'accountNumber': accountNumber,
      'ifscCode': ifscCode,
      'accountHolder': accountHolder,
      'bankName': bankName ?? '',
    });
  }

  /// 5. Submit Withdrawal Request
  Future<Map<String, dynamic>> requestWithdrawal({
    required double amount,
    String payoutMethod = 'upi',
    String? upiId,
  }) async {
    const query = r'''
      mutation RequestPayout($amount: Float!, $payoutMethod: String, $upiId: String) {
        requestAffiliatePayout(amount: $amount, payoutMethod: $payoutMethod, upiId: $upiId) {
          success
          message
          payoutNumber
          amountFormatted
          availableBalance
          lockedBalance
        }
      }
    ''';
    final data = await _sendGraphQL(query, {
      'amount': amount,
      'payoutMethod': payoutMethod,
      if (upiId != null) 'upiId': upiId,
    });
    return data['requestAffiliatePayout'];
  }

  /// 6. Track Referral Click on Deep Link
  Future<void> trackReferralClick(String referralCode) async {
    const query = r'''
      mutation TrackReferral($referralCode: String!) {
        trackAffiliateReferral(referralCode: $referralCode) {
          success
        }
      }
    ''';
    await _sendGraphQL(query, {'referralCode': referralCode});
  }
}
```

---

### 6.2 Flutter PDP "Share & Earn" Button Widget Example

```dart
import 'package:flutter/material.dart';
import 'package:share_plus/share_plus.dart';

class ShareAndEarnButton extends StatelessWidget {
  final Map<String, dynamic> shareInfo;

  const ShareAndEarnButton({Key? key, required this.shareInfo}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final commission = shareInfo['commission'];
    final displayText = commission?['displayText'] ?? 'Share & Earn';
    final shareMessage = shareInfo['shareMessage'] ?? shareInfo['shareUrl'] ?? '';

    return ElevatedButton.icon(
      style: ElevatedButton.styleFrom(
        backgroundColor: Colors.amber.shade700,
        foregroundColor: Colors.white,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
      ),
      icon: const Icon(Icons.share, size: 20),
      label: Text(
        displayText,
        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
      ),
      onPressed: () {
        Share.share(shareMessage);
      },
    );
  }
}
```

---

### 6.3 Withdrawal Dialog with Instant Client Validation

```dart
void showWithdrawalBottomSheet(BuildContext context, {
  required double availableBalance,
  required double minAmount,
  required String savedUpi,
  required Function(double amount, String upi) onConfirm,
}) {
  final amountController = TextEditingController();
  final upiController = TextEditingController(text: savedUpi);

  showModalBottomSheet(
    context: context,
    isScrollControlled: true,
    shape: const RoundedRectangleBorder(
      borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
    ),
    builder: (ctx) => Padding(
      padding: EdgeInsets.only(
        left: 20, right: 20, top: 20,
        bottom: MediaQuery.of(ctx).viewInsets.bottom + 20,
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Withdraw Money to UPI',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Text(
            'Available Balance: ₹${availableBalance.toStringAsFixed(2)} | Min: ₹${minAmount.toStringAsFixed(0)}',
            style: TextStyle(color: Colors.grey.shade600),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: amountController,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: const InputDecoration(
              labelText: 'Enter Amount (₹)',
              border: OutlineInputBorder(),
              prefixText: '₹ ',
            ),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: upiController,
            decoration: const InputDecoration(
              labelText: 'UPI ID (e.g. mobile@upi)',
              border: OutlineInputBorder(),
            ),
          ),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            height: 48,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green.shade700,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
              ),
              child: const Text('Confirm Withdrawal', style: TextStyle(color: Colors.white, fontSize: 16)),
              onPressed: () {
                final amt = double.tryParse(amountController.text) ?? 0;
                final upi = upiController.text.trim();

                if (amt < minAmount) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Minimum withdrawal amount is ₹${minAmount.toStringAsFixed(0)}')),
                  );
                  return;
                }
                if (amt > availableBalance) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Insufficient available balance!')),
                  );
                  return;
                }
                if (upi.isEmpty) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Please enter a valid UPI ID')),
                  );
                  return;
                }

                Navigator.pop(ctx);
                onConfirm(amt, upi);
              },
            ),
          ),
        ],
      ),
    ),
  );
}
```

---

## 7. Error Handling & Validation Matrix

| HTTP / GraphQL Error | Exact Error Message | Reason & App Action |
|---|---|---|
| `401 Unauthorized` | `Unauthenticated. Please provide a valid Bearer token.` | User logged out hai. App ko Login screen par redirect karna chahiye. |
| `400 Bad Request` | `Minimum withdrawal amount is ₹500.` | User ne ₹500 se kam ka amount dala hai. Alert show karein. |
| `400 Bad Request` | `Requested amount exceeds available balance of ₹X.` | User ke wallet me itne paise nahi hain. Alert show karein. |
| `400 Bad Request` | `UPI ID is required for UPI payout method.` | UPI method select kiya par UPI ID blank chhodi. |
| `400 Bad Request` | `Account number, IFSC code, and account holder name are required.` | Bank transfer me zaroori bank details missing hain. |
| `400 Bad Request` | `Invalid or inactive referral code.` | Invalid referral code se deep link open hua. |

---

## 8. Summary Checklist for Flutter Developer

- [x] Configure Base URL to `https://benzoo.in`
- [x] Pass `Authorization: Bearer <token>` in GraphQL/REST requests
- [x] Implement deep link listener to call `trackAffiliateReferral(referralCode)` on open
- [x] Add "Share & Earn" badge on PDP using `productShareInfo`
- [x] Add "Refer & Earn / Wallet" Screen in Customer Profile with `affiliateDashboard`
- [x] Implement Withdrawal BottomSheet with minimum ₹500 and available balance check
- [x] Show Payout History & Transaction Passbook with status badges
