import React, { createContext, useState, useEffect } from 'react';

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
  const [user, setUser] = useState(() => {
    const storedUser = localStorage.getItem('user');
    return storedUser ? JSON.parse(storedUser) : null;
  });

  const [role, setRole] = useState(() => {
    const storedRole = localStorage.getItem('role');
    return storedRole ? storedRole : user?.role || '';
  });

  // ✅ Update role whenever user changes
  useEffect(() => {
    if (user?.role) {
      localStorage.setItem('role', user.role);
      setRole(user.role);
    }
  }, [user]);

  const updateRole = (newRole) => {
    localStorage.setItem('role', newRole);
    setRole(newRole);
  };

  const updateUser = (userData) => {
    localStorage.setItem('user', JSON.stringify(userData));
    setUser(userData);
    if (userData.role) {
      updateRole(userData.role);
    }
  };

  return (
    <UserContext.Provider value={{ user, setUser: updateUser, role, updateRole }}>
      {children}
    </UserContext.Provider>
  );
};
