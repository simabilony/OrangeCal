<div align="center">

# 🍊 OrangeCal - نظام إدارة التغذية واللياقة البدنية

### OrangeCal - Nutrition & Fitness Management System

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)

**نظام متكامل لتتبع التغذية والتمارين الرياضية مع دعم الذكاء الاصطناعي**

**A comprehensive nutrition and fitness tracking system with AI support**

[التوثيق](#-المحتويات) • [التثبيت](#-التثبيت) • [API Docs](#-api-endpoints) • [المساهمة](#-المساهمة)

</div>

---

## 📋 المحتويات | Table of Contents

- [نظرة عامة | Overview](#-نظرة-عامة--overview)
- [المميزات | Features](#-المميزات--features)
- [التقنيات المستخدمة | Tech Stack](#-التقنيات-المستخدمة--tech-stack)
- [متطلبات النظام | Requirements](#-متطلبات-النظام--system-requirements)
- [التثبيت | Installation](#-التثبيت--installation)
- [إعداد قاعدة البيانات | Database Setup](#-إعداد-قاعدة-البيانات--database-setup)
- [المتغيرات البيئية | Environment Variables](#-المتغيرات-البيئية--environment-variables)
- [API Endpoints](#-api-endpoints)
- [هيكل المشروع | Project Structure](#-هيكل-المشروع--project-structure)
- [قاعدة البيانات | Database Schema](#-قاعدة-البيانات--database-schema)
- [المصادقة | Authentication](#-المصادقة--authentication)
- [الاختبارات | Testing](#-الاختبارات--testing)
- [النشر | Deployment](#-النشر--deployment)
- [المساهمة | Contributing](#-المساهمة--contributing)
- [الترخيص | License](#-الترخيص--license)

---

## 🎯 نظرة عامة | Overview

### العربية

**OrangeCal** هو نظام متكامل لإدارة التغذية واللياقة البدنية مبني على Laravel. يوفر النظام أدوات متقدمة لتتبع السعرات الحرارية، الوجبات، التمارين الرياضية، مع دعم الذكاء الاصطناعي لمسح الأطعمة والباركود.

**الهدف الرئيسي:** مساعدة المستخدمين على تحقيق أهدافهم الصحية من خلال تتبع دقيق للتغذية والنشاط البدني مع واجهة سهلة الاستخدام ودعم كامل للغة العربية.

### English

**OrangeCal** is a comprehensive nutrition and fitness management system built on Laravel. The system provides advanced tools for tracking calories, meals, and exercises, with AI support for food scanning and barcode recognition.

**Main Goal:** Help users achieve their health goals through accurate nutrition and physical activity tracking with an easy-to-use interface and full Arabic language support.

---

## ✨ المميزات | Features

| الميزة | Feature | الوصف | Description |
|--------|---------|-------|-------------|
| 🔐 | **Multi-Auth** | تسجيل دخول متعدد (Google, Apple, Mobile) | Multiple login methods |
| 🤖 | **AI Food Scanner** | مسح الطعام بالذكاء الاصطناعي | AI-powered food recognition |
| 📷 | **Barcode Scanner** | مسح الباركود للأطعمة | Barcode scanning for foods |
| 🏷️ | **Label Scanner** | مسح ملصقات القيم الغذائية | Nutrition label scanning |
| 🍽️ | **Custom Meals** | إنشاء وجبات مخصصة | Create custom meals |
| 💪 | **Exercise Tracking** | تتبع التمارين والسعرات المحروقة | Track exercises and burned calories |
| 📊 | **Daily Sync** | مزامنة البيانات اليومية | Daily data synchronization |
| 💳 | **Subscriptions** | إدارة الاشتراكات والدفع | Subscription management |
| 🔔 | **Push Notifications** | إشعارات فورية | Real-time notifications |
| 🕌 | **Halal Filter** | فلتر الأطعمة الحلال | Halal food filtering |

---

## 🛠️ التقنيات المستخدمة | Tech Stack

### Backend
- **PHP** 8.4+
- **Laravel** 12.x
- **MySQL** 8.4
- **Redis** (للتخزين المؤقت والجلسات)

### External Services
- **Firebase Cloud Messaging** (للإشعارات)
- **Google OAuth 2.0** (تسجيل الدخول)
- **Apple Sign In** (تسجيل الدخول)
- **OpenAI API** (مسح الطعام بالذكاء الاصطناعي)
- **Barcode Lookup API** (مسح الباركود)

### Development Tools
- **Laravel Sanctum** (API Authentication)
- **Laravel Pint** (Code Style)
- **PHPUnit** (Testing)
- **Laravel Telescope** (Debugging)

---

## 📦 متطلبات النظام | System Requirements

| المتطلب | Requirement | الإصدار | Version |
|---------|-------------|---------|---------|
| PHP | PHP | 8.1 أو أحدث | 8.1 or higher |
| Composer | Composer | 2.x | 2.x |
| MySQL | MySQL | 8.0 أو أحدث | 8.0 or higher |
| Redis | Redis | 6.x أو أحدث | 6.x or higher |
| Node.js | Node.js | 16.x أو أحدث | 16.x or higher |

---

## 🚀 التثبيت | Installation

### 1. استنساخ المشروع | Clone Repository

```bash
git clone https://github.com/your-username/orangecal-backend.git
cd orangecal-backend
```

### 2. تثبيت المكتبات | Install Dependencies

```bash
composer install
npm install
```

### 3. إعداد البيئة | Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات | Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 5. تشغيل الخادم | Run Server

```bash
php artisan serve
```

---

## 🔧 إعداد قاعدة البيانات | Database Setup

قم بتحديث ملف `.env` بمعلومات قاعدة البيانات الخاصة بك:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orangecal
DB_USERNAME=root
DB_PASSWORD=
```

ثم قم بتشغيل:

```bash
php artisan migrate
```

---

## 🔐 المتغيرات البيئية | Environment Variables

أضف المتغيرات التالية إلى ملف `.env`:

```env
# Application
APP_NAME="OrangeCal"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orangecal
DB_USERNAME=root
DB_PASSWORD=

# Services
FCM_SERVER_KEY=your_fcm_server_key
OPENAI_API_KEY=your_openai_api_key
GOOGLE_CLIENT_ID=your_google_client_id
APPLE_CLIENT_ID=your_apple_client_id
```

---

## 📡 API Endpoints

جميع الطلبات تستخدم `Content-Type: application/json` ويجب إرفاق `Authorization: Bearer {token}` للطلبات المحمية.

All requests use `Content-Type: application/json` and require `Authorization: Bearer {token}` header for protected routes.

### 🔐 Authentication Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| POST | `/auth/google` | Google Sign In | تسجيل دخول Google | ❌ |
| POST | `/auth/apple` | Apple Sign In | تسجيل دخول Apple | ❌ |
| POST | `/auth/mobile` | Mobile OTP Login | تسجيل دخول برقم الجوال | ❌ |
| POST | `/logout` | Logout | تسجيل خروج | ✅ |
| POST | `/auth/refresh` | Refresh Token | تحديث الرمز | ✅ |

**Request Example (Google Login):**
```json
{
  "googleId": "123456789",
  "email": "user@example.com",
  "name": "John Doe",
  "fcmToken": "fcm_token_here",
  "deviceType": "ios",
  "deviceId": "device_id_here"
}
```

**Response Example:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "is_onboarded": false,
      "is_premium": false
    },
    "token": "1|randomtokenstring"
  }
}
```

### 👤 User Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| POST | `/user-onboarding` | Complete Onboarding | إكمال التسجيل | ✅ |
| GET | `/user/profile` | Get User Profile | الحصول على الملف الشخصي | ✅ |
| PUT | `/user/profile` | Update Profile | تحديث الملف الشخصي | ✅ |
| GET | `/user/stats` | Get User Statistics | إحصائيات المستخدم | ✅ |

**Request Example (Onboarding):**
```json
{
  "gender": "male",
  "birthDate": "1990-01-01",
  "height": 175,
  "weight": 75,
  "targetWeight": 70,
  "goal": "lose_weight",
  "activityLevel": "moderate",
  "dietaryPreferences": ["halal"],
  "allergies": [],
  "preferredLanguage": "ar"
}
```

### 🍽️ Food & Meals Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| GET | `/food-db` | Search Foods | البحث عن الأطعمة | ✅ |
| GET | `/food-db-id/{id}` | Get Food by ID | الحصول على طعام محدد | ✅ |
| POST | `/barcode` | Scan Barcode | مسح الباركود | ✅ |
| POST | `/label` | Scan Label | مسح الملصق الغذائي | ✅ |
| POST | `/analyze-food` | AI Food Scanner | مسح الطعام بالذكاء الاصطناعي | ✅ |
| GET | `/user-meals` | Get User Meals | الحصول على وجبات المستخدم | ✅ |
| POST | `/new-meal` | Create New Meal | إنشاء وجبة جديدة | ✅ |
| PUT | `/user-meals/{id}` | Update Meal | تحديث وجبة | ✅ |
| DELETE | `/user-meals/{id}` | Delete Meal | حذف وجبة | ✅ |

**Request Example (Search Foods):**
```
GET /food-db?query=apple&page=1&perPage=20
```

**Request Example (Create Meal):**
```json
{
  "name": {
    "ar": "سلطة الكينوا",
    "en": "Quinoa Salad"
  },
  "description": {
    "ar": "سلطة صحية",
    "en": "Healthy salad"
  },
  "mealType": "lunch",
  "ingredients": [
    {
      "foodId": 1,
      "quantity": 100,
      "unit": "g",
      "grams": 100
    }
  ],
  "servings": 2,
  "prepTime": 15
}
```

### 📊 Food Logs Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| GET | `/food-logs` | Get Food Logs | الحصول على سجل الطعام | ✅ |
| POST | `/food-db-id` | Save Food from DB | حفظ طعام من قاعدة البيانات | ✅ |
| POST | `/meal-id` | Save User Meal | حفظ وجبة مستخدم | ✅ |
| PUT | `/food-logs/{id}` | Edit Food Log | تعديل سجل طعام | ✅ |
| DELETE | `/food-logs/{id}` | Delete Food Log | حذف سجل طعام | ✅ |

**Request Example (Save Food Log):**
```json
{
  "foodId": 1,
  "logDate": "2024-01-01",
  "logTime": "12:00",
  "mealType": "lunch",
  "quantity": 200,
  "unit": "g",
  "grams": 200
}
```

### 💪 Exercise Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| GET | `/exercise` | Get Exercise Logs | الحصول على سجل التمارين | ✅ |
| POST | `/exercise/save` | Save Exercise | حفظ تمرين | ✅ |
| POST | `/exercise/saveAi` | Save AI Exercise | حفظ تمرين بالذكاء الاصطناعي | ✅ |
| PUT | `/exercise/{id}` | Edit Exercise | تعديل تمرين | ✅ |
| DELETE | `/exercise/{id}` | Delete Exercise | حذف تمرين | ✅ |

**Request Example (Save Exercise):**
```json
{
  "type": {
    "ar": "ركض",
    "en": "Running"
  },
  "logDate": "2024-01-01",
  "startTime": "07:00",
  "endTime": "07:30",
  "duration": 30,
  "intensity": "high",
  "caloriesBurned": 300,
  "distance": 5.0
}
```

### 🔖 Sync & Saved Foods Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| POST | `/sync` | Daily Sync | المزامنة اليومية | ✅ |
| GET | `/saved-food` | Get Saved Foods | الحصول على الأطعمة المحفوظة | ✅ |
| POST | `/save/{foodId}` | Save Food | حفظ طعام | ✅ |
| POST | `/save-meal/{mealId}` | Save Meal | حفظ وجبة | ✅ |
| DELETE | `/saved-food/{id}` | Remove Saved Food | إزالة طعام محفوظ | ✅ |

### 💳 Payment & Subscription Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| POST | `/payment/create` | Create Payment | إنشاء دفعة | ✅ |
| GET | `/subscription/status` | Get Subscription Status | حالة الاشتراك | ✅ |
| POST | `/subscription/cancel` | Cancel Subscription | إلغاء الاشتراك | ✅ |

**Request Example (Create Payment):**
```json
{
  "planType": "monthly",
  "paymentMethod": "apple",
  "storeProductId": "com.app.subscription",
  "storeTransactionId": "transaction_id",
  "receipt": {}
}
```

### 🔔 Notification Endpoints

| Method | Endpoint | Description | الوصف | Auth |
|--------|----------|-------------|-------|------|
| GET | `/notifications` | Get Notifications | الحصول على الإشعارات | ✅ |
| POST | `/notifications/{id}/read` | Mark as Read | تعليم كمقروء | ✅ |
| POST | `/test-notification` | Test Notification | اختبار إشعار | ✅ |

---

## 🏗️ هيكل المشروع | Project Structure

```
orangecal-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controllers
│   │   ├── Middleware/        # Custom Middleware
│   │   └── Requests/          # Form Request Validation
│   ├── Models/                # Eloquent Models
│   ├── Services/              # Business Logic Services
│   └── Traits/                # Reusable Traits
├── database/
│   ├── migrations/            # Database Migrations
│   ├── seeders/               # Database Seeders
│   └── factories/             # Model Factories
├── routes/
│   └── api.php                # API Routes
└── tests/                     # Tests
```

---

## 🗄️ قاعدة البيانات | Database Schema

### Main Tables

- **users** - المستخدمون
- **user_profiles** - ملفات المستخدمين الشخصية
- **food** - قاعدة بيانات الأطعمة
- **user_meals** - وجبات المستخدمين المخصصة
- **meal_ingredients** - مكونات الوجبات
- **food_logs** - سجل الأطعمة المستهلكة
- **exercise_logs** - سجل التمارين
- **subscriptions** - الاشتراكات
- **daily_syncs** - المزامنة اليومية
- **notifications** - الإشعارات

---

## 🔐 المصادقة | Authentication

يستخدم المشروع **Laravel Sanctum** للمصادقة. بعد تسجيل الدخول، يجب إرفاق الرمز المميز في رأس الطلب:

```
Authorization: Bearer {token}
```

The project uses **Laravel Sanctum** for authentication. After login, include the token in the request header:

```
Authorization: Bearer {token}
```

---

## 🧪 الاختبارات | Testing

```bash
php artisan test
```

---

## 🚀 النشر | Deployment

### Production Checklist

1. تعيين `APP_ENV=production`
2. تعيين `APP_DEBUG=false`
3. تشغيل `php artisan config:cache`
4. تشغيل `php artisan route:cache`
5. تشغيل `php artisan view:cache`
6. تحديث `APP_URL` في `.env`

---

## 📝 المساهمة | Contributing

نرحب بمساهماتكم! يرجى قراءة دليل المساهمة أولاً.

We welcome contributions! Please read the contributing guide first.

---

## 📄 الترخيص | License

This project is licensed under the MIT License.

---

## 🙏 الشكر | Acknowledgments

- Laravel Framework
- Spatie Translatable Package
- The developer heroine Sima Bilony

