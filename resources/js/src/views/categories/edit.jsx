import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';

const CategoryEdit = ({ cat, csrfToken }) => {
  const { id } = useParams();
  // Use optional chaining to avoid errors if cat is undefined
  const [catName, setCatName] = useState(cat?.cat_name || '');
  const [errors, setErrors] = useState([]);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState('');

  useEffect(() => {
    setLoading(true);
    axios
      .get(`${import.meta.env.VITE_API_URL}/categories/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        // Extract the category name from the fetched data
        const categoryData = response.data.data;
        setCatName(categoryData.cat_name);
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching category data:', error);
        setLoading(false);
      });
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors([]);
    setSuccess('');

    try {
      // Fetch CSRF token cookie first if using Sanctum
      await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
        credentials: 'include'
      });

      const token = localStorage.getItem('auth_token');
      const response = await fetch(`${import.meta.env.VITE_API_URL}/categories/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({ cat_name: catName })
      });

      const data = await response.json();

      if (!response.ok) {
        if (data.errors) {
          // Flatten Laravel-style validation errors
          const errorArray = Object.values(data.errors).flat();
          setErrors(errorArray);
        } else if (data.message) {
          setErrors([data.message]);
        } else {
          setErrors(['Failed to update category']);
        }
      } else {
        setSuccess('Category updated successfully!');
        setErrors([]);
      }
    } catch (error) {
      console.error('Submission error:', error);
      setErrors(['Network error. Please check your connection.']);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      <div className="row">
        <div className="col-md-12">
          <div className="card mb-4">
            {/* Error Messages */}
            {errors.length > 0 && (
              <div className="alert alert-danger">
                <ul>
                  {errors.map((error, index) => (
                    <li key={index}>{error}</li>
                  ))}
                </ul>
              </div>
            )}

            {/* Success Message */}
            {success && <div className="alert alert-success">{success}</div>}

            <h5 className="card-header">Edit Category</h5>
            <hr className="my-0" />
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                <div className="row">
                  <div className="mb-3 col-md-6">
                    <label htmlFor="categoryName" className="form-label">
                      Name
                    </label>
                    <input
                      type="text"
                      className="form-control"
                      id="categoryName"
                      name="cat_name"
                      value={catName}
                      onChange={(e) => setCatName(e.target.value)}
                      autoFocus
                      required
                    />
                  </div>
                </div>
                <div className="mt-2">
                  <button type="submit" className="btn btn-primary me-2" disabled={loading}>
                    {loading ? 'Saving...' : 'Save changes'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CategoryEdit;
