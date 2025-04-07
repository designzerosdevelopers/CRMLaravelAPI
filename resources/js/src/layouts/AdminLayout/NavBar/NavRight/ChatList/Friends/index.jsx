import PropTypes from 'prop-types';
import React, { useState, useEffect } from 'react';

import friendData from './friends'; // renamed to friendData to avoid confusion with Friend component
import Friend from './Friend';
import Chat from './Chat';

const Friends = ({ listOpen }) => {
  // Initialize chatOpen based on listOpen and user as null
  const [chatOpen, setChatOpen] = useState(listOpen);
  const [user, setUser] = useState(null);

  // Only update chatOpen if listOpen changes to false and chatOpen isn't already false.
  useEffect(() => {
    if (!listOpen && chatOpen !== false) {
      setChatOpen(false);
    }
  }, [listOpen, chatOpen]);

  // Map through friendData to render the friend list.
  const friendList = friendData.map((f) => (
    <Friend
      key={f.id}
      data={f}
      activeId={user ? user.id : null}
      clicked={() => {
        setChatOpen(true);
        setUser(f);
      }}
    />
  ));

  return (
    <>
      {friendList}
      <Chat
        user={user}
        chatOpen={chatOpen}
        listOpen={listOpen}
        closed={() => {
          setChatOpen(false);
          setUser(null);
        }}
      />
    </>
  );
};

Friends.propTypes = {
  listOpen: PropTypes.bool
};

export default Friends;
