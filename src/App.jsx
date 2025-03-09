import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import SignUp from './components/SignUp.jsx';
import RecSignUp from './components/recruiters/RecruiterSignUp.jsx';
import RecruiterLogin from './components/recruiters/RecruiterLogin.jsx';
import RecruiterDashboard from './components/recruiters/RecruiterDashboard.jsx';

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* Redirect root to recruiter login by default */}
        <Route path="/" element={<Navigate to="/recruiter/login" replace />} />
        
        {/* Recruiter routes */}
        <Route path="/recruiter/login" element={<RecruiterLogin />} />
        <Route path="/recruiter/signup" element={<RecSignUp />} />
        <Route path="/recruiter/dashboard" element={<RecruiterDashboard />} />
        
        {/* Default redirect for undefined routes */}
        <Route path="*" element={<Navigate to="/recruiter/login" replace />} />
      </Routes>
    </BrowserRouter>
  );
}

