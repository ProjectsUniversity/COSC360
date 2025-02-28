import { useState } from "react";
import 'bootstrap/dist/css/bootstrap.min.css';
import '../styles/Login.css'; // You may need to create this file

export default function Login({ onSwitchToSignUp }) {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        rememberMe: false
    });

    function handleFormDataChange(event) {
        const input = event.currentTarget;
        if(input.type === "checkbox"){
            setFormData({
                ...formData,
                [input.name]: input.checked
            });
        } else {
            setFormData({
                ...formData,
                [input.name]: input.value
            });
        }
    }

    function handleSubmit(event) {
        event.preventDefault();
        console.log(formData);
    }

    return(
        <>
            <div className="container">
                <div className="login-container">
                    <h2 className="form-title">Login</h2>
                    <h2 className="form-title">Sign Up</h2>
                <button 
                    onClick={onClose || (() => {})}
                    className="close-button"
                    aria-label="Close"
                >X</button>
                    <form id="loginForm" onSubmit={handleSubmit}>
                        <div className="form-group mb-3">
                            <label htmlFor="email" className="form-label">Email address or Username</label>
                            <input 
                                type="text" 
                                className="form-input" 
                                id="email" 
                                name="email" 
                                value={formData.email}
                                onChange={handleFormDataChange}
                                required 
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="password" className="form-label">Password</label>
                            <input 
                                type="password" 
                                className="form-input" 
                                id="password" 
                                name="password" 
                                value={formData.password}
                                onChange={handleFormDataChange}
                                required 
                            />
                        </div>
                        <div className="form-check mb-4">
                            <input 
                                type="checkbox" 
                                className="form-checkbox" 
                                id="rememberMe" 
                                name="rememberMe"
                                checked={formData.rememberMe}
                                onChange={handleFormDataChange}
                            />
                            <label className="form-label" htmlFor="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" className="login-button">Login</button>
                        <div className="signup-text">
                            <p>Don't have an account? <button 
                                onClick={onSwitchToSignUp} 
                                className="btn btn-link p-0 signup-link"
                                style={{ textDecoration: 'none' }}
                            >Sign Up</button></p>
                            <p><a href="#" className="forgot-password">Forgot Password?</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </>
    )
}