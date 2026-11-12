# ALLO_SERVICE - Project Presentation

---

## SLIDE 1: Title Slide

# DESIGN AND IMPLEMENTATION OF A LOCAL SERVICES BOOKING SYSTEM: ALLO_SERVICE

**Candidate:** Maneng Sielinou Dannick Pharell Malone

**Registration Number:** ET20231273

**Institution:** Siantou University Institute of Technology and Industry

**Department:** Engineering - Software Engineering

**Academic Year:** 2025-2026

---

## SLIDE 2: Presentation Plan

### Presentation Overview

| Phase | Topic | Time |
|-------|-------|------|
| 1 | Introduction & Background | 1 min |
| 2 | Problem Statement | 30 sec |
| 3 | Objectives | 30 sec |
| 4 | Proposed Solution | 1 min |
| 5 | Material Used | 30 sec |
| 6 | Methodology | 1 min |
| 7 | System Features | 1 min |
| 8 | Conclusion | 30 sec |

**Total Duration:** 6 minutes

---

## SLIDE 3: Introduction

### Background

- **Location:** Yaoundé, Cameroon (Population: ~2.4 million)
- **Problem:** Difficulty accessing reliable local service providers
- **Traditional Methods:** Personal recommendations, trial-and-error
- **Challenges:** No verification, no pricing standards, no payment protection

### Project Overview

- **Platform Name:** Allo Service
- **Type:** Web-based local services booking system
- **Purpose:** Connect clients with service providers (plumbing, electrical, cleaning, tutoring, etc.)

---

## SLIDE 4: Problem Statement

### Key Problems Identified

1. **Difficulty verifying provider reliability** - No centralized verification system

2. **Lack of standardized pricing** - Arbitrary rates, no comparison ability

3. **Payment security concerns** - No protection against fraud or non-delivery

4. **Inefficient booking coordination** - No centralized scheduling system

5. **Lack of accountability** - No review or rating mechanism

6. **Geographic dispersion** - Providers not easily discoverable

---

## SLIDE 5: Objectives

### Main Objective
To design and implement a web-based local services booking system that connects clients with qualified service providers in Yaoundé, Cameroon, while ensuring trust, security, and efficiency.

### Specific Objectives

1. Develop a web-based platform connecting clients with local service providers
2. Implement trust and security mechanisms between clients and providers
3. Design and implement an escrow payment system
4. Develop user identity verification processes (KYC)
5. Integrate mobile money functionality for Cameroonian users

---

## SLIDE 6: Proposed Solution

### Allo Service Platform Features

- **User Registration:** Clients and service providers can register with email/password
- **Provider Profiles:** Detailed profiles with online/offline status, pricing, ratings
- **Service Listings:** Multiple categories (plumbing, electrical, cleaning, tutoring, etc.)
- **Booking System:** Complete booking lifecycle management
- **Escrow Payment:** Funds held until service completion confirmed
- **KYC Verification:** ID document and selfie upload for identity verification
- **Wallet & Payouts:** Internal wallet with mobile money payout capability
- **Admin Panel:** Platform management and dispute resolution

---

## SLIDE 7: Materials Used

### Technology Stack

| Component | Technology |
|-----------|------------|
| Server-side | PHP 8.x |
| Database | MySQL 8.x |
| Web Server | Apache |
| Frontend | HTML5, CSS3, JavaScript |
| Icons | Font Awesome 6 |
| Charts | Chart.js |
| Development Env | WAMP |

### Development Tools

- Visual Studio Code (Code Editor)
- phpMyAdmin (Database Management)
- Git (Version Control)
- Chrome/Firefox (Testing)

### Deployment

- **Live URL:** http://alloservice.42web.io/
- **Hosting Platform:** Free web hosting (42web.io)
- **Domain:** Custom domain from hosting provider

---

## SLIDE 8: Methodology

### Agile Scrum Methodology

- **Approach:** Iterative development with sprints
- **Architecture:** Model-View-Controller (MVC)
- **Database Design:** Entity-Relationship with normalization

### Project Timeline (6 Months)

| Sprint | Duration | Focus |
|--------|----------|-------|
| 1 | 2 weeks | Requirements & Planning |
| 2 | 3 weeks | Database & Authentication |
| 3 | 3 weeks | Service Listings & Profiles |
| 4 | 3 weeks | Booking & Payment System |
| 5 | 2 weeks | KYC & Wallet System |
| 6 | 2 weeks | Testing & Deployment |

---

## SLIDE 9: Key Features & Security

### Implemented Features

- **Online/Offline Status:** Real-time provider availability
- **Contact Protection:** Client numbers hidden until booking confirmed
- **Report Button:** Active even when provider offline (fraud protection)
- **Escrow Flow:** Client pays → Admin holds → Released on completion
- **KYC System:** ID verification + Selfie for trust building

### Security Measures

- Password hashing with bcrypt
- SQL injection prevention (prepared statements)
- Input validation on all forms
- Session management with secure cookies
- Role-based access control (Client, Provider, Admin)

---

## SLIDE 10: Conclusion

### Summary

- Successfully developed a functional local services booking system
- Implemented trust mechanisms: KYC verification, escrow payments
- Integrated mobile money for Cameroon context
- Created comprehensive admin panel for platform management

### Impact

- Contributes to digital transformation in Cameroon's service economy
- Provides scalable solution for local service marketplace operations
- Enables informal providers to reach broader customer base

### Future Recommendations

- Native mobile application development
- Direct mobile money API integration
- Real-time chat functionality
- Map-based service search

**Thank You!**

**Questions?**

