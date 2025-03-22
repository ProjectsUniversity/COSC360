import { useState } from "react";
import '../styles/index.css';
import 'bootstrap/dist/css/bootstrap.min.css';

export default function SignUp({onClose, onLoginClick}) {
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
            <div className="container" style={{width: "100vw", height: "100vh"}}>
                <div className="row justify-content-center align-items-center min-vh-100">
                    <div className="col-md-6 col-lg-5">
                        <div className="card shadow">
                            <div className="card-body">
                                <h2 className="text-center mb-4">Sign Up</h2>
                                {onClose && (
                                    <button 
                                        onClick={onClose}
                                        className="btn-close position-absolute top-0 end-0 m-3"
                                        aria-label="Close"
                                    />
                                )}
                                <form id="signupForm" onSubmit={handleSubmit}>
                                    <div className="mb-3">
                                        <label htmlFor="username" className="form-label">Username</label>
                                        <input type="text" className="form-control" id="username" name="username" required onChange={handleFormDataChange}/>
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="email" className="form-label">Email address</label>
                                        <input type="email" className="form-control" id="email" name="email" required onChange={handleFormDataChange}/>
                                        <div className="form-text">We'll never share your email with anyone else.</div>
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="password" className="form-label">Password</label>
                                        <input type="password" className="form-control" id="password" name="password" required onChange={handleFormDataChange}/>
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="confirmPassword" className="form-label">Confirm Password</label>
                                        <input type="password" className="form-control" id="confirmPassword" name="confirmPassword" required onChange={handleFormDataChange}/>
                                    </div>
                                    <div className="mb-3 form-check">
                                        <input type="checkbox" className="form-check-input" id="terms" name="terms" required onChange={handleFormDataChange}/>
                                        <label className="form-check-label" htmlFor="terms">I agree to the Terms and Conditions</label>
                                    </div>
                                    <button type="submit" className="btn btn-primary w-100">Sign Up</button>
                                    <div className="mt-3 text-center">
                                        <p>Already have an account? <a href="#" onClick={(e) => {
                                            e.preventDefault();
                                            onLoginClick();
                                        }} className="text-decoration-none">Login</a></p>
                                        <p>Are you a recruiter? <a href="#" onClick={() => window.location.href = '/recruiter/signup'} className="text-decoration-none">Create a recruiter account</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}