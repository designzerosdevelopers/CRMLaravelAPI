import React, { useState } from 'react';
import { Row, Col, Card, Form, Button } from 'react-bootstrap';
import PageTitle from '../../components/PageTitle';
import { useNavigate } from 'react-router-dom';

const EmployeeCreate = ({ orgId, csrfToken, initialErrors = [], initialSuccess = '' }) => {
  // Form state
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [errors, setErrors] = useState(initialErrors);
  const [success, setSuccess] = useState(initialSuccess);
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors([]);
    setSuccess('');

    // Call CSRF endpoint first if not already done
    await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
      credentials: 'include'
    });

    const endpoint = orgId ? '/org-employee-store' : '/employee';
    const url = `${import.meta.env.VITE_API_URL}${endpoint}`;

    // Retrieve token if needed (for token-based auth)
    const token = localStorage.getItem('auth_token');

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken, // if needed
          Accept: 'application/json',

          // You may not need the Authorization header if using cookie-based auth with Sanctum.
          Authorization: `Bearer ${token}`
        },
        credentials: 'include',
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation: passwordConfirmation,
          ...(orgId ? { creator: orgId } : {})
        })
      });

      if (!response.ok) {
        const data = await response.json();
        setErrors(data.errors || ['An error occurred']);
      } else {
        const data = await response.json();
        setSuccess(data.message || 'Employee created successfully');
        setName('');
        setEmail('');
        setPassword('');
        setPasswordConfirmation('');
        navigate('/employee/index');
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      setErrors(['An unexpected error occurred']);
    } finally {
      setLoading(false);
    }
  };

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

      {success && <div className="alert alert-success">{success}</div>}

      <Row>
        <Col sm={12}>
          <Card className="mb-4">
            <Card.Header className="d-flex align-items-center justify-content-between">
              <Card.Title as="h5">Create Form</Card.Title>
              <small className="text-muted float-end">Fill all the fields</small>
            </Card.Header>
            <Card.Body>
              <Form onSubmit={handleSubmit}>
                <Form.Group as={Row} className="mb-3" controlId="formName">
                  <Form.Label column sm={2}>
                    Name
                  </Form.Label>
                  <Col sm={10}>
                    <Form.Control type="text" placeholder="Enter name" value={name} onChange={(e) => setName(e.target.value)} />
                  </Col>
                </Form.Group>

                <Form.Group as={Row} className="mb-3" controlId="formEmail">
                  <Form.Label column sm={2}>
                    Email
                  </Form.Label>
                  <Col sm={10}>
                    <Form.Control type="text" placeholder="Enter email" value={email} onChange={(e) => setEmail(e.target.value)} />
                  </Col>
                </Form.Group>

                <Form.Group as={Row} className="mb-3" controlId="formPassword">
                  <Form.Label column sm={2}>
                    Password
                  </Form.Label>
                  <Col sm={10}>
                    <Form.Control
                      type="password"
                      placeholder="Enter password"
                      required
                      autoComplete="new-password"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                    />
                  </Col>
                </Form.Group>

                <Form.Group as={Row} className="mb-3" controlId="formPasswordConfirm">
                  <Form.Label column sm={2}>
                    Confirm Password
                  </Form.Label>
                  <Col sm={10}>
                    <Form.Control
                      type="password"
                      placeholder="Confirm password"
                      required
                      autoComplete="new-password"
                      value={passwordConfirmation}
                      onChange={(e) => setPasswordConfirmation(e.target.value)}
                    />
                  </Col>
                </Form.Group>

                {orgId && <input type="hidden" name="creator" value={orgId} />}

                <Form.Group as={Row}>
                  <Col sm={{ span: 10, offset: 2 }}>
                    <Button variant="primary" type="submit" disabled={loading}>
                      {loading ? 'Sending...' : 'Send'}
                    </Button>
                  </Col>
                </Form.Group>
              </Form>
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </div>
  );
};

export default EmployeeCreate;
