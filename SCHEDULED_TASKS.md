# Scheduled Tasks

This document outlines all scheduled tasks configured for the YAPA application.

## Daily Tasks

### Trial Batch Cleanup (02:00 AM)
- **Command**: `batches:cleanup-trials`
- **Purpose**: Close expired trial batches and notify members
- **Frequency**: Daily at 2:00 AM
- **Features**: Dry-run support, member notifications, admin reports

### Avatar Generation (02:00 AM)
- **Command**: `avatars:generate`
- **Purpose**: Generate avatars for users without avatars
- **Frequency**: Daily at 2:00 AM

### Failed Jobs Cleanup (03:00 AM)
- **Command**: `queue:prune-failed --hours=168`
- **Purpose**: Remove failed jobs older than 7 days
- **Frequency**: Daily at 3:00 AM

### Application Cache Clear (04:00 AM)
- **Command**: `cache:clear`
- **Purpose**: Clear application cache
- **Frequency**: Daily at 4:00 AM

### Notification Log Cleanup (01:00 AM)
- **Purpose**: Remove old notification logs (30+ days)
- **Frequency**: Daily at 1:00 AM

## Weekly Tasks

### Weekly Engagement Report (Sunday 00:00 AM)
- **Command**: `reports:weekly-engagement`
- **Purpose**: Generate and send comprehensive engagement reports to admins
- **Frequency**: Weekly on Sundays at midnight
- **Features**: User metrics, batch analytics, revenue tracking, transaction statistics

### Avatar Refresh (Sunday 03:00 AM)
- **Command**: `avatars:generate --provider=dicebear --style=avataaars`
- **Purpose**: Weekly avatar refresh with DiceBear provider
- **Frequency**: Weekly on Sundays at 3:00 AM

### Log File Cleanup (Sunday 03:00 AM)
- **Purpose**: Remove log files older than 30 days
- **Frequency**: Weekly on Sundays at 3:00 AM

### Old Notification Logs Cleanup (Monday 01:00 AM)
- **Purpose**: Remove notification logs older than 30 days
- **Frequency**: Weekly on Mondays at 1:00 AM

## Hourly Tasks

### Ad Campaign Expiry (Every hour)
- **Command**: `ads:expire-campaigns`
- **Purpose**: Close ad campaigns that have exceeded their duration
- **Frequency**: Every hour
- **Features**: Owner notifications, status updates, admin reports

### Avatar Validation (Every hour)
- **Purpose**: Validate and fix broken user avatars
- **Frequency**: Hourly

## Frequent Tasks

### Queue Worker Health Check (Every 5 minutes)
- **Command**: `queue:work --stop-when-empty`
- **Purpose**: Ensure queue workers are running
- **Frequency**: Every 5 minutes

### Notification Processing (Every 5 minutes)
- **Purpose**: Process pending notifications
- **Frequency**: Every 5 minutes

## Queue Jobs

### SendOtpJob
- **Queue**: `otp`
- **Purpose**: Send OTP via WhatsApp or SMS with retry logic
- **Retry**: 3 attempts with exponential backoff (5s, 10s, 20s)

### SendEmailJob
- **Queue**: `emails`
- **Purpose**: Send emails with comprehensive retry and failure handling
- **Retry**: 3 attempts with exponential backoff

### SendBatchFullNotificationJob
- **Queue**: `notifications`
- **Purpose**: Notify batch members when batch becomes full
- **Channels**: WhatsApp and Email
- **Retry**: 3 attempts with exponential backoff

### SendNotificationJob
- **Queue**: `notifications`
- **Purpose**: General notification processing
- **Retry**: 3 attempts with 1-minute backoff

## Setup Instructions

### Cron Configuration
Add this line to your crontab to enable Laravel's task scheduler:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Workers
Start queue workers to process background jobs:

```bash
# Start all queues
php artisan queue:work

# Start specific queues
php artisan queue:work --queue=otp,emails,notifications

# Production setup with Supervisor
php artisan queue:work --tries=3 --timeout=60 --sleep=3
```

### Manual Execution
You can run any scheduled task manually:

```bash
# Run all scheduled tasks
php artisan schedule:run

# Run specific commands
php artisan batches:cleanup-trials
php artisan ads:expire-campaigns
php artisan reports:weekly-engagement
php artisan avatars:generate

# Dry run commands (preview only)
php artisan batches:cleanup-trials --dry-run
php artisan ads:expire-campaigns --dry-run

# Send report to specific email
php artisan reports:weekly-engagement --send-to=admin@example.com
```

### Monitoring

#### View Scheduled Tasks
```bash
php artisan schedule:list
```

#### Test Scheduler
```bash
php artisan schedule:test
```

#### Monitor Queues
```bash
# View queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## Error Handling & Notifications

### Admin Notifications
Admins receive email notifications for:
- Trial batch cleanup results
- Ad campaign expiry reports
- Weekly engagement reports
- OTP sending failures
- Email delivery failures
- Batch notification failures

### Logging
- All scheduled tasks log their execution
- Failed jobs are logged with detailed error information
- Notification logs track delivery status and attempts
- Queue processing is monitored and logged

### Failure Recovery
- Jobs implement exponential backoff retry logic
- Failed jobs are preserved for analysis
- Automatic cleanup prevents log bloat
- Admin notifications ensure visibility of issues

## Notes

- All tasks use `withoutOverlapping()` to prevent multiple instances
- Background tasks use `runInBackground()` for better performance
- Failed tasks are logged for debugging
- Critical tasks have retry mechanisms built-in
- Queue workers should be monitored in production
- Use process managers like Supervisor for production queue workers

## Management Commands

### Schedule Manager
Use the `schedule:manage` command to control scheduled tasks:

```bash
# Check system status
php artisan schedule:manage status

# Run avatar generation manually
php artisan schedule:manage run-avatars
php artisan schedule:manage run-avatars --force

# Process pending notifications
php artisan schedule:manage run-notifications
php artisan schedule:manage run-notifications --limit=50

# Run cleanup tasks
php artisan schedule:manage cleanup
php artisan schedule:manage cleanup --force

# Run system tests
php artisan schedule:manage test
```

### Avatar Generation
Direct avatar management:

```bash
# Generate avatars for users without them
php artisan avatars:generate

# Force regenerate all avatars
php artisan avatars:generate --force

# Use different providers
php artisan avatars:generate --provider=dicebear --style=avataaars
php artisan avatars:generate --provider=gravatar

# Download and store locally
php artisan avatars:generate --download
```

## Avatar Providers

### 1. UI Avatars (Default)
- **URL**: https://ui-avatars.com/
- **Features**: Text-based avatars with customizable colors
- **Best for**: Consistent, professional look

### 2. DiceBear
- **URL**: https://api.dicebear.com/
- **Features**: Various avatar styles (avataaars, personas, etc.)
- **Best for**: Fun, diverse avatar styles

### 3. Gravatar
- **URL**: https://www.gravatar.com/
- **Features**: User-uploaded avatars with fallbacks
- **Best for**: Users who have Gravatar accounts

## Background Jobs

### SendNotificationJob
- **Queue**: `notifications`
- **Retries**: 3 attempts
- **Backoff**: 1 min, 5 min, 15 min
- **Timeout**: 2 hours

## Monitoring

### System Status
The `schedule:manage status` command provides:
- Notification metrics (pending, failed, sent today)
- Avatar coverage statistics
- Queue size information

### Health Checks
The `schedule:manage test` command verifies:
- Avatar service functionality
- Notification system accessibility
- Queue system connectivity

## Production Setup

### 1. Enable Laravel Scheduler
Add to your server's crontab:
```bash
* * * * * cd /path/to/yapa && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Configure Queue Worker
Run queue workers for background jobs:
```bash
php artisan queue:work --queue=notifications,default --tries=3
```

### 3. Monitor Logs
Check Laravel logs for scheduled task execution:
```bash
tail -f storage/logs/laravel.log
```

## Troubleshooting

### Common Issues

1. **Avatars not generating**
   - Check internet connectivity
   - Verify avatar service URLs are accessible
   - Run `php artisan schedule:manage test`

2. **Notifications not processing**
   - Ensure queue workers are running
   - Check notification logs table
   - Verify job configuration

3. **Scheduled tasks not running**
   - Confirm cron job is set up correctly
   - Check server timezone settings
   - Verify Laravel scheduler is enabled

### Debug Commands

```bash
# Check scheduled tasks
php artisan schedule:list

# Test specific schedule
php artisan schedule:test

# Clear failed jobs
php artisan queue:flush

# Restart queue workers
php artisan queue:restart
```

## Configuration

### Environment Variables
Add to your `.env` file:

```env
# Queue Configuration
QUEUE_CONNECTION=database

# Notification Settings
NOTIFICATION_QUEUE=notifications
NOTIFICATION_RETRY_ATTEMPTS=3

# Avatar Settings
AVATAR_DEFAULT_PROVIDER=ui-avatars
AVATAR_CACHE_DURATION=86400
```

### Customization
Modify scheduled tasks in `routes/console.php` to adjust:
- Execution times
- Frequency
- Parameters
- Cleanup policies

## Performance Considerations

- Avatar generation is batched to avoid API rate limits
- Notification processing is limited to prevent memory issues
- Old data cleanup runs during low-traffic hours
- Queue workers should be monitored and restarted regularly

## Security

- All external API calls include timeout limits
- User data is validated before avatar generation
- Notification content is sanitized
- Failed jobs are logged for audit purposes