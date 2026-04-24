# Supabase Connection Guide

## Prerequisites
1. PHP PostgreSQL extension enabled on your hosting environment
2. Supabase project created

## Required Supabase Information
Get these from your Supabase dashboard > Settings > Database:
- **Project URL**: `https://your-project-ref.supabase.co`
- **Database Password**: Set during project creation
- **Database Name**: Usually `postgres`
- **Port**: `5432`

## Environment Configuration
Update your `.env` file with:
```env
DB_CONNECTION=pgsql
DB_HOST=your-project-ref.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-db-password
```

## Additional Supabase Environment Variables
```env
SUPABASE_URL=https://your-project-ref.supabase.co
SUPABASE_KEY=your-anon-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key
```

## Migration Notes
- Supabase uses PostgreSQL, so ensure your migrations are PostgreSQL compatible
- Laravel's pgsql driver is already configured in `config/database.php`
- Run `php artisan migrate` after updating environment variables

## Connection Pooling
For production, consider using Supabase's connection pooling:
```env
DB_HOST=your-project-ref.supabase.co
DB_PORT=6543  # Pooling port
```

## SSL Configuration
Supabase requires SSL connections. The pgsql driver handles this automatically.
