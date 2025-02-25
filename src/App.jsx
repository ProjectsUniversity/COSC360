import React, { useState } from 'react'
import SignUpModal from "./components/SignUp.jsx"
import SignUp from './components/SignUp.jsx';

export default function App() {
  const[isModalOpen, setIsModalOpen] = useState(false);

  function handleModal(){
    setIsModalOpen(!isModalOpen);
  }

  return (
    <div>
      {!isModalOpen && <button onClick={handleModal}>Open Modal</button>}
      {isModalOpen && <SignUp onClose={handleModal}/>}
    </div>
  );
}

