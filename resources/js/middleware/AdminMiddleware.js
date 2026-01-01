import { useUserStore } from '@/stores/useUserStore';

/**
 * Admin Middleware
 * Checks if the user has admin role
 * Redirects to home if not admin
 */
export default (to, from) => {
  const store = useUserStore();
  
  // Check if user exists and has admin role
  if (!store.user || store.user.role !== 'admin') {
    // Redirect to home page
    return { name: 'home' };
  }
  
  // Allow access for admin users
  return true;
};
