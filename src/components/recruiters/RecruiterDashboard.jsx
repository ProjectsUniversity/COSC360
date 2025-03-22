import React, { useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../../../HTML, CSS, JS/CSS/Recruiters/dashboard.css';
import RecruiterSidebar from './RecruiterSidebar';
import JobCard from './JobCard';

const RecruiterDashboard = () => {
  // Sample job data - would typically come from an API
  const [jobs, setJobs] = useState([
    {
      id: 1,
      title: "Software Engineer",
      company: "Tech Corp",
      description: "Looking for a skilled developer with experience in JavaScript and Python. This is a full-time position with competitive salary and benefits.",
      status: "Active",
      type: "Full-time",
      location: "Remote",
      category: "Tech",
      views: 145,
      applicants: 12,
      postedDays: 5
    },
    {
      id: 2,
      title: "Product Manager",
      company: "Biz Solutions",
      description: "Seeking a highly motivated individual with leadership skills. The ideal candidate will have experience in agile methodologies and a track record of successful product launches.",
      status: "Active",
      type: "Full-time",
      location: "Hybrid",
      category: "Management",
      views: 98,
      applicants: 8,
      postedDays: 7
    },
    {
      id: 3,
      title: "Data Analyst",
      company: "Data Insights",
      description: "Strong analytical skills required. Experience in SQL preferred. Must be proficient in data visualization tools and comfortable presenting insights to stakeholders.",
      status: "On Hold",
      type: "Full-time",
      location: "In-office",
      category: "Analytics",
      views: 112,
      applicants: 9,
      postedDays: 3
    },
    {
      id: 4,
      title: "UX/UI Designer",
      company: "Tech Corp",
      description: "Looking for a creative designer with experience in user research, wireframing, and prototyping. Portfolio showcasing previous work is required.",
      status: "Active",
      type: "Part-Time",
      location: "Remote",
      category: "Design",
      views: 87,
      applicants: 5,
      postedDays: 2
    },
    {
      id: 5,
      title: "Marketing Specialist",
      company: "Biz Solutions",
      description: "Join our marketing team to develop and implement digital marketing strategies. Experience with social media management and content creation is essential.",
      status: "Inactive",
      type: "Full-time",
      location: "Hybrid",
      category: "Marketing",
      views: 64,
      applicants: 1,
      postedDays: 0
    }
  ]);

  // Dashboard stats
  const activeJobs = jobs.filter(job => job.status === "Active").length;
  const totalApplicants = jobs.reduce((sum, job) => sum + job.applicants, 0);
  const interviewsScheduled = 2; // This would typically come from an API

  const handleEditJob = (id) => {
    console.log("Edit job:", id);
    // Implementation for editing a job
  };

  const handleCloseJob = (id) => {
    console.log("Close job:", id);
    // Implementation for closing/deactivating a job
  };

  const handlePostNewJob = () => {
    console.log("Post new job");
    // Implementation for posting a new job
  };

  return (
    <div className="d-flex">
      <RecruiterSidebar />

      {/* Main Content */}
      <main className="main-content">
        {/* Main Header */}
        <header>
          <div className="dashboard-header d-flex justify-content-between align-items-center">
            <div>
              <h1 className="mb-0">Recruiter Dashboard</h1>
              <p className="text-muted">Welcome back, Shlok!</p>
            </div>
            <button 
              className="btn btn-success btn-with-plus"
              onClick={handlePostNewJob}
            >
              Post New Job
            </button>
          </div>
        </header>
        
        {/* Widgets */}
        <div className="row py-4">
          <div className="col-md-4">
            <div className="widgets-col">
              <h3>{activeJobs}</h3>
              <p>Active Jobs</p>
            </div>
          </div>
          <div className="col-md-4">
            <div className="widgets-col">
              <h3>{totalApplicants}</h3>
              <p>Total Applicants</p>
            </div>
          </div>
          <div className="col-md-4">
            <div className="widgets-col">
              <h3>{interviewsScheduled}</h3>
              <p>Interviews Scheduled</p>
            </div>
          </div>
        </div>

        {/* Job Posts */}
        <ul className="nav nav-tabs">
          <li className="nav-item">
            <a className="nav-link active" aria-current="page" href="#">My Jobs</a>
          </li>
          <li className="nav-item">
            <a className="nav-link" href="#">All Applicants</a>
          </li>
        </ul>

        <section id="mainJobs">
          <h3>My Posted Jobs</h3>
          
          {jobs.map(job => (
            <JobCard 
              key={job.id} 
              job={job} 
              onEdit={() => handleEditJob(job.id)} 
              onClose={() => handleCloseJob(job.id)}
            />
          ))}
        </section>
      </main>
    </div>
  );
};

export default RecruiterDashboard;
