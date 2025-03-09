import React, { useState } from 'react'
import SignUp from './components/SignUp.jsx';
import RecSignUp from './components/recruiters/RecruiterSignUp.jsx';
import RecruiterLogin from './components/recruiters/RecruiterLogin.jsx';

export default function App() {
  const[showRecruiterLogin, setShowRecruiterLogin] = useState(false);

  function toggleRecruiterLogin() {
    setShowRecruiterLogin(!showRecruiterLogin);
  }

  return (
    <div>
      {showRecruiterLogin && <RecruiterLogin />}

      {!showRecruiterLogin && <RecSignUp />}
    </div>
  );
}

