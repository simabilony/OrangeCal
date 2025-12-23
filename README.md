<div align="center">

# 🍊 OrangeCal - نظام إدارة التغذية واللياقة البدنية

### OrangeCal - Nutrition & Fitness Management System

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
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


