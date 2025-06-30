# NetworldSports Custom Order Processing Module

## Overview
This module extends Magento 2's order processing capabilities by allowing administrators to create and manage custom order statuses. It provides comprehensive tracking of status changes and ensures data integrity when statuses are modified or removed.

## Features
- Custom order status management with full CRUD operations
- Order status change logging with detailed history
- Frontend filtering to hide orders with disabled statuses
- REST API for programmatic status updates
- Automatic fallback to core statuses when custom statuses are deleted
- Full ACL permission control
- Cache management for optimal performance
- Email notifications for status changes to "Shipped"

## Installation

### Manual Installation

Create directory: app/code/NetworldSports/CustomOrderProcessing

Copy module files to the directory

Run installation commands

```
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
bin/magento cache:flush
```

## Architecture
###Database Schema

networldsports_custom_order_status: Stores custom status definitions

status_id - Primary key
status_name - The custom status name
is_enabled - Enable/disable flag
created_at - Creation timestamp
updated_at - Last update timestamp


networldsports_order_status_log: Tracks all status changes

log_id - Primary key
order_id - Foreign key to sales_order
old_status - Previous status
new_status - New status
changed_at - Change timestamp



## Key Components

Repository Pattern: Clean data access layer with interfaces
Service Contracts: API interfaces for external integrations
Plugin System: Extends core functionality without modifying code
Observer Pattern: Reacts to order status changes
ViewModel: Separates presentation logic from blocks

## Design Decisions

Fallback Mechanism: Orders revert to default state status when custom status is deleted
Cache Invalidation: Automatic cache clearing on status changes
Permission Granularity: Separate ACL for different operations

## Usage
### Admin Panel

Navigate to Sales > Custom Order Processing > Manage Custom Statuses
Create new statuses with descriptive names
Enable/disable statuses as needed
View status change logs for audit trail

##REST API

### Update order status:
```
# returns a JWT token string
TOKEN=$(curl -s -X POST http://magento.loc/rest/V1/integration/admin/token \
  -H "Content-Type: application/json" \
  -d '{"username":"arun","password":"Arun@1234"}')


# call the custom API
curl -X PUT http://<magento-url>/rest/V1/customorder/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
        "orderIncrementId":"000000001",
        "newStatus":"dispatched"
      }'
```
## ACL Permissions

NetworldSports_CustomOrderProcessing::main - Main module access
NetworldSports_CustomOrderProcessing::status_add - Add/Edit Order Statuses
NetworldSports_CustomOrderProcessing::status_delete - Delete Order statuses
NetworldSports_CustomOrderProcessing::status_enable - Enable/Disable Orders statuses
NetworldSports_CustomOrderProcessing::log - View Order Status Change logs

## Testing
Run unit tests:
```
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/NetworldSports/CustomOrderProcessing
```
Run integration tests:
```
vendor/bin/phpunit -c dev/tests/integration/phpunit.xml.dist app/code/NetworldSports/CustomOrderProcessing/Test/Integration
```
##Performance Considerations

Implements IdentityInterface for efficient cache management
Indexed database columns for quick lookups
Optimized queries using collection processors

## Security

Input validation for all user inputs
ACL permissions for all operations
XSS protection in templates
CSRF protection on forms

## Compatibility

Magento 2.4.x
PHP 7.4, 8.1, 8.2
Compatible with both Luma and Hyvä themes
