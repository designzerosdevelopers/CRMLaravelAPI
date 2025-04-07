import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const CategoryIndex = ({ cats: initialCats, successMessage }) => {
  const [cats, setCats] = useState(initialCats || []);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false); // Define loading state

  useEffect(() => {
    setLoading(true);
    axios
      .get(`${import.meta.env.VITE_API_URL}/categories`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        const fetchedCategories = Array.isArray(response.data.data)
          ? response.data.data
          : Array.isArray(response.data)
            ? response.data
            : [];
        setCats(fetchedCategories);
        setError('');
      })
      .catch((err) => {
        setError(err.response?.data?.message || 'Error fetching categories');
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this category?')) return;

    try {
      const endpoint = `/categories/${id}`;
      const url = `${import.meta.env.VITE_API_URL}${endpoint}`;

      const response = await fetch(url, {
        method: 'DELETE',
        credentials: 'include', // Include cookies for authentication
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          'Content-Type': 'application/json'
        }
      });

      if (!response.ok) {
        const data = await response.json();
        setError(data.error || 'Failed to delete category');
      } else {
        // Remove the deleted category from state
        setCats(cats.filter((c) => c.id !== id));
      }
    } catch (err) {
      console.error('Error deleting category:', err);
      setError('An unexpected error occurred');
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <Link to="/categories/create">
          <button type="button" className="btn rounded-pill btn-primary">
            Create
          </button>
        </Link>
      </div>

      <div className="card">
        {successMessage && <div className="alert alert-success">{successMessage}</div>}
        {error && <div className="alert alert-danger">{error}</div>}
        <h5 className="card-header">Categories List</h5>
        <div className="table-responsive text-nowrap">
          <table className="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody className="table-border-bottom-0">
              {cats.length > 0 ? (
                cats.map((cat) => (
                  <tr key={cat.id}>
                    <td>{cat.cat_name}</td>
                    <td>
                      <div className="dropdown">
                        <button type="button" className="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i className="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div className="dropdown-menu">
                          <Link className="dropdown-item" to={`/categories/edit/${cat.id}`}>
                            <i className="fas fa-edit me-2"></i> Edit
                          </Link>
                          <button type="button" className="dropdown-item" onClick={() => handleDelete(cat.id)}>
                            <i className="fas fa-trash me-2"></i> Delete
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="2" className="text-center">
                    No categories found.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default CategoryIndex;
