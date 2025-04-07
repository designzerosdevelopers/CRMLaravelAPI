import React, { useState } from 'react';
import { Card, Row, Col, Form, Button } from 'react-bootstrap';
import { NavLink, useNavigate } from 'react-router-dom';
import Breadcrumb from '../../../layouts/AdminLayout/Breadcrumb';
import axios from 'axios';

const SignUp1 = () => {
  const [accountType, setAccountType] = useState('candidate');
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    profession: '',
    cv: null,
    organization_name: '',
    website: ''
  });
  const [error, setError] = useState('');
  const [passwordError, setPasswordError] = useState('');
  const navigate = useNavigate();

  // Handle input changes and validate password match if needed
  const handleInputChange = (e) => {
    const { name, value } = e.target;
    const updatedData = { ...formData, [name]: value };

    // Validate password and confirmation match when either changes
    if (name === 'password' || name === 'password_confirmation') {
      if (updatedData.password !== updatedData.password_confirmation) {
        setPasswordError('Passwords do not match');
      } else {
        setPasswordError('');
      }
    }

    setFormData(updatedData);
  };

  // Handle file input for CV (optional)
  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      if (file.type !== 'application/pdf') {
        setError('Only PDF files are allowed');
        return;
      }
      if (file.size > 1024 * 1024) {
        setError('File size must be less than 1MB');
        return;
      }
      setError('');
      setFormData((prev) => ({ ...prev, cv: file }));
    }
  };

  // Switch between candidate and organization registration and reset type-specific fields
  const handleAccountTypeChange = (e) => {
    const type = e.target.value;
    setAccountType(type);
    setFormData((prev) => ({
      ...prev,
      profession: '',
      cv: null,
      organization_name: '',
      website: ''
    }));
  };

  // Submit the form data to the backend API
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (passwordError) return;

    const data = new FormData();
    data.append('name', formData.name);
    data.append('email', formData.email);
    data.append('password', formData.password);
    data.append('password_confirmation', formData.password_confirmation);

    if (accountType === 'candidate') {
      data.append('is_candidate', true);
      data.append('profession', formData.profession);
      if (formData.cv) {
        data.append('cv', formData.cv);
      }
    } else {
      data.append('organization_name', formData.organization_name);
      data.append('website', formData.website);
    }

    try {
      const response = await axios.post('http://127.0.0.1:8000/api/register', data, {
        headers: {
          'Content-Type': 'multipart/form-data',
          Accept: 'application/json'
        }
      });

      if (response.status === 200) {
        console.log('data', response.data.data);
        // Store the token in localStorage
        localStorage.setItem('auth_token', response.data.data.token);

        // Access the name property from the first role in the roles array
        const roleName = response.data.data.user.role?.[0]?.name;
        localStorage.setItem('role', roleName);


        // Set the default Authorization header for Axios
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;

        // Redirect to the dashboard route
        navigate('/app/dashboard/default');
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Registration failed');
    }
  };

  return (
    <>
      <Breadcrumb />
      <div className="auth-wrapper">
        <div className="auth-content">
          <div className="auth-bg">
            <span className="r" />
            <span className="r s" />
            <span className="r s" />
            <span className="r" />
          </div>
          <Card className="borderless">
            <Row className="align-items-center">
              <Col>
                <Card.Body className="text-center">
                  <div className="mb-4">
                    <i className="feather icon-user-plus auth-icon" />
                  </div>
                  <h3 className="mb-4">Sign Up</h3>
                  {error && <div className="alert alert-danger">{error}</div>}
                  <Form onSubmit={handleSubmit}>
                    {/* Account Type Selection */}
                    <Form.Group className="mb-3" controlId="accountType">
                      <Form.Check
                        inline
                        label="Candidate"
                        type="radio"
                        id="candidateRadio"
                        name="accountType"
                        value="candidate"
                        checked={accountType === 'candidate'}
                        onChange={handleAccountTypeChange}
                      />
                      <Form.Check
                        inline
                        label="Organization"
                        type="radio"
                        id="organizationRadio"
                        name="accountType"
                        value="organization"
                        checked={accountType === 'organization'}
                        onChange={handleAccountTypeChange}
                      />
                    </Form.Group>

                    {/* Common Fields */}
                    <Form.Group className="mb-3" controlId="name">
                      <Form.Control
                        type="text"
                        placeholder="Name"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        required
                      />
                    </Form.Group>

                    <Form.Group className="mb-3" controlId="email">
                      <Form.Control
                        type="email"
                        placeholder="Email Address"
                        name="email"
                        value={formData.email}
                        onChange={handleInputChange}
                        required
                      />
                    </Form.Group>

                    <Form.Group className="mb-3" controlId="password">
                      <Form.Control
                        type="password"
                        placeholder="Password"
                        name="password"
                        value={formData.password}
                        onChange={handleInputChange}
                        required
                        minLength="8"
                      />
                    </Form.Group>

                    <Form.Group className="mb-3" controlId="password_confirmation">
                      <Form.Control
                        type="password"
                        placeholder="Confirm Password"
                        name="password_confirmation"
                        value={formData.password_confirmation}
                        onChange={handleInputChange}
                        required
                        isInvalid={!!passwordError}
                      />
                      <Form.Control.Feedback type="invalid">{passwordError}</Form.Control.Feedback>
                    </Form.Group>

                    {/* Candidate Fields */}
                    {accountType === 'candidate' ? (
                      <>
                        <Form.Group className="mb-3" controlId="profession">
                          <Form.Control
                            type="text"
                            placeholder="Profession"
                            name="profession"
                            value={formData.profession}
                            onChange={handleInputChange}
                            required
                          />
                        </Form.Group>
                        <Form.Group className="mb-3" controlId="cv">
                          <Form.Control type="file" name="cv" accept="application/pdf" onChange={handleFileChange} />
                          <Form.Text className="text-muted">PDF only, max size 1MB (optional)</Form.Text>
                        </Form.Group>
                      </>
                    ) : (
                      /* Organization Fields */
                      <>
                        <Form.Group className="mb-3" controlId="organization_name">
                          <Form.Control
                            type="text"
                            placeholder="Organization Name"
                            name="organization_name"
                            value={formData.organization_name}
                            onChange={handleInputChange}
                            required
                          />
                        </Form.Group>
                        <Form.Group className="mb-3" controlId="website">
                          <Form.Control
                            type="url"
                            placeholder="Website URL"
                            name="website"
                            value={formData.website}
                            onChange={handleInputChange}
                            required
                            pattern="https?://.+"
                          />
                          <Form.Text className="text-muted">Include http:// or https://</Form.Text>
                        </Form.Group>
                      </>
                    )}

                    <Button variant="primary" type="submit" className="mb-4" disabled={!!passwordError}>
                      Sign Up
                    </Button>
                  </Form>
                  <p className="mb-2">
                    Already have an account?{' '}
                    <NavLink to="/auth/signin-1" className="f-w-400">
                      Login
                    </NavLink>
                  </p>
                </Card.Body>
              </Col>
            </Row>
          </Card>
        </div>
      </div>
    </>
  );
};

export default SignUp1;
