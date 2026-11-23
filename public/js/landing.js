/**
 * ==========================================================================
 * Landing Page Scripts - Credit Engine AI
 * ==========================================================================
 */

// Configure Tailwind locally if runtime is present
if (typeof tailwind !== 'undefined') {
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          brand: {
            50: '#eff6ff',
            100: '#dbeafe',
            200: '#bfdbfe',
            300: '#93c5fd',
            400: '#60a5fa',
            500: '#3b82f6',
            600: '#2563eb',
            700: '#1d4ed8',
            800: '#1e40af',
            900: '#1e3a8a',
          },
          slate: {
            50: '#f8fafc',
            100: '#f1f5f9',
            200: '#e2e8f0',
            300: '#cbd5e1',
            400: '#94a3b8',
            500: '#64748b',
            600: '#475569',
            700: '#334155',
            800: '#1e293b',
            900: '#0f172a',
          }
        },
        borderRadius: {
          '2xl': '1rem',
          '3xl': '1.5rem',
          '4xl': '2rem',
        },
        boxShadow: {
          'subtle': '0 2px 10px rgba(0, 0, 0, 0.03), 0 10px 30px rgba(0, 0, 0, 0.04)',
          'card': '0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02)',
          'float': '0 20px 40px -15px rgba(37, 99, 235, 0.12)',
          'blue-glow': '0 10px 25px -5px rgba(37, 99, 235, 0.25)',
        }
      }
    }
  };
}

/**
 * Toggle Mobile Navigation Menu Drawer
 * @param {boolean} [force] - Force state (true = show, false = hide)
 */
function toggleMobileMenu(force) {
  const menu = document.getElementById('mobileMenu');
  const icon = document.getElementById('mobileMenuIcon');
  if (!menu) return;

  const isCurrentlyHidden = menu.classList.contains('hidden');
  const shouldShow = typeof force === 'boolean' ? force : isCurrentlyHidden;

  if (shouldShow) {
    menu.classList.remove('hidden');
    if (icon) icon.textContent = 'close';
  } else {
    menu.classList.add('hidden');
    if (icon) icon.textContent = 'menu';
  }
}

// Attach event listeners when document is ready
document.addEventListener('DOMContentLoaded', function () {
  // Auto-close mobile menu on Escape key press
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      toggleMobileMenu(false);
    }
  });

  // Auto-close mobile menu when clicking outside
  document.addEventListener('click', function (e) {
    const menu = document.getElementById('mobileMenu');
    const toggleBtn = e.target.closest('button[onclick*="toggleMobileMenu"]');
    if (menu && !menu.classList.contains('hidden') && !menu.contains(e.target) && !toggleBtn) {
      toggleMobileMenu(false);
    }
  });
});
