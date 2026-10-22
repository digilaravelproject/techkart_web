# BEENZO Affiliate & Referral System — Mobile App (APK) Integration Documentation

This document covers the **GraphQL API** (Primary for Mobile APK) and **REST API** (Alternative) to integrate the **Affiliate, Referral & Wallet System** into the **Mobile App (Flutter / React Native / Android Native / iOS)**.

---

## 1. GraphQL API (Primary Endpoint for APK)

- **Endpoint URL:** `https://indigo-rhinoceros-151425.hostingersite.com/api/graphql`
- **HTTP Method:** `POST`
- **Headers:**
  ```http
  Content-Type: application/json
  Accept: application/json
  Authorization: Bearer <customer_sanctum_token>
  ```
  *(Note: For dev testing, `X-Customer-Id: <id>` can also be passed).*

---

### GraphQL Queries

#### 1. `affiliateDashboard` (Customer Wallet & Overview)
Fetches the affiliate's live wallet balances, referral code, click/order counts, and saved payout settings.

**Query:**
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

**Response Example:**
```json
{
  "data": {
    "affiliateDashboard": {
      "affiliateId": 1,
      "status": "active",
      "referralCode": "BNZ8X9K2L",
      "referralLink": "https://indigo-rhinoceros-151425.hostingersite.com?ref=BNZ8X9K2L",
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
        "conversionRate": 7.1
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
Call on the **Product Detail Page (PDP)**. Dynamically calculates the estimated commission and generates a WhatsApp-ready share link.

**Query:**
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

**Response Example:**
```json
{
  "data": {
    "productShareInfo": {
      "productId": 1,
      "name": "Testing Product by Digi Store",
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
      "shareUrl": "https://indigo-rhinoceros-151425.hostingersite.com/testing-product-by-digi-store?ref=BNZ8X9K2L",
      "shareMessage": "Hey! Check out Testing Product by Digi Store on BEENZO: https://indigo-rhinoceros-151425.hostingersite.com/testing-product-by-digi-store?ref=BNZ8X9K2L"
    }
  }
}
```

---

#### 3. `affiliateTransactions` (Wallet Passbook)
Paginated list of transactions (credits, debits, withdrawals).

**Query:**
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

---

#### 4. `affiliateCommissions` (Referral Order Earnings)
List of referral orders that earned commissions.

**Query:**
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
List of payout requests with status and bank UTR reference once paid.

**Query:**
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

### GraphQL Mutations

#### 1. `registerAffiliate` (1-Click Activate Program)
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

---

#### 2. `generateAffiliateLink` (Custom Link Generator)
**Mutation:**
```graphql
mutation GenerateAffiliateLink($url: String, $productId: Int) {
  generateAffiliateLink(url: $url, productId: $productId) {
    success
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

---

#### 3. `updateAffiliatePayoutSettings` (Save UPI or Bank Account)
**Mutation (UPI):**
```graphql
mutation UpdatePayoutSettings($payoutMethod: String!, $upiId: String) {
  updateAffiliatePayoutSettings(payoutMethod: $payoutMethod, upiId: $upiId) {
    success
    message
    payoutMethod
    details
  }
}
```
**Variables:**
```json
{
  "payoutMethod": "upi",
  "upiId": "rahul@okhdfcbank"
}
```

**Mutation (Bank Transfer):**
```graphql
mutation UpdateBankPayoutSettings(
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

---

#### 4. `requestAffiliatePayout` (Withdraw Available Balance)
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
**Variables:**
```json
{
  "amount": 500.0,
  "payoutMethod": "upi",
  "upiId": "rahul@okhdfcbank"
}
```

---

#### 5. `trackAffiliateReferral` (Record Referral Click)
Call when a user opens the mobile app via a referral deep link (`myapp://?ref=CODE`).

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
**Variables:**
```json
{
  "referralCode": "BNZ8X9K2L"
}
```

---

## 2. REST API (Alternative / Backup)

If you prefer REST, all endpoints are also available:

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/affiliate/dashboard` | Dashboard, balances, stats |
| `POST` | `/api/v1/affiliate/register` | 1-Click activate affiliate |
| `GET` | `/api/v1/affiliate/product-share-info/{id}` | Product commission & share details |
| `POST` | `/api/v1/affiliate/generate-link` | Custom referral link |
| `POST` | `/api/v1/affiliate/payout-settings` | Save UPI / Bank details |
| `GET` | `/api/v1/affiliate/wallet/transactions` | Passbook |
| `GET` | `/api/v1/affiliate/commissions` | Order earnings |
| `POST` | `/api/v1/affiliate/payout-request` | Request withdrawal |
| `GET` | `/api/v1/affiliate/payouts` | Withdrawal requests history |
| `POST` | `/api/v1/affiliate/track-referral` | Record referral click |

---

## 3. Flutter / React Native App Integration Steps

### Step 1: Product Detail Screen (PDP)
1. Send GraphQL query:
   ```graphql
   query { productShareInfo(productId: 1) { commission { displayText } shareMessage } }
   ```
2. Render button:
   `"Share & Earn ₹149.00 (10%)"`
3. Tap triggers `Share.share(data.productShareInfo.shareMessage)`.

### Step 2: "Refer & Earn" Screen in User Profile
1. Send `query { affiliateDashboard { ... } }`.
2. Display Referral Code, Available Wallet Balance, and "Withdraw Funds" button.
3. Tap "Withdraw" opens modal for amount & UPI ID, calling `mutation { requestAffiliatePayout(...) }`.
