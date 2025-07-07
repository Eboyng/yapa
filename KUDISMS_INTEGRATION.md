# Kudisms API Integration Documentation

## Overview

This document outlines the integration of Kudisms API for WhatsApp messaging, OTP services, and the implementation of queue jobs and scheduled tasks for the YAPA application.

## Features Implemented

### 1. Kudisms API Configuration

#### Settings Page Integration
New configuration fields have been added to the Filament Settings page under the "Notifications" tab:

- **Kudisms API Key**: Your API key for authentication
- **WhatsApp Template Code**: Template code for WhatsApp messages
- **Sender ID**: Your registered sender ID
- **WhatsApp API URL**: The API endpoint URL (default: https://my.kudisms.net/api/whatsapp)

#### Environment Variables
Add these to your `.env` file:
```env
KUDISMS_API_KEY=your_api_key_here
KUDISMS_WHATSAPP_TEMPLATE_CODE=your_template_code
KUDISMS_SENDER_ID=YourSenderID
KUDISMS_WHATSAPP_URL=https://my.kudisms.net/api/whatsapp
```

### 2. Updated Services

#### WhatsAppService
- **New API Format**: Updated to use the new Kudisms API with template support
- **Dynamic Configuration**: Loads settings from the database instead of config files
- **Template Support**: Supports `templateCode`, `parameters`, `buttonParameters`, and `headerParameters`
- **Balance Check**: Updated balance endpoint integration

#### OtpService
- **WhatsApp Integration**: Uses the new WhatsAppService for sending OTPs
- **SMS Fallback**: Maintains SMS fallback functionality
- **Phone Number Formatting**: Separate formatting for WhatsApp (without +) and SMS (with +)

### 3. Queue Jobs

#### SendOtpJob
- **Purpose**: Handles OTP sending via WhatsApp or SMS
- **Queue**: `otp`
- **Retry Logic**: 3 attempts with exponential backoff (5s, 10s, 20s)
- **Failure Handling**: Logs failures and notifies admins

#### SendEmailJob
- **Purpose**: Handles email delivery with retry logic
- **Queue**: `emails`
- **Retry Logic**: 3 attempts with exponential backoff
- **Failure Handling**: Comprehensive logging and admin notifications

#### SendBatchFullNotificationJob
- **Purpose**: Notifies batch members when a batch becomes full
- **Queue**: `notifications`
- **Multi-channel**: Sends both WhatsApp and email notifications
- **Retry Logic**: 3 attempts with exponential backoff

### 4. Scheduled Commands

#### CleanupTrialBatches
- **Schedule**: Daily at 2:00 AM
- **Purpose**: Closes expired trial batches
- **Command**: `php artisan batches:cleanup-trials`
- **Options**: `--dry-run` to preview changes
- **Features**:
  - Notifies batch members about expiration
  - Logs closure reasons
  - Sends admin reports

#### ExpireAdCampaigns
- **Schedule**: Every hour
- **Purpose**: Closes ad campaigns that have exceeded their duration
- **Command**: `php artisan ads:expire-campaigns`
- **Options**: `--dry-run` to preview changes
- **Features**:
  - Notifies ad owners about expiration
  - Updates campaign status to 'expired'
  - Comprehensive logging

#### GenerateWeeklyEngagementReport
- **Schedule**: Weekly on Sunday at midnight
- **Purpose**: Generates and sends weekly engagement reports
- **Command**: `php artisan reports:weekly-engagement`
- **Options**: `--send-to=email@example.com` to send to specific email
- **Report Includes**:
  - User metrics (total, new registrations, active users)
  - Batch metrics (active, new, completed, top performing)
  - Revenue metrics (credit sales, week-over-week changes)
  - Transaction metrics (total, successful, success rate)

## Queue Configuration

### Queue Names
- `otp`: For OTP-related jobs
- `emails`: For email delivery jobs
- `notifications`: For general notification jobs

### Running Queue Workers
```bash
# Start queue workers for all queues
php artisan queue:work

# Start workers for specific queues
php artisan queue:work --queue=otp,emails,notifications

# Start with specific options
php artisan queue:work --tries=3 --timeout=60
```

## Scheduled Tasks

### Running the Scheduler
Add this to your cron tab to run Laravel's scheduler:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Manual Command Execution
```bash
# Clean up trial batches
php artisan batches:cleanup-trials

# Expire ad campaigns
php artisan ads:expire-campaigns

# Generate weekly report
php artisan reports:weekly-engagement

# Preview changes without executing
php artisan batches:cleanup-trials --dry-run
php artisan ads:expire-campaigns --dry-run
```

## Monitoring and Logging

### Queue Monitoring
- Failed jobs are logged in the `failed_jobs` table
- Queue health checks run every 5 minutes
- Failed jobs older than 7 days are automatically pruned

### Notification Logging
- All notifications are logged in the `notification_logs` table
- Logs include status, attempts, error messages, and metadata
- Old logs (30+ days) are automatically cleaned up

### Admin Notifications
- Admins are notified about:
  - OTP sending failures
  - Email delivery failures
  - Batch notification failures
  - Weekly engagement reports
  - Trial batch cleanup results
  - Ad campaign expiry results

## Error Handling

### Retry Logic
- All jobs implement exponential backoff
- Maximum 3 retry attempts for most jobs
- Timeout protection to prevent hanging jobs

### Failure Recovery
- Failed jobs are logged with detailed error information
- Admin notifications for critical failures
- Automatic cleanup of old failed jobs

## Best Practices

### Queue Management
1. Monitor queue workers regularly
2. Use `--stop-when-empty` for development
3. Use process managers like Supervisor for production
4. Monitor failed job counts

### Configuration
1. Test API credentials before going live
2. Use environment-specific settings
3. Monitor API rate limits
4. Keep backup configurations

### Monitoring
1. Set up log monitoring
2. Monitor notification delivery rates
3. Track queue processing times
4. Monitor API response times

## Troubleshooting

### Common Issues

#### WhatsApp Messages Not Sending
1. Check API credentials in settings
2. Verify template code is correct
3. Check phone number formatting
4. Review API rate limits

#### Queue Jobs Failing
1. Check database connection
2. Verify queue worker is running
3. Check job timeout settings
4. Review error logs

#### Scheduled Tasks Not Running
1. Verify cron job is set up
2. Check Laravel scheduler logs
3. Ensure proper file permissions
4. Test commands manually

### Debug Commands
```bash
# Check queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush

# Test scheduler
php artisan schedule:list
php artisan schedule:test
```

## API Documentation Reference

For detailed Kudisms API documentation, refer to their official documentation at: https://docs.kudisms.net

## Support

For issues related to:
- **Kudisms API**: Contact Kudisms support
- **Application Integration**: Check application logs and this documentation
- **Queue Issues**: Review Laravel queue documentation
- **Scheduled Tasks**: Review Laravel task scheduling documentation