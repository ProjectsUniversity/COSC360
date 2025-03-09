import React, { useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
  faChartSimple, faMessage, faBuilding, 
  faGear, faUser, faDoorOpen 
} from '@fortawesome/free-solid-svg-icons';

const RecruiterSidebar = () => {
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
          <a href="#" className="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle justify-content-center" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://avatars.githubusercontent.com/u/106793433?v=4" alt="" width="32" height="32" className="rounded-circle me-2" />
            <strong>Shlok Shah</strong>
          </a>
          <ul className="dropdown-menu text-small shadow">
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
