import React, { useState } from 'react';

const CategoryCreate = ({ catId, csrfToken, initialErrors = [], initialSuccess = '' }) => {
  const [catName, setCatName] = useState('');
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState(initialErrors);
  const [success, setSuccess] = useState(initialSuccess);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors([]);
    setSuccess(''); // Clear previous success messages

    try {
      // Fetch CSRF token
      await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
        credentials: 'include'
      });

      const url = `${import.meta.env.VITE_API_URL}/categories`;
      const token = localStorage.getItem('auth_token');

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({ cat_name: catName })
      });

      const data = await response.json(); // Moved here to handle both success and error cases

      if (!response.ok) {
        // Handle error response (adjust based on your API's error structure)
        if (data.errors) {
          // Flatten errors if they're an object (e.g., Laravel validation errors)
          const errorArray = Object.values(data.errors).flat();
          setErrors(errorArray);
        } else if (data.message) {
          setErrors([data.message]);
        } else {
          setErrors(['An error occurred']);
        }
      } else {
        // On success
        setCatName('');
        setSuccess('Category created successfully!'); // Set success message
      }
    } catch (error) {
      console.error('Submission error:', error);
      setErrors(['Failed to connect to the server.']);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      {/* Consolidated error display to one location */}
      {errors.length > 0 && (
        <div className="alert alert-danger">
          <ul>
            {errors.map((err, idx) => (
              <li key={idx}>{err}</li>
            ))}
          </ul>
        </div>
      )}

      {success && <div className="alert alert-success">{success}</div>}

      <div className="row">
        <div className="col-8">
          <div className="card mb-4">
            <div className="card-header d-flex align-items-center justify-content-between">
              <h5 className="mb-0">Create Category</h5>
              <small className="text-muted float-end">Fill the name field</small>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                <div className="row mb-3">
                  <label htmlFor="basic-default-name" className="col-sm-2 col-form-label">
                    Category Name
                  </label>
                  <div className="col-sm-8">
                    <input
                      type="text"
                      className="form-control"
                      name="cat_name"
                      id="basic-default-name"
                      value={catName}
                      onChange={(e) => setCatName(e.target.value)}
                      required
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

export default CategoryCreate;
