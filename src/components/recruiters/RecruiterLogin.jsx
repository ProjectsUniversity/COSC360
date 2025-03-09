import React, { useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../../../HTML, CSS, JS/CSS/Recruiters/recLogin.css';

const RecruiterLogin = ({ onToggleForm }) => {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        rememberMe: false
    });

    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value,
        });
        
        // Clear error when user starts typing in a field
        if (errors[name]) {
            setErrors({
                ...errors,
                [name]: ''
            });
        }
    };

    const validateForm = () => {
        const newErrors = {};
        
        if (!formData.email.trim()) {
            newErrors.email = 'Email is required';
        }
        
        if (!formData.password) {
            newErrors.password = 'Password is required';
        }
        
        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (validateForm()) {
            console.log('Form submitted successfully:', formData);
            // Here you would typically make an API call to authenticate the user
            // and handle the response accordingly
            alert('Login successful!');
            
            // Redirect to dashboard or other page
            // You can use React Router for navigation
            // history.push('/dashboard');
        }
    };

    return (
        <div className="container">
            <div className="login-container">
                <h2 className="form-title">Recruiter Login</h2>
                <form id="recruiterLoginForm" onSubmit={handleSubmit}>
                    <div className="mb-3">
                        <label htmlFor="email" className="form-label">Email address</label>
                        <input 
                            type="email" 
                            className={`form-control ${errors.email ? 'is-invalid' : ''}`} 
                            id="email" 
                            name="email" 
                            value={formData.email}
                            onChange={handleChange}
                        />
                        {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="password" className="form-label">Password</label>
                        <input 
                            type="password" 
                            className={`form-control ${errors.password ? 'is-invalid' : ''}`} 
                            id="password" 
                            name="password" 
                            value={formData.password}
                            onChange={handleChange}
                        />
                        {errors.password && <div className="invalid-feedback">{errors.password}</div>}
                    </div>
                    <div className="mb-3 form-check">
                        <input 
                            type="checkbox" 
                            className="form-check-input" 
                            id="rememberMe" 
                            name="rememberMe"
                            checked={formData.rememberMe}
                            onChange={handleChange}
                        />
                        <label className="form-check-label" htmlFor="rememberMe">Remember me</label>
                    </div>
                    <button type="submit" className="btn btn-primary w-100">Login</button>
                    <div className="mt-3 text-center">
                        <p>Don't have a recruiter account? <a href="#" onClick={(e) => {
                            e.preventDefault();
                            onToggleForm();
                        }}>Sign Up as Recruiter</a></p>
                        <a href="../login.html">Login as a job seeker</a>
                        <p><a href="#" className="text-muted">Forgot Password?</a></p>
                        <p><a href="../index.html">Continue as guest</a></p>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default RecruiterLogin;
