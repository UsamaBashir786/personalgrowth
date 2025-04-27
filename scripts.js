/**
 * Elevate - Personal Growth Tracking System
 * Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
  // Set active navigation link based on current page
  setActiveNavLink();
  
  // Initialize any tooltips
  initTooltips();
  
  // Add fade-in animation to cards
  animateCards();
  
  // Add event listeners for mobile menu toggle if it exists
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  if (mobileMenuButton) {
      mobileMenuButton.addEventListener('click', toggleMobileMenu);
  }
  
  // Add form submission handlers if forms exist
  initFormHandlers();
  
  // Initialize any charts if they exist
  initCharts();
});

/**
* Sets the active navigation link based on current page
*/
function setActiveNavLink() {
  const currentPage = window.location.pathname.split('/').pop();
  
  // Handle both desktop and mobile navigation
  const navLinks = document.querySelectorAll('.nav-link, .nav-link-mobile');
  
  navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href === currentPage || 
          (currentPage === '' && href === 'index.html') || 
          (currentPage === 'index.php' && href === 'index.html')) {
          link.classList.add('active');
      }
  });
}

/**
* Toggle mobile menu visibility
*/
function toggleMobileMenu() {
  const mobileMenu = document.getElementById('mobile-menu');
  mobileMenu.classList.toggle('hidden');
}

/**
* Initialize tooltips
*/
function initTooltips() {
  // Simple tooltip implementation
  const tooltips = document.querySelectorAll('[data-tooltip]');
  tooltips.forEach(tooltip => {
      tooltip.addEventListener('mouseenter', function() {
          const tooltipText = this.getAttribute('data-tooltip');
          const tooltipElement = document.createElement('div');
          tooltipElement.classList.add('tooltip');
          tooltipElement.textContent = tooltipText;
          document.body.appendChild(tooltipElement);
          
          const rect = this.getBoundingClientRect();
          tooltipElement.style.top = rect.bottom + 10 + 'px';
          tooltipElement.style.left = rect.left + (rect.width / 2) - (tooltipElement.offsetWidth / 2) + 'px';
          tooltipElement.style.opacity = '1';
      });
      
      tooltip.addEventListener('mouseleave', function() {
          const tooltipElement = document.querySelector('.tooltip');
          if (tooltipElement) {
              tooltipElement.remove();
          }
      });
  });
}

/**
* Add fade-in animation to cards
*/
function animateCards() {
  const cards = document.querySelectorAll('.card');
  cards.forEach((card, index) => {
      setTimeout(() => {
          card.classList.add('fade-in-up');
      }, index * 100); // Stagger the animations
  });
}

/**
* Initialize form submission handlers
*/
function initFormHandlers() {
  // Task form
  const taskForm = document.getElementById('task-form');
  if (taskForm) {
      taskForm.addEventListener('submit', handleTaskSubmit);
  }
  
  // Quote form
  const quoteForm = document.getElementById('quote-form');
  if (quoteForm) {
      quoteForm.addEventListener('submit', handleQuoteSubmit);
  }
  
  // Thought form
  const thoughtForm = document.getElementById('thought-form');
  if (thoughtForm) {
      thoughtForm.addEventListener('submit', handleThoughtSubmit);
  }
  
  // Learning form
  const learningForm = document.getElementById('learning-form');
  if (learningForm) {
      learningForm.addEventListener('submit', handleLearningSubmit);
  }
  
  // Timetable form
  const timetableForm = document.getElementById('timetable-form');
  if (timetableForm) {
      timetableForm.addEventListener('submit', handleTimetableSubmit);
  }
}

/**
* Handle task form submission
* @param {Event} e - Form submit event
*/
function handleTaskSubmit(e) {
  e.preventDefault();
  
  // Show loading spinner
  showLoadingSpinner('task-submit-btn');
  
  const formData = new FormData(e.target);
  
  fetch('api/tasks.php?action=add', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      hideLoadingSpinner('task-submit-btn', 'Add Task');
      
      if (data.success) {
          showAlert('success', 'Task added successfully!');
          e.target.reset();
          // Refresh task list if it exists
          if (typeof refreshTasks === 'function') {
              refreshTasks();
          }
      } else {
          showAlert('error', data.message || 'Error adding task');
      }
  })
  .catch(error => {
      hideLoadingSpinner('task-submit-btn', 'Add Task');
      showAlert('error', 'An error occurred. Please try again.');
      console.error('Error:', error);
  });
}

/**
* Handle quote form submission
* @param {Event} e - Form submit event
*/
function handleQuoteSubmit(e) {
  e.preventDefault();
  
  showLoadingSpinner('quote-submit-btn');
  
  const formData = new FormData(e.target);
  
  fetch('api/quotes.php?action=add', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      hideLoadingSpinner('quote-submit-btn', 'Add Quote');
      
      if (data.success) {
          showAlert('success', 'Quote added successfully!');
          e.target.reset();
          if (typeof refreshQuotes === 'function') {
              refreshQuotes();
          }
      } else {
          showAlert('error', data.message || 'Error adding quote');
      }
  })
  .catch(error => {
      hideLoadingSpinner('quote-submit-btn', 'Add Quote');
      showAlert('error', 'An error occurred. Please try again.');
      console.error('Error:', error);
  });
}

/**
* Handle thought form submission
* @param {Event} e - Form submit event
*/
function handleThoughtSubmit(e) {
  e.preventDefault();
  
  showLoadingSpinner('thought-submit-btn');
  
  const formData = new FormData(e.target);
  
  fetch('api/thoughts.php?action=add', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      hideLoadingSpinner('thought-submit-btn', 'Add Thought');
      
      if (data.success) {
          showAlert('success', 'Thought added successfully!');
          e.target.reset();
          if (typeof refreshThoughts === 'function') {
              refreshThoughts();
          }
      } else {
          showAlert('error', data.message || 'Error adding thought');
      }
  })
  .catch(error => {
      hideLoadingSpinner('thought-submit-btn', 'Add Thought');
      showAlert('error', 'An error occurred. Please try again.');
      console.error('Error:', error);
  });
}

/**
* Handle learning form submission
* @param {Event} e - Form submit event
*/
function handleLearningSubmit(e) {
  e.preventDefault();
  
  showLoadingSpinner('learning-submit-btn');
  
  const formData = new FormData(e.target);
  
  fetch('api/learnings.php?action=add', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      hideLoadingSpinner('learning-submit-btn', 'Add Learning');
      
      if (data.success) {
          showAlert('success', 'Learning added successfully!');
          e.target.reset();
          if (typeof refreshLearnings === 'function') {
              refreshLearnings();
          }
      } else {
          showAlert('error', data.message || 'Error adding learning');
      }
  })
  .catch(error => {
      hideLoadingSpinner('learning-submit-btn', 'Add Learning');
      showAlert('error', 'An error occurred. Please try again.');
      console.error('Error:', error);
  });
}

/**
* Handle timetable form submission
* @param {Event} e - Form submit event
*/
function handleTimetableSubmit(e) {
  e.preventDefault();
  
  showLoadingSpinner('timetable-submit-btn');
  
  const formData = new FormData(e.target);
  
  fetch('api/timetable.php?action=add', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      hideLoadingSpinner('timetable-submit-btn', 'Add Schedule');
      
      if (data.success) {
          showAlert('success', 'Schedule added successfully!');
          e.target.reset();
          if (typeof refreshTimetable === 'function') {
              refreshTimetable();
          }
      } else {
          showAlert('error', data.message || 'Error adding schedule');
      }
  })
  .catch(error => {
      hideLoadingSpinner('timetable-submit-btn', 'Add Schedule');
      showAlert('error', 'An error occurred. Please try again.');
      console.error('Error:', error);
  });
}

/**
* Show loading spinner in button
* @param {string} buttonId - ID of the button
*/
function showLoadingSpinner(buttonId) {
  const button = document.getElementById(buttonId);
  if (button) {
      const originalText = button.textContent;
      button.setAttribute('data-original-text', originalText);
      button.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-b-transparent border-white rounded-full animate-spin mr-2"></span> Processing...';
      button.disabled = true;
  }
}

/**
* Hide loading spinner in button
* @param {string} buttonId - ID of the button
* @param {string} originalText - Original button text (optional)
*/
function hideLoadingSpinner(buttonId, originalText = null) {
  const button = document.getElementById(buttonId);
  if (button) {
      originalText = originalText || button.getAttribute('data-original-text') || 'Submit';
      button.textContent = originalText;
      button.disabled = false;
  }
}

/**
* Show alert message
* @param {string} type - Alert type ('success', 'error', 'info', 'warning')
* @param {string} message - Alert message
* @param {number} duration - Duration in ms (default: 5000)
*/
function showAlert(type, message, duration = 5000) {
  // Create alert element
  const alertElement = document.createElement('div');
  alertElement.classList.add('fixed', 'top-20', 'right-4', 'max-w-xs', 'p-4', 'rounded-lg', 'shadow-lg', 'z-50', 'fade-in-up');
  
  // Set background color based on type
  switch (type) {
      case 'success':
          alertElement.classList.add('bg-green-600', 'text-white');
          break;
      case 'error':
          alertElement.classList.add('bg-red-600', 'text-white');
          break;
      case 'warning':
          alertElement.classList.add('bg-yellow-500', 'text-gray-900');
          break;
      case 'info':
      default:
          alertElement.classList.add('bg-blue-600', 'text-white');
          break;
  }
  
  // Set content
  alertElement.innerHTML = `
      <div class="flex items-center justify-between">
          <div class="flex items-center">
              <span class="text-lg font-medium">${message}</span>
          </div>
          <button class="text-white ml-4 focus:outline-none">
              <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
              </svg>
          </button>
      </div>
  `;
  
  document.body.appendChild(alertElement);
  
  // Close button functionality
  const closeButton = alertElement.querySelector('button');
  if (closeButton) {
      closeButton.addEventListener('click', () => {
          removeAlert(alertElement);
      });
  }
  
  // Auto remove after duration
  setTimeout(() => {
      removeAlert(alertElement);
  }, duration);
}

/**
* Remove alert with animation
* @param {HTMLElement} alertElement - The alert element to remove
*/
function removeAlert(alertElement) {
  alertElement.style.opacity = '0';
  alertElement.style.transform = 'translateY(-20px)';
  
  setTimeout(() => {
      if (alertElement.parentNode) {
          alertElement.parentNode.removeChild(alertElement);
      }
  }, 300);
}

/**
* Initialize charts on dashboard
*/
function initCharts() {
  initTaskCompletionChart();
  initTimeSpentChart();
  initProgressChart();
}

/**
* Initialize task completion chart
*/
function initTaskCompletionChart() {
  const taskChartCanvas = document.getElementById('taskCompletionChart');
  if (!taskChartCanvas) return;
  
  // Fetch task completion data
  fetch('api/tasks.php?action=stats')
      .then(response => response.json())
      .then(data => {
          if (!data.success) {
              console.error('Error fetching task completion data:', data.message);
              return;
          }
          
          const ctx = taskChartCanvas.getContext('2d');
          new Chart(ctx, {
              type: 'bar',
              data: {
                  labels: data.labels || ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                  datasets: [{
                      label: 'Tasks Completed',
                      data: data.values || [0, 0, 0, 0, 0, 0, 0],
                      backgroundColor: 'rgba(245, 158, 11, 0.8)',
                      borderColor: 'rgba(245, 158, 11, 1)',
                      borderWidth: 1
                  }]
              },
              options: {
                  responsive: true,
                  plugins: {
                      legend: {
                          position: 'top',
                          labels: {
                              color: '#F3F4F6'
                          }
                      },
                      title: {
                          display: true,
                          text: 'Tasks Completed This Week',
                          color: '#F3F4F6',
                          font: {
                              size: 16
                          }
                      }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          grid: {
                              color: 'rgba(255, 255, 255, 0.1)'
                          },
                          ticks: {
                              color: '#D1D5DB'
                          }
                      },
                      x: {
                          grid: {
                              color: 'rgba(255, 255, 255, 0.1)'
                          },
                          ticks: {
                              color: '#D1D5DB'
                          }
                      }
                  }
              }
          });
      })
      .catch(error => {
          console.error('Error initializing task completion chart:', error);
      });
}

/**
* Initialize time spent chart
*/
function initTimeSpentChart() {
  const timeChartCanvas = document.getElementById('timeSpentChart');
  if (!timeChartCanvas) return;
  
  // Fetch time spent data
  fetch('api/tasks.php?action=timeStats')
      .then(response => response.json())
      .then(data => {
          if (!data.success) {
              console.error('Error fetching time spent data:', data.message);
              return;
          }
          
          const ctx = timeChartCanvas.getContext('2d');
          new Chart(ctx, {
              type: 'pie',
              data: {
                  labels: data.labels || ['No Data'],
                  datasets: [{
                      data: data.values || [1],
                      backgroundColor: [
                          'rgba(245, 158, 11, 0.8)',
                          'rgba(16, 185, 129, 0.8)',
                          'rgba(59, 130, 246, 0.8)',
                          'rgba(139, 92, 246, 0.8)',
                          'rgba(236, 72, 153, 0.8)'
                      ],
                      borderColor: [
                          'rgba(245, 158, 11, 1)',
                          'rgba(16, 185, 129, 1)',
                          'rgba(59, 130, 246, 1)',
                          'rgba(139, 92, 246, 1)',
                          'rgba(236, 72, 153, 1)'
                      ],
                      borderWidth: 1
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: {
                          position: 'right',
                          labels: {
                              color: '#F3F4F6'
                          }
                      },
                      title: {
                          display: true,
                          text: 'Time Spent by Category',
                          color: '#F3F4F6',
                          font: {
                              size: 16
                          }
                      }
                  }
              }
          });
      })
      .catch(error => {
          console.error('Error initializing time spent chart:', error);
      });
}

/**
* Initialize progress over time chart
*/
function initProgressChart() {
  const progressChartCanvas = document.getElementById('progressChart');
  if (!progressChartCanvas) return;
  
  // Fetch progress data
  fetch('api/tasks.php?action=progressStats')
      .then(response => response.json())
      .then(data => {
          if (!data.success) {
              console.error('Error fetching progress data:', data.message);
              return;
          }
          
          const ctx = progressChartCanvas.getContext('2d');
          new Chart(ctx, {
              type: 'line',
              data: {
                  labels: data.labels || ['No Data'],
                  datasets: [{
                      label: 'Tasks Completed',
                      data: data.values || [0],
                      backgroundColor: 'rgba(245, 158, 11, 0.2)',
                      borderColor: 'rgba(245, 158, 11, 1)',
                      borderWidth: 2,
                      tension: 0.4,
                      fill: true
                  }]
              },
              options: {
                  responsive: true,
                  plugins: {
                      legend: {
                          position: 'top',
                          labels: {
                              color: '#F3F4F6'
                          }
                      },
                      title: {
                          display: true,
                          text: 'Progress Over Time',
                          color: '#F3F4F6',
                          font: {
                              size: 16
                          }
                      }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          grid: {
                              color: 'rgba(255, 255, 255, 0.1)'
                          },
                          ticks: {
                              color: '#D1D5DB'
                          }
                      },
                      x: {
                          grid: {
                              color: 'rgba(255, 255, 255, 0.1)'
                          },
                          ticks: {
                              color: '#D1D5DB'
                          }
                      }
                  }
              }
          });
      })
      .catch(error => {
          console.error('Error initializing progress chart:', error);
      });
}

/**
* Mark task as complete
* @param {number} taskId - ID of task to mark as complete
*/
function completeTask(taskId) {
  if (!confirm('Mark this task as complete?')) return;
  
  fetch(`api/tasks.php?action=complete&id=${taskId}`)
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              showAlert('success', 'Task marked as complete!');
              if (typeof refreshTasks === 'function') {
                  refreshTasks();
              }
          } else {
              showAlert('error', data.message || 'Error completing task');
          }
      })
      .catch(error => {
          showAlert('error', 'An error occurred. Please try again.');
          console.error('Error:', error);
      });
}

/**
* Delete item (task, quote, thought, learning, or timetable entry)
* @param {string} type - Type of item ('task', 'quote', 'thought', 'learning', 'timetable')
* @param {number} id - ID of item to delete
*/
function deleteItem(type, id) {
  if (!confirm('Are you sure you want to delete this item?')) return;
  
  fetch(`api/${type}s.php?action=delete&id=${id}`)
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              showAlert('success', 'Item deleted successfully!');
              
              // Refresh the appropriate list
              switch (type) {
                  case 'task':
                      if (typeof refreshTasks === 'function') refreshTasks();
                      break;
                  case 'quote':
                      if (typeof refreshQuotes === 'function') refreshQuotes();
                      break;
                  case 'thought':
                      if (typeof refreshThoughts === 'function') refreshThoughts();
                      break;
                  case 'learning':
                      if (typeof refreshLearnings === 'function') refreshLearnings();
                      break;
                  case 'timetable':
                      if (typeof refreshTimetable === 'function') refreshTimetable();
                      break;
              }
          } else {
              showAlert('error', data.message || 'Error deleting item');
          }
      })
      .catch(error => {
          showAlert('error', 'An error occurred. Please try again.');
          console.error('Error:', error);
      });
}