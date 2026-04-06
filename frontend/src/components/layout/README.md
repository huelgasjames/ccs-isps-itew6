# Layout Components

This folder contains the main layout components for the CCS Comprehensive Profiling System.

## Components

### AppHeader.vue
- **Purpose**: Top navigation header with search, notifications, theme toggle, and user menu
- **Features**:
  - Brand logo and system name
  - Global search functionality
  - Notification system with badge
  - Theme toggle (dark/light mode)
  - User dropdown menu with profile, settings, and logout
  - Responsive design for mobile devices

### AppNavBar.vue
- **Purpose**: Left sidebar navigation with organized menu sections
- **Features**:
  - Organized navigation sections (Main, User Management, Academic, Activities)
  - Role-based menu items (admin-only features)
  - Quick action buttons for common tasks
  - Mobile-responsive with hamburger menu
  - Active route highlighting
  - Smooth transitions and hover effects

### AppFooter.vue
- **Purpose**: Application footer with system information and links
- **Features**:
  - System information (version, status, security)
  - Quick navigation links
  - Resource links (help, documentation, support)
  - Contact information
  - Social media links
  - Legal links (privacy, terms, cookies)
  - Fully responsive design

## Usage

These components are automatically imported and used in `AppLayout.vue`. The layout structure is:

```
AppLayout
├── AppHeader (fixed top)
├── AppNavBar (fixed left)
├── Main Content (flexible area)
└── AppFooter (bottom)
```

## Styling

All components use:
- Consistent color scheme with CSS custom properties
- Dark mode support via `.dark` class
- Responsive design with mobile-first approach
- Smooth transitions and micro-interactions
- Semantic HTML5 structure
- Accessibility considerations (ARIA labels, keyboard navigation)

## Customization

To customize the layout:
1. Modify individual component files
2. Update CSS variables in each component
3. Adjust responsive breakpoints in media queries
4. Add new navigation items to AppNavBar
5. Update footer links and information in AppFooter
