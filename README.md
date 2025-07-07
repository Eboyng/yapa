# Yapa - WhatsApp Bulk Messaging Platform

Yapa is a comprehensive WhatsApp bulk messaging platform built with Laravel and Livewire, featuring a robust credit system, payment integration, and user management.

## Features

### 🎯 Core Features
- **WhatsApp Bulk Messaging**: Send messages to multiple contacts efficiently
- **Credit System**: Purchase and manage credits for messaging
- **Payment Integration**: Seamless Paystack integration for credit purchases
- **User Management**: Complete user registration, verification, and profile management
- **Transaction History**: Detailed tracking of all financial transactions
- **Interest Management**: User interest categorization and targeting

### 💳 Credit System
- **Multiple Wallet Types**: Credits, Naira, and Earnings wallets
- **Secure Transactions**: Optimistic locking and transaction integrity
- **Payment Methods**: Paystack integration with webhook support
- **Transaction Tracking**: Complete audit trail for all transactions
- **Retry Mechanism**: Automatic retry for failed transactions

### 🔐 Security Features
- **OTP Verification**: WhatsApp and SMS verification via Kudisms
- **BVN Encryption**: Secure storage of sensitive user data
- **Webhook Verification**: Secure payment webhook handling
- **Rate Limiting**: Protection against abuse

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL 8.0 or higher

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd yapa
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure environment variables**
   Edit `.env` file with your configuration:
   ```env
   # Database
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=yapa
   DB_USERNAME=root
   DB_PASSWORD=your_password
   
   # Paystack
   PAYSTACK_PUBLIC_KEY=pk_test_your_key
   PAYSTACK_SECRET_KEY=sk_test_your_key
   PAYSTACK_WEBHOOK_SECRET=your_webhook_secret
   
   # Kudisms (WhatsApp/SMS)
   KUDISMS_API_KEY=your_api_key
   KUDISMS_SENDER_ID=Yapa
   
   # Application Settings
   YAPA_CREDIT_PRICE=3.00
   YAPA_MINIMUM_CREDITS=100
   YAPA_FREE_CREDITS_ON_REGISTRATION=100
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the application**
   ```bash
   php artisan serve
   ```

## Configuration

### Paystack Setup
1. Create a Paystack account at [paystack.com](https://paystack.com)
2. Get your API keys from the dashboard
3. Set up webhook URL: `https://yourdomain.com/paystack/webhook`
4. Configure webhook secret in your environment

### Kudisms Setup
1. Register at [kudisms.net](https://kudisms.net)
2. Get your API key and sender ID
3. Configure WhatsApp and SMS settings

## Usage

### Credit System

#### Purchasing Credits
```php
// Via Livewire component
<livewire:credit-purchase />

// Via API
POST /api/paystack/initialize
{
    "amount": 1000,
    "credits": 333
}
```

#### Checking Balances
```php
$user = auth()->user();
$creditWallet = $user->getCreditWallet();
$nairaWallet = $user->getNairaWallet();
$earningsWallet = $user->getEarningsWallet();

echo $creditWallet->formatted_balance; // "1,000 credits"
echo $nairaWallet->formatted_balance;  // "₦5,000.00"
```

#### Transaction Management
```php
use App\Services\TransactionService;

$transactionService = app(TransactionService::class);

// Credit user account
$transaction = $transactionService->credit(
    $user,
    'credits',
    100,
    'Credit purchase',
    ['payment_reference' => 'PAY_123']
);

// Debit user account
$transaction = $transactionService->debit(
    $user,
    'credits',
    10,
    'WhatsApp message sent',
    ['message_id' => 'MSG_456']
);
```

## API Endpoints

### Authentication
- `POST /register` - User registration
- `POST /login` - User login
- `POST /logout` - User logout

### Credit System
- `GET /credits/purchase` - Credit purchase page
- `POST /api/paystack/initialize` - Initialize payment
- `GET /api/paystack/verify/{reference}` - Verify payment
- `GET /api/paystack/public-key` - Get public key and pricing

### Transactions
- `GET /transactions` - Transaction history page
- `POST /transactions/{id}/retry` - Retry failed transaction

### Webhooks
- `POST /paystack/webhook` - Paystack webhook handler
- `GET /paystack/callback` - Payment callback

## Database Schema

### Key Tables

#### Users
- Enhanced with WhatsApp verification, balances, and BVN fields
- Relationships with wallets, transactions, and interests

#### Wallets
- Multiple wallet types per user (credits, naira, earnings)
- Optimistic locking for concurrent updates
- Soft deletes and audit trail

#### Transactions
- Complete transaction history with metadata
- Support for refunds and retries
- Payment method and gateway response tracking

#### Interests
- User interest categorization
- Icon and color customization
- Soft deletes and ordering

## Security Considerations

### Payment Security
- Webhook signature verification
- Idempotency checks for duplicate payments
- Secure API key management

### Data Protection
- BVN encryption at rest
- Secure OTP generation and verification
- Rate limiting on sensitive endpoints

### Transaction Integrity
- Optimistic locking for wallet updates
- Atomic transaction processing
- Comprehensive error handling

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Database Seeding
```bash
php artisan db:seed --class=InterestSeeder
```

## Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Configure production database
- [ ] Set up SSL certificates
- [ ] Configure webhook URLs
- [ ] Set up monitoring and logging
- [ ] Configure backup strategy
- [ ] Set up queue workers
- [ ] Configure caching (Redis recommended)

### Queue Configuration
For production, configure queue workers:
```bash
php artisan queue:work --daemon
```

## Support

For support and questions:
- Check the documentation
- Review the code comments
- Create an issue in the repository

## License

This project is proprietary software. All rights reserved.

---

**Built with ❤️ using Laravel, Livewire, and modern web technologies.**
