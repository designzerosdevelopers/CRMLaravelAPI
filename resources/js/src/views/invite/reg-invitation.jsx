import React, { useEffect, useState } from 'react';
import { Card, Row, Col, Form, Button, Breadcrumb } from 'react-bootstrap';
import { NavLink, useParams } from 'react-router-dom';
import axios from 'axios';

const OrganizationRegister = ({ user: currentUser, csrfToken }) => {
  // Extract the token from the URL. Ensure your route is defined like "/accept-invitation/:token"
  const { token } = useParams();

  // Renamed the state variable to avoid shadowing the prop "user"
  const [invitedUser, setInvitedUser] = useState(null);
  const [name, setName] = useState('');
  const [email, setEmail] = useState(''); // This will be updated from the backend
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [terms, setTerms] = useState(false);
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Use the extracted token in the API request to fetch the invited user's data
    axios
      .get(`${import.meta.env.VITE_API_URL}/accept-invitation/${token}`)
      .then((response) => {
        // Assuming the API returns user data under response.data.data

        console.log('Invited user is:', response);
        console.log('Email is:', response.data.user.email);

        const fetchedUser = response.data.user;

        setInvitedUser(fetchedUser);
        setEmail(fetchedUser.email);
        console.log('Invited user is:', fetchedUser);
        console.log('Email is:', fetchedUser.email);
      })
      .catch((error) => {
        console.error('Error fetching user:', error);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [token]);

  const handleSubmit = async (e) => {
    e.preventDefault();

    // Must match EXACTLY your route: '/invitation-registred'
    const endpoint = 'invitation-registred';
    const url = `${import.meta.env.VITE_API_URL}/${endpoint}`;
    console.log('url is ', url);

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation: passwordConfirmation,
          terms,
          creator_id: invitedUser?.creator_id || currentUser?.creator_id,
          token
        }),
        credentials: 'include'
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
    } catch (error) {
      console.error('Submission error:', error);
    }
  };

  return (
    <>
      <Breadcrumb>
        <Breadcrumb.Item active>Organization Register</Breadcrumb.Item>
      </Breadcrumb>
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
                  <h4 className="mb-2">Organization starts here 🚀</h4>
                  <p className="mb-4">You can Register as Employee</p>

                  <Form onSubmit={handleSubmit}>
                    {/* Name Field */}
                    <Form.Group className="mb-3" controlId="name">
                      <Form.Control
                        type="text"
                        placeholder="Enter your Name"
                        name="name"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        required
                        autoFocus
                        autoComplete="name"
                      />
                      {errors.name && <Form.Text className="text-danger">{errors.name[0]}</Form.Text>}
                    </Form.Group>
                    {/* Email Field */}
                    <Form.Group className="mb-3" controlId="email">
                      <Form.Control
                        type="email"
                        placeholder="Email Address"
                        name="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                        autoComplete="username"
                        readOnly
                      />
                      {errors.email && <Form.Text className="text-danger">{errors.email[0]}</Form.Text>}
                    </Form.Group>
                    {/* Password Field */}
                    <Form.Group className="mb-3" controlId="password">
                      <Form.Control
                        type="password"
                        placeholder="Password"
                        name="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                        autoComplete="new-password"
                      />
                      {errors.password && <Form.Text className="text-danger">{errors.password[0]}</Form.Text>}
                    </Form.Group>
                    {/* Confirm Password Field */}
                    <Form.Group className="mb-3" controlId="passwordConfirmation">
                      <Form.Control
                        type="password"
                        placeholder="Confirm Password"
                        name="password_confirmation"
                        value={passwordConfirmation}
                        onChange={(e) => setPasswordConfirmation(e.target.value)}
                        required
                        autoComplete="new-password"
                      />
                      {errors.password_confirmation && <Form.Text className="text-danger">{errors.password_confirmation[0]}</Form.Text>}
                    </Form.Group>
                    {/* Terms & Conditions */}
                    <Form.Group className="mb-3" controlId="terms">
                      <Form.Check
                        type="checkbox"
                        id="terms"
                        name="terms"
                        label={
                          <>
                            I agree to <a href="/privacy-policy">privacy policy &amp; terms</a>
                          </>
                        }
                        checked={terms}
                        onChange={(e) => setTerms(e.target.checked)}
                      />
                      {errors.terms && <Form.Text className="text-danger">{errors.terms[0]}</Form.Text>}
                    </Form.Group>
                    <input type="hidden" name="creator_id" value={invitedUser?.creator_id || currentUser?.creator_id} />
                    <Button variant="primary" type="submit" className="mb-4" block>
                      Sign up
                    </Button>
                  </Form>
                  <p className="mb-2">
                    Already have an account?{' '}
                    <NavLink to="/auth/signin-1" className="f-w-400">
                      Sign in instead
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

export default OrganizationRegister;
