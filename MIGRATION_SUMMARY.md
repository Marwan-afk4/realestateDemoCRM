# Database Migration Summary: transaction_deals → deals

## Overview
Successfully renamed the `transaction_deals` table to `deals` and restructured it with the new required columns.

## Changes Made

### 1. Database Structure Changes

#### New Tables Created:
- **`deals`** table with columns:
  - `id` (primary key)
  - `fullname` (string)
  - `nationality_id` (string, nullable)
  - `phone` (string)
  - `email` (string)
  - `developer_id` (foreign key to developers)
  - `compound_id` (foreign key to compounds)
  - `number_of_units` (integer)
  - `status` (enum: pending, approved, rejected, semidone, default: pending)
  - `timestamps`

#### Migration Files Created:
- `database/migrations/2025_01_10_000000_rename_transaction_deals_to_deals_and_restructure.php`

### 2. Model Changes

#### New Models Created:
- **`app/Models/Deal.php`** - Replaces TransactionDeal

#### Model Relationships Updated:
- **Lead**: `transaction_deals()` → `deals()`
- **Developer**: `transaction_deals()` → `deals()`
- **Compound**: `transaction_deals()` → `deals()`
- **Uptown**: `transaction_deals()` → `deals()`

#### Models Removed:
- `app/Models/TransactionDeal.php` (deleted)

### 3. Controller Changes

#### New Controllers Created:
- **`app/Http/Controllers/Api/User/DealController.php`** - Replaces TransactionDealController

#### Controllers Updated:
- **`app/Http/Controllers/Api/Admin/DealsController.php`**
  - Updated to use Deal model
  - Simplified methods to work with new structure
  - Removed broker-related logic
  
- **`app/Http/Controllers/HomePageController.php`**
  - Updated TransactionDeal references to Deal
  
- **`app/Http/Controllers/Api/User/UserProfitController.php`**
  - Updated to use Deal model
  - Simplified queries
  
- **`app/Http/Controllers/Api/Admin/HomepageController.php`**
  - Updated TransactionDeal references to Deal

#### Controllers Removed:
- `app/Http/Controllers/Api/User/TransactionDealController.php` (deleted)

### 4. Route Changes

Updated `routes/api.php`:
- `/user/send-transaction-deal` → `/user/send-deal`
- Removed broker and lead related endpoints
- Added `/user/getNationalities` endpoint
- Simplified compound and developer endpoints

### 5. Additional Files Created



## Migration Instructions

1. **Run the migrations:**
   ```bash
   php artisan migrate
   ```



## API Endpoint Changes

### Old Endpoints (Removed):
- `POST /user/send-transaction-deal`
- `GET /user/getLeadsIds`
- `GET /user/getSalesDeveloperIds/{developer_id}`
- `GET /user/getUptownIds/{compound_id}`

### New Endpoints:
- `POST /user/send-deal`
- `GET /user/getDeveloperIds`
- `GET /user/getCompoundIds/{developer_id}`

## Data Structure Changes

### Old transaction_deals table had:
- brocker_id, sales_developer_id, uptown_id, lead_id
- deal_value, image, profit, days_for_profits

### New deals table has:
- fullname, nationality_id (string), phone, email
- developer_id, compound_id, number_of_units, status

## Notes
- All old migration files for transaction_deals are preserved but will be superseded by the new migration
- The new structure is simpler and focuses on core deal information
- Broker and lead relationships have been removed as requested
- Status field maintains the same enum values for compatibility
- nationality_id is now a simple string field instead of a foreign key relationship
