import React, { useState } from 'react'
import SignUpModal from "./components/SignUp.jsx"
import SignUp from './components/SignUp.jsx';
import RecSignUp from './components/recruiters/recSignUp.jsx';
import RecruiterLogin from './components/recruiters/RecruiterLogin.jsx';

export default function App() {
  const[isModalOpen, setIsModalOpen] = useState(false);
  const[showRecruiterLogin, setShowRecruiterLogin] = useState(false);

  function handleModal(){
    setIsModalOpen(!isModalOpen);
  }

  function toggleRecruiterLogin() {
    setShowRecruiterLogin(!showRecruiterLogin);
  }

  return (
    <div>
      {!isModalOpen && !showRecruiterLogin && (
        <div>
          <button onClick={handleModal}>Open SignUp</button>
          <button onClick={toggleRecruiterLogin}>Open Recruiter Login</button>
        </div>
      )}
      {isModalOpen && <SignUp onClose={handleModal}/>}
      {showRecruiterLogin && <RecruiterLogin />}

      {!showRecruiterLogin && <RecSignUp />}
    </div>
  );
}

