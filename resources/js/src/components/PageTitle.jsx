import React from 'react';

const PageTitle = ({ menu, page }) => {
  return (
    <div className="page-title">
      <h1>{menu} - {page}</h1>
    </div>
  );
};

export default PageTitle;
