# Changelog

## 0.1.11
- Added ```incrementProgress(int $offset, int $every)``` method
- Added ability to override JobStatus class, with fallback when config is not set
- Database migration is now published instead of loaded directly, to allow customization

## 0.1.10
- Fixed compatibility of PHP 5.6

## 0.1.9
- Automatic package discovery for Laravel 5.5, thanks to @PixellUp

## 0.1.8
- Fixed Job ID is not stored correctly in some case

## 0.1.7
- Fixed error for Job do not use Trackable

## 0.1.6
- Initial release
