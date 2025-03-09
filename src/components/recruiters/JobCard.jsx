import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEye, faUser, faCalendar } from '@fortawesome/free-solid-svg-icons';

const JobCard = ({ job, onEdit, onClose }) => {
  const getStatusBadgeClass = (status) => {
    switch (status) {
      case 'Active':
        return 'bg-success';
      case 'On Hold':
        return 'bg-warning text-dark';
      case 'Inactive':
        return 'bg-danger';
      default:
        return 'bg-secondary';
    }
  };

  const formatPostedTime = (days) => {
    if (days === 0) {
      return 'Posted today';
    } else if (days === 1) {
      return 'Posted yesterday';
    } else {
      return `Posted ${days} days ago`;
    }
  };

  return (
    <div className="card">
      <div className="card-body">
        <div className="d-flex justify-content-between align-items-start">
          <div>
            <h5 className="card-title">{job.title}</h5>
            <h6 className="card-subtitle mb-2 text-body-secondary">{job.company}</h6>
          </div>
          <span className={`badge ${getStatusBadgeClass(job.status)}`}>{job.status}</span>
        </div>
        <p className="card-text">{job.description}</p>
        <div>
          <span className="badge bg-primary">{job.type}</span>
          <span className="badge bg-secondary">{job.location}</span>
          <span className="badge bg-info">{job.category}</span>
        </div>
        <hr />
        <div className="d-flex justify-content-between">
          <div>
            <span className="job-stat">
              <FontAwesomeIcon icon={faEye} className="me-1" /> {job.views} Views
            </span>
            <span className="job-stat">
              <FontAwesomeIcon icon={faUser} className="me-1" /> {job.applicants} {job.applicants === 1 ? 'Applicant' : 'Applicants'}
            </span>
            <span className="job-stat">
              <FontAwesomeIcon icon={faCalendar} className="me-1" /> {formatPostedTime(job.postedDays)}
            </span>
          </div>
          <div>
            <button onClick={onEdit} className="btn btn-sm btn-outline-primary me-2">Edit</button>
            <button onClick={onClose} className="btn btn-sm btn-outline-danger">Close</button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default JobCard;
