import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom'; // Added for navigation
import { UserContext } from '../../contexts/UserContext';
import { useContext } from 'react';

const JobCreateForm = ({ csrfToken, orgId }) => {
  const { user, role, updateUser, updateRole } = useContext(UserContext);

  const [jobTitle, setJobTitle] = useState('Learning Menagment System (LMS)');
  const [address, setAddress] = useState('TRC Enterprice,Avanue Road, 3rd Block, Lahore');
  const [categoryId, setCategoryId] = useState('');
  const [categories, setCategories] = useState([]);
  const [degreeId, setDegreeId] = useState('');
  const [degrees, setDegrees] = useState([]);
  const [zipcode, setZipcode] = useState('98766');
  const [isRemote, setIsRemote] = useState('');
  const [skill, setSkill] = useState('HTML, CSS, JS, Vue, Laravel, Php');
  const [experience, setExperience] = useState('5');
  const [budget, setBudget] = useState('8743');
  const [bidClose, setBidClose] = useState('2023-03-09');
  const [deadline, setDeadline] = useState('2023-03-09');
  const [description, setDescription] = useState('The Learning System Application is For a University.');
  const [loading, setLoading] = useState(true);
  const [errors, setErrors] = useState([]); // Local error state
  const [success, setSuccess] = useState('');
  const navigate = useNavigate(); // Navigation hook
  const token = localStorage.getItem('auth_token');

  // Fetch CSRF cookie on mount
  useEffect(() => {
    const getCsrfCookie = async () => {
      await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
        credentials: 'include'
      });
    };
    getCsrfCookie();
  }, []);

  // Fetch categories and degrees
  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/data`, {
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Authorization: `Bearer ${token}`
        }
      })
      .then((response) => {
        setCategories(response.data.categories || []);
        setDegrees(response.data.degrees || []);
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching data:', error);
        setLoading(false);
      });
  }, [csrfToken]); // Added csrfToken dependency

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors([]);
    setSuccess('');

    console.log('user is', user);

    const formData = {
      job_title: jobTitle,
      address,
      category_id: categoryId,
      degree_id: degreeId,
      zipcode,
      is_remote: isRemote,
      skill,
      experience,
      budget,
      bid_close: bidClose,
      deadline,
      description,
      user: user,
      user_role: role,
      ...(orgId && { creator: orgId })
    };

    try {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/job`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Authorization: `Bearer ${token}`
        },
        credentials: 'include',
        body: JSON.stringify(formData)
      });

      const data = await response.json();

      if (!response.ok) {
        setErrors(data.errors || ['Submission failed']);
      } else {
        setSuccess('Job created successfully');
        // Reset form fields
        setJobTitle('');
        setAddress('');
        setCategoryId('');
        setDegreeId('');
        setZipcode('');
        setIsRemote('');
        setSkill('');
        setExperience('');
        setBudget('');
        setBidClose('');
        setDeadline('');
        setDescription('');
        navigate('/job/index');
      }
    } catch (error) {
      setErrors(['Network error. Please try again.']);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <p>Loading...</p>;

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      {/* Error messages */}
      {errors.length > 0 && (
        <div className="alert alert-danger">
          {errors.map((error, index) => (
            <p key={index}>{error}</p>
          ))}
        </div>
      )}

      {/* Success message */}
      {success && <div className="alert alert-success">{success}</div>}

      {/* Rest of your form JSX remains the same */}
      <div className="row">
        <div className="col-xxl">
          <div className="card mb-4">
            <div className="card-header d-flex align-items-center justify-content-between">
              <h5 className="mb-0">Create Form</h5>
              <small className="text-muted float-end">Fill all the fields</small>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                {/* Job Title */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="jobTitle">
                    Job Title
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      className="form-control"
                      name="job_title"
                      id="jobTitle"
                      value={jobTitle}
                      onChange={(e) => setJobTitle(e.target.value)}
                    />
                  </div>
                </div>
                {/* Address */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="address">
                    Address
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      name="address"
                      className="form-control"
                      id="address"
                      value={address}
                      onChange={(e) => setAddress(e.target.value)}
                    />
                  </div>
                </div>
                {/* Category and Degree */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="category">
                    Job category
                  </label>
                  <div className="col-sm-4">
                    <select
                      name="category_id"
                      id="category"
                      className="form-select"
                      value={categoryId}
                      onChange={(e) => setCategoryId(e.target.value)}
                    >
                      <option value="" disabled>
                        -- Select an option --
                      </option>
                      {categories.map((cat) => (
                        <option key={cat.id} value={cat.id}>
                          {cat.cat_name}
                        </option>
                      ))}
                    </select>
                  </div>
                  <label className="col-sm-2 col-form-label" htmlFor="degree">
                    Degree
                  </label>
                  <div className="col-sm-4">
                    <select
                      name="degree_id"
                      id="degree"
                      className="form-select"
                      value={degreeId}
                      onChange={(e) => setDegreeId(e.target.value)}
                    >
                      <option value="" disabled>
                        -- Select an option --
                      </option>
                      {degrees.map((degree) => (
                        <option key={degree.id} value={degree.id}>
                          {degree.degree_title}
                        </option>
                      ))}
                    </select>
                  </div>
                </div>
                {/* Zip Code & Is Remote */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="zipcode">
                    Zip Code
                  </label>
                  <div className="col-sm-4">
                    <input
                      type="number"
                      name="zipcode"
                      className="form-control"
                      id="zipcode"
                      value={zipcode}
                      onChange={(e) => setZipcode(e.target.value)}
                    />
                  </div>
                  <div className="col-sm-2">
                    <label className="col-form-label" htmlFor="isRemote">
                      Is Remote
                    </label>
                  </div>
                  <div className="col-sm-4">
                    <select
                      name="is_remote"
                      className="form-select"
                      id="isRemote"
                      value={isRemote}
                      onChange={(e) => setIsRemote(e.target.value)}
                    >
                      <option value="" disabled>
                        -- Select an option --
                      </option>
                      <option value="Remote">Remote</option>
                      <option value="On-site">On-site</option>
                      <option value="Hybrid">Hybrid</option>
                    </select>
                  </div>
                </div>
                {/* Skill */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="skill">
                    Required Skill
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      className="form-control"
                      name="skill"
                      id="skill"
                      value={skill}
                      onChange={(e) => setSkill(e.target.value)}
                    />
                  </div>
                </div>
                {/* Experience and Budget */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="experience">
                    Experience Years
                  </label>
                  <div className="col-sm-4">
                    <input
                      type="number"
                      className="form-control"
                      name="experience"
                      id="experience"
                      value={experience}
                      onChange={(e) => setExperience(e.target.value)}
                    />
                  </div>
                  <label className="col-sm-2 col-form-label" htmlFor="budget">
                    Budget
                  </label>
                  <div className="col-sm-4">
                    <input
                      type="text"
                      className="form-control"
                      name="budget"
                      id="budget"
                      value={budget}
                      onChange={(e) => setBudget(e.target.value)}
                    />
                  </div>
                </div>
                {/* Bid Close and Deadline */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="bidClose">
                    Bid Closing
                  </label>
                  <div className="col-sm-4">
                    <input
                      type="date"
                      name="bid_close"
                      className="form-control"
                      id="bidClose"
                      value={bidClose}
                      onChange={(e) => setBidClose(e.target.value)}
                    />
                  </div>
                  <label className="col-sm-2 col-form-label" htmlFor="deadline">
                    Deadline
                  </label>
                  <div className="col-sm-4">
                    <input
                      type="date"
                      name="deadline"
                      className="form-control"
                      id="deadline"
                      value={deadline}
                      onChange={(e) => setDeadline(e.target.value)}
                    />
                  </div>
                </div>
                {/* Description */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="description">
                    Description
                  </label>
                  <div className="col-sm-10">
                    <textarea
                      id="description"
                      className="form-control"
                      name="description"
                      placeholder="Tell us about the project!"
                      aria-label="Tell us about the project!"
                      value={description}
                      onChange={(e) => setDescription(e.target.value)}
                    />
                  </div>
                </div>
                {orgId && <input type="hidden" name="creator" value={orgId} />}
                <div className="row justify-content-end">
                  <div className="col-sm-10">
                    <button type="submit" className="btn btn-primary">
                      Send
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

export default JobCreateForm;
