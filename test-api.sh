#!/bin/bash

# Test Paynow PHP API locally

echo "🧪 Testing Paynow PHP API..."
echo ""

# Start PHP server in background
echo "🚀 Starting PHP server on localhost:8000..."
cd "$(dirname "$0")/public"
php -S localhost:8000 > /dev/null 2>&1 &
PHP_PID=$!
sleep 2

echo "✅ PHP server started (PID: $PHP_PID)"
echo ""

# Test EcoCash payment
echo "📱 Testing EcoCash payment initiation..."
ECOCASH_RESPONSE=$(curl -s -X POST http://localhost:8000/initiate.php \
  -H "Content-Type: application/json" \
  -d '{
    "orderId": "TEST-ECOCASH-123",
    "email": "test@example.com",
    "phone": "0771234567",
    "amount": 1.00,
    "method": "ecocash"
  }')

echo "Response: $ECOCASH_RESPONSE"
echo ""

# Test Paynow web payment
echo "🌐 Testing Paynow web payment initiation..."
PAYNOW_RESPONSE=$(curl -s -X POST http://localhost:8000/initiate.php \
  -H "Content-Type: application/json" \
  -d '{
    "orderId": "TEST-PAYNOW-456",
    "email": "test@example.com",
    "phone": "0771234567",
    "amount": 5.00,
    "method": "paynow"
  }')

echo "Response: $PAYNOW_RESPONSE"
echo ""

# Test payment status check (example)
echo "📊 Testing payment status check..."
STATUS_RESPONSE=$(curl -s -X POST http://localhost:8000/check-status.php \
  -H "Content-Type: application/json" \
  -d '{
    "pollUrl": "https://www.paynow.co.zw/Interface/CheckPayment/?guid=test-guid"
  }')

echo "Response: $STATUS_RESPONSE"
echo ""

# Stop PHP server
echo "🛑 Stopping PHP server..."
kill $PHP_PID
echo "✅ Done!"
