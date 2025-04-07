import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';

const OrganizationEditForm = () => {
  const { id } = useParams();
  const [orgData, setOrgData] = useState({
    email: '',
    organization_name: '',
    website: '',
    address: '',
    description: ''
  });
  const [loading, setLoading] = useState(true);
  const [errors, setErrors] = useState([]);
  const [successMessage, setSuccessMessage] = useState('');
  const [selectedFile, setSelectedFile] = useState(null);

  // Fetch existing organization data
  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/organization/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        // Assume the API returns the organization object in response.data.data
        const org = response.data.data;
        setOrgData({
          email: org.email || '',
          organization_name: org.organization_name || '',
          website: org.website || '',
          address: org.address || '',
          description: org.description || ''
        });
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching organization data:', error);
        setLoading(false);
      });
  }, [id]);

  // Handle text input changes
  const handleChange = (e) => {
    setOrgData({ ...orgData, [e.target.name]: e.target.value });
  };

  // Handle file input changes (e.g. for a logo)
  const handleFileChange = (e) => {
    setSelectedFile(e.target.files[0]);
  };

  // Submit updated data
  const handleSubmit = async (e) => {
    e.preventDefault();
    setSuccessMessage('');
    setErrors([]);

    const formData = new FormData();

    // Append all non-empty text fields
    Object.entries(orgData).forEach(([key, value]) => {
      if (value) {
        formData.append(key, value);
      }
    });

    // Append the file if selected (for example, an organization logo)
    if (selectedFile) {
      formData.append('logo', selectedFile);
    }

    // Append _method for method spoofing
    formData.append('_method', 'PUT');

    // Debug: log FormData entries
    for (let [key, val] of formData.entries()) {
      console.log(key, val);
    }

    try {
      const response = await axios.post(`${import.meta.env.VITE_API_URL}/organization/${id}`, formData, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
          // Let Axios set the Content-Type automatically.
        }
      });
      setSuccessMessage('Organization updated successfully!');
      setOrgData({ ...orgData, ...response.data.data });
    } catch (error) {
      console.error('Error updating organization:', error.response?.data);
      setErrors(error.response?.data?.errors || ['An unexpected error occurred']);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="container">
      <h2>Edit Organization</h2>

      {errors.length > 0 && (
        <div className="alert alert-danger">
          <ul>
            {errors.map((err, index) => (
              <li key={index}>{err}</li>
            ))}
          </ul>
        </div>
      )}

      {successMessage && <div className="alert alert-success">{successMessage}</div>}

      <form onSubmit={handleSubmit}>
        {/* Email */}
        <div className="mb-3">
          <label>Email:</label>
          <input type="email" name="email" value={orgData.email} onChange={handleChange} className="form-control" />
        </div>
        {/* Organization Name */}
        <div className="mb-3">
          <label>Organization Name:</label>
          <input type="text" name="organization_name" value={orgData.organization_name} onChange={handleChange} className="form-control" />
        </div>
        {/* Website */}
        <div className="mb-3">
          <label>Website:</label>
          <input type="text" name="website" value={orgData.website} onChange={handleChange} className="form-control" />
        </div>
        {/* Address */}
        <div className="mb-3">
          <label>Address:</label>
          <input type="text" name="address" value={orgData.address} onChange={handleChange} className="form-control" />
        </div>
        {/* Description */}
        <div className="mb-3">
          <label>Description:</label>
          <textarea name="description" value={orgData.description} onChange={handleChange} className="form-control"></textarea>
        </div>
        {/* File Input for Logo (optional) */}
        <div className="mb-3">
          <label>Organization Logo:</label>
          <input type="file" onChange={handleFileChange} className="form-control" />
        </div>

        <button type="submit" className="btn btn-primary">
          Update Organization
        </button>
      </form>
    </div>
  );
};

export default OrganizationEditForm;
