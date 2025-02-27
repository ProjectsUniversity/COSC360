import { useState } from "react";
import '../styles/index.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../styles/SignUp.css';

export default function SignUp({onClose}) {
   const [formData, setFormData] = useState({
    username: '',
    email: '',
    password: '',
    confirmPassword: '',
    terms: false,
   });
   
   function handleSubmit(event) {
    event.preventDefault();
    console.log(formData);
   }

   function handleFormDataChange(event) {
        const input = event.currentTarget;
        if(input.type === "checkbox"){
            setFormData({
                ...formData,
                terms: input.checked
            })
        }else{
            setFormData({
                ...formData,
                [input.name]: input.value
            })
        }
   }
    return (
        <>
            <div className="container">
                <div className="signup-container">
                    <h2 className="form-title">Sign Up</h2>
                    <button 
                        onClick={onClose}
                        className="close-button"
                        aria-label="Close"
                    >×</button>
                    <form id="signupForm" onSubmit={handleSubmit}>
                        <div className="form-group mb-3">
                            <label htmlFor="username" className="form-label">Username</label>
                            <input type="text" className="form-control" id="username" name="username" required onChange={handleFormDataChange}/>
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="email" className="form-label">Email address</label>
                            <input type="email" className="form-control" id="email" name="email" required onChange={handleFormDataChange}/>
                            <div className="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="password" className="form-label">Password</label>
                            <input type="password" className="form-control" id="password" name="password" required onChange={handleFormDataChange}/>
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="confirmPassword" className="form-label">Confirm Password</label>
                            <input type="password" className="form-control" id="confirmPassword" name="confirmPassword" required onChange={handleFormDataChange}/>
                        </div>
                        <div className="form-check mb-4">
                            <input type="checkbox" className="form-check-input" id="terms" name="terms" required onChange={handleFormDataChange}/>
                            <label className="form-check-label" htmlFor="terms">I agree to the Terms and Conditions</label>
                        </div>
                        <button type="submit" className="btn btn-primary signup-button">Sign Up</button>
                        <div className="login-text">
                            <p>Already have an account? <a href="login.html" className="login-link">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </>
    )
}