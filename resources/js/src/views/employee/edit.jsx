import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';

const EmployeeEdit = () => {
  const { id } = useParams();
  const [employeeData, setEmployeeData] = useState({
    // Combine all fields from both "user" and "employee"
    name: '',
    email: '',
    gender: '',
    phone_number: '',
    address: '',
    birth_date: '',
    zipcode: '',
    latest_degree: '',
    latest_university: '',
    current_organization: '',
    current_department: '',
    current_position: '',
    password: '',
    password_confirmation: ''
  });

  const [loading, setLoading] = useState(true);
  const [errors, setErrors] = useState([]);
  const [successMessage, setSuccessMessage] = useState('');
  const [selectedFile, setSelectedFile] = useState(null);
  
  // Fetch existing employee data
  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/employee/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      })
      .then((response) => {
        // "data" is the employee object, which includes "employee" fields + "user" relationship
        // e.g., response.data.data => { id, phone_number, user: { name, email }, etc. }
        const emp = response.data.data; // The "Employee" model
        const usr = emp.user; // The related "User"

        // Merge "user" fields + "employee" fields into one object
        setEmployeeData({
          name: usr.name || '',
          email: usr.email || '',
          gender: emp.gender || '',
          phone_number: emp.phone_number || '',
          address: emp.address || '',
          birth_date: emp.birth_date || '',
          zipcode: emp.zipcode || '',
          latest_degree: emp.latest_degree || '',
          latest_university: emp.latest_university || '',
          current_organization: emp.current_organization || '',
          current_department: emp.current_department || '',
          current_position: emp.current_position || '',
          password: '',
          password_confirmation: ''
        });

        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching employee data:', error);
        setLoading(false);
      });
  }, [id]);

  // Handle text input changes
  const handleChange = (e) => {
    setEmployeeData({ ...employeeData, [e.target.name]: e.target.value });
  };

  // Handle file change
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
    Object.entries(employeeData).forEach(([key, value]) => {
      if (value) {
        formData.append(key, value);
      }
    });

    // Append file if selected
    if (selectedFile) {
      formData.append('avatar', selectedFile);
    }
    console.log('EmployeeData just before submitting:', employeeData);

    // ✅ Debug: Log the FormData key/value pairs
    for (let [key, val] of formData.entries()) {
      console.log(key, val);
    }

    // Append _method for spoofing
    formData.append('_method', 'PUT');

    try {
      const response = await axios.post(`${import.meta.env.VITE_API_URL}/employee/update/${id}`, formData, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
          // Let Axios set the Content-Type header automatically.
        }
      });
      setSuccessMessage('Employee updated successfully!');
      setEmployeeData({ ...employeeData, ...response.data.data });
    } catch (error) {
      console.error('Error updating employee:', error.response?.data);
      setErrors(error.response?.data?.errors || ['An unexpected error occurred']);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="container">
      <h2>Edit Employee</h2>

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
        {/* Name */}
        <div className="mb-3">
          <label>Name:</label>
          <input type="text" name="name" value={employeeData.name} onChange={handleChange} className="form-control" />
        </div>

        {/* Email */}
        <div className="mb-3">
          <label>Email:</label>
          <input type="email" name="email" value={employeeData.email} onChange={handleChange} className="form-control" />
        </div>

        {/* Gender */}
        <div className="mb-3">
          <label>Gender:</label>
          <select name="gender" value={employeeData.gender} onChange={handleChange} className="form-control">
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>

        {/* Phone Number */}
        <div className="mb-3">
          <label>Phone Number:</label>
          <input type="text" name="phone_number" value={employeeData.phone_number} onChange={handleChange} className="form-control" />
        </div>

        {/* Address */}
        <div className="mb-3">
          <label>Address:</label>
          <input type="text" name="address" value={employeeData.address} onChange={handleChange} className="form-control" />
        </div>

        {/* Birth Date */}
        <div className="mb-3">
          <label>Birth Date:</label>
          <input type="date" name="birth_date" value={employeeData.birth_date} onChange={handleChange} className="form-control" />
        </div>

        {/* Zipcode */}
        <div className="mb-3">
          <label>Zipcode:</label>
          <input type="text" name="zipcode" value={employeeData.zipcode} onChange={handleChange} className="form-control" />
        </div>

        {/* Latest Degree */}
        <div className="mb-3">
          <label>Latest Degree:</label>
          <input type="text" name="latest_degree" value={employeeData.latest_degree} onChange={handleChange} className="form-control" />
        </div>

        {/* Latest University */}
        <div className="mb-3">
          <label>Latest University:</label>
          <input
            type="text"
            name="latest_university"
            value={employeeData.latest_university}
            onChange={handleChange}
            className="form-control"
          />
        </div>

        {/* Current Organization */}
        <div className="mb-3">
          <label>Current Organization:</label>
          <input
            type="text"
            name="current_organization"
            value={employeeData.current_organization}
            onChange={handleChange}
            className="form-control"
          />
        </div>

        {/* Current Department */}
        <div className="mb-3">
          <label>Current Department:</label>
          <input
            type="text"
            name="current_department"
            value={employeeData.current_department}
            onChange={handleChange}
            className="form-control"
          />
        </div>

        {/* Current Position */}
        <div className="mb-3">
          <label>Current Position:</label>
          <input
            type="text"
            name="current_position"
            value={employeeData.current_position}
            onChange={handleChange}
            className="form-control"
          />
        </div>

        {/* Password (Optional) */}
        <div className="mb-3">
          <label>New Password:</label>
          <input
            type="password"
            name="password"
            onChange={handleChange}
            className="form-control"
            minLength="8" // Browser-level validation for at least 8 characters
          />
        </div>

        {/* Confirm Password */}
        <div className="mb-3">
          <label>Confirm Password:</label>
          <input type="password" name="password_confirmation" onChange={handleChange} className="form-control" minLength="8" />
        </div>

        {/* Avatar Upload */}
        <div className="mb-3">
          <label>Profile Picture:</label>
          <input type="file" onChange={handleFileChange} className="form-control" />
        </div>

        <button type="submit" className="btn btn-primary">
          Update Employee
        </button>
      </form>
    </div>
  );
};

export default EmployeeEdit;
