openSideMenu = document.getElementById('menu-toggle');
closeSideMenu = document.getElementById('menu-close');
sidebar = document.getElementById('sidebar');
listItem = document.getElementsByClassName('list-group-item');
pageContent = document.getElementById('page-content-wrapper');

closeSideMenu.addEventListener('click', function() {
  sidebar.style.width = '0';
  pageContent.style.marginLeft = '0'; // Adjust margin
  sidebar.style.opacity = 0; // Hide the sidebar
  sidebar.style.visibility = 'hidden'; // Hide the sidebar
});

openSideMenu.addEventListener('click', function() {
  sidebar.style.width = '250px'; // You can adjust the width as needed
  //pageContent.style.marginLeft = '250px'; // Match the sidebar width
  sidebar.style.opacity = 1; // Show the sidebar
  sidebar.style.visibility = 'visible'; // Show the sidebar
});


// Active page 
const activeLink = window.location.pathname;
console.log(activeLink);
const sideMenuLinks = document.querySelectorAll('.list-group-item');

sideMenuLinks.forEach(link => {
  if (link.href.includes(`${activeLink}`))  {
      link.classList.add("active");
  };
});

//Date
const date = new Date();

const day = date.getDate();
// getMonth() returns month from 0 to 11
const monthLists = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
let month = monthLists[date.getMonth()]; 
const year = date.getFullYear();

const date_time = `${month} ${day}, ${year}`;
document.getElementById("date").innerHTML = date_time;
