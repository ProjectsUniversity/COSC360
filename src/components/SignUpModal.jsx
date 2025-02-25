export default function SignUpModal() {
    return (
        <body>
            <div class="container">
                <div class="signup-container">
                    <h2 class="form-title">Sign Up</h2>
                    <form id="signupForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required/>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required/>
                            <div class="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"/>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required/>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required/>
                            <label class="form-check-label" for="terms">I agree to the Terms and Conditions</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                        <div class="mt-3 text-center">
                            <p>Already have an account? <a href="login.html">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </body>
    )
}