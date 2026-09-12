/**
 * Government Workflow OS — Client-Side Authentication Service
 * Mirrors PHP session management for standalone HTML environments (localStorage)
 */

const AuthService = {
  STORAGE_KEY: 'gov_workflow_session',

  // Demo user profile
  DEFAULT_USER: {
    id: 1,
    first_name: 'Juan',
    last_name: 'Dela Cruz',
    full_name: 'Juan Dela Cruz',
    initials: 'JD',
    position: 'Administrative Officer',
    email: 'juan.delacruz@pgov.ph',
    role: 'admin',
    office_id: 1,
    office_name: 'Provincial Assessor\'s Office',
    organization_id: 1,
    organization_name: 'Provincial Government'
  },

  /**
   * Check if user is logged in
   */
  isLoggedIn() {
    try {
      const session = localStorage.getItem(this.STORAGE_KEY);
      return !!session;
    } catch (e) {
      return false;
    }
  },

  /**
   * Get current authenticated user
   */
  getCurrentUser() {
    try {
      const session = localStorage.getItem(this.STORAGE_KEY);
      if (!session) return this.DEFAULT_USER;
      return JSON.parse(session);
    } catch (e) {
      return this.DEFAULT_USER;
    }
  },

  /**
   * Authenticate with email & password
   */
  login(email, password) {
    const cleanEmail = (email || '').trim().toLowerCase();
    
    // Accept demo credentials
    if ((cleanEmail === 'juan.delacruz@pgov.ph' || cleanEmail === 'admin@pgov.ph') && password === 'Password123!') {
      const user = { ...this.DEFAULT_USER, email: cleanEmail };
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(user));
      return { success: true, user };
    }

    return {
      success: false,
      message: 'Invalid official email or password. Use demo credentials.'
    };
  },

  /**
   * Sign out
   */
  logout() {
    localStorage.removeItem(this.STORAGE_KEY);
  },

  /**
   * Route Guard: Require authentication on protected pages
   */
  requireAuth(loginUrl = '../login.html') {
    if (!this.isLoggedIn()) {
      window.location.replace(loginUrl);
    }
  },

  /**
   * Route Guard: Require guest on login page
   */
  requireGuest(dashboardUrl = 'pages/dashboard.html') {
    if (this.isLoggedIn()) {
      window.location.replace(dashboardUrl);
    }
  }
};

window.AuthService = AuthService;
