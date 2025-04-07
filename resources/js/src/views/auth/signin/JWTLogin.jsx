// E:\ongoing-projects\CRMReactAdmin\src\views\auth\signin\JWTLogin.jsx
import React, { useContext, useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Formik } from 'formik';
import * as Yup from 'yup';
import { Row, Col, Button, Alert } from 'react-bootstrap';
import { UserContext } from '../../../contexts/UserContext';

const JWTLogin = () => {
  const { setUser, updateRole } = useContext(UserContext);
  const navigate = useNavigate();
  const [componentErrors, setComponentErrors] = useState([]);
  const [loading, setLoading] = useState(false);

  // On mount, fetch CSRF cookie
  useEffect(() => {
    const fetchCsrfCookie = async () => {
      try {
        // Adjust URL based on your Sanctum configuration.
        // If sanctum.php has 'prefix' => 'api', then use `/api/sanctum/csrf-cookie`
        await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
          method: 'GET',
          credentials: 'include', // send cookies
          headers: {
            Accept: 'application/json'
          }
        });
      } catch (error) {
        console.error('Error fetching CSRF cookie:', error);
      }
    };
    fetchCsrfCookie();
  }, []);

  // Updated handleLogin to receive Formik values instead of event
  const handleLogin = async (values, { setSubmitting }) => {
    setLoading(true);
    setComponentErrors([]);
    try {
      const loginUrl = `${import.meta.env.VITE_API_URL}/login`;
      const token = localStorage.getItem('auth_token');

      const response = await fetch(loginUrl, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: token ? `Bearer ${token}` : ''
        },
        body: JSON.stringify({
          email: values.email,
          password: values.password
        })
      });

      if (!response.ok) {
        const data = await response.json();
        setComponentErrors(data.errors || ['Login failed']);
      } else {
        const data = await response.json();

        // ✅ Extract the role correctly (convert to string if needed)
        const userRole = Array.isArray(data.data.roles) ? data.data.roles[0] : data.data.roles;

        // ✅ Store role & user in context
        updateRole(userRole);
        setUser({ ...data.data.user, role: userRole });

        // ✅ Save role & token in localStorage
        localStorage.setItem('role', userRole);
        if (data.data.token) {
          localStorage.setItem('auth_token', data.data.token);
        }

        navigate('/app/dashboard/default');
      }
    } catch (error) {
      console.error('Login error:', error);
      setComponentErrors(['An unexpected error occurred']);
    } finally {
      setLoading(false);
      setSubmitting(false);
    }
  };

  return (
    <Formik
      initialValues={{
        email: 'org@gmail.com',
        password: 'password',
        submit: null
      }}
      validationSchema={Yup.object().shape({
        email: Yup.string().email('Must be a valid email').max(255).required('Email is required'),
        password: Yup.string().max(255).required('Password is required')
      })}
      onSubmit={handleLogin}
    >
      {({ errors, handleBlur, handleChange, handleSubmit, isSubmitting, touched, values }) => (
        <form noValidate onSubmit={handleSubmit}>
          <div className="form-group mb-3">
            <input
              className="form-control"
              name="email"
              onBlur={handleBlur}
              onChange={handleChange}
              type="email"
              value={values.email}
              placeholder="Email Address / Username"
            />
            {touched.email && errors.email && <small className="text-danger form-text">{errors.email}</small>}
          </div>
          <div className="form-group mb-4">
            <input
              className="form-control"
              name="password"
              onBlur={handleBlur}
              onChange={handleChange}
              type="password"
              value={values.password}
              placeholder="Password"
            />
            {touched.password && errors.password && <small className="text-danger form-text">{errors.password}</small>}
          </div>

          <div className="custom-control custom-checkbox text-start mb-4 mt-2">
            <input type="checkbox" className="custom-control-input mx-2" id="customCheck1" />
            <label className="custom-control-label" htmlFor="customCheck1">
              Save credentials.
            </label>
          </div>

          {componentErrors.length > 0 && (
            <Col sm={12}>
              <Alert variant="danger">
                {componentErrors.map((err, idx) => (
                  <div key={idx}>{err}</div>
                ))}
              </Alert>
            </Col>
          )}

          <Row className="mt-2">
            <Col>
              <Button className="btn-block mb-4" variant="primary" type="submit" size="lg" disabled={isSubmitting || loading}>
                {loading ? 'Logging in...' : 'Signin'}
              </Button>
            </Col>
          </Row>
        </form>
      )}
    </Formik>
  );
};

export default JWTLogin;
