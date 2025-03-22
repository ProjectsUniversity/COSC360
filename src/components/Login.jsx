import { useState } from "react";
import '../styles/index.css';
import 'bootstrap/dist/css/bootstrap.min.css';

export default function Login({ onClose, onSignUpClick }) {
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    rememberMe: false
  });

  function handleSubmit(event) {
    event.preventDefault();
    console.log(formData);
    // Here you would typically make an API call to authenticate the user
    // For now, just redirect to homepage
    window.location.href = '/homepage';
  }

  function handleFormDataChange(event) {
    const input = event.currentTarget;
    if (input.type === "checkbox") {
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

  return (
    <>
      <div className="container" style={{width: "100vw", height: "100vh"}}>
        <div className="row justify-content-center align-items-center min-vh-100">
          <div className="col-md-6 col-lg-5">
            <div className="card shadow">
              <div className="card-body">
                <h2 className="text-center mb-4">Login</h2>
                {onClose && (
                  <button 
                    onClick={onClose}
                    className="btn-close position-absolute top-0 end-0 m-3"
                    aria-label="Close"
                  />
                )}
                <form id="loginForm" onSubmit={handleSubmit}>
                  <div className="mb-3">
                    <label htmlFor="email" className="form-label">Email address or Username</label>
                    <input
                      type="text"
                      className="form-control"
                      id="email"
                      name="email"
                      required
                      onChange={handleFormDataChange}
                    />
                  </div>
                  <div className="mb-3">
                    <label htmlFor="password" className="form-label">Password</label>
                    <input
                      type="password"
                      className="form-control"
                      id="password"
                      name="password"
                      required
                      onChange={handleFormDataChange}
                    />
                  </div>
                  <div className="mb-3 form-check">
                    <input
                      type="checkbox"
                      className="form-check-input"
                      id="rememberMe"
                      name="rememberMe"
                      onChange={handleFormDataChange}
                    />
                    <label className="form-check-label" htmlFor="rememberMe">Remember me</label>
                  </div>
                  <button type="submit" className="btn btn-primary w-100">Login</button>
                  <div className="mt-3 text-center">
                    <p>Don't have an account? <a href="#" onClick={(e) => {
                      e.preventDefault();
                      onSignUpClick();
                    }}>Sign Up</a></p>
                    <p><a href="#" onClick={() => window.location.href = '/recruiter/login'}>Login as a recruiter</a></p>
                    <p><a href="#" className="text-muted">Forgot Password?</a></p>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}