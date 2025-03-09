import React, { useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../../../HTML, CSS, JS/CSS/Recruiters/recSignUp.css';

const RecSignUp = ({ onToggleForm }) => {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        confirmPassword: '',
        companyName: '',
        jobTitle: '',
        companySize: '',
        industry: '',
        terms: false,
    });

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value,
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        // Add form validation and submission logic here
    };

    return (
        <div className="container">
            <div className="signup-container">
                <h2 className="form-title">Recruiter Sign Up</h2>
                <form id="recruiterSignupForm" onSubmit={handleSubmit}>
                    <div className="mb-3">
                        <label htmlFor="email" className="form-label">Email address</label>
                        <input type="email" className="form-control" id="email" name="email" value={formData.email} onChange={handleChange} />
                        <div className="form-text">We'll never share your email with anyone else.</div>
                    </div>
                    <div className="mb-3">
                        <label htmlFor="password" className="form-label">Password</label>
                        <input type="password" className="form-control" id="password" name="password" value={formData.password} onChange={handleChange} />
                        <div className="form-text">Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</div>
                    </div>
                    <div className="mb-3">
                        <label htmlFor="confirmPassword" className="form-label">Confirm Password</label>
                        <input type="password" className="form-control" id="confirmPassword" name="confirmPassword" value={formData.confirmPassword} onChange={handleChange} />
                    </div>
                    <div className="mb-3">
                        <label htmlFor="companyName" className="form-label">Company Name</label>
                        <input type="text" className="form-control" id="companyName" name="companyName" value={formData.companyName} onChange={handleChange} />
                    </div>
                    <div className="mb-3">
                        <label htmlFor="jobTitle" className="form-label">Your Job Title</label>
                        <input type="text" className="form-control" id="jobTitle" name="jobTitle" value={formData.jobTitle} onChange={handleChange} />
                    </div>
                    <div className="mb-3">
                        <label htmlFor="companySize" className="form-label">Company Size</label>
                        <select className="form-select" id="companySize" name="companySize" value={formData.companySize} onChange={handleChange}>
                            <option value="1-10">1-10 employees</option>
                            <option value="11-50">11-50 employees</option>
                            <option value="51-200">51-200 employees</option>
                            <option value="201-500">201-500 employees</option>
                            <option value="501-1000">501-1000 employees</option>
                            <option value="1000+">1000+ employees</option>
                        </select>
                    </div>
                    <div className="mb-3">
                        <label htmlFor="industry" className="form-label">Industry</label>
                        <input type="text" className="form-control" id="industry" name="industry" value={formData.industry} onChange={handleChange} />
                    </div>
                    <div className="mb-3 form-check">
                        <input type="checkbox" className="form-check-input" id="terms" name="terms" checked={formData.terms} onChange={handleChange} />
                        <label className="form-check-label" htmlFor="terms">I agree to the Terms and Conditions</label>
                    </div>
                    <button type="submit" className="btn btn-primary w-100">Create Recruiter Account</button>
                    <div className="mt-3 text-center">
                        <p>Already have an account? <a href="#" onClick={(e) => {
                            e.preventDefault();
                            onToggleForm();
                        }}>Login as Recruiter</a></p>
                        <p>Looking for a job? <a href="../signup.html">Sign up as a job seeker</a></p>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default RecSignUp;