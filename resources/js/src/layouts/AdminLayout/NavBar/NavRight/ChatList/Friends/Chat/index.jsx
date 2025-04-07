import PropTypes from 'prop-types';
import React from 'react';
import { FormControl, Button, InputGroup, Card } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import PerfectScrollbar from 'react-perfect-scrollbar';

import chatMsg from './chat';
import Messages from './Messages';

const Chat = ({ user, chatOpen, listOpen, closed }) => {
  // Conditionally add 'open' class if chatOpen and listOpen are true
  let chatClass = ['header-chat'];
  if (chatOpen && listOpen) {
    chatClass.push('open');
  }

  // Default message if no matching chats are found or if user is null
  let message = (
    <Card className="d-flex align-items-center shadow-none mb-0 p-0" style={{ flexDirection: 'row', backgroundColor: 'unset' }}>
      <Card.Body className="p-0 chat-menu-content">
        <div>
          <p className="chat-cont">CHAT NOT FOUND</p>
        </div>
      </Card.Body>
    </Card>
  );

  // Only filter through chatMsg if we have a valid user with an id
  if (user && user.id) {
    chatMsg.filter((chats) => {
      if (chats.friend_id === user.id) {
        // Build the message array if there's a matching friend_id
        message = chats.messages.map((msg, index) => <Messages key={index} message={msg} name={user.name} photo={chats.friend_photo} />);
      }
      return false; // filter callback expects a boolean
    });
  }

  return (
    <>
      <div className={chatClass.join(' ')}>
        <div className="h-list-header">
          <h6>{user && user.name ? user.name : 'No user selected'}</h6>
          <Link to="#" className="h-back-user-list" onClick={closed}>
            <i className="feather icon-chevron-left text-muted" />
          </Link>
        </div>
        <div className="h-list-body">
          <div className="main-chat-cont">
            <PerfectScrollbar>
              <div className="main-friend-chat">{message}</div>
            </PerfectScrollbar>
          </div>
        </div>
        <div className="h-list-footer">
          <InputGroup>
            <Button variant="success" className="btn-attach">
              <i className="feather icon-paperclip" />
            </Button>
            <FormControl type="text" name="h-chat-text" className="h-send-chat" placeholder="Write here..." />
            <Button type="submit" className="input-group-append btn-send">
              <i className="feather icon-message-circle" />
            </Button>
          </InputGroup>
        </div>
      </div>
    </>
  );
};

Chat.propTypes = {
  user: PropTypes.oneOfType([PropTypes.array, PropTypes.object]),
  chatOpen: PropTypes.bool,
  listOpen: PropTypes.bool,
  id: PropTypes.number,
  closed: PropTypes.func,
  name: PropTypes.string
};

export default Chat;
