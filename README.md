# Paynow PHP API for Garden Guru

Standalone PHP API for handling Paynow payments.

## Setup

1. Install dependencies:
```bash
cd paynow-api
composer install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Update `.env` with your credentials:
```env
PAYNOW_INTEGRATION_ID=your_id
PAYNOW_INTEGRATION_KEY=your_key
PAYNOW_RESULT_URL=https://your-api-url.com/webhook.php
PAYNOW_RETURN_URL=https://shop.gardenguru.co.zw/confirmation
```

## Deploy to Wasmer

1. Install Wasmer Edge:
```bash
curl https://get.wasmer.io -sSfL | sh
```

2. Deploy:
```bash
wasmer deploy
```

## API Endpoints

### POST /initiate.php
Initiate a payment

**Request:**
```json
{
  "orderId": "GG-123456",
  "email": "customer@example.com",
  "phone": "0771234567",
  "amount": 100.00,
  "method": "ecocash"
}
```

**Response:**
```json
{
  "success": true,
  "pollUrl": "https://...",
  "redirectUrl": "https://...",
  "instructions": "..."
}
```

### POST /check-status.php
Check payment status

**Request:**
```json
{
  "pollUrl": "https://www.paynow.co.zw/..."
}
```

**Response:**
```json
{
  "success": true,
  "status": "Paid",
  "paid": true,
  "amount": 100.00,
  "reference": "GG-123456"
}
```

### POST /webhook.php
Receives payment status updates from Paynow (configured in your Paynow dashboard)

## Update Your Store

In your Next.js store, update the checkout to call this API:

```javascript
const response = await fetch('https://your-api-url.com/initiate.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    orderId: 'GG-123456',
    email: 'customer@example.com',
    phone: '0771234567',
    amount: 100.00,
    method: 'ecocash'
  })
});
```

## Test Locally

```bash
cd public
php -S localhost:8000
```

Then test:
```bash
curl -X POST http://localhost:8000/initiate.php \
  -H "Content-Type: application/json" \
  -d '{"orderId":"TEST-123","email":"test@example.com","phone":"0771234567","amount":1.00,"method":"ecocash"}'
```
