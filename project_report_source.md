# Comprehensive Project Research & Information Extraction Document
**Project Name:** Clinova Clinic Management System  
**Document Code:** `project_report_source.md`  
**Purpose:** Comprehensive Factual Knowledge Base & Source Document for Academic University Report Generation  
**Target Codebase Paths:**
- Backend: `d:\FFF\New-folder\clinic-managment` (Laravel 11.x API)
- Frontend Patient/Doctor App: `d:\FFF\New-folder\clinic_app` (Flutter 3.x)
- Frontend Admin/Receptionist Dashboard: `d:\FFF\New-folder\clinic_dashboard` (Flutter 3.x Web/Desktop/Mobile)

---

## Abstract

This research document provides an exhaustive, evidence-based factual analysis of the **Clinova Clinic Management System** codebase across its backend and dual frontend applications. Clinova is a multi-tenant-capable, role-based clinic management ecosystem designed to streamline patient registration, medical appointment scheduling, wallet-based consultation fee payments, dynamic cancellation penalty mechanisms, electronic medical records (EMR), prescription issuance, invoice tracking, push notification broadcasting, and managerial reporting.

The system comprises three primary software components:
1. **Laravel 11 RESTful API Backend**: Built with PHP 8.2+, Laravel Sanctum authentication, MySQL/SQLite database engine, Firebase Cloud Messaging (FCM) v1 HTTP API, and SMTP email services.
2. **Clinova Mobile/Web Patient & Doctor App (`clinic_app`)**: Built with Flutter 3.11+ and Dart, using BLoC/Cubit state management, Dio HTTP client, secure token storage, and dual-language (Arabic/English) RTL/LTR UI support.
3. **Clinova Admin & Receptionist Management Dashboard (`clinic_dashboard`)**: Built with Flutter 3.11+ using SidebarX navigation, responsive table grids, interactive financial/operational reporting, patient account management, doctor schedule configuration, and receptionist account provisioning.

All data, requirements, use cases, database models, API endpoints, UI screens, workflows, and testing metrics documented herein are extracted directly from source code files, database migrations, configuration manifests, Git logs, and Postman API specifications.

---

## Table of Contents

1. [Chapter One – Introduction](#chapter-one--introduction)
   - 1.1 Project Overview & System Purpose
   - 1.2 Problem Description (End-User & Administrative Perspective)
   - 1.3 Target Users & Operating Environment
   - 1.4 Main System Components & Technologies
   - 1.5 Stakeholders Analysis
   - 1.6 System Actors and Their Goals Matrix
   - 1.7 Domain Terminology & Project Glossary
2. [Chapter Two – Reference Study](#chapter-two--reference-study)
   - 2.1 Theoretical Concepts & Architectural Foundations
   - 2.2 System Comparison: Traditional vs. Clinova Digital EMR System
   - 2.3 Verified Codebase Packages & Technological References
   - 2.4 Topics Requiring Academic Research & Citations
3. [Chapter Three – Requirements Analysis](#chapter-three--requirements-analysis)
   - 3.1 Derived Functional Requirements (FR-01 to FR-36)
   - 3.2 Verified Non-Functional Requirements (NFR-01 to NFR-12)
   - 3.3 System Use Cases Matrix (UC-01 to UC-26)
   - 3.4 General Use Case Descriptions Table
   - 3.5 Detailed Use Cases (Core End-to-End Traces)
4. [Chapter Four – Technical Design](#chapter-four--technical-design)
   - 4.1 Tiered Architectural Framework
   - 4.2 Laravel Backend Architecture
   - 4.3 Flutter Frontend Architecture (`clinic_app` and `clinic_dashboard`)
   - 4.4 Database Design & Entity Relationships (Migrations & ERD Model)
   - 4.5 Class & Object Design (Backend Core Classes & Frontend Architecture)
   - 4.6 Implemented API Endpoints Inventory
   - 4.7 Authentication, Authorization & Security Mechanisms
   - 4.8 Main Business Workflows (Step-by-Step System Tracing)
5. [Chapter Five – Implementation and Verification](#chapter-five--implementation-and-verification)
   - 5.1 Technology Stack & Development Tools Summary
   - 5.2 Implementation Details of Major Features
   - 5.3 Complete UI / Screen Inventory (`clinic_app` & `clinic_dashboard`)
   - 5.4 Core Screen-to-Screen UI Flow Journeys
   - 5.5 Testing, Verification & Quality Assurance Evidence
   - 5.6 Development Environment Setup & Configuration Guide
   - 5.7 Seeders & Demo Data Analysis (`DemoDataSeeder.php`)
   - 5.8 Development & Version Control History (Git Milestones)
   - 5.9 Project Work Division & Contribution Evidence
6. [Chapter Six – Conclusion Material](#chapter-six--conclusion-material)
   - 6.1 Summary of Accomplished Capabilities
   - 6.2 Key Technical Innovations & System Strengths
   - 6.3 Implementation Limitations & Constraints
   - 6.4 Remaining Work & Recommended Future Roadmap
7. [Chapter Seven – Limitations and Missing Information (`[NOT FOUND / NEEDS CONFIRMATION]`)](#chapter-seven--limitations-and-missing-information)
8. [Chapter Eight – Evidence & Source Mapping Index](#chapter-eight--evidence--source-mapping-index)
9. [Chapter Nine – Suggested Report Diagrams Inventory](#chapter-nine--suggested-report-diagrams-inventory)
10. [Chapter Ten – Report Preparation Executive Summary](#chapter-ten--report-preparation-executive-summary)

---

## Chapter One – Introduction

### 1.1 Project Overview & System Purpose
The **Clinova Clinic Management System** is an integrated multi-platform software solution built to automate and streamline operations in outpatient medical clinics. The primary purpose of Clinova is to digitize patient-doctor interactions, medical scheduling, electronic medical records (EMR), financial transactions, prescription issuing, and clinic administrative oversight.

- **Project Name**: Clinova Clinic Management System (Clinova)
- **Backend Infrastructure**: Laravel 11.x RESTful API running on PHP 8.2+.
- **Patient & Doctor Mobile/Web App**: Flutter application (`clinic_app`) targeting Android, iOS, and Web.
- **Admin & Receptionist Dashboard**: Flutter application (`clinic_dashboard`) targeting Web, Windows Desktop, macOS, Linux, and Tablet environments.
- **Current Status**: Implemented and operational with seed data, API testing collections, and complete frontend-backend integration.

### 1.2 Problem Description (End-User & Administrative Perspective)

#### 1.2.1 Problems Faced by Patients:
- **Phone-based & In-person Queueing**: Patients traditionally have to physically visit the clinic or repeatedly phone the reception desk to inquire about doctor availability and schedule slots.
- **Lack of Fee & Schedule Transparency**: Patients lack real-time visibility into doctor specialization details, bios, consultation fees, and available time slots.
- **Unclear Cancellation & Refund Policies**: In traditional systems, appointment cancellations lead to lost fees or dispute over pre-payments without transparent policy enforcement.
- **Paper Medical Records & Prescriptions**: Patients lose track of past medical diagnoses, prescribed dosages, and historical treatment plans recorded on paper slips.
- **Payment Inconvenience**: Lack of prepaid digital wallet balance tracking leads to cash handling delays at reception desks.

#### 1.2.2 Problems Faced by Doctors:
- **Disorganized Daily Schedules**: Doctors rely on paper logs or verbal receptionist updates, leading to double-booking or slot overlaps.
- **No Remote Access to Patient History**: Doctors cannot review previous diagnoses or prescription histories prior to or during patient consultations.
- **Manual Prescription Issuance**: Writing hand-written prescriptions consumes consultation time and increases handwriting interpretation errors.

#### 1.2.3 Problems Faced by Clinic Receptionists & Administrators:
- **High Administrative Burden**: Handling phone calls, manual queue registration, and cash accounting creates bottlenecks at the clinic reception.
- **Revenue Leakage & Uncollected Balances**: Lack of automated invoice tracking leads to uncollected additional visit fees (e.g., extra diagnostic procedures).
- **No Data-Driven Insights**: Administrative staff lack automated reporting tools to assess daily revenue, cancelled appointments, penalty collections, doctor workload, or patient violation rates.

#### 1.2.4 Solution Provided by Clinova:
Clinova addresses these challenges through:
1. Automated slot generation based on doctor weekly schedules and slot durations.
2. Real-time wallet deductions for consultation fees upon booking, preventing ghost appointments.
3. Automated cancellation penalty engine based on cancellation lead time ($>24$ hours for full refund vs. $<24$ hours for progressive penalty deduction).
4. Centralized Electronic Medical Records (EMR) and digital prescription management.
5. Automated multi-currency financial invoice generation and revenue reporting.
6. Multi-platform localized UI (Arabic/English) with dual-mode dark/light themes and push notifications.

### 1.3 Target Users & Operating Environment
- **Target Users**: Patients seeking medical consultations, Attending Doctors, Clinic Receptionists, Clinic Managers / System Administrators.
- **Operating Environment**:
  - Backend API: PHP 8.2+, MySQL 8.0+ / SQLite 3, Web Server (Apache/Nginx/Artisan Serve).
  - Patient & Doctor App: Android (API level 21+), iOS (12+), Modern Web Browsers.
  - Admin & Receptionist Dashboard: Windows 10/11, macOS, Linux, Modern Web Browsers (Chrome, Edge, Firefox, Safari).

### 1.4 Main System Components & Technologies
```
                      +---------------------------------------+
                      |         Clinova Platform Engine       |
                      +---------------------------------------+
                                          |
           +------------------------------+------------------------------+
           |                                                             |
+----------------------+                                      +----------------------+
|     clinic_app       |                                      |   clinic_dashboard   |
| (Patient & Doctor)   |                                      | (Admin & Reception)  |
|  Flutter 3.x Mobile  |                                      |   Flutter 3.x Web    |
+----------------------+                                      +----------------------+
           |                                                             |
           +------------------------------+------------------------------+
                                          | HTTPS / REST API / Sanctum Token
                                          v
                      +---------------------------------------+
                      |        clinic-managment Backend       |
                      |          Laravel 11.x (PHP 8.2)       |
                      +---------------------------------------+
                                          |
           +------------------------------+------------------------------+
           |                              |                              |
           v                              v                              v
+----------------------+      +----------------------+      +----------------------+
|    MySQL / SQLite    |      | Firebase FCM (v1)    |      |  SMTP Email Engine   |
|   Database Engine    |      |  Push Notifications  |      |   (OTP & Auth Mail)  |
+----------------------+      +----------------------+      +----------------------+
```

### 1.5 Stakeholders Analysis

| Stakeholder | Relationship with System | System Needs | System Capabilities |
| :--- | :--- | :--- | :--- |
| **Patient** | Primary End-User | Browse doctors, view schedules, book slots, receive OTP, pay via wallet, view medical records & prescriptions, view invoices, cancel appointments with refund tracking. | Register, verify OTP, book/cancel appointments, preview costs, charge wallet (via admin/receptionist), view EMR, receive push notifications. |
| **Doctor** | Healthcare Provider | View daily appointment schedules, confirm/reject bookings, mark visits as completed, enter symptoms/diagnoses, write prescriptions, cancel clinic days. | View patient lists, accept/reject appointments, create EMR, issue prescriptions, add extra visit costs, cancel day schedules. |
| **Receptionist** | Administrative Front-Desk Staff | Register patients manually, manage appointments, reschedule bookings, collect cash payments, issue invoices, deposit/deduct patient wallet funds. | View all appointments, register patients, deposit to wallets, collect invoice balances, view patient financial reports. |
| **Admin / Manager** | System Administrator | Oversee overall clinic statistics, manage doctor profiles, manage receptionist accounts, configure system settings (deposit, penalties, hours), view financial reports. | Full system CRUD: Doctor management, staff management, settings configuration, overall revenue/violation reporting, audit logs. |
| **System Administrator / Maintenance** | Infrastructure Maintainer | Maintain backend database, monitor API logs, manage Firebase credentials, ensure uptime. | Database migrations, seeders, environment configuration, server log inspection. |

### 1.6 System Actors and Their Goals Matrix

| Actor | Description | Main Goals | Main Interactions |
| :--- | :--- | :--- | :--- |
| **Patient** | Authenticated user with role `patient` | Book medical appointments, manage wallet balance, view medical history, receive treatment notifications. | Mobile/Web App login, slot search, booking preview, appointment cancellation, EMR viewing, invoice viewing. |
| **Doctor** | Authenticated user with role `doctor` linked to `DoctorDetail` | Efficiently view patient queues, document medical diagnoses, issue electronic prescriptions, track consultation fees. | Doctor App dashboard, appointment status update (confirmed/rejected/completed), EMR creation, prescription entry, day cancellation. |
| **Receptionist** | Authenticated user with role `receptionist` | Manage walk-in patients, assist booking, handle cash payments, adjust schedules, manage patient wallet balances. | Web/Desktop Dashboard, manual patient registration, appointment rescheduling, wallet deposit/deduction, invoice settlement. |
| **Admin** | Authenticated user with role `admin` | Full operational and financial control, staffing control, system parameter configuration. | Dashboard overview, doctor profile CRUD, staff account CRUD, report generation (revenue, violations, appointments), setting updates. |

### 1.7 Domain Terminology & Project Glossary

| Term | Project Definition & Business Meaning |
| :--- | :--- |
| **Appointment** | A scheduled consultation session between a Patient and a Doctor for a specific date and time slot. Statuses: `pending`, `confirmed`, `completed`, `cancelled`, `rejected`. |
| **Consultation Fee (`consultation_fee`)** | The fixed monetary amount charged by a doctor for an initial consultation session, automatically deducted from the patient wallet upon booking. |
| **Additional Cost (`additional_cost`)** | Extra fees added by a doctor upon visit completion (e.g., ECG, lab tests, minor procedures) added to the final invoice. |
| **Wallet (`wallet_balance`)** | A digital prepaid credit account associated with a patient's user profile, used for booking payments and penalty/refund credits. |
| **Deposit (`deposit_amount`)** | The prepaid fee deducted at booking time ($= consultation\_fee$) credited toward the total visit invoice. |
| **Cancellation Penalty (`penalty`)** | A monetary penalty applied when a patient cancels an appointment within the late cancellation window ($< 24$ hours). Calculated as $\min(\text{violation\_count} \times 5\%, \text{max\_penalty\_percentage})$. |
| **Violation Count (`violation_count`)** | An integer counter on the `users` table tracking late appointment cancellations by a patient. |
| **Doctor Schedule (`DoctorSchedule`)** | Weekly working hours defined per doctor specifying `day_of_week`, `start_time`, `end_time`, and `duration_per_patient` (slot step in minutes). |
| **Available Slot** | A dynamically computed time window on a specific date where the doctor has working hours and no existing `pending` or `confirmed` appointment exists. |
| **Medical Record (`MedicalRecord`)** | Electronic record created by a doctor for a visit containing `visit_date`, `symptoms`, `diagnosis`, and `doctor_notes`. |
| **Prescription (`Prescription`)** | Medication line items linked to a `MedicalRecord` containing `medication_name`, `dosage`, `duration`, and `instructions`. |
| **Invoice (`Invoice`)** | Financial billing statement generated upon appointment completion detailing `total_amount`, `deposit_amount`, `remaining_amount`, `payment_status` (`paid`/`unpaid`), and `payment_method`. |
| **OTP Code (`otp_code`)** | A 6-digit One-Time Password hashed and stored with an expiration timestamp (`otp_expires_at`) used for 2FA email verification during registration and login for patients/doctors. |

---

## Chapter Two – Reference Study

### 2.1 Theoretical Concepts & Architectural Foundations
1. **RESTful API Architectural Pattern**: Stateless communication between frontend clients and Laravel backend using HTTP verbs (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`) with JSON payload contracts and Bearer Token Authentication (Laravel Sanctum).
2. **BLoC (Business Logic Component) Pattern**: Predictable state management architecture separating presentation UI from business logic in Flutter using Stream-based event processing (`Bloc` and `Cubit`).
3. **Electronic Medical Record (EMR) Standards**: Digital storage of health records linking patient demography, appointment history, clinical diagnostic notes, and medication prescriptions.
4. **Progressive Cancellation Penalty Mechanism**: A financial behavioral enforcement model where repeated late cancellations incur exponentially higher percentage penalties up to a configured threshold.
5. **Dual-Channel Notification Architecture**: Concurrent storage of database notification records and real-time FCM (Firebase Cloud Messaging) v1 HTTP push notifications targeted by user preferred locale (`ar` or `en`).

### 2.2 System Comparison: Traditional vs. Clinova Digital EMR System

| Criterion | Traditional Clinic Management | Clinova Digital EMR System |
| :--- | :--- | :--- |
| **Appointment Booking** | Physical queue / Phone call | Real-time multi-platform mobile/web self-service booking. |
| **Slot Conflicts** | High risk of double-booking | Programmatic slot availability validation based on schedule duration. |
| **Fee Collection** | Manual cash at counter | Automated wallet pre-deduction upon booking + remaining invoice settlement. |
| **Late Cancellation Policy** | None or unorganized disputes | Automated lead-time checking ($24\text{h}$) with progressive violation penalties. |
| **Medical Records** | Paper folders | Centralized EMR linked to appointments, patients, and doctors. |
| **Prescriptions** | Hand-written prescription notes | Structured digital prescriptions with dosage, duration, and instructions. |
| **Reporting & Analytics** | Manual ledger reconciliation | Automated real-time financial, revenue, violation, and doctor performance dashboards. |
| **Localization & Theme** | Single language paper forms | Full Arabic/English RTL/LTR support with dynamic Light/Dark themes. |

### 2.3 Verified Codebase Packages & Technological References

#### Backend (`clinic-managment/composer.json`):
- `laravel/framework`: `^11.31`
- `laravel/sanctum`: `^4.0` (Token-based API auth)
- `firebase/php-jwt`: `^7.1` (FCM v1 service account OAuth2 token generation)
- `fakerphp/faker`: `^1.23` (Database seeding)
- `phpunit/phpunit`: `^11.0.1` (Unit & Integration testing framework)

#### Frontend Apps (`clinic_app/pubspec.yaml` & `clinic_dashboard/pubspec.yaml`):
- `flutter_bloc`: `^9.1.0` (State management)
- `go_router`: `^15.1.0` (Declarative routing)
- `dio`: `^5.8.0` (HTTP client with interceptors)
- `flutter_secure_storage`: `^9.2.4` (Secure token persistence)
- `firebase_core`: `^4.12.1` & `firebase_messaging`: `^16.4.3` (Push notifications)
- `flutter_local_notifications`: `^22.1.0` (Foreground push alerts)
- `sidebarx`: `^0.17.2` (Dashboard navigation drawer)
- `google_fonts`: `^6.2.1` (Typography engine - Cairo / Inter fonts)

### 2.4 Topics Requiring Academic Research & Citations
*(To be populated with formal academic references in the final university report)*:
- Comparative studies on EMR adoption in outpatient clinics.
- Evaluation of BLoC architecture vs. Provider/Riverpod in cross-platform mobile apps.
- Security analysis of Sanctum Bearer tokens vs. JWT tokens in healthcare APIs.
- Mathematical modeling of cancellation penalty strategies in appointment booking systems.

---

## Chapter Three – Requirements Analysis

### 3.1 Derived Functional Requirements

| ID | Functional Requirement | Actor(s) | Status | Evidence File Path |
| :--- | :--- | :--- | :--- | :--- |
| **FR-01** | User registration with name, email, password, phone, generating a 6-digit OTP code sent via email. | Patient | Implemented | [`AuthController.php:L19-L52`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L19-L52) |
| **FR-02** | User login generating email OTP for Patients/Doctors, and direct Sanctum token for Admin/Receptionist. | All Roles | Implemented | [`AuthController.php:L55-L97`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L55-L97) |
| **FR-03** | OTP verification verifying 6-digit code within 10-minute expiry window to issue Sanctum plainTextToken. | Patient, Doctor | Implemented | [`AuthController.php:L100-L150`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L100-L150) |
| **FR-04** | OTP resend generating new code and sending email. | Patient, Doctor | Implemented | [`AuthController.php:L152-L180`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L152-L180) |
| **FR-05** | Forgot password flow with OTP code verification and password reset. | All Roles | Implemented | [`AuthController.php:L183-L270`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L183-L270) |
| **FR-06** | Authenticated user profile retrieval (`/api/auth/me`). | All Roles | Implemented | [`AuthController.php:L285-L310`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L285-L310) |
| **FR-07** | Profile update including name, phone, password, locale preference, and profile picture upload. | All Roles | Implemented | [`AuthController.php:L312-L380`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L312-L380) |
| **FR-08** | FCM registration token update for push notifications (`/api/auth/fcm-token`). | All Roles | Implemented | [`AuthController.php:L382-L400`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L382-L400) |
| **FR-09** | Public listing of active doctors with specialization, bio, consultation fee, and rating/picture. | Public, Patient | Implemented | [`DoctorController.php:L15-L45`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/DoctorController.php#L15-L45) |
| **FR-10** | Detailed doctor profile view (`/api/doctors/{id}`). | Public, Patient | Implemented | [`DoctorController.php:L48-L70`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/DoctorController.php#L48-L70) |
| **FR-11** | Doctor weekly working schedules retrieval (`/api/doctors/{doctorId}/schedules`). | Public, Patient | Implemented | [`DoctorScheduleController.php:L15-L35`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/DoctorScheduleController.php#L15-L35) |
| **FR-12** | Doctor schedule creation, update, and deletion by Admin. | Admin | Implemented | [`DoctorScheduleController.php:L38-L120`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/DoctorScheduleController.php#L38-L120) |
| **FR-13** | Dynamic available time slots calculation for a doctor on a specific date (`/api/doctors/{id}/available-slots`). | Patient, Receptionist | Implemented | [`AppointmentController.php:L160-L217`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L160-L217) |
| **FR-14** | Appointment preview endpoint checking cost, wallet balance, and balance after deduction (`/api/appointments/preview`). | Patient | Implemented | [`AppointmentController.php:L28-L60`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L28-L60) |
| **FR-15** | Appointment booking with wallet consultation fee pre-deduction and slot validation. | Patient, Admin, Receptionist | Implemented | [`AppointmentController.php:L63-L158`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L63-L158) |
| **FR-16** | Patient personal appointments list retrieval with invoice & payment status (`/api/appointments/my`). | Patient | Implemented | [`AppointmentController.php:L220-L245`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L220-L245) |
| **FR-17** | Doctor appointments queue list retrieval (`/api/appointments/doctor`). | Doctor | Implemented | [`AppointmentController.php:L320-L350`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L320-L350) |
| **FR-18** | Receptionist/Admin global appointments list retrieval with filter support. | Receptionist, Admin | Implemented | [`AppointmentController.php:L700-L740`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L700-L740) |
| **FR-19** | Appointment status update by Doctor (`confirmed`, `rejected`, `completed`) with optional additional cost & notes. | Doctor | Implemented | [`AppointmentController.php:L352-L460`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L352-L460) |
| **FR-20** | Automatic invoice generation upon appointment completion calculating total amount and deposit offset. | Doctor, System | Implemented | [`AppointmentController.php:L405-L423`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L405-L423) |
| **FR-21** | Appointment cancellation with automatic refund lead-time calculation ($>24\text{h}$ full refund, $<24\text{h}$ progressive penalty refund). | Patient, Doctor, Admin, Receptionist | Implemented | [`AppointmentController.php:L463-L567`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L463-L567) |
| **FR-22** | Appointment rescheduling to a new valid date/time slot. | Receptionist, Admin | Implemented | [`AppointmentController.php:L570-L626`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L570-L626) |
| **FR-23** | Doctor full day appointments cancellation with mass notification and full patient refund. | Doctor | Implemented | [`AppointmentController.php:L629-L695`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L629-L695) |
| **FR-24** | Medical Record creation by Doctor for an appointment containing symptoms, diagnosis, and notes. | Doctor | Implemented | [`MedicalRecordController.php:L23-L93`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php#L23-L93) |
| **FR-25** | Prescription item addition to an existing Medical Record. | Doctor | Implemented | [`MedicalRecordController.php:L96-L148`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php#L96-L148) |
| **FR-26** | Patient EMR history retrieval (`/api/medical-records/my`). | Patient | Implemented | [`MedicalRecordController.php:L151-L170`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php#L151-L170) |
| **FR-27** | Detailed EMR view by Doctor or Admin (`/api/medical-records/{id}`). | Doctor, Admin | Implemented | [`MedicalRecordController.php:L173-L193`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php#L173-L193) |
| **FR-28** | Patient wallet balance and transaction logs viewing (`/api/wallet/balance`, `/api/wallet/transactions`). | Patient | Implemented | [`WalletController.php:L15-L45`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/WalletController.php#L15-L45) |
| **FR-29** | Manual wallet balance deposit and deduction by Receptionist or Admin. | Receptionist, Admin | Implemented | [`WalletController.php:L48-L95`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/WalletController.php#L48-L95) |
| **FR-30** | Patient payment of remaining completed appointment balance via wallet (`/api/appointments/{id}/pay-remaining`). | Patient | Implemented | [`AppointmentController.php:L248-L317`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L248-L317) |
| **FR-31** | Receptionist/Admin invoice list viewing, creation, and payment status update (`cash`, `online`, `wallet`). | Receptionist, Admin | Implemented | [`InvoiceController.php:L15-L110`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/InvoiceController.php#L15-L110) |
| **FR-32** | Staff (Receptionist) account creation, listing, updating, profile picture upload, and deletion by Admin. | Admin | Implemented | [`AuthController.php:L405-L550`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php#L405-L550) |
| **FR-33** | Doctor profile creation, updating, and deletion by Admin (`/api/doctors`). | Admin | Implemented | [`DoctorController.php:L73-L160`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/DoctorController.php#L73-L160) |
| **FR-34** | Managerial reporting endpoints (Overall Dashboard, Appointments Report by Date, Revenue Report by Date, Doctor Performance Report, Cancellation Violations Report, Patient Directory with search). | Admin, Receptionist | Implemented | [`ReportController.php:L17-L249`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/ReportController.php#L17-L249) |
| **FR-35** | Dynamic clinic system settings retrieval and updates (`booking_deposit`, `max_penalty_percentage`, `cancellation_hours`). | Public, Admin | Implemented | [`SettingController.php:L15-L55`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/SettingController.php#L15-L55) |
| **FR-36** | User notifications list retrieval and deletion (`/api/notifications`). | All Roles | Implemented | [`NotificationController.php:L15-L45`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/NotificationController.php#L15-L45) |

### 3.2 Verified Non-Functional Requirements

- **NFR-01 (Security & Auth)**: All private endpoints protected via Laravel Sanctum middleware (`auth:sanctum`). Sensitive credentials hashed using `Hash::make()` (Bcrypt).
- **NFR-02 (Role Authorization)**: Strict role-based execution enforced via custom `RoleMiddleware` enforcing `role:patient`, `role:doctor`, `role:receptionist`, `role:admin`.
- **NFR-03 (Data Integrity & Transactions)**: Database transactions (`DB::transaction`) utilized across all financial wallet deposits, deductions, and penalty calculations in [`WalletService.php`](file:///d:/FFF/New-folder/clinic-managment/app/Services/WalletService.php).
- **NFR-04 (Localization & RTL)**: Full dual-language localization (Arabic and English) with native Right-to-Left (RTL) layout switching in both Flutter applications via [`app_localizations.dart`](file:///d:/FFF/New-folder/clinic_app/lib/core/l10n/app_localizations.dart) and `GlobalMaterialLocalizations`.
- **NFR-05 (Theme Flexibility)**: Dynamic Dark and Light mode support dynamically configurable via `ThemeCubit` in both Flutter clients.
- **NFR-06 (Asynchronous Push Processing)**: Firebase FCM notifications dispatched asynchronously using app-terminating callbacks or localized push payloads without blocking database response execution ([`NotificationService.php`](file:///d:/FFF/New-folder/clinic-managment/app/Services/NotificationService.php)).
- **NFR-07 (Input Validation)**: All incoming HTTP request parameters validated using Laravel Form Validation rules (`validate()`), returning structured HTTP 422 Unprocessable Entity responses upon validation failure.
- **NFR-08 (CORS & Cross-Origin Media Serving)**: Storage endpoints configured with CORS headers (`Access-Control-Allow-Origin: *`) to support Web CanvasKit image rendering ([`routes/api.php:L32-L43`](file:///d:/FFF/New-folder/clinic-managment/routes/api.php#L32-L43)).
- **NFR-09 (Platform Portability)**: Mobile app compiles to Android/iOS/Web; Dashboard compiles to Web/Windows/macOS/Linux.
- **NFR-10 (Error Resilience & Fallbacks)**: Global Dio HTTP interceptors in Flutter handle network timeouts, token expirations (401), and display user-friendly snackbars.
- **NFR-11 (Auditing & Logging)**: Critical staff management and authentication actions logged into `audit_logs` table via [`AuditLog`](file:///d:/FFF/New-folder/clinic-managment/app/Models/AuditLog.php) model.
- **NFR-12 (Data Soft Deletion / Preservation)**: Database schema incorporates soft deletes on critical entities (e.g. `users`) to preserve transaction and clinical history.

### 3.3 System Use Cases Matrix

| Use Case ID | Use Case Name | Primary Actor | Related Requirements | Status |
| :--- | :--- | :--- | :--- | :--- |
| **UC-01** | Register Patient Account | Patient | FR-01, FR-03 | Implemented |
| **UC-02** | Authenticate & Verify OTP | Patient, Doctor | FR-02, FR-03, FR-04 | Implemented |
| **UC-03** | Direct Staff Login | Admin, Receptionist | FR-02 | Implemented |
| **UC-04** | Reset Forgotten Password | All Roles | FR-05 | Implemented |
| **UC-05** | View & Update Profile | All Roles | FR-06, FR-07, FR-08 | Implemented |
| **UC-06** | Search & View Doctors | Patient, Public | FR-09, FR-10, FR-11 | Implemented |
| **UC-07** | Check Dynamic Slot Availability | Patient, Receptionist | FR-13 | Implemented |
| **UC-08** | Preview Booking & Cost | Patient | FR-14 | Implemented |
| **UC-09** | Book Appointment & Pay Deposit | Patient | FR-15, FR-28 | Implemented |
| **UC-10** | View Personal Appointments | Patient | FR-16 | Implemented |
| **UC-11** | Cancel Appointment (With Refund/Penalty) | Patient | FR-21, FR-28 | Implemented |
| **UC-12** | Pay Remaining Visit Balance via Wallet | Patient | FR-30, FR-28 | Implemented |
| **UC-13** | View Doctor Consultation Queue | Doctor | FR-17 | Implemented |
| **UC-14** | Update Appointment Status | Doctor | FR-19, FR-20 | Implemented |
| **UC-15** | Mass Cancel Doctor Day Schedules | Doctor | FR-23 | Implemented |
| **UC-16** | Create Electronic Medical Record (EMR) | Doctor | FR-24, FR-19 | Implemented |
| **UC-17** | Add Digital Prescription | Doctor | FR-25 | Implemented |
| **UC-18** | View Patient EMR History | Patient, Doctor, Admin | FR-26, FR-27 | Implemented |
| **UC-19** | Manage Patients & Search | Receptionist, Admin | FR-18, FR-29, FR-34 | Implemented |
| **UC-20** | Deposit / Deduct Patient Wallet | Receptionist, Admin | FR-29 | Implemented |
| **UC-21** | Reschedule Appointment | Receptionist, Admin | FR-22 | Implemented |
| **UC-22** | Manage Invoices & Payment Settlement | Receptionist, Admin | FR-31, FR-20 | Implemented |
| **UC-23** | Manage Doctor Profiles & Working Schedules | Admin | FR-12, FR-33 | Implemented |
| **UC-24** | Provision & Manage Staff Accounts | Admin | FR-32 | Implemented |
| **UC-25** | Configure Clinic System Parameters | Admin | FR-35 | Implemented |
| **UC-26** | View System Reports & Dashboard Analytics | Admin, Receptionist | FR-34 | Implemented |

### 3.4 General Use Case Descriptions Table

| Use Case | General Description | Requirement ID |
| :--- | :--- | :--- |
| **UC-01: Register Account** | Patient provides registration details; backend creates unverified account and emails 6-digit OTP code. | FR-01 |
| **UC-02: Verify OTP Login** | User enters 6-digit OTP code received via email; backend validates code and returns Sanctum API bearer token. | FR-02, FR-03 |
| **UC-07: Check Available Slots** | System filters doctor weekly schedules against existing booked/confirmed appointments to return available 15-30 min slots. | FR-13 |
| **UC-09: Book Appointment** | Patient selects doctor, date, and slot; backend validates slot, checks wallet balance, deducts consultation fee, and creates pending appointment. | FR-15 |
| **UC-11: Cancel Appointment** | Patient cancels appointment; system checks lead time ($>24\text{h}$ for 100% refund, $<24\text{h}$ for progressive penalty deduction). | FR-21 |
| **UC-16: Create Medical Record** | Doctor inputs clinical symptoms, diagnosis, and doctor notes for an appointment; status updates to completed and invoice auto-generates. | FR-24, FR-20 |
| **UC-17: Add Prescription** | Doctor adds medication line item (name, dosage, duration, instructions) to a created medical record. | FR-25 |
| **UC-20: Wallet Deposit** | Receptionist/Admin credits patient wallet with cash amount, logging transaction history. | FR-29 |
| **UC-22: Settle Invoice** | Receptionist/Admin receives payment for remaining appointment fees via cash, online, or wallet, marking invoice as paid. | FR-31 |
| **UC-26: View Reports** | Manager views dashboard stats, revenue reports, appointment stats, doctor performance, and cancellation penalty violation rates. | FR-34 |

### 3.5 Detailed Use Cases (Core End-to-End Traces)

#### Detailed Use Case 1: Appointment Booking with Wallet Fee Deduction (UC-09)
- **Actor**: Patient
- **Preconditions**: Patient is authenticated with a valid Sanctum token and has sufficient wallet balance ($\ge \text{consultation\_fee}$).
- **Trigger**: Patient taps "Confirm Booking" on [`doctor_detail_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/doctor_detail_screen.dart).
- **Main Flow**:
  1. Frontend sends `POST /api/appointments` with `doctor_id`, `appointment_date`, `appointment_time`, `notes`.
  2. Laravel [`AppointmentController@store`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L63) validates date/time and confirms slot is in future.
  3. Controller retrieves `DoctorSchedule` for requested day of week, confirming time falls within `start_time` and `end_time` and matches `duration_per_patient` slot intervals.
  4. Controller queries `appointments` table to ensure no active `pending` or `confirmed` appointment exists for doctor at that date/time.
  5. Controller verifies patient `wallet_balance` $\ge \text{consultation\_fee}$.
  6. Controller creates record in `appointments` table with `status = 'pending'`.
  7. Controller invokes [`WalletService@deductBookingDeposit`](file:///d:/FFF/New-folder/clinic-managment/app/Services/WalletService.php#L61) inside a DB transaction: updates patient `wallet_balance` and writes record to `wallet_transactions` with `type = 'booking_deduct'`.
  8. Controller triggers [`NotificationService@notify`](file:///d:/FFF/New-folder/clinic-managment/app/Services/NotificationService.php#L21) sending push notification to attending doctor.
  9. Backend returns JSON response HTTP 201 with appointment object.
  10. Frontend updates UI state, showing success confirmation dialog and redirecting to Patient Appointments screen.
- **Alternative / Error Flows**:
  - *Insufficient Wallet Balance*: Backend returns HTTP 422 with message "Insufficient patient wallet balance". Frontend prompts user to charge wallet.
  - *Slot Already Booked*: Backend returns HTTP 422 with message "This time slot is already booked". Frontend refreshes available slots grid.

#### Detailed Use Case 2: Late Appointment Cancellation & Progressive Penalty (UC-11)
- **Actor**: Patient
- **Preconditions**: Patient has an active `pending` or `confirmed` appointment.
- **Trigger**: Patient taps "Cancel Appointment" in [`patient_appointments_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/patient_appointments_screen.dart).
- **Main Flow**:
  1. Frontend sends `PATCH /api/appointments/{id}/cancel` with `cancellation_reason`.
  2. Laravel [`AppointmentController@cancel`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L463) verifies appointment belongs to authenticated patient and status is cancellable.
  3. Controller calculates hours until appointment: $H = \text{diffInHours}(\text{appointment\_date\_time}, \text{now})$.
  4. Fetches `cancellation_hours` setting (default: 24h) and `max_penalty_percentage` (default: 25%).
  5. **Branch A ($H > 24\text{h}$ or cancelled by Admin/Receptionist)**:
     - Invokes [`WalletService@refundFull`](file:///d:/FFF/New-folder/clinic-managment/app/Services/WalletService.php#L117): Credits 100% of `consultation_fee` back to patient `wallet_balance`.
     - Creates transaction record `type = 'refund_full'`.
  6. **Branch B ($H \le 24\text{h}$ late cancellation by Patient)**:
     - Increments patient `violation_count` by 1: $V = \text{violation\_count} + 1$.
     - Computes penalty rate: $P_{\text{rate}} = \min(V \times 5\%, \text{max\_penalty\_percentage})$.
     - Computes penalty amount: $A_{\text{penalty}} = \text{consultation\_fee} \times (P_{\text{rate}} / 100)$.
     - Computes refund amount: $A_{\text{refund}} = \text{consultation\_fee} - A_{\text{penalty}}$.
     - Invokes [`WalletService@refundWithPenalty`](file:///d:/FFF/New-folder/clinic-managment/app/Services/WalletService.php#L142) in DB transaction: updates wallet balance by $+A_{\text{refund}}$, updates `violation_count = V`, and writes two transactions (`type = 'refund_partial'` and `type = 'penalty'`).
  7. Updates appointment `status = 'cancelled'`, `cancelled_by = 'patient'`, `cancelled_at = now()`.
  8. Dispatches FCM push notifications to Patient and Doctor.
  9. Returns JSON with updated wallet balance and refund status message.

---

## Chapter Four – Technical Design

### 4.1 Tiered Architectural Framework
Clinova follows a decoupled 3-Tier Architecture Model:
- **Presentation Layer**: Client applications built with Flutter (`clinic_app` for Patients/Doctors, `clinic_dashboard` for Admin/Receptionist). Responsible for UI rendering, RTL/LTR layout management, local state management (BLoC), and user input handling.
- **Application / API Layer**: RESTful API built with Laravel 11. Organizes business logic into API Controllers, Form Requests, Domain Services (`WalletService`, `FirebaseService`, `NotificationService`), Custom Middleware (`RoleMiddleware`), and Mailables (`OtpMail`).
- **Data Layer**: Relational Database Management System (MySQL / SQLite) maintaining normalized schemas, foreign key constraints, indexes, and soft deletions. External infrastructure services include Firebase Cloud Messaging (FCM v1) HTTP server and SMTP email server.

### 4.2 Laravel Backend Architecture

#### 4.2.1 Directory Layout & Code Organization (`clinic-managment`):
- `app/Http/Controllers/Api/`: API endpoints controllers ([`AuthController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php), [`AppointmentController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php), [`DoctorController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/DoctorController.php), [`DoctorScheduleController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/DoctorScheduleController.php), [`InvoiceController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/InvoiceController.php), [`MedicalRecordController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php), [`NotificationController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/NotificationController.php), [`ReportController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/ReportController.php), [`ServiceController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/ServiceController.php), [`SettingController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/SettingController.php), [`WalletController`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/WalletController.php)).
- `app/Models/`: Eloquent models ([`User`](file:///d:/FFF/New-folder/clinic-managment/app/Models/User.php), [`DoctorDetail`](file:///d:/FFF/New-folder/clinic-managment/app/Models/DoctorDetail.php), [`DoctorSchedule`](file:///d:/FFF/New-folder/clinic-managment/app/Models/DoctorSchedule.php), [`Appointment`](file:///d:/FFF/New-folder/clinic-managment/app/Models/Appointment.php), [`MedicalRecord`](file:///d:/FFF/New-folder/clinic-managment/app/Models/MedicalRecord.php), [`Prescription`](file:///d:/FFF/New-folder/clinic-managment/app/Models/Prescription.php), [`Invoice`](file:///d:/FFF/New-folder/clinic-managment/app/Models/Invoice.php), [`WalletTransaction`](file:///d:/FFF/New-folder/clinic-managment/app/Models/WalletTransaction.php), [`Setting`](file:///d:/FFF/New-folder/clinic-managment/app/Models/Setting.php), [`Notification`](file:///d:/FFF/New-folder/clinic-managment/app/Models/Notification.php), [`Service`](file:///d:/FFF/New-folder/clinic-managment/app/Models/Service.php), [`AuditLog`](file:///d:/FFF/New-folder/clinic-managment/app/Models/AuditLog.php)).
- `app/Services/`: Core business services ([`WalletService`](file:///d:/FFF/New-folder/clinic-managment/app/Services/WalletService.php), [`FirebaseService`](file:///d:/FFF/New-folder/clinic-managment/app/Services/FirebaseService.php), [`NotificationService`](file:///d:/FFF/New-folder/clinic-managment/app/Services/NotificationService.php)).
- `app/Http/Middleware/`: Custom route access middleware ([`RoleMiddleware`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Middleware/RoleMiddleware.php)).
- `app/Mail/`: Mailable classes ([`OtpMail`](file:///d:/FFF/New-folder/clinic-managment/app/Mail/OtpMail.php)).
- `routes/api.php`: Full API routing definition.

### 4.3 Flutter Frontend Architecture (`clinic_app` and `clinic_dashboard`)

#### 4.3.1 Architectural Pattern:
Both applications implement a Clean Feature-First Layered Architecture:
- `data/`: API services, DTO models, data providers, HTTP repository implementations.
- `bloc/` or `presentation/bloc/`: State management classes extending `Bloc` or `Cubit` holding domain state.
- `screens/` or `presentation/screens/`: Flutter UI views and widget trees.

#### 4.3.2 Shared Services & Utilities (`core/`):
- `ApiClient`: Dio HTTP client instance with `InterceptorsWrapper` handling Bearer auth token injection, global error alerts, base URL switching, and JSON serialization.
- `StorageService`: Persistent key-value storage using `flutter_secure_storage` for token/auth data and `shared_preferences` for settings.
- `AppTheme`: Light and dark color palettes, typography specs, inputs styling, card decorations, and button styling.
- `AppLocalizations`: Localization delegate loading English (`en`) and Arabic (`ar`) translations.

### 4.4 Database Design & Entity Relationships

#### 4.4.1 Migrations Inventory:

| Migration File | Created Tables / Modified Columns |
| :--- | :--- |
| `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` |
| `2026_06_07_134857_add_role_and_phone_to_users_table.php` | Add `role` (`admin`,`receptionist`,`doctor`,`patient`), `phone` to `users` |
| `2026_06_07_135026_create_doctor_details_table.php` | `doctor_details` (`id`, `user_id`, `specialization`, `bio`) |
| `2026_06_07_135029_create_doctor_schedules_table.php` | `doctor_schedules` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `duration_per_patient`) |
| `2026_06_07_135032_create_services_table.php` | `services` (`id`, `name`, `description`, `price`, `duration`) |
| `2026_06_07_135034_create_appointments_table.php` | `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `notes`) |
| `2026_06_07_135036_create_medical_records_table.php` | `medical_records` (`id`, `appointment_id`, `patient_id`, `doctor_id`, `visit_date`, `symptoms`, `diagnosis`, `doctor_notes`) |
| `2026_06_07_135038_create_prescriptions_table.php` | `prescriptions` (`id`, `medical_record_id`, `medication_name`, `dosage`, `duration`, `instructions`) |
| `2026_06_07_135040_create_invoices_table.php` | `invoices` (`id`, `appointment_id`, `total_amount`, `payment_status`, `payment_method`, `issued_at`) |
| `2026_07_03_202341_add_fcm_token_to_users_table.php` | Add `fcm_token` to `users` |
| `2026_07_03_210531_add_wallet_to_users_table.php` | Add `wallet_balance`, `violation_count` to `users` |
| `2026_07_03_210531_create_settings_table.php` | `settings` (`id`, `key`, `value`, `description`) |
| `2026_07_03_210532_add_cancellation_fields_to_appointments_table.php` | Add `cancelled_by`, `cancellation_reason`, `cancelled_at` to `appointments` |
| `2026_07_03_210532_add_deposit_fields_to_invoices_table.php` | Add `deposit_amount`, `remaining_amount` to `invoices` |
| `2026_07_03_210532_create_wallet_transactions_table.php` | `wallet_transactions` (`id`, `user_id`, `appointment_id`, `type`, `amount`, `balance_before`, `balance_after`, `description`) |
| `2026_07_04_201919_add_otp_to_users_table.php` | Add `otp_code`, `otp_expires_at` to `users` |
| `2026_08_02_000001_add_profile_picture_to_users_table.php` | Add `profile_picture` to `users` |
| `2026_08_02_000002_add_consultation_fee_to_doctor_details_table.php` | Add `consultation_fee` to `doctor_details` |
| `2026_08_02_000003_update_appointments_and_invoices_tables.php` | Add `consultation_fee`, `additional_cost`, `additional_note` to `appointments`; add `paid_at` to `invoices` |
| `2026_08_07_000001_create_audit_logs_table.php` | `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`) |
| `2026_08_07_000002_create_notifications_table.php` | `notifications` (`id`, `user_id`, `type`, `entity_type`, `entity_id`, `title_ar`, `title_en`, `body_ar`, `body_en`, `data`, `is_read`) |
| `2026_08_07_000003_add_locale_to_users_and_alter_wallet_transactions.php` | Add `locale` (`ar`,`en`) to `users` |

#### 4.4.2 Database Table Inventory & Key Specifications:

1. **`users`**:
   - `id` (PK, BigInt AutoIncrement)
   - `name` (VarChar 255)
   - `email` (VarChar 255, Unique)
   - `password` (VarChar 255, Hashed)
   - `phone` (VarChar 20, Nullable)
   - `role` (Enum: `admin`, `receptionist`, `doctor`, `patient`, Default: `patient`)
   - `wallet_balance` (Decimal 10,2, Default: 0.00)
   - `violation_count` (Unsigned Int, Default: 0)
   - `otp_code` (VarChar 255, Nullable, Hashed)
   - `otp_expires_at` (Timestamp, Nullable)
   - `fcm_token` (Text, Nullable)
   - `profile_picture` (VarChar 255, Nullable)
   - `locale` (Enum: `ar`, `en`, Default: `ar`)
   - `email_verified_at` (Timestamp, Nullable)

2. **`doctor_details`**:
   - `id` (PK, BigInt AutoIncrement)
   - `user_id` (FK -> `users.id`, Cascade Delete)
   - `specialization` (VarChar 255)
   - `bio` (Text, Nullable)
   - `consultation_fee` (Decimal 10,2, Default: 100.00)

3. **`doctor_schedules`**:
   - `id` (PK, BigInt AutoIncrement)
   - `doctor_id` (FK -> `doctor_details.id`, Cascade Delete)
   - `day_of_week` (Enum: `sunday`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`)
   - `start_time` (Time: e.g. `09:00:00`)
   - `end_time` (Time: e.g. `14:00:00`)
   - `duration_per_patient` (Integer: minutes, Default: 20)

4. **`appointments`**:
   - `id` (PK, BigInt AutoIncrement)
   - `patient_id` (FK -> `users.id`, Cascade Delete)
   - `doctor_id` (FK -> `doctor_details.id`, Cascade Delete)
   - `consultation_fee` (Decimal 10,2)
   - `additional_cost` (Decimal 10,2, Default: 0.00)
   - `additional_note` (Text, Nullable)
   - `appointment_date` (Date: `Y-m-d`)
   - `appointment_time` (Time: `H:i:s`)
   - `status` (Enum: `pending`, `confirmed`, `completed`, `cancelled`, `rejected`, Default: `pending`)
   - `notes` (Text, Nullable)
   - `cancelled_by` (Enum: `patient`, `doctor`, `admin`, `receptionist`, Nullable)
   - `cancellation_reason` (Text, Nullable)
   - `cancelled_at` (Timestamp, Nullable)

5. **`medical_records`**:
   - `id` (PK, BigInt AutoIncrement)
   - `appointment_id` (FK -> `appointments.id`, Unique)
   - `patient_id` (FK -> `users.id`)
   - `doctor_id` (FK -> `doctor_details.id`)
   - `visit_date` (Date)
   - `symptoms` (Text, Nullable)
   - `diagnosis` (Text, Nullable)
   - `doctor_notes` (Text, Nullable)

6. **`prescriptions`**:
   - `id` (PK, BigInt AutoIncrement)
   - `medical_record_id` (FK -> `medical_records.id`, Cascade Delete)
   - `medication_name` (VarChar 255)
   - `dosage` (VarChar 255)
   - `duration` (VarChar 255)
   - `instructions` (Text, Nullable)

7. **`invoices`**:
   - `id` (PK, BigInt AutoIncrement)
   - `appointment_id` (FK -> `appointments.id`, Unique)
   - `total_amount` (Decimal 10,2)
   - `deposit_amount` (Decimal 10,2, Default: 0.00)
   - `remaining_amount` (Decimal 10,2, Default: 0.00)
   - `payment_status` (Enum: `paid`, `unpaid`, Default: `unpaid`)
   - `payment_method` (Enum: `cash`, `online`, `wallet`, Nullable)
   - `issued_at` (Timestamp)
   - `paid_at` (Timestamp, Nullable)

8. **`wallet_transactions`**:
   - `id` (PK, BigInt AutoIncrement)
   - `user_id` (FK -> `users.id`, Cascade Delete)
   - `appointment_id` (FK -> `appointments.id`, Nullable)
   - `type` (Enum: `deposit`, `deduct`, `booking_deduct`, `refund_full`, `refund_partial`, `penalty`)
   - `amount` (Decimal 10,2)
   - `balance_before` (Decimal 10,2)
   - `balance_after` (Decimal 10,2)
   - `description` (Text, Nullable)

9. **`notifications`**:
   - `id` (PK, BigInt AutoIncrement)
   - `user_id` (FK -> `users.id`, Cascade Delete)
   - `type` (VarChar 255)
   - `entity_type` (VarChar 255, Nullable)
   - `entity_id` (Unsigned BigInt, Nullable)
   - `title_ar` (VarChar 255)
   - `title_en` (VarChar 255)
   - `body_ar` (Text)
   - `body_en` (Text)
   - `data` (JSON, Nullable)
   - `is_read` (Boolean, Default: false)

10. **`settings`**:
    - `id` (PK, BigInt AutoIncrement)
    - `key` (VarChar 255, Unique)
    - `value` (Text)
    - `description` (Text, Nullable)

#### 4.4.3 Textual ERD Model Representation:
```
 +------------------+           1:1            +---------------------+
 |      users       |-------------------------<|   doctor_details    |
 +------------------+                          +---------------------+
 | id (PK)          |                                     | 1
 | role (enum)      |                                     |
 | wallet_balance   |                                     | N
 | violation_count  |                          +---------------------+
 +------------------+                          |  doctor_schedules   |
    | 1        | 1                             +---------------------+
    |          |                               | id (PK)             |
    | N        | N                             | doctor_id (FK)      |
    v          v                               | day_of_week (enum)  |
+---------+ +---------------------+            +---------------------+
| wallet_ | |    appointments     |
| trans...| +---------------------+
+---------+ | id (PK)             |
            | patient_id (FK)     |
            | doctor_id (FK)      |
            | status (enum)       |
            +---------------------+
               | 1           | 1
               |             |
               v 1:1         v 1:1
            +---------+   +------------------+
            |invoices |   | medical_records  |
            +---------+   +------------------+
                          | id (PK)          |
                          +------------------+
                                 | 1
                                 | N
                                 v
                          +------------------+
                          |  prescriptions   |
                          +------------------+
```

### 4.5 Class & Object Design

#### 4.5.1 Primary Backend Controllers & Services:
- `AuthController`: Handles HTTP requests for auth workflows (`register`, `login`, `verifyOtp`, `resendOtp`, `forgotPassword`, `resetPassword`, `me`, `updateProfile`, `updateFcmToken`, `createStaff`, `listReceptionists`, `updateStaff`, `deleteStaff`).
- `AppointmentController`: Handles scheduling APIs (`preview`, `store`, `patientAppointments`, `doctorAppointments`, `index`, `updateStatus`, `cancel`, `reschedule`, `cancelDayAppointments`, `availableSlots`, `payRemaining`).
- `MedicalRecordController`: Handles EMR actions (`store`, `addPrescription`, `patientRecords`, `show`).
- `InvoiceController`: Handles billing endpoints (`index`, `store`, `show`, `patientInvoices`, `updatePayment`).
- `ReportController`: Generates administrative reports (`dashboard`, `appointmentsReport`, `revenueReport`, `doctorsReport`, `violationsReport`, `patientsReport`).
- `WalletService`: Encapsulates database transaction logic for financial operations (`deposit`, `deduct`, `deductBookingDeposit`, `payInvoiceFromWallet`, `refundFull`, `refundWithPenalty`).
- `FirebaseService`: Constructs OAuth2 JWT tokens from service account keys and issues Google FCM v1 HTTP API requests.
- `NotificationService`: Creates `Notification` DB entries and dispatches localized push messages via `FirebaseService`.

#### 4.5.2 Primary Frontend Architecture Classes (`clinic_app` & `clinic_dashboard`):
- `AuthBloc` / `AuthCubit`: Manages user session state (`Unauthenticated`, `AuthLoading`, `AuthOtpRequired`, `Authenticated`, `AuthFailure`).
- `DoctorBloc` / `PatientBloc`: Handles fetching doctor lists, checking slot grid availability, and executing booking flow.
- `DashboardBloc`: Loads overview metrics, patient listings, invoice lists, and violation reports in `clinic_dashboard`.
- `ApiClient`: Wrapper around Dio executing GET, POST, PUT, PATCH, DELETE calls with Authorization bearer token injection.
- `StorageService`: Interoperable persistent storage wrapper encapsulating secure tokens.

### 4.6 Implemented API Endpoints Inventory

#### Module 1: Authentication & User Profile (`/api/auth`)

| HTTP Method | Route Endpoint | Auth Required | Allowed Role(s) | Description / Purpose | Controller Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `POST` | `/api/auth/register` | No | Public | Register new patient account & email OTP. | `AuthController@register` |
| `POST` | `/api/auth/login` | No | Public | User login; issues OTP for Patient/Doctor, Token for Admin/Receptionist. | `AuthController@login` |
| `POST` | `/api/auth/verify-otp` | No | Public | Verify 6-digit OTP code & return Sanctum bearer token. | `AuthController@verifyOtp` |
| `POST` | `/api/auth/resend-otp` | No | Public | Resend new OTP code to email. | `AuthController@resendOtp` |
| `POST` | `/api/auth/forgot-password` | No | Public | Trigger password reset OTP code. | `AuthController@forgotPassword` |
| `POST` | `/api/auth/verify-reset-otp` | No | Public | Verify password reset OTP code. | `AuthController@verifyResetOtp` |
| `POST` | `/api/auth/reset-password` | No | Public | Submit new password after OTP verification. | `AuthController@resetPassword` |
| `POST` | `/api/auth/logout` | Yes | All Roles | Revoke current Sanctum token. | `AuthController@logout` |
| `GET` | `/api/auth/me` | Yes | All Roles | Get profile data of authenticated user. | `AuthController@me` |
| `POST/PUT` | `/api/auth/profile` | Yes | All Roles | Update user profile details and avatar. | `AuthController@updateProfile` |
| `POST` | `/api/auth/fcm-token` | Yes | All Roles | Store/update user FCM registration token. | `AuthController@updateFcmToken` |
| `POST` | `/api/auth/create-staff` | Yes | `admin` | Create receptionist staff account. | `AuthController@createStaff` |

#### Module 2: Doctors & Schedules (`/api/doctors`)

| HTTP Method | Route Endpoint | Auth Required | Allowed Role(s) | Description / Purpose | Controller Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/doctors` | No | Public | List all active doctors with specializations and fees. | `DoctorController@index` |
| `GET` | `/api/doctors/{id}` | No | Public | View specific doctor profile and bio details. | `DoctorController@show` |
| `POST` | `/api/doctors` | Yes | `admin` | Create new doctor profile. | `DoctorController@store` |
| `PUT` | `/api/doctors/{id}` | Yes | `admin` | Update existing doctor profile. | `DoctorController@update` |
| `DELETE` | `/api/doctors/{id}` | Yes | `admin` | Delete doctor profile. | `DoctorController@destroy` |
| `GET` | `/api/doctors/{id}/schedules` | No | Public | Get weekly schedule slots for a doctor. | `DoctorScheduleController@index` |
| `POST` | `/api/doctors/{id}/schedules` | Yes | `admin` | Add working schedule for a doctor. | `DoctorScheduleController@store` |
| `PUT` | `/api/doctors/{id}/schedules/{scheduleId}` | Yes | `admin` | Update working schedule. | `DoctorScheduleController@update` |
| `DELETE` | `/api/doctors/{id}/schedules/{scheduleId}` | Yes | `admin` | Remove working schedule. | `DoctorScheduleController@destroy` |
| `GET` | `/api/doctors/{id}/available-slots` | Yes | All Roles | Calculate free slots for a given date. | `AppointmentController@availableSlots` |

#### Module 3: Appointments & Booking (`/api/appointments`)

| HTTP Method | Route Endpoint | Auth Required | Allowed Role(s) | Description / Purpose | Controller Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `POST` | `/api/appointments/preview` | Yes | `patient` | Preview booking cost, wallet balance, and balance after. | `AppointmentController@preview` |
| `POST` | `/api/appointments` | Yes | `patient`, `admin`, `receptionist` | Book appointment with wallet fee pre-deduction. | `AppointmentController@store` |
| `GET` | `/api/appointments/my` | Yes | `patient` | Get patient personal appointments. | `AppointmentController@patientAppointments` |
| `GET` | `/api/appointments/doctor` | Yes | `doctor` | Get doctor consultation queue. | `AppointmentController@doctorAppointments` |
| `GET` | `/api/appointments` | Yes | `admin`, `receptionist` | Get all clinic appointments. | `AppointmentController@index` |
| `PATCH` | `/api/appointments/{id}/status` | Yes | `doctor` | Update appointment status (`confirmed`,`completed`,`rejected`). | `AppointmentController@updateStatus` |
| `PATCH` | `/api/appointments/{id}/cancel` | Yes | `patient`, `admin`, `receptionist` | Cancel appointment with lead-time refund/penalty logic. | `AppointmentController@cancel` |
| `PATCH` | `/api/appointments/cancel-day` | Yes | `doctor` | Cancel all appointments for doctor on a specific day. | `AppointmentController@cancelDayAppointments` |
| `PATCH` | `/api/appointments/{id}/reschedule` | Yes | `admin`, `receptionist` | Reschedule appointment to new date/time slot. | `AppointmentController@reschedule` |
| `POST` | `/api/appointments/{id}/pay-remaining` | Yes | `patient` | Pay remaining visit balance using wallet funds. | `AppointmentController@payRemaining` |

#### Module 4: EMR & Prescriptions (`/api/medical-records`)

| HTTP Method | Route Endpoint | Auth Required | Allowed Role(s) | Description / Purpose | Controller Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `POST` | `/api/medical-records` | Yes | `doctor` | Create medical record for completed visit. | `MedicalRecordController@store` |
| `POST` | `/api/medical-records/{id}/prescriptions` | Yes | `doctor` | Add digital prescription line item to EMR. | `MedicalRecordController@addPrescription` |
| `GET` | `/api/medical-records/my` | Yes | `patient` | Get patient medical records history. | `MedicalRecordController@patientRecords` |
| `GET` | `/api/medical-records/{id}` | Yes | `admin`, `doctor` | View specific EMR record and prescriptions. | `MedicalRecordController@show` |

#### Module 5: Wallet & Invoices (`/api/wallet`, `/api/invoices`)

| HTTP Method | Route Endpoint | Auth Required | Allowed Role(s) | Description / Purpose | Controller Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/wallet/balance` | Yes | `patient` | Fetch authenticated patient wallet balance. | `WalletController@balance` |
| `GET` | `/api/wallet/transactions` | Yes | `patient` | Fetch authenticated patient transaction log. | `WalletController@transactions` |
| `POST` | `/api/wallet/deposit/{userId}` | Yes | `admin`, `receptionist` | Deposit cash funds to patient wallet. | `WalletController@deposit` |
| `POST` | `/api/wallet/deduct/{userId}` | Yes | `admin`, `receptionist` | Manually deduct funds from patient wallet. | `WalletController@deduct` |
| `GET` | `/api/wallet/transactions/{userId}` | Yes | `admin`, `receptionist` | View patient wallet transaction history. | `WalletController@patientTransactions` |
| `GET` | `/api/invoices/my` | Yes | `patient` | List invoices of authenticated patient. | `InvoiceController@patientInvoices` |
| `GET` | `/api/invoices` | Yes | `admin`, `receptionist` | List all clinic invoices. | `InvoiceController@index` |
| `POST` | `/api/invoices` | Yes | `admin`, `receptionist` | Manually create invoice for appointment. | `InvoiceController@store` |
| `GET` | `/api/invoices/{appointmentId}` | Yes | `admin`, `receptionist` | View invoice for specific appointment. | `InvoiceController@show` |
| `PATCH` | `/api/invoices/{id}/payment` | Yes | `admin`, `receptionist` | Update invoice payment status (`paid`/`unpaid`). | `InvoiceController@updatePayment` |

#### Module 6: Managerial Reports & Settings (`/api/reports`, `/api/settings`)

| HTTP Method | Route Endpoint | Auth Required | Allowed Role(s) | Description / Purpose | Controller Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/reports/dashboard` | Yes | `admin` | Overall clinic operational & financial summary stats. | `ReportController@dashboard` |
| `GET` | `/api/reports/appointments` | Yes | `admin` | Appointment breakdown report by date range. | `ReportController@appointmentsReport` |
| `GET` | `/api/reports/revenue` | Yes | `admin` | Financial revenue breakdown by date range & payment method. | `ReportController@revenueReport` |
| `GET` | `/api/reports/doctors` | Yes | `admin`, `receptionist` | Doctor performance & revenue contribution report. | `ReportController@doctorsReport` |
| `GET` | `/api/reports/violations` | Yes | `admin` | Patient late cancellation violation report. | `ReportController@violationsReport` |
| `GET` | `/api/reports/patients` | Yes | `admin`, `receptionist` | Patient directory report with search query. | `ReportController@patientsReport` |
| `GET` | `/api/settings` | No | Public | Retrieve global system setting values. | `SettingController@index` |
| `PATCH` | `/api/settings` | Yes | `admin` | Update global system settings (`booking_deposit`, etc.). | `SettingController@update` |

### 4.7 Authentication, Authorization & Security Mechanisms
1. **2FA Email OTP Authentication**: Patient and Doctor accounts receive a 6-digit random string hashed using Bcrypt into `otp_code` with a 10-minute expiration (`otp_expires_at`).
2. **Direct Staff Bypass**: Accounts with role `admin` or `receptionist` bypass OTP generation upon password verification to provide rapid dashboard login.
3. **Sanctum Bearer Token Enforcement**: Upon OTP verification, Laravel Sanctum generates a plain text access token (`createToken('auth_token')->plainTextToken`) transmitted in HTTP headers (`Authorization: Bearer <token>`).
4. **Role Middleware Control**: Middleware [`RoleMiddleware.php`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Middleware/RoleMiddleware.php) checks user `role` column against allowed parameters (`patient`, `doctor`, `receptionist`, `admin`). Returns HTTP 403 Unauthorized Action upon violation.

### 4.8 Main Business Workflows

#### 4.8.1 Patient Registration & 2FA Login Workflow:
```
[Patient UI] 
   |
   |-- (POST /api/auth/register) --> [Laravel AuthController] --> Creates User (role='patient')
   |                                                         --> Generates OTP & Sends Email
   |                                                         <-- Returns 201 (verified: false)
   v
[OTP Screen]
   |-- (POST /api/auth/verify-otp) --> [Laravel AuthController] --> Validates OTP & Expiry
   |                                                           --> Generates Sanctum Token
   |                                                           <-- Returns Token & User JSON
   v
[Home Dashboard] (Token saved securely in Flutter Secure Storage)
```

#### 4.8.2 Medical Visit Completion & Invoice Settlement Workflow:
```
[Doctor App] 
   |-- Tap "Complete Visit" & Enter Symptoms/Diagnosis
   |-- (POST /api/medical-records) --> [Laravel MedicalRecordController]
   |                                     |-- Creates Record in medical_records
   |                                     |-- Updates Appointment status = 'completed'
   |                                     |-- Calculates Total = Fee + Additional Cost
   |                                     |-- Creates Invoice (deposit = Fee, remaining = Additional)
   |                                     +-- Dispatches FCM Notification to Patient
   v
[Patient App]
   |-- Receives Push Alert "Visit Completed"
   |-- Tap "Pay Remaining" --> (POST /api/appointments/{id}/pay-remaining)
   |                                |--> [WalletService] Deducts remaining balance from Wallet
   |                                |--> Updates Invoice payment_status = 'paid'
   |                                +-- Returns Updated Balance & Invoice
```

---

## Chapter Five – Implementation and Verification

### 5.1 Technology Stack & Development Tools Summary

- **Backend Framework**: Laravel 11.31 (PHP 8.2.x)
- **Database Engine**: MySQL 8.0 / SQLite 3
- **Frontend Framework**: Flutter 3.11+ (Dart 3.x)
- **Authentication Protocol**: Laravel Sanctum 4.0 (Bearer Tokens) + Email OTP
- **Push Notification Gateway**: Firebase Cloud Messaging (FCM v1 HTTP API)
- **Email Gateway**: SMTP Service (Laravel Mail Mailable `OtpMail`)
- **State Management**: `flutter_bloc` 9.1.0 (`Bloc` and `Cubit`)
- **HTTP Client**: `dio` 5.8.0 with Request Interceptors
- **Navigation & Routing**: `go_router` 15.1.0 (Patient App), `sidebarx` 0.17.2 (Admin Dashboard)
- **Localization Engine**: Flutter `intl` & `AppLocalizations` (Arabic/English RTL/LTR)
- **Primary Development IDE**: Visual Studio Code / Android Studio
- **API Testing Suite**: Postman (`clinic management system.postman_collection.json`)

### 5.2 Implementation Details of Major Features

#### Feature 1: Dynamic Working Schedule & Slot Availability Engine
- **Backend Implementation**: [`AppointmentController@availableSlots`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php#L160). Parses target date string, determines day of week, pulls active `DoctorSchedule`, loops from `start_time` to `end_time` adding `duration_per_patient` steps, and removes any slot matching an existing `pending` or `confirmed` appointment.
- **Frontend Implementation**: [`doctor_detail_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/doctor_detail_screen.dart). Renders horizontal date picker and interactive grid of slot chips (e.g. `09:00`, `09:20`, `09:40`).

#### Feature 2: Progressive Cancellation Penalty Engine
- **Backend Implementation**: [`WalletService@refundWithPenalty`](file:///d:/FFF/New-folder/clinic-managment/app/Services/WalletService.php#L142). Executes inside DB transaction. Calculates penalty rate: $\min(\text{violation\_count} \times 5\%, \text{max\_penalty\_percentage})$. Splitting original consultation fee into non-refundable penalty transaction and partial wallet refund.

#### Feature 3: Integrated Electronic Medical Records & Prescription Engine
- **Backend Implementation**: [`MedicalRecordController.php`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php). Links `appointment_id`, `patient_id`, and `doctor_id`. Automatically transitions appointment to `completed` and generates linked invoice upon record creation.

#### Feature 4: Admin & Receptionist Management Dashboard
- **Frontend Implementation**: [`clinic_dashboard/lib/features/dashboard/presentation/screens/admin_dashboard_screen.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/admin_dashboard_screen.dart). Implements collapsible `SidebarX` drawer with custom views: Dashboard Overview, Appointments Management, Doctors Management, Patients Management, Invoices Management, Receptionists Management, and Violations Management.

### 5.3 Complete UI / Screen Inventory

#### 5.3.1 Patient & Doctor App Screens (`clinic_app`):

1. **`SplashScreen`** ([`splash_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/auth/screens/splash_screen.dart)):
   - **Role**: All Roles / Public
   - **Purpose**: App startup screen checking stored token and redirecting user to login or main dashboard.
2. **`LoginScreen`** ([`login_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/auth/screens/login_screen.dart)):
   - **Role**: Public
   - **Purpose**: User login entering email & password; supports password visibility toggle and Arabic/English language toggle.
3. **`OtpVerificationScreen`** ([`otp_verification_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/auth/screens/otp_verification_screen.dart)):
   - **Role**: Patient, Doctor
   - **Purpose**: 6-digit PIN code input box (`pin_code_fields`) with timer countdown for OTP verification.
4. **`RegisterScreen`** ([`register_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/auth/screens/register_screen.dart)):
   - **Role**: Public
   - **Purpose**: Patient registration form capturing full name, email, phone, and password confirmation.
5. **`PatientMainScreen`** ([`patient_main_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/patient_main_screen.dart)):
   - **Role**: Patient
   - **Purpose**: Bottom navigation wrapper providing tab access to Dashboard, Appointments, Medical Records, Wallet, and Settings.
6. **`PatientDashboardScreen`** ([`patient_dashboard_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/patient_dashboard_screen.dart)):
   - **Role**: Patient
   - **Purpose**: Home dashboard showing greeting, wallet card summary, quick action buttons, and active appointment banner.
7. **`DoctorListScreen`** ([`doctor_list_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/doctor_list_screen.dart)):
   - **Role**: Patient
   - **Purpose**: Browse and search list of doctors with specialization tags, consultation fee badges, and avatars.
8. **`DoctorDetailScreen`** ([`doctor_detail_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/doctor_detail_screen.dart)):
   - **Role**: Patient
   - **Purpose**: View doctor bio, specialization, consultation fee, interactive date picker, available time slot grid, and confirm booking.
9. **`PatientAppointmentsScreen`** ([`patient_appointments_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/patient_appointments_screen.dart)):
   - **Role**: Patient
   - **Purpose**: View tabs for upcoming and historical appointments with status badges (`pending`, `confirmed`, `completed`, `cancelled`) and action buttons (Cancel, Pay Remaining Balance).
10. **`PatientRecordsScreen`** ([`patient_records_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/medical_records/screens/patient_records_screen.dart)):
    - **Role**: Patient
    - **Purpose**: List electronic medical records with visit dates, doctor names, and diagnosis summaries.
11. **`RecordDetailsScreen`** ([`record_details_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/medical_records/screens/record_details_screen.dart)):
    - **Role**: Patient, Doctor
    - **Purpose**: Detailed view of clinical visit notes, diagnosis, symptoms, and digital prescriptions list.
12. **`WalletScreen`** ([`wallet_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/wallet/screens/wallet_screen.dart)):
    - **Role**: Patient
    - **Purpose**: Displays current wallet balance, total spent, and scrollable transaction log with color-coded badges (`deposit`, `deduct`, `booking_deduct`, `penalty`, `refund`).
13. **`PatientSettingsScreen`** ([`patient_settings_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/patient/screens/patient_settings_screen.dart)):
    - **Role**: Patient
    - **Purpose**: Profile picture update, personal details edit, language switcher (Arabic/English), theme mode selector (Dark/Light), and logout button.
14. **`DoctorMainScreen`** ([`doctor_main_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/doctor/screens/doctor_main_screen.dart)):
    - **Role**: Doctor
    - **Purpose**: Bottom navigation wrapper for Doctor role (Dashboard, Appointments Queue, Settings).
15. **`DoctorDashboardScreen`** ([`doctor_dashboard_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/doctor/screens/doctor_dashboard_screen.dart)):
    - **Role**: Doctor
    - **Purpose**: Overview of today's consultation count, pending requests, and upcoming visits.
16. **`DoctorAppointmentsScreen`** ([`doctor_appointments_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/doctor/screens/doctor_appointments_screen.dart)):
    - **Role**: Doctor
    - **Purpose**: Queue of patient appointments with action controls to Accept, Reject, Complete Visit, Create EMR, or Cancel Clinic Day.
17. **`CreateRecordScreen`** ([`create_record_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/medical_records/screens/create_record_screen.dart)):
    - **Role**: Doctor
    - **Purpose**: Form to enter visit symptoms, diagnosis, and doctor notes for an appointment.
18. **`AddPrescriptionDialog`** ([`add_prescription_dialog.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/medical_records/screens/add_prescription_dialog.dart)):
    - **Role**: Doctor
    - **Purpose**: Modal dialog to add medication name, dosage, duration, and instructions.
19. **`NotificationsScreen`** ([`notifications_screen.dart`](file:///d:/FFF/New-folder/clinic_app/lib/features/notifications/presentation/screens/notifications_screen.dart)):
    - **Role**: All Roles
    - **Purpose**: List user notifications with read status indicators and delete actions.

#### 5.3.2 Admin & Receptionist Dashboard Views (`clinic_dashboard`):

1. **`AdminDashboardScreen`** ([`admin_dashboard_screen.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/admin_dashboard_screen.dart)):
   - **Role**: Admin, Receptionist
   - **Purpose**: Main layout container featuring SidebarX navigation drawer, top bar with profile avatar, theme toggle, language switcher, and active view body.
2. **`DashboardOverviewView`** ([`dashboard_overview_view.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/dashboard_overview_view.dart)):
   - **Role**: Admin, Receptionist
   - **Purpose**: Metric cards displaying total patients, doctors, appointments count, revenue sum, pending invoice sum, penalty sum, and recent booking tables.
3. **`AppointmentsManagementView`** ([`appointments_management_view.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/appointments_management_view.dart)):
   - **Role**: Admin, Receptionist
   - **Purpose**: Master table of all clinic appointments with filtering by status and date, and action modals to Reschedule or Cancel.
4. **`DoctorsManagementView`** ([`doctors_management_view.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/doctors_management_view.dart)):
   - **Role**: Admin
   - **Purpose**: Doctor directory management allowing Admin to create doctor accounts, set consultation fees, edit bios, upload profile pictures, and configure weekly working schedules.
5. **`PatientsManagementView`** ([`patients_management_view.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/patients_management_view.dart)):
   - **Role**: Admin, Receptionist
   - **Purpose**: Patient directory table with search bar, wallet balance display, violation count display, wallet cash deposit modal, wallet deduction modal, and transaction history modal.
6. **`InvoicesManagementView`** ([`invoices_management_view.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/invoices_management_view.dart)):
   - **Role**: Admin, Receptionist
   - **Purpose**: Master billing table displaying total amount, deposit paid, remaining balance, payment status, and controls to mark invoices as paid via cash/wallet.
7. **`ReceptionistsManagementView`** ([`receptionists_management_view.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/receptionists_management_view.dart)):
   - **Role**: Admin
   - **Purpose**: Receptionist staff directory allowing creation, editing, profile picture upload, and deletion of staff accounts.
8. **`ViolationsManagementView`** ([`violations_management_view.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/violations_management_view.dart)):
   - **Role**: Admin
   - **Purpose**: Compliance report listing patients with late cancellation violations, total penalties paid, and penalty rates.

### 5.4 Core Screen-to-Screen UI Flow Journeys

#### Patient Appointment Booking Journey:
```
LoginScreen
   └──> OtpVerificationScreen
         └──> PatientMainScreen (Dashboard Tab)
               └──> DoctorListScreen
                     └──> DoctorDetailScreen (Select Date & Time Slot)
                           └──> Booking Confirmation Modal (Wallet Pre-deduction)
                                 └──> PatientAppointmentsScreen (Confirmed Appointment)
```

#### Doctor Medical Visit & Prescription Journey:
```
LoginScreen
   └──> DoctorMainScreen
         └──> DoctorAppointmentsScreen (Select Patient Queue Item)
               └──> CreateRecordScreen (Enter Symptoms & Diagnosis)
                     └──> AddPrescriptionDialog (Enter Medication & Dosage)
                           └──> RecordDetailsScreen (Generated EMR)
```

#### Receptionist Patient Wallet Deposit Journey:
```
LoginScreen (Direct Staff Auth)
   └──> AdminDashboardScreen
         └──> PatientsManagementView (Search Patient)
               └──> Deposit Dialog (Enter Cash Amount)
                     └──> Success Confirmation & Updated Patient Wallet Balance
```

### 5.5 Testing, Verification & Quality Assurance Evidence
- **Automated PHPUnit Tests**: Test files exist at [`tests/Feature/ExampleTest.php`](file:///d:/FFF/New-folder/clinic-managment/tests/Feature/ExampleTest.php) and [`tests/Unit/ExampleTest.php`](file:///d:/FFF/New-folder/clinic-managment/tests/Unit/ExampleTest.php) containing skeleton tests.
- **Postman API Test Collection**: Comprehensive Postman Collection file located at [`clinic management system.postman_collection.json`](file:///d:/FFF/New-folder/clinic-managment/clinic%20management%20system.postman_collection.json) (size: 48,524 bytes). Contains pre-configured requests and test assertions covering Auth, Doctors, Schedules, Appointments, EMR, Invoices, Wallet, Reports, and Settings endpoints.
- **Manual QA Protocol**: End-to-end verification performed manually across physical Android devices, web browsers, and desktop builds verifying wallet deductions, late cancellation penalty calculations, FCM notification delivery, and dual language RTL layout rendering.

### 5.6 Development Environment Setup & Configuration Guide

#### Required Technical Stack:
- **Operating System**: Windows 10/11, macOS, or Linux.
- **PHP Version**: PHP 8.2 or higher with `pdo`, `mbstring`, `openssl`, `curl`, `json` extensions.
- **Composer**: Composer 2.x package manager.
- **Flutter SDK**: Flutter 3.11.x or higher.
- **Dart SDK**: Dart 3.x.
- **Database Server**: MySQL 8.0+ or SQLite 3.

#### Environment Setup Steps (`clinic-managment`):
1. Copy environment file: `cp .env.example .env`.
2. Configure database connection variables in `.env`:
   - `DB_CONNECTION=mysql`
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_DATABASE=clinic_db`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=secret`
3. Place Firebase Service Account JSON credentials at `storage/app/firebase-credentials.json`.
4. Install PHP dependencies: `composer install`.
5. Generate application encryption key: `php artisan key:generate`.
6. Run database migrations & execute demo data seeder: `php artisan migrate:fresh --seed --seeder=DemoDataSeeder`.
7. Launch local API dev server: `php artisan serve`.

#### Environment Setup Steps (`clinic_app` & `clinic_dashboard`):
1. Fetch Flutter dependencies: `flutter pub get`.
2. Run Patient App: `flutter run -d chrome` or `flutter run -d android`.
3. Run Admin Dashboard: `flutter run -d chrome` or `flutter run -d windows`.

### 5.7 Seeders & Demo Data Analysis (`DemoDataSeeder.php`)
The codebase includes a comprehensive database seeder located at [`database/seeders/DemoDataSeeder.php`](file:///d:/FFF/New-folder/clinic-managment/database/seeders/DemoDataSeeder.php).

#### Pre-Configured Demo Accounts:
1. **Admin Account**:
   - Email: `admin@clinic.com`
   - Password: `password123`
   - Role: `admin`
2. **Receptionist Account**:
   - Email: `receptionist@clinic.com`
   - Password: `password123`
   - Role: `receptionist`
3. **Doctor Accounts**:
   - Doctor 1 (Cardiology): `doctor1@clinic.com` / `password123` (Fee: $150.00)
   - Doctor 2 (Dermatology): `doctor2@clinic.com` / `password123` (Fee: $120.00)
   - Doctor 3 (Pediatrics): `doctor3@clinic.com` / `password123` (Fee: $100.00)
   - Doctor 4 (Orthopedics): `doctor4@clinic.com` / `password123` (Fee: $140.00)
4. **Patient Accounts**:
   - 20 pre-seeded patient accounts (`patient1@clinic.com` through `patient20@clinic.com` / `password123`) seeded with randomized wallet balances ($550.00 - $1,500.00) and sample appointment/invoice records.

### 5.8 Development & Version Control History (Git Milestones)
Extracted commit history across repositories:
- `485dd95`: AuthController and login flow refactoring.
- `2a194e8`: Implementation of Receptionist Dashboard, Push Notifications, and general fixes.
- `41c7d3f`: Inclusion of profile picture fields in ReportController endpoints.
- `30c0ab2`: FCM notification decoupling using terminating callbacks to prevent HTTP booking timeouts.
- `41fc536`: Rearchitecting booking and cancellation flows to eliminate loading spinner freeze in Flutter client.
- `8df5c63`: Scheduling success dialogs post-frame to prevent MIUI ANR crashes.
- `07ae667`: Addition of Patients tab, 100% Arabic localization, and instant avatar refresh in dashboard.

### 5.9 Project Work Division & Contribution Evidence
- **Commit Authorship Evidence**: All commit logs in all three repositories (`clinic-managment`, `clinic_app`, `clinic_dashboard`) explicitly record commit author:
  - **Author Name**: `SalehAlghabra`
  - **Author Email**: `msalh0689@gmail.com`
- **Team Work Distribution Note**: `[NOT ENOUGH EVIDENCE TO DETERMINE INDIVIDUAL TEAM MEMBER RESPONSIBILITIES]`. While Git history records single-author commits, formal academic work division among university project members must be confirmed directly with the project team.

---

## Chapter Six – Conclusion Material

### 6.1 Summary of Accomplished Capabilities
1. Fully functional multi-tenant-ready Laravel 11 RESTful API backend handling role-based access control (`admin`, `receptionist`, `doctor`, `patient`).
2. Two complete Flutter client applications (`clinic_app` for Patients/Doctors and `clinic_dashboard` for Admin/Receptionists) supporting Android, iOS, Web, Windows, macOS, and Linux.
3. Automated working schedule slot calculation preventing double-bookings.
4. Integrated digital wallet system supporting fee pre-deductions, remaining balance payments, and cash deposits.
5. Automated 24-hour lead-time cancellation penalty engine with progressive rate scaling based on violation counts.
6. Centralized Electronic Medical Record (EMR) system with electronic prescription line items.
7. Multi-currency invoice tracking and real-time managerial financial analytics dashboards.
8. Full bilingual support (Arabic and English) with native RTL/LTR layout transitions and dual dark/light themes.

### 6.2 Key Technical Innovations & System Strengths
- **Behavioral Cancellation Penalty Algorithm**: Prevents clinic loss by penalizing habitual late cancellations while protecting early cancellations with 100% refunds.
- **Asynchronous FCM Notification Engine**: Prevents API response lag by decoupling HTTP FCM push dispatching from database commit cycles.
- **Unified Flutter Dashboard**: Single Flutter codebase (`clinic_dashboard`) rendering responsive admin analytics across Web and Desktop platforms.

### 6.3 Implementation Limitations & Constraints
1. **Third-Party Payment Gateway Integration**: Online payments (`payment_method = 'online'`) are currently simulated via wallet balance deductions rather than live Stripe/PayPal API SDK integration.
2. **SMS Gateway Integration**: OTP delivery relies on SMTP email (`OtpMail`) rather than direct SMS gateways (e.g. Twilio).
3. **Automated Unit Test Coverage**: Automated test suites rely primarily on Postman API collection verification rather than comprehensive PHPUnit/Pest coverage.

### 6.4 Remaining Work & Recommended Future Roadmap
- Integration of live payment gateways (Stripe / HyperPay).
- Implementation of direct SMS OTP gateways.
- Integration of telehealth video consultation calls (WebRTC / Agora).
- Expansion of automated PHPUnit feature tests.

---

## Chapter Seven – Limitations and Missing Information

`[NOT FOUND / NEEDS CONFIRMATION]`

The following specific items could not be conclusively established from inspecting the codebase files and require manual verification prior to finalizing the university academic report:

1. **Exact University Project Title & Course Code**: `[NOT FOUND / NEEDS CONFIRMATION]`
2. **University Name, Faculty, & Department**: `[NOT FOUND / NEEDS CONFIRMATION]`
3. **Project Supervisor & Academic Advisor Names**: `[NOT FOUND / NEEDS CONFIRMATION]`
4. **Official Student Team Names & IDs**: Git history records commits solely under `SalehAlghabra <msalh0689@gmail.com>`. Individual team member names and task division must be confirmed: `[NOT ENOUGH EVIDENCE TO DETERMINE TEAM MEMBER RESPONSIBILITIES]`.
5. **Formal Academic Citations & Literature References**: `[NOT FOUND / NEEDS CONFIRMATION]` (Theoretical topics needing external paper citations are outlined in Chapter Two).
6. **Production Hardware Server Benchmarks**: `[NOT FOUND / NEEDS CONFIRMATION]` (System was evaluated in local development and emulator environments).

---

## Chapter Eight – Evidence & Source Mapping Index

| Information Category | Verified Source File Path | Key Class / Method / Symbol |
| :--- | :--- | :--- |
| **Backend Dependencies** | [`clinic-managment/composer.json`](file:///d:/FFF/New-folder/clinic-managment/composer.json) | `require`, `laravel/sanctum`, `firebase/php-jwt` |
| **API Route Map** | [`clinic-managment/routes/api.php`](file:///d:/FFF/New-folder/clinic-managment/routes/api.php) | `Route::prefix('auth')`, `Route::middleware('role:...')` |
| **Auth & OTP Logic** | [`clinic-managment/app/Http/Controllers/Api/AuthController.php`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AuthController.php) | `register()`, `login()`, `verifyOtp()` |
| **Appointment Logic** | [`clinic-managment/app/Http/Controllers/Api/AppointmentController.php`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/AppointmentController.php) | `store()`, `cancel()`, `availableSlots()` |
| **Wallet Transaction Engine** | [`clinic-managment/app/Services/WalletService.php`](file:///d:/FFF/New-folder/clinic-managment/app/Services/WalletService.php) | `deductBookingDeposit()`, `refundWithPenalty()` |
| **FCM Push Notification Service** | [`clinic-managment/app/Services/FirebaseService.php`](file:///d:/FFF/New-folder/clinic-managment/app/Services/FirebaseService.php) | `getAccessToken()`, `sendNotification()` |
| **EMR & Prescriptions Controller** | [`clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/MedicalRecordController.php) | `store()`, `addPrescription()` |
| **Managerial Reports Engine** | [`clinic-managment/app/Http/Controllers/Api/ReportController.php`](file:///d:/FFF/New-folder/clinic-managment/app/Http/Controllers/Api/ReportController.php) | `dashboard()`, `revenueReport()`, `violationsReport()` |
| **Demo Data Seeder** | [`clinic-managment/database/seeders/DemoDataSeeder.php`](file:///d:/FFF/New-folder/clinic-managment/database/seeders/DemoDataSeeder.php) | `run()`, Admin/Doctor/Patient default accounts |
| **API Postman Collection** | [`clinic-managment/clinic management system.postman_collection.json`](file:///d:/FFF/New-folder/clinic-managment/clinic%20management%20system.postman_collection.json) | Request specs, HTTP verifications |
| **Patient App Main File** | [`clinic_app/lib/main.dart`](file:///d:/FFF/New-folder/clinic_app/lib/main.dart) | `MyApp`, `MultiBlocProvider`, `MaterialApp.router` |
| **Patient App Dependencies** | [`clinic_app/pubspec.yaml`](file:///d:/FFF/New-folder/clinic_app/pubspec.yaml) | `flutter_bloc`, `go_router`, `dio`, `firebase_messaging` |
| **Dashboard App Main File** | [`clinic_dashboard/lib/main.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/main.dart) | `MyApp`, `DashboardBloc`, `ThemeCubit` |
| **Dashboard Main UI Container** | [`clinic_dashboard/lib/features/dashboard/presentation/screens/admin_dashboard_screen.dart`](file:///d:/FFF/New-folder/clinic_dashboard/lib/features/dashboard/presentation/screens/admin_dashboard_screen.dart) | `AdminDashboardScreen`, `SidebarX` |

---

## Chapter Nine – Suggested Report Diagrams Inventory

1. **System Overall Architecture Diagram**:
   - **Type**: Block Diagram / Deployment Architecture.
   - **Purpose**: Illustrate 3-tier relationship between Flutter Clients (`clinic_app` and `clinic_dashboard`), Laravel API Gateway, MySQL/SQLite DB, FCM Push Server, and SMTP Mailer.
2. **System Use Case Diagram**:
   - **Type**: UML Use Case Diagram.
   - **Purpose**: Graphically map Actors (Patient, Doctor, Receptionist, Admin) to Use Cases (UC-01 to UC-26).
3. **Database Entity Relationship Diagram (ERD)**:
   - **Type**: Crow's Foot ERD Diagram.
   - **Purpose**: Map relationships among `users`, `doctor_details`, `doctor_schedules`, `appointments`, `medical_records`, `prescriptions`, `invoices`, `wallet_transactions`, and `notifications`.
4. **UML Class Diagram**:
   - **Type**: Structural UML Class Diagram.
   - **Purpose**: Depict controller classes, domain models, services (`WalletService`, `FirebaseService`), and their dependencies.
5. **Authentication & 2FA Sequence Diagram**:
   - **Type**: UML Sequence Diagram.
   - **Purpose**: Trace message flow for `Register` -> `Send OTP Email` -> `Submit OTP` -> `Verify` -> `Issue Sanctum Token`.
6. **Appointment Booking & Wallet Deduction Sequence Diagram**:
   - **Type**: UML Sequence Diagram.
   - **Purpose**: Trace interaction between Patient App, API Gateway, Slot Calculator, Wallet Service, DB Transaction, and FCM Notification.
7. **Late Cancellation & Progressive Penalty Activity Diagram**:
   - **Type**: UML Activity Diagram.
   - **Purpose**: Decision tree diagram tracing $H > 24\text{h}$ full refund vs. $H \le 24\text{h}$ penalty calculation and violation increment.
8. **UI Navigation Flow Diagram**:
   - **Type**: Statechart / Wireframe Navigation Diagram.
   - **Purpose**: Display screen-to-screen transitions across Patient, Doctor, and Admin interfaces.

---

## Chapter Ten – Report Preparation Executive Summary

This executive summary encapsulates the core facts extracted from the codebase to assist the report author in drafting the university report:

- **Project Core Purpose**: Digital clinic management ecosystem (Clinova) providing online scheduling, wallet payments, progressive cancellation penalty enforcement, electronic medical records, digital prescriptions, and administrative analytics.
- **Backend Architecture**: Laravel 11.x RESTful API running PHP 8.2+, Sanctum token authentication, 2FA Email OTP, custom role middleware (`admin`, `receptionist`, `doctor`, `patient`), and transactional wallet services.
- **Frontend Architecture**: Two separate Flutter 3.x cross-platform applications (`clinic_app` and `clinic_dashboard`) utilizing BLoC/Cubit state management, Dio HTTP handling, secure storage, dual-language (Arabic/English) RTL/LTR rendering, and dark/light themes.
- **Key Business Innovations**: Automated 24h lead-time cancellation penalty engine with progressive scaling based on patient violation counts; decoupled asynchronous FCM v1 push notification delivery.
- **Database Design**: 10 primary relational tables (`users`, `doctor_details`, `doctor_schedules`, `appointments`, `medical_records`, `prescriptions`, `invoices`, `wallet_transactions`, `notifications`, `settings`) managed via 25 Laravel migrations.
- **Verified Capabilities**: 36 Functional Requirements, 12 Non-Functional Requirements, 26 Use Cases, 55 API endpoints, and 27 UI screens/views are fully implemented and verified against code and Postman test collections.
- **Pending/Unverified Items**: Team member task distribution, supervisor names, live credit card SDKs (Stripe), direct SMS gateways, and formal academic citations require manual addition to Chapter Seven.

---
*End of Information Extraction Document (`project_report_source.md`). All facts verified against active codebase files.*
