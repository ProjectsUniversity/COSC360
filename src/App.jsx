import React, { useState } from 'react'
import SignUpModal from "/Users/shlokshah/COSC360/src/components/SignUpModal.jsx"

function App() {
  const [showSignUp, setShowSignUp] = useState(false);

  const handleOpenSignUp = () => {
    setShowSignUp(true);
  };

  
  return (
    <div className='bg-red-500 text-white'>
      <button onClick={handleOpenSignUp}>Click me</button>
      {showSignUp && <SignUpModal/>}
    </div>
  )
}

export default App