import { useState } from "react";
import 'bootstrap/dist/css/bootstrap.min.css';
import { Link
    
 } from "react-router-dom";
export default function Login(){
    return(
        <>
            <div class="container">
        <div class="login-container">
            <h2 class="form-title">Login</h2>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address or Username</label>
                    <input type="text" class="form-control" id="email" name="email" required />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required />
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" />
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="mt-3 text-center">
                    <p>Don't have an account? <a href="Signup.jsx">Sign Up</a></p>
                    <p><a href="#" class="text-muted">Forgot Password?</a></p>
                </div>
            </form>
        </div>
    </div>
        </>
    )
}