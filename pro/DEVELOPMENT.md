# Modern Financial Management System - Development Guide

## 🚀 Quick Start

### 1. Install Dependencies
```bash
npm install
```

### 2. Set Up Environment
```bash
# Copy environment file
cp .env.example .env

# Edit .env with your settings
# Database credentials, JWT secret, etc.
```

### 3. Set Up Database
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
mysql -u root -p pro < database/migrations/001_create_tables.sql
```

### 4. Start Development
```bash
# Start all services
npm run dev

# Or start individually:
npm run dev:css    # SCSS compilation
npm run dev:js     # TypeScript compilation
npm run dev:php    # PHP development server
```

## 🛠️ Development Workflow

### File Structure
```
src/
├── api/           # Backend API (PHP)
├── components/    # React components (TSX)
├── pages/         # Page components (TSX)
├── styles/        # SCSS styles
├── utils/         # Utility functions (TS)
└── types/         # TypeScript definitions
```

### Adding New Features

1. **Backend API**
   - Add routes in `src/api/routes.php`
   - Create controllers in `src/api/`
   - Update database schema in `database/migrations/`

2. **Frontend Components**
   - Create components in `src/components/`
   - Add pages in `src/pages/`
   - Update types in `src/types/`

3. **Styling**
   - Add SCSS files in `src/styles/`
   - Use mixins and variables
   - Follow the design system

### Code Standards

- **TypeScript**: Use strict typing
- **PHP**: Follow PSR-12 standards
- **SCSS**: Use BEM methodology
- **Git**: Conventional commits

### Testing

```bash
# Run all tests
npm test

# Run specific tests
npm test -- --grep "authentication"

# Watch mode
npm run test:watch
```

## 🔧 Configuration

### Environment Variables
```env
# Application
APP_NAME="Financial Management System"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=pro
DB_USER=root
DB_PASS=

# JWT
JWT_SECRET=your-super-secret-jwt-key
JWT_EXPIRES_IN=24h
JWT_REFRESH_EXPIRES_IN=7d
```

### TypeScript Configuration
- Strict mode enabled
- Path mapping configured
- Source maps enabled
- Declaration files generated

### SCSS Configuration
- Modern CSS features
- Autoprefixer enabled
- Source maps in development
- Minified in production

## 🎨 Design System

### Colors
```scss
// Primary
--primary-50: #f0f9ff;
--primary-500: #0ea5e9;
--primary-900: #0c4a6e;

// Secondary
--secondary-50: #f8fafc;
--secondary-500: #64748b;
--secondary-900: #0f172a;
```

### Typography
```scss
// Font families
--font-family-primary: 'Vazirmatn', sans-serif;
--font-family-mono: 'JetBrains Mono', monospace;

// Font sizes
--text-xs: 0.75rem;
--text-sm: 0.875rem;
--text-base: 1rem;
--text-lg: 1.125rem;
```

### Spacing
```scss
// Spacing scale
--spacing-xs: 0.25rem;
--spacing-sm: 0.5rem;
--spacing-md: 1rem;
--spacing-lg: 1.5rem;
--spacing-xl: 2rem;
```

## 🔐 Authentication Flow

1. **Login**
   - User submits credentials
   - Server validates and returns JWT
   - Client stores token in localStorage

2. **API Requests**
   - Token sent in Authorization header
   - Server validates token
   - Returns data or 401 error

3. **Token Refresh**
   - Client detects expired token
   - Sends refresh token
   - Server returns new access token

## 📱 Responsive Design

### Breakpoints
```scss
$breakpoints: (
  xs: 0,
  sm: 576px,
  md: 768px,
  lg: 992px,
  xl: 1200px,
  xxl: 1400px
);
```

### Mobile-First Approach
- Start with mobile styles
- Use min-width media queries
- Progressive enhancement

## 🌙 Dark Mode

### Implementation
- CSS custom properties
- Data attribute switching
- Smooth transitions
- System preference detection

### Usage
```scss
[data-theme="dark"] {
  --primary-50: #0c4a6e;
  --primary-500: #38bdf8;
  // ... other dark mode variables
}
```

## 🚀 Performance

### Frontend
- Code splitting
- Lazy loading
- Image optimization
- Bundle analysis

### Backend
- Database indexing
- Query optimization
- Caching strategies
- Response compression

## 🧪 Testing Strategy

### Unit Tests
- Utility functions
- Component logic
- API endpoints

### Integration Tests
- Authentication flow
- Database operations
- API responses

### E2E Tests
- User workflows
- Cross-browser testing
- Performance testing

## 📦 Build Process

### Development
```bash
npm run dev
# - SCSS compilation with source maps
# - TypeScript compilation with watch mode
# - PHP development server
```

### Production
```bash
npm run build
# - SCSS compilation with minification
# - TypeScript compilation with optimization
# - Asset optimization
```

## 🔍 Debugging

### Frontend
- Browser DevTools
- React DevTools
- Source maps
- Console logging

### Backend
- PHP error logging
- Database query logging
- API response logging
- Performance profiling

## 📚 Resources

### Documentation
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [React Documentation](https://reactjs.org/docs/)
- [SCSS Documentation](https://sass-lang.com/documentation)
- [PHP Documentation](https://www.php.net/docs.php)

### Tools
- [VS Code](https://code.visualstudio.com/)
- [PHPStorm](https://www.jetbrains.com/phpstorm/)
- [MySQL Workbench](https://www.mysql.com/products/workbench/)
- [Postman](https://www.postman.com/)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

### Code Review Checklist
- [ ] Code follows standards
- [ ] Tests pass
- [ ] Documentation updated
- [ ] Performance considered
- [ ] Security reviewed
