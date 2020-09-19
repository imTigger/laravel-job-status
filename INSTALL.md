# Installation - Laravel

This plugin can only be installed from [Composer](https://getcomposer.org/).

Run the following command:
```
composer require imtigger/laravel-job-status
```

#### 1. Add Service Provider (Laravel < 5.5)

Add the following to your `config/app.php`:

```php
'providers' => [
    ...
    Imtigger\LaravelJobStatus\LaravelJobStatusServiceProvider::class,
]
```

#### 2. Publish migration and config (optional)

```bash
php artisan vendor:publish --provider="Imtigger\LaravelJobStatus\LaravelJobStatusServiceProvider"
```

#### 3. Migrate Database

```bash
php artisan migrate
```

#### 4. Use a custom JobStatus model (optional)

To use your own JobStatus model you can change the model in `config/job-status.php`

```php
return [
    'model' => App\JobStatus::class,
];

```

#### 5. Improve job_id capture (optional)

The first Laravel event that can be captured to insert the job_id into the JobStatus model is the `Queue::before` event. This means that the JobStatus won't have a job_id until it is being processed for the first time.

If you would like the job_id to be stored immediately you can add the `LaravelJobStatusServiceProvider` to your `config/app.php`, which tells laravel to use our `Dispatcher`.
```php
'providers' => [
    ...
    \Imtigger\LaravelJobStatus\LaravelJobStatusBusServiceProvider::class,
]
```

#### 6. Setup dedicated database connection (optional)

Laravel support only one transaction per database connection.

All changes made by JobStatus are also within transaction and therefore invisible to other connnections (e.g. progress page)

If your job will update progress within transaction, copy your connection in `config/database.php` under another name like `'mysql-job-status'` with same config.

Then set your connection to `'database_connection' => 'mysql-job-status'` in `config/job-status.php`
