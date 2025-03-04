# JobSwipe - Job Board Platform

A full-stack job board platform connecting job seekers and employers. Built with HTML, CSS, JavaScript (frontend) and PHP, MySQL (backend), it allows users to register, post jobs, apply, and manage applications. Features include secure authentication, responsive UI, and dynamic job search with filtering for a seamless experience.

## Project Overview

JobSwipe is a modern job board application that connects job seekers with potential employers. The platform offers two distinct user experiences:

1. **Job Seekers**: Can create accounts, browse job listings, apply to positions, and manage their applications.
2. **Recruiters/Employers**: Can post job openings, review applications, and connect with potential candidates.

## Codebase Structure

The project is organized into the following main directories:

- **HTML, CSS, JS/**: Contains the frontend code
  - **HTML/**: HTML files for different pages
    - **Recruiters/**: Pages specific to recruiter functionality
  - **CSS/**: Stylesheets for the application
  - **JS/**: JavaScript files for client-side functionality
    - **Recruiters/**: JS files specific to recruiter functionality
- **src/**: React component files (for future integration)

## Key Features

- **User Authentication**: Secure login and registration for both job seekers and recruiters
- **Job Listings**: Browse and search for available positions
- **Application System**: Easy application process for job seekers
- **Recruiter Dashboard**: Manage job postings and review applications
- **User Profiles**: Maintain professional profiles for job seekers
- **Guest Browsing**: Unregistered users can browse jobs but are prompted to sign up when interacting

## Navigation Guide

### For Unregistered Users:
1. **Landing Page** (`HTML, CSS, JS/HTML/index.html`): Browse jobs without logging in
2. When trying to interact with job listings, users are prompted to sign up

### For Job Seekers:

1. **Homepage** (`HTML, CSS, JS/HTML/homepage.html`): The main page where job seekers can browse and swipe through job listings
2. **User Login** (`HTML, CSS, JS/HTML/login.html`): Authentication page for job seekers
3. **User Registration** (`HTML, CSS, JS/HTML/signup.html`): Registration page for new job seekers
4. **User Profile** (`HTML, CSS, JS/HTML/userprofile.html`): Page for users to manage their profile information
5. **Apply** (`HTML, CSS, JS/HTML/apply.html`): Page for submitting job applications

### For Recruiters:

1. **Recruiter Login** (`HTML, CSS, JS/HTML/Recruiters/recLogin.html`): Authentication page for recruiters
2. **Recruiter Registration** (`HTML, CSS, JS/HTML/Recruiters/recSignUp.html`): Registration page for new recruiters
3. **Recruiter Dashboard** (`HTML, CSS, JS/HTML/Recruiters/dashboard.html`): Main dashboard for recruiters to manage job postings and applications
4. **Company Dashboard** (`HTML, CSS, JS/HTML/company-dashboard.html`): Company-specific dashboard for organizational management

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **UI Components**: Font Awesome, Custom CSS components
- **Form Validation**: Client-side validation using JavaScript

## Future Enhancements

- Complete React integration for a more dynamic user experience
- Backend API integration for persistent data storage
- Enhanced matching algorithm based on user skills and job requirements
- Messaging system for direct communication between recruiters and applicants
