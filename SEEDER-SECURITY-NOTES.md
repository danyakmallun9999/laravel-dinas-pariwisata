# Seeder Security Notes

## ✅ Fixed Issues

1. **Production Protection**: Seeder sekarang akan abort jika dijalankan di production
2. **Environment Variable**: Password bisa di-set via `INITIAL_ADMIN_PASSWORD` di `.env`
3. **Random Password Fallback**: Jika tidak ada env var, generate random password (harus diubah)

## Usage

### Development/Testing
```bash
# Set password di .env
INITIAL_ADMIN_PASSWORD=YourSecurePassword123!

# Run seeder
php artisan db:seed --class=AdminUserSeeder
```

### Production (Recommended)

**DO NOT run AdminUserSeeder in production!**

Instead, create initial admin via:
1. Artisan command (recommended)
2. Migration with secure password
3. Manual database insert with hashed password

### Example: Create Admin Command

```bash
php artisan make:command CreateAdminUser
```

Then implement:
```php
public function handle()
{
    $email = $this->ask('Admin email');
    $password = $this->secret('Admin password');
    
    $admin = Admin::create([
        'name' => 'Super Admin',
        'email' => $email,
        'password' => Hash::make($password),
        'is_admin' => true,
    ]);
    
    $admin->assignRole('super_admin');
}
```

## Security Best Practices

1. ✅ Never commit `.env` file
2. ✅ Never hardcode passwords in seeders
3. ✅ Always change default passwords immediately
4. ✅ Use strong passwords (min 12 chars, mixed case, numbers, symbols)
5. ✅ Consider implementing "force password reset on first login" feature

