# BEENZO Affiliate & Referral System — Mobile App (APK) REST API Documentation

This documentation provides all REST API endpoints required to integrate the **Affiliate, Referral & Wallet System** into the **Mobile App (Flutter / React Native / Android Native / iOS)**.

---

## 1. General Information & Base URL

- **Base URL (Production / Live):** `https://indigo-rhinoceros-151425.hostingersite.com`
- **Supported API Prefixes:**
  - Preferred: `/api/v1/affiliate`
  - Alias: `/api/affiliate`
- **Content-Type:** `application/json`
- **Accept:** `application/json`

---

## 2. Authentication

Endpoints that access customer-specific data (wallet, payouts, personal referral code) require customer authentication.

### Supported Auth Headers:
1. **Bearer Token (Sanctum / Bagisto Customer API):**
   ```http
   Authorization: Bearer <customer_access_token>
   ```
2. **Session / Cookie:**
   Standard customer web session cookie (if using In-App WebView or cookie-based client).
3. **Developer / Testing Header:**
   ```http
   X-Customer-Id: 1
   ```

### Standard Error Response (401 Unauthenticated):
```json
{
  "success": false,
  "message": "Unauthenticated. Please provide a valid Bearer token."
}
```

---

## 3. Endpoints Overview

| Method | Endpoint | Auth Required | Description |
|---|---|---|---|
| `GET` | `/api/v1/affiliate/dashboard` | **Yes** | Full affiliate profile, wallet balances, stats, and payout settings |
| `POST` | `/api/v1/affiliate/register` | **Yes** | 1-Click activate affiliate program for logged-in user |
| `GET` | `/api/v1/affiliate/product-share-info/{productId}` | *Optional* | Product commission %, estimated earning, and personalized share link |
| `POST` | `/api/v1/affiliate/generate-link` | **Yes** | Generate a custom referral link for any product or store URL |
| `POST` | `/api/v1/affiliate/payout-settings` | **Yes** | Save or update customer's UPI ID or Bank Account details |
| `GET` | `/api/v1/affiliate/wallet/transactions` | **Yes** | Paginated wallet passbook transactions |
| `GET` | `/api/v1/affiliate/commissions` | **Yes** | Paginated order commission earnings |
| `POST` | `/api/v1/affiliate/payout-request` | **Yes** | Request a withdrawal from available wallet balance |
| `GET` | `/api/v1/affiliate/payouts` | **Yes** | History of withdrawal requests with UTR / status |
| `POST` | `/api/v1/affiliate/track-referral` | *No* | Track click when someone opens an in-app referral link |

---

## 4. Detailed API Specifications

### 1. Affiliate Dashboard & Wallet Overview
Returns complete wallet balances, statistics, referral code, and saved payout settings.

- **URL:** `GET /api/v1/affiliate/dashboard`
- **Headers:** `Authorization: Bearer <token>`
- **Response `200 OK`:**
```json
{
  "success": true,
  "data": {
    "affiliate_id": 1,
    "status": "active",
    "referral_code": "BNZ8X9K2L",
    "referral_link": "https://indigo-rhinoceros-151425.hostingersite.com?ref=BNZ8X9K2L",
    "wallet": {
      "available_balance": 1250.0,
      "available_formatted": "₹1,250.00",
      "pending_balance": 350.0,
      "pending_formatted": "₹350.00",
      "locked_balance": 500.0,
      "locked_formatted": "₹500.00",
      "lifetime_earned": 2100.0,
      "lifetime_formatted": "₹2,100.00",
      "total_withdrawn": 500.0,
      "total_withdrawn_formatted": "₹500.00",
      "currency": "INR",
      "currency_symbol": "₹"
    },
    "stats": {
      "total_clicks": 84,
      "total_orders": 6,
      "conversion_rate": 7.1
    },
    "payout_profile": {
      "method": "upi",
      "details": {
        "upi_id": "user@okaxis"
      }
    },
    "config": {
      "min_withdrawal_amount": 500.0,
      "clearance_days": 7,
      "default_commission_type": "percentage",
      "default_commission_value": 10.0
    }
  }
}
```

---

### 2. 1-Click Activate / Register as Affiliate
If a customer is not yet registered as an affiliate, this endpoint activates their affiliate profile, assigns a unique referral code, and creates their wallet.

- **URL:** `POST /api/v1/affiliate/register`
- **Headers:** `Authorization: Bearer <token>`
- **Response `200 OK`:**
```json
{
  "success": true,
  "message": "Affiliate account is active.",
  "data": {
    "affiliate_id": 1,
    "referral_code": "BNZ8X9K2L",
    "referral_link": "https://indigo-rhinoceros-151425.hostingersite.com?ref=BNZ8X9K2L",
    "status": "active"
  }
}
```

---

### 3. Product Share & Earn Details (For App Product Detail Page)
Use this endpoint on the **Product Detail Screen** in the mobile app. It dynamically calculates the commission rate (checking product rules, category hierarchy rules, or global defaults).

- **URL:** `GET /api/v1/affiliate/product-share-info/{productId}`
- **Headers (Optional):** `Authorization: Bearer <token>`
- **Response `200 OK`:**
```json
{
  "success": true,
  "data": {
    "product_id": 1,
    "name": "Testing Product by Digi Store",
    "url_key": "testing-product-by-digi-store",
    "price": 1490.0,
    "price_formatted": "₹1,490.00",
    "commission": {
      "is_excluded": false,
      "type": "percentage",
      "rate": 10.0,
      "estimated_amount": 149.0,
      "display_text": "Earn ₹149.00 (10%)"
    },
    "is_affiliate": true,
    "referral_code": "BNZ8X9K2L",
    "share_url": "https://indigo-rhinoceros-151425.hostingersite.com/testing-product-by-digi-store?ref=BNZ8X9K2L",
    "share_message": "Hey! Check out Testing Product by Digi Store on BEENZO: https://indigo-rhinoceros-151425.hostingersite.com/testing-product-by-digi-store?ref=BNZ8X9K2L"
  }
}
```

> **App UI Tip:** Display a prominent badge or button: **"Share & Earn ₹149.00"**. On tap, trigger the native Android/iOS share sheet with `data.share_message`.

---

### 4. Custom Referral Link Generator
Generates a tracked referral URL for any specific store page or product.

- **URL:** `POST /api/v1/affiliate/generate-link`
- **Headers:** `Authorization: Bearer <token>`
- **Request Body (JSON):**
```json
{
  "url": "https://indigo-rhinoceros-151425.hostingersite.com/shoes"
}
```
*OR by product ID:*
```json
{
  "product_id": 1
}
```
- **Response `200 OK`:**
```json
{
  "success": true,
  "data": {
    "referral_code": "BNZ8X9K2L",
    "share_url": "https://indigo-rhinoceros-151425.hostingersite.com/shoes?ref=BNZ8X9K2L",
    "share_message": "Check out this product on BEENZO! Buy now: https://indigo-rhinoceros-151425.hostingersite.com/shoes?ref=BNZ8X9K2L"
  }
}
```

---

### 5. Save Payout Settings (UPI / Bank Account)
Saves the customer's preferred withdrawal method and account details for future payouts.

- **URL:** `POST /api/v1/affiliate/payout-settings`
- **Headers:** `Authorization: Bearer <token>`

#### Option A: UPI Payout
```json
{
  "payout_method": "upi",
  "upi_id": "rahul@okhdfcbank"
}
```

#### Option B: Bank Transfer Payout
```json
{
  "payout_method": "bank_transfer",
  "account_holder": "Rahul Sharma",
  "account_number": "123456789012",
  "bank_name": "HDFC Bank",
  "ifsc_code": "HDFC0001234"
}
```

- **Response `200 OK`:**
```json
{
  "success": true,
  "message": "Payout details saved successfully.",
  "data": {
    "payout_method": "upi",
    "payout_details": {
      "upi_id": "rahul@okhdfcbank"
    }
  }
}
```

---

### 6. Wallet Transactions History (Passbook)
Returns paginated list of all debits, credits, and withdrawals.

- **URL:** `GET /api/v1/affiliate/wallet/transactions?page=1&limit=15`
- **Headers:** `Authorization: Bearer <token>`
- **Response `200 OK`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 10,
      "transaction_number": "TXN-20261022-ABC123",
      "type": "credit",
      "amount": 149.0,
      "amount_formatted": "₹149.00",
      "balance_after": 1250.0,
      "balance_after_formatted": "₹1,250.00",
      "status": "completed",
      "title": "Commission from Order #10042",
      "description": "Commission released for order items",
      "created_at": "2026-10-22 14:30:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

### 7. Commission Earnings History
Returns paginated list of all referral orders that earned commissions.

- **URL:** `GET /api/v1/affiliate/commissions?page=1&limit=15`
- **Headers:** `Authorization: Bearer <token>`
- **Response `200 OK`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "order_id": 42,
      "order_number": "#10042",
      "item_price": 1490.0,
      "item_qty": 1,
      "commission_type": "percentage",
      "commission_rate": 10.0,
      "commission_amount": 149.0,
      "commission_formatted": "₹149.00",
      "status": "pending",
      "clears_at": "2026-10-29 14:30:00",
      "created_at": "2026-10-22 14:30:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

### 8. Request Wallet Withdrawal (Payout)
Submits a withdrawal request to transfer money from `available_balance` to the affiliate's UPI or Bank Account.

- **URL:** `POST /api/v1/affiliate/payout-request`
- **Headers:** `Authorization: Bearer <token>`

#### Request Body:
```json
{
  "amount": 500,
  "payout_method": "upi",
  "upi_id": "rahul@okhdfcbank"
}
```
*(Note: If `payout_method` is omitted, the API automatically uses the saved payout details from customer's profile).*

- **Response `200 OK`:**
```json
{
  "success": true,
  "message": "Payout request submitted successfully and is pending admin approval.",
  "data": {
    "payout_id": 1,
    "payout_number": "PO-20261022-X7Y9Z1",
    "amount": 500.0,
    "amount_formatted": "₹500.00",
    "method": "upi",
    "status": "requested",
    "available_balance": 750.0,
    "locked_balance": 500.0
  }
}
```

#### Error Validation Responses:
- **Amount less than minimum threshold (₹500):**
  `HTTP 422 Unprocessable Content` -> `"The amount must be at least 500."`
- **Amount greater than available balance:**
  `HTTP 422 Unprocessable Content` -> `"The amount may not be greater than 250."`

---

### 9. Customer Payout Withdrawal History
Returns the list of withdrawal requests made by the customer with their current status and bank UTR reference once paid.

- **URL:** `GET /api/v1/affiliate/payouts?page=1&limit=15`
- **Headers:** `Authorization: Bearer <token>`
- **Response `200 OK`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "payout_number": "PO-20261022-X7Y9Z1",
      "amount": 500.0,
      "amount_formatted": "₹500.00",
      "payout_method": "upi",
      "payout_details": {
        "upi_id": "rahul@okhdfcbank"
      },
      "status": "completed",
      "transaction_reference": "UTR98765432100",
      "admin_notes": "Paid via HDFC Current Account",
      "created_at": "2026-10-22 15:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

### 10. Track Referral Click (For Mobile App Deep Links)
When a user opens the mobile app through a referral link or referral deep link (`myapp://product?ref=BNZ8X9K2L`), call this endpoint to register the click.

- **URL:** `POST /api/v1/affiliate/track-referral`
- **Request Body:**
```json
{
  "referral_code": "BNZ8X9K2L"
}
```
- **Response `200 OK`:**
```json
{
  "success": true,
  "message": "Referral click recorded.",
  "data": {
    "referral_code": "BNZ8X9K2L",
    "affiliate_id": 1
  }
}
```

---

## 5. Mobile App (APK) Implementation Blueprint

### Step 1: Product Detail Screen (PDP)
1. Call `GET /api/v1/affiliate/product-share-info/{productId}`.
2. If `data.commission.is_excluded === false`:
   - Render a card below the Buy/Cart buttons:
     - Text: **"Share & Earn ₹149.00 (10%)"**
     - Button: **"Share via WhatsApp"**
3. When clicked:
   - Call native sharing API (e.g., `Share.share(data.share_message)` in React Native or `SharePlus.share()` in Flutter).

### Step 2: "Referral & Wallet" Section in User Account / Menu
1. Add a menu item in user profile: **"Refer & Earn / Wallet"**.
2. Call `GET /api/v1/affiliate/dashboard`.
3. Display:
   - **Referral Code & Link** with **"Copy"** and **"Share"** buttons.
   - **Wallet Cards:**
     - Available Balance (Green)
     - Pending Clearance Balance (Amber)
     - Lifetime Earned
   - **"Withdraw Money"** button:
     - Opens a bottom sheet or dialog to enter amount (min ₹500) and choose UPI ID or Bank Account.
     - Calls `POST /api/v1/affiliate/payout-request`.
   - **Tabs:**
     - Tab 1: **Passbook / Transactions** (`GET /api/v1/affiliate/wallet/transactions`)
     - Tab 2: **Earnings from Referrals** (`GET /api/v1/affiliate/commissions`)
     - Tab 3: **Payout Requests** (`GET /api/v1/affiliate/payouts`)

### Step 3: Deep Link Referral Handling in App
1. When app opens with URL parameter `?ref=CODE`:
   - Save `ref` in local storage (AsyncStorage / SharedPreferences).
   - Fire `POST /api/v1/affiliate/track-referral` with `{ "referral_code": CODE }`.
   - When user places an order in the app, pass the saved `referral_code` in the order checkout request.
