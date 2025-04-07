import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

const OrganizationCreateForm = () => {
  // Local state for form fields
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');

  // Define additional state variables
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState([]);
  const [success, setSuccess] = useState('');

  // If orgId is needed, define it or remove it
  const orgId = null; // Replace null with an actual value if required

  // If you need a CSRF token, either retrieve it from your backend or remove it if not needed
  const csrfToken = ''; // Replace with actual CSRF token logic if required

  // For navigation after submission
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors([]);
    setSuccess('');

    // Call CSRF endpoint first if not already done
    await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
      credentials: 'include'
    });

    const endpoint = '/organization';
    const url = `${import.meta.env.VITE_API_URL}${endpoint}`;

    // Retrieve token if needed (for token-based auth)
    const token = localStorage.getItem('auth_token');

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          // Include CSRF token if your backend requires it.
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
          // You may not need the Authorization header if using cookie-based auth with Sanctum.
          Authorization: `Bearer ${token}`
        },
        credentials: 'include',
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation: passwordConfirmation,
          ...(orgId ? { creator: orgId } : {})
        })
      });

      if (!response.ok) {
        const data = await response.json();
        setErrors(data.errors || ['An error occurred']);
      } else {
        const data = await response.json();
        setSuccess(data.message || 'Organization created successfully');
        setName('');
        setEmail('');
        setPassword('');
        setPasswordConfirmation('');
        navigate('/organization/index');
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      setErrors(['An unexpected error occurred']);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      {errors.length > 0 && (
        <div className="alert alert-danger">
          <ul>
            {errors.map((error, idx) => (
              <li key={idx}>{error}</li>
            ))}
          </ul>
        </div>
      )}

      {success && <div className="alert alert-success">{success}</div>}

      <div className="row">
        <div className="col-xxl">
          <div className="card mb-4">
            <div className="card-header d-flex align-items-center justify-content-between">
              <h5 className="mb-0">Create Form</h5>
              <small className="text-muted float-end">Fill all the fields</small>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="name">
                    Organization Name
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      className="form-control"
                      name="name"
                      id="name"
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                    />
                  </div>
                </div>
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="email">
                    Email
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      className="form-control"
                      name="email"
                      id="email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                    />
                  </div>
                </div>
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="password">
                    Password
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="password"
                      id="password"
                      className="form-control"
                      name="password"
                      placeholder="••••••••••••"
                      required
                      autoComplete="new-password"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                    />
                  </div>
                </div>
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="password_confirmation">
                    Confirm Password
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="password"
                      id="password_confirmation"
                      className="form-control"
                      name="password_confirmation"
                      placeholder="••••••••••••"
                      required
                      autoComplete="new-password"
                      value={passwordConfirmation}
                      onChange={(e) => setPasswordConfirmation(e.target.value)}
                    />
                  </div>
                </div>
                <div className="row justify-content-end">
                  <div className="col-sm-10">
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                      {loading ? 'Sending...' : 'Send'}
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OrganizationCreateForm;
