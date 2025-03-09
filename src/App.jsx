import React, { useState, useEffect } from 'react'
import SignUp from './components/SignUp.jsx';
import RecSignUp from './components/recruiters/RecruiterSignUp.jsx';
import RecruiterLogin from './components/recruiters/RecruiterLogin.jsx';
import RecruiterDashboard from './components/recruiters/RecruiterDashboard.jsx';

export default function App() {
  const [currentView, setCurrentView] = useState('recLogin'); // Options: recLogin, recSignUp, dashboard
  
  useEffect(() => {
    // For development testing only
    const handleViewDashboard = () => setCurrentView('dashboard');
    const handleViewLogin = () => setCurrentView('recLogin');
    const handleViewSignup = () => setCurrentView('recSignUp');
    
    window.addEventListener('viewDashboard', handleViewDashboard);
    window.addEventListener('viewLogin', handleViewLogin);
    window.addEventListener('viewSignup', handleViewSignup);
    
    return () => {
      window.removeEventListener('viewDashboard', handleViewDashboard);
      window.removeEventListener('viewLogin', handleViewLogin);
      window.removeEventListener('viewSignup', handleViewSignup);
    };
  }, []);
  
  function handleViewChange(view) {
    setCurrentView(view);
  }

  // Display the proper component based on currentView
  const renderComponent = () => {
    switch(currentView) {
      case 'recLogin':
        return <RecruiterLogin onToggleForm={() => handleViewChange('recSignUp')} onLogin={() => handleViewChange('dashboard')} />;
      case 'recSignUp':
        return <RecSignUp onToggleForm={() => handleViewChange('recLogin')} onSignup={() => handleViewChange('dashboard')} />;
      case 'dashboard':
        return <RecruiterDashboard />;
      default:
        return <RecruiterLogin onToggleForm={() => handleViewChange('recSignUp')} onLogin={() => handleViewChange('dashboard')} />;
    }
  };

  return (
    <div>
      {renderComponent()}
    </div>
  );
}

