import React, { useState, useEffect } from 'react';
import { Row, Col } from 'react-bootstrap';
import axios from 'axios';
import Card from '../../components/Card/MainCard';
import { Link } from 'react-router-dom';

const JobIndexPage = () => {
  const [jobs, setJobs] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this job?')) return;

    try {
      const endpoint = `/job/${id}`;
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
        setError(data.error || 'Failed to delete job');
      } else {
        // Remove the deleted job from state by filtering it out
        setJobs((prevJobs) => prevJobs.filter((job) => job.id !== id));
      }
    } catch (err) {
      console.error('Error deleting job:', err);
      setError('An unexpected error occurred');
    }
  };

  useEffect(() => {
    axios
      .get(`${import.meta.env.VITE_API_URL}/jobs`)
      .then((response) => {
        console.log('Response data:', response);

        let fetchedJobs = [];

        // Check multiple possible structures of the API response
        if (Array.isArray(response.data.jobs)) {
          fetchedJobs = response.data.jobs;
        } else if (Array.isArray(response.data.data)) {
          fetchedJobs = response.data.data;
        } else if (Array.isArray(response.data)) {
          fetchedJobs = response.data;
        } else {
          console.warn('Unexpected response structure:', response.data);
        }

        setJobs(fetchedJobs);
        setError('');
      })
      .catch((err) => {
        setError(err.response?.data?.message || 'Error fetching jobs');
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  return (
    <>
      <Row className="mb-3">
        <Col>
          <Link to="/job/create">
            <button className="btn btn-primary float-end">Create</button>
          </Link>
        </Col>
      </Row>

      <Row>
        <Col>
          <Card title="Jobs List" isOption>
            {loading ? (
              <p>Loading...</p>
            ) : error ? (
              <p className="text-danger">{error}</p>
            ) : (
              <table className="table">
                <thead>
                  <tr>
                    <th>Job Title</th>
                    <th>Budget</th>
                    <th>Bid Close</th>
                    <th>Organization</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {jobs.map((job) => (
                    <tr key={job.id}>
                      <td>{job.job_title}</td>
                      <td>{job.budget}</td>
                      <td>{job.bid_close}</td>
                      <td>{job.organization ? job.organization.organization_name : 'N/A'}</td>
                      <td>
                        <Link className="dropdown-item" to={`/job/edit/${job.id}`} state={{ job: job, jobId: job.id }}>
                          <i className="fas fa-edit me-2"></i> Edit
                        </Link>

                        <button type="button" className="dropdown-item" onClick={() => handleDelete(job.id)}>
                          <i className="fas fa-trash me-2"></i> Delete
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </Card>
        </Col>
      </Row>
    </>
  );
};

export default JobIndexPage;
