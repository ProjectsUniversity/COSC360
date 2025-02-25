import React, { useState } from 'react'
import SignUpModal from "/Users/shlokshah/COSC360/src/components/SignUpModal.jsx"

export default function App() {
  const[isModalOpen, setIsModalOpen] = useState(false);

  // const handleOpenModal = () => {
  //   setIsModalOpen(true);
  // };

  // const handleCloseModal = () => {
  //   setIsModalOpen(false);
  // };

  function handleModal(){
    setIsModalOpen(!isModalOpen);
  }

  return (
    <div>
      <button onClick={handleModal}>Open Modal</button>
      {isModalOpen && <SignUpModal />}
    </div>
  );
}

