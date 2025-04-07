import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import { useNavigate } from 'react-router-dom'; // Added for navigation

const JobEditForm = ({ job, csrfToken, orgId, onSubmit }) => {
  const { id } = useParams();
  const token = localStorage.getItem('auth_token');

  // Initialize state with job properties (using optional chaining)
  const [jobTitle, setJobTitle] = useState(job?.job_title || '');
  const [address, setAddress] = useState(job?.address || '');
  const [categoryId, setCategoryId] = useState(job?.category_id || '');
  const [degreeId, setDegreeId] = useState(job?.degree_id || '');
  const [zipcode, setZipcode] = useState(job?.zipcode || '');
  const [isRemote, setIsRemote] = useState(job?.is_remote || '');
  const [skill, setSkill] = useState(job?.skill || '');
  const [experience, setExperience] = useState(job?.experience || '');
  const [budget, setBudget] = useState(job?.budget || '');
  const [bidClose, setBidClose] = useState(job?.bid_close || '');
  const [deadline, setDeadline] = useState(job?.deadline || '');
  const [description, setDescription] = useState(job?.description || '');
  const [categories, setCategories] = useState([]);
  const [degrees, setDegrees] = useState([]);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState('');
  const navigate = useNavigate(); // Navigation hook
  const [errors, setErrors] = useState([]);
  // const token = localStorage.getItem('auth_token');

  // Fetch job details based on the id from the URL
  useEffect(() => {
    setLoading(true);
    axios
      .get(`${import.meta.env.VITE_API_URL}/job/${id}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      .then((response) => {
        const fetchedJob = response.data.job;
        setJobTitle(fetchedJob.job_title || '');
        setAddress(fetchedJob.address || '');
        setCategoryId(fetchedJob.category_id || '');
        setDegreeId(fetchedJob.degree_id || '');
        setZipcode(fetchedJob.zipcode || '');
        setIsRemote(fetchedJob.is_remote || '');
        setSkill(fetchedJob.skill || '');
        setExperience(fetchedJob.experience || '');
        setBudget(fetchedJob.budget || '');
        setBidClose(fetchedJob.bid_close || '');
        setDeadline(fetchedJob.deadline || '');
        setDescription(fetchedJob.description || '');
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching job data:', error);
        setLoading(false);
      });
  }, [id, token]);

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
      })
      .catch((error) => {
        console.error('Error fetching data:', error);
      });
  }, [csrfToken, token]);

  const handleSubmit = async (e) => {
    e.preventDefault();

    const updatedData = {
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
      ...(orgId ? { creator: orgId } : {})
    };

    try {
      const response = await axios.put(`${import.meta.env.VITE_API_URL}/job/${id}`, updatedData, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });

      if (response.status === (200 || 201) && response.data.success) {
        setSuccess(response.data.message || 'Job updated successfully');
        navigate('/job/index');
      } else {
        setErrors(response.data.errors || ['Submission failed']);
      }
    } catch (error) {
      console.error('Error updating job:', error.response?.data);
      setErrors(error.response?.data?.errors || ['An unexpected error occurred']);
    }
  };

  if (loading) return <p>Loading...</p>;

  return (
    <div className="container-xxl flex-grow-1 container-p-y">
      {errors.length > 0 && (
        <div className="alert alert-danger">
          <ul>
            {errors.map((err, idx) => (
              <li key={idx}>{err}</li>
            ))}
          </ul>
        </div>
      )}
      {/* Success message */}
      {success && <div className="alert alert-success">{success}</div>}

      <div className="row">
        <div className="col-xxl">
          <div className="card mb-4">
            <div className="card-header d-flex align-items-center justify-content-between">
              <h5 className="mb-0">Edit Job</h5>
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
                      id="jobTitle"
                      name="job_title"
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
                {/* Job Category and Degree */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="category">
                    Job Category
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
                {/* Zip Code and Is Remote */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="zipcode">
                    Zip Code
                  </label>
                  <div className="col-sm-4">
                    <input
                      type="number"
                      className="form-control"
                      id="zipcode"
                      name="zipcode"
                      value={zipcode}
                      onChange={(e) => setZipcode(e.target.value)}
                    />
                  </div>
                  <label className="col-sm-2" htmlFor="isRemote">
                    Is Remote
                  </label>
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
                {/* Required Skill */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="skill">
                    Required Skill
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      className="form-control"
                      id="skill"
                      name="skill"
                      value={skill}
                      onChange={(e) => setSkill(e.target.value)}
                    />
                  </div>
                </div>
                {/* Experience Years */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="experience">
                    Experience Years
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      className="form-control"
                      id="experience"
                      name="experience"
                      value={experience}
                      onChange={(e) => setExperience(e.target.value)}
                    />
                  </div>
                </div>
                {/* Budget */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="budget">
                    Budget
                  </label>
                  <div className="col-sm-10">
                    <input
                      type="text"
                      className="form-control"
                      id="budget"
                      name="budget"
                      value={budget}
                      onChange={(e) => setBudget(e.target.value)}
                    />
                  </div>
                </div>
                {/* Bid Closing and Deadline */}
                <div className="row mb-3">
                  <label className="col-sm-2 col-form-label" htmlFor="bidClose">
                    Bid Closing
                  </label>
                  <div className="col-sm-4">
                    <input
                      type="date"
                      className="form-control"
                      id="bidClose"
                      name="bid_close"
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
                      className="form-control"
                      id="deadline"
                      name="deadline"
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
                      className="form-control"
                      id="description"
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
                      Update
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

export default JobEditForm;
