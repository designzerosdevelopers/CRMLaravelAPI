import React, { useContext, useMemo } from 'react';
import { ConfigContext } from '../../../contexts/ConfigContext';
import { UserContext } from '../../../contexts/UserContext';
import useWindowSize from '../../../hooks/useWindowSize';
import NavLogo from './NavLogo';
import NavContent from './NavContent';
import getMenuItems from '../../../menu-items';

const NavigationComponent = () => {
  const {
    state: { collapseMenu }
  } = useContext(ConfigContext);
  const windowSize = useWindowSize();
  const { role } = useContext(UserContext);

  // Compute the navigation menu only when role changes.
  const navigation = useMemo(() => getMenuItems(role), [role]);

  const navClass = ['pcoded-navbar'];
  if (windowSize.width < 992 && collapseMenu) {
    navClass.push('mob-open');
  } else if (collapseMenu) {
    navClass.push('navbar-collapsed');
  }

  return (
    <nav className={navClass.join(' ')}>
      <div className="navbar-wrapper">
        <NavLogo />
        <NavContent navigation={navigation.items} />
      </div>
    </nav>
  );
};

export default NavigationComponent;
