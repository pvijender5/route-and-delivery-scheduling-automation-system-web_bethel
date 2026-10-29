# 🌐 FarmSync (Bethel Farm) – Web Application

## 📘 Application Overview

The FarmSync (Bethel Farm) Web Application serves as the administrative and operational backbone of the Farm Sync mobile ecosystem. Designed for dispatchers, routers, and administrators, this web platform allows users to manage shipment logistics, create optimized delivery routes, assign drivers, and monitor real-time truck movements. Shipment data is integrated from **Acumatica ERP**, and routing decisions are powered by **Google OR Tools**. The system utilizes WebSockets and GPS data from the mobile app to provide real-time updates for loading, delivery, and truck location.

## ⚙️ Tech Stack

### 🔧 Backend

#### Core

* **PHP 8.1+**
* **Laravel 10.x**
* **MySQL / MariaDB**

#### Key Laravel Packages

* **Laravel Sanctum** – API authentication
* **Laravel WebSockets** – Real-time communication
* **Laravel Breeze** – Authentication scaffolding
* **Laravel DataTables** – Server-side data tables
* **Laravel Dusk** – End-to-end browser testing

#### Document Processing

* **DomPDF** – PDF generation
* **PHPSpreadsheet** – Excel export/import

#### External Services

* **Firebase PHP SDK** – Push notifications
* **Google Maps API** – Mapping and geolocation
* **Here Map API** – Commercial navigation integration
* **Google OR Tools** – Optimized route generation
* **Twilio SDK** – Messaging services
* **Pusher** – Real-time event broadcasting

### 🎨 Frontend

#### Core

* **JavaScript (ES6+)**
* **Pusher JS** – WebSocket client

#### Styling

* **Tailwind CSS 3.1**
* **Bootstrap 4** (with DataTables support)
* **PostCSS**

#### Key Libraries

* **TinyMCE 6.7** – Rich text editor
* **DataTables** – Interactive tables
* **Axios** – HTTP client
* **Laravel Echo** – Real-time event listener

### ⚙️ Build Tools

* **Vite**
* **Node.js & NPM**
* **Composer**

## 🌐 External APIs

* **Google Maps Distance Matrix API**
* **HERE Map API**
* **Firebase Services**
* **Twilio API**
* **Pusher WebSockets**

## 🔐 Security Features

* Cross-Site Request Forgery (CSRF) Protection
* Cross-Site Scripting (XSS) Prevention
* SQL Injection Protection
* Secure API Authentication
* Encrypted Sessions

## 🖥️ Server Requirements

* PHP 8.1+
* MySQL / MariaDB
* Node.js & NPM
* Apache / Nginx Web Server
* SSL Certificate
* Required PHP Extensions (OpenSSL, PDO, Mbstring, Tokenizer, etc.)
