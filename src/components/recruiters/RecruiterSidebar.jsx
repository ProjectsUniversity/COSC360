import React, { useState, useEffect } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
  faChartSimple, faMessage, faBuilding, 
  faGear, faUser, faDoorOpen 
} from '@fortawesome/free-solid-svg-icons';

const RecruiterSidebar = () => {
  const [dropdownOpen, setDropdownOpen] = useState(false);
  
  // Handle click outside to close dropdown
  useEffect(() => {
    const handleClickOutside = (event) => {
      const dropdown = document.getElementById('profile-dropdown');
      const toggleButton = document.getElementById('profile-dropdown-toggle');
      
      if (dropdown && !dropdown.contains(event.target) && 
          toggleButton && !toggleButton.contains(event.target)) {
        setDropdownOpen(false);
      }
    };
    
    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);
  
  const toggleDropdown = () => {
    setDropdownOpen(!dropdownOpen);
  };

  return (
    <aside className="sidebar">
      <div className="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary">
        <div className="sidebar-brand mb-3">
          <a href="/" className="link-body-emphasis text-decoration-none">
            <span className="fs-4"><h3>Job Swipe</h3></span>
          </a>
        </div>
        <hr />
        <ul className="nav nav-pills flex-column mb-auto">
          <li className="nav-item">
            <a href="#" className="nav-link active" aria-current="page">
              <FontAwesomeIcon icon={faChartSimple} className="me-2" />
              Dashboard
            </a>
          </li>
          <li>
            <a href="#" className="nav-link link-body-emphasis">
              <FontAwesomeIcon icon={faMessage} className="me-2" />
              Messages
            </a>
          </li>
          <li>
            <a href="#" className="nav-link link-body-emphasis">
              <FontAwesomeIcon icon={faBuilding} className="me-2" />
              Company Profile
            </a>
          </li>
        </ul>
        <hr />
        <div className="dropdown">
          <a 
            href="#" 
            id="profile-dropdown-toggle"
            className="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle justify-content-center" 
            style={{ maxHeight: '32px' }}
            onClick={toggleDropdown}
            aria-expanded={dropdownOpen}
          >
            <img src="https://avatars.githubusercontent.com/u/106793433?v=4" alt="" width="32px" height="32px" className="rounded-circle me-2" />
            <strong>Shlok Shah</strong>
          </a>
          <ul 
            id="profile-dropdown"
            className={`dropdown-menu text-small shadow ${dropdownOpen ? 'show' : ''}`}
            style={{ 
              display: dropdownOpen ? 'block' : 'none', 
              position: 'absolute',
              bottom: '100%',  // Position above the toggle button
              left: 0,         // Align with the left of the parent
              marginBottom: '5px', // Add some space between menu and button
              transform: 'none' // Remove any default transform
            }}
          >
            <li>
              <a className="dropdown-item" href="#">
                <FontAwesomeIcon icon={faGear} className="me-2" />
                Settings
              </a>
            </li>
            <li>
              <a className="dropdown-item" href="#">
                <FontAwesomeIcon icon={faUser} className="me-2" />
                Profile
              </a>
            </li>
            <li><hr className="dropdown-divider" /></li>
            <li>
              <a className="dropdown-item" href="../login.html">
                <FontAwesomeIcon icon={faDoorOpen} className="me-2" />
                Sign out
              </a>
            </li>
          </ul>
        </div>
      </div>
    </aside>
  );
};

export default RecruiterSidebar;
