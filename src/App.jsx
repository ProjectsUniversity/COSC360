import React, { useState } from 'react'
import SignUpModal from "/Users/shlokshah/COSC360/src/components/SignUpModal.jsx"

export default function App() {
  const[isModalOpen, setIsModalOpen] = useState(false);

  function handleModal(){
    setIsModalOpen(!isModalOpen);
  }

  return (
    <div>
      {!isModalOpen && <button onClick={handleModal}>Open Modal</button>}
      {isModalOpen && <SignUpModal onClose={handleModal}/>}
    </div>
  );
}

