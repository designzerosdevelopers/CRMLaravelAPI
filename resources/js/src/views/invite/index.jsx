import React, { useState } from 'react';
import { Row, Col, Form, Button } from 'react-bootstrap';
import Card from '../../components/Card/MainCard';

const InvitePage = () => {
  const [email, setEmail] = useState('');
  const [errors, setErrors] = useState([]);
  const [success, setSuccess] = useState('');
  const [loading, setLoading] = useState(false);

  const handleInvite = async (e) => {
    e.preventDefault(); // Prevent page reload

    setLoading(true);
    setErrors([]);
    setSuccess('');

    try {
      // ✅ Correct CSRF request (without `/api`)
      await fetch(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
        credentials: 'include'
      });

      const endpoint = '/invite-sent';
      const url = `${import.meta.env.VITE_API_URL}${endpoint}`;
      const token = localStorage.getItem('auth_token');

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: `Bearer ${token}` // Needed if using token-based auth
        },
        credentials: 'include', // ✅ Required for Laravel Sanctum
        body: JSON.stringify({ email })
      });

      const data = await response.json();

      if (!response.ok) {
        setErrors(data.errors || ['An error occurred']);
      } else {
        setSuccess(`Invitation sent to: ${email}`);
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      setErrors(['An unexpected error occurred']);
    } finally {
      setLoading(false);
    }
  };

  return (
    <React.Fragment>
      <Row>
        <Col>
          <Card title="Send an Invitation">
            {errors.length > 0 && (
              <div className="alert alert-danger">
                {errors.map((err, index) => (
                  <p key={index}>{err}</p>
                ))}
              </div>
            )}
            {success && <div className="alert alert-success">{success}</div>}

            <Form onSubmit={handleInvite}>
              <Form.Group controlId="email">
                <Form.Label>Email Address</Form.Label>
                <Form.Control type="email" placeholder="Enter email" value={email} onChange={(e) => setEmail(e.target.value)} required />
              </Form.Group>
              <Button variant="primary" type="submit" className="mt-3" disabled={loading}>
                {loading ? 'Sending...' : 'Send'}
              </Button>
            </Form>
          </Card>
        </Col>
      </Row>
    </React.Fragment>
  );
};

export default InvitePage;
