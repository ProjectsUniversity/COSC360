import { useState } from 'react';
import Login from './components/Login';
import SignUp from './components/SignUp';

function App() {
  const [currentView, setCurrentView] = useState('signup'); 
  
  const switchToLogin = () => setCurrentView('login');
  const switchToSignUp = () => setCurrentView('signup');
  
  return (
    <div className="app-container">
      {currentView === 'login' ? (
        <Login onSwitchToSignUp={switchToSignUp} />
      ) : (
        <SignUp onSwitchToLogin={switchToLogin} />
      )}
    </div>
  );
}

export default App;