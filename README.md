# Navkwa Group Ltd.

Navkwa Group Ltd. builds intelligent software systems for businesses across Africa. The company focuses on practical, long-term technology: custom enterprise platforms, web applications, mobile apps, cloud infrastructure, AI automation, API integrations, and digital transformation support.

Navkwa works with organizations that need software built around their real operations, including construction, healthcare, education, logistics, retail, finance, government, and manufacturing. The goal is to help teams replace manual processes, connect fragmented tools, improve visibility, and scale with reliable systems.

## What The Website Includes

- A modern company homepage for Navkwa Group Ltd.
- A first-page carousel slider for hero images.
- A contact form for project inquiries.
- A live chat widget that stores conversations.
- A payment page for Ghana Mobile Money and Visa/Mastercard payments.
- A backend inbox for reviewing contact messages, chat transcripts, and payment records.

## Carousel Images

Add hero carousel images to:

`public/assets/images/carousel`

Supported formats:

`jpg`, `jpeg`, `png`, `webp`, `gif`, `avif`

The carousel loads images from that folder automatically. File names are sorted naturally, so names like `01-office.jpg`, `02-dashboard.jpg`, and `03-team.jpg` will appear in that order.

After adding or replacing images, refresh the website preview.

## Payments

Payment page:

`http://127.0.0.1:8000/payments`

Supported customer payment choices:

- MTN MoMo
- Telecel Cash
- AirtelTigo Money
- Visa debit card
- Mastercard debit card

Supported gateway adapters:

- Paystack
- Hubtel

The site uses hosted checkout redirects. Do not collect raw card numbers, CVV, or card PINs on this website. Visa and Mastercard details should be entered only on the selected provider's secure checkout page.

### Payment Files

- `routes/web.php` - payment route definitions. Search for `PAYMENT INTEGRATION ROUTES`.
- `app/Http/Controllers/PaymentController.php` - starts payments, handles callbacks, and handles webhooks.
- `app/Models/PaymentTransaction.php` - local SQL record for each payment attempt.
- `app/Payments/PaystackGateway.php` - Paystack API integration. Search for `PAYSTACK INTEGRATION POINT`.
- `app/Payments/HubtelGateway.php` - Hubtel API integration. Search for `HUBTEL INTEGRATION POINT`.
- `resources/views/payments/create.blade.php` - public payment form.
- `resources/views/payments/result.blade.php` - callback result page.
- `resources/views/payments/demo.blade.php` - local fallback page when live API keys are not configured.
- `database/migrations/2026_07_19_000000_create_payment_transactions_table.php` - payment table.

### Environment Variables

Add live credentials to `.env`:

```env
PAYSTACK_PUBLIC_KEY=
PAYSTACK_SECRET_KEY=
PAYSTACK_BASE_URL=https://api.paystack.co

HUBTEL_ACCOUNT_NUMBER=
HUBTEL_CLIENT_ID=
HUBTEL_CLIENT_SECRET=
HUBTEL_CHECKOUT_ENDPOINT=
HUBTEL_STATUS_ENDPOINT=
```

Do not commit live keys to Git.

### Paystack Setup

Paystack is wired to the hosted transaction initialize endpoint and verify endpoint:

- Initialize: `POST https://api.paystack.co/transaction/initialize`
- Verify: `GET https://api.paystack.co/transaction/verify/{reference}`
- Callback route: `/payments/paystack/callback`
- Webhook route: `/payments/paystack/webhook`

The Paystack adapter sends:

- `email`
- `amount` in pesewas
- `currency` as `GHS`
- `reference`
- `callback_url`
- `channels`, either `card` or `mobile_money`
- metadata for customer name, phone, mobile network, and payment note

In the Paystack dashboard, set the webhook URL to:

`https://your-domain.com/payments/paystack/webhook`

For local development without a public URL, use a tunnel such as ngrok and set the tunneled webhook URL in the Paystack dashboard.

### Hubtel Setup

Hubtel supports receiving mobile money payments across networks and card payments, but exact merchant checkout endpoint and payload details may be issued inside the Hubtel developer portal for the merchant account.

Set these values in `.env` after Hubtel provides them:

- `HUBTEL_ACCOUNT_NUMBER`
- `HUBTEL_CLIENT_ID`
- `HUBTEL_CLIENT_SECRET`
- `HUBTEL_CHECKOUT_ENDPOINT`
- `HUBTEL_STATUS_ENDPOINT`

Then open:

`app/Payments/HubtelGateway.php`

Search for:

`HUBTEL INTEGRATION POINT`

Confirm the payload keys and response checkout URL key against the Hubtel merchant documentation for the specific account. The current adapter is intentionally isolated there so Hubtel changes do not affect the rest of the site.

Suggested Hubtel return URLs:

- Callback route: `/payments/hubtel/callback`
- Webhook route: `/payments/hubtel/webhook`

### Payment Database

Run migrations after pulling payment changes:

```bash
php artisan migrate
```

Payment attempts are stored in `payment_transactions` with:

- unique reference
- provider
- payment method
- mobile network
- amount and currency
- customer details
- status
- checkout URL
- provider payload
- paid timestamp

### Provider Documentation

- Hubtel developer overview: `https://developers.hubtel.com/`
- Paystack transaction API: `https://paystack.com/docs/api/transaction/`
- Paystack verify payments: `https://paystack.com/docs/payments/verify-payments/`
- Paystack payment channels: `https://paystack.com/docs/payments/payment-channels/`

## Local Preview

Install dependencies:

```bash
composer install
```

Run database migrations:

```bash
php artisan migrate
```

Start the Laravel server:

```bash
php artisan serve
```

Open the website:

`http://127.0.0.1:8000`

Open the backend inbox:

`http://127.0.0.1:8000/admin`
