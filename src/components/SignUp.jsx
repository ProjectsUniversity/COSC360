import { useState } from "react";
import '../styles/index.css'
import 'bootstrap/dist/css/bootstrap.min.css'

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
            <div className="container mx-auto px-4">
                <div className="signup-container max-w-md mx-auto my-8 p-6 bg-white relative">
                    <h2 className="form-title text-center text-2xl font-bold mb-6">Sign Up</h2>
                    <button 
                        onClick={onClose}
                        className="absolute top-3 right-3 text-gray-500 hover:text-red-600 text-xl font-bold"
                        aria-label="Close"
                    >×</button>
                    <form id="signupForm" onSubmit={handleSubmit}>
                        <div className="mb-3">
                            <label htmlFor="username" className="form-label block text-sm font-medium">Username</label>
                            <input type="text" className="form-control w-full" id="username" name="username" required onChange={handleFormDataChange}/>
                        </div>
                        <div className="mb-3">
                            <label htmlFor="email" className="form-label block text-sm font-medium">Email address</label>
                            <input type="email" className="form-control w-full" id="email" name="email" required onChange={handleFormDataChange}/>
                            <div className="form-text text-xs mt-1 text-gray-500">We'll never share your email with anyone else.</div>
                        </div>
                        <div className="mb-3">
                            <label htmlFor="password" className="form-label block text-sm font-medium">Password</label>
                            <input type="password" className="form-control w-full" id="password" name="password" required onChange={handleFormDataChange}/>
                        </div>
                        <div className="mb-3">
                            <label htmlFor="confirmPassword" className="form-label block text-sm font-medium">Confirm Password</label>
                            <input type="password" className="form-control w-full" id="confirmPassword" name="confirmPassword" required onChange={handleFormDataChange}/>
                        </div>
                        <div className="mb-4 form-check">
                            <input type="checkbox" className="form-check-input" id="terms" name="terms" required onChange={handleFormDataChange}/>
                            <label className="form-check-label ms-2 text-sm" htmlFor="terms">I agree to the Terms and Conditions</label>
                        </div>
                        <button type="submit" className="btn btn-primary w-full py-2">Sign Up</button>
                        <div className="mt-4 text-center text-sm">
                            <p>Already have an account? <a href="login.html" className="text-blue-600 hover:underline">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </>
    )
}